<?php

function api_v1($domain) {
    // Validate if the 'config.php' file exists
    $current_path = dirname(__FILE__);
    if (!file_exists($current_path.'/config.php')) {
        echo '{"error":"config.php file not found","code":500}';
        exit;
    }

    // Load configuration
    require_once 'config.php';

    // Disable all errors
    if (!DEBUG) {
        error_reporting(0);
    }

    // Load the dependency and functions
    require_once 'vendor/autoload.php';
    // require_once '../functions.php';

    // Validate if $domain is a valid domain name
    if (!preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain) || !filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
        echo '{"error":"Invalid domain","code":400}';
        exit;
    }

    // Validate if the A record exists
    if (!nslookup_A_record($domain, '1.0.0.1')) {
        echo '{"error":"Domain A record not found","code":404}';
        exit;
    }

    // Detect web_server_status using the US server (localhost)
    $web_server_status_us_local = detect_web_server_status_localhost($domain);

    // Query the remote node API
    $api_json = query_node_api(DETECT_NODE_API_URL, $domain, $web_server_status_us_local);

    // Validate if this is a valid JSON
    $api_array = json_decode($api_json, true);
    if (!$api_array) {
        return '{"error":"Gateway Timeout: Node API timeout, try again","code":504}';
    } else {
        return $api_json;
    }
}

// Querying the API
function query_geoip_api($domain_or_ip) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GEOIP_API_URL . $domain_or_ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo '{"error":"Gateway Timeout: GeoIP API timeout, try again","code":504}';
        exit;
    }
    curl_close($ch);

    // Debug
    if (DEBUG) {
        echo $result;
    }

    // Validate if the result 'status' is 'success'
    $result_array = json_decode($result, true);
    if ($result_array['status'] !== 'success') {
        echo '{"error":"Gateway Timeout: GeoIP API timeout, try again","code":504}';
        exit;
    }
    return $result;
}

// Resolve the domain name to get the detected IP
function nslookup_A_record($domain, $dns_server_ip) {

    try {
        $resolver = new Net_DNS2_Resolver(array('nameservers' => array($dns_server_ip)));

        $resp = $resolver->query($domain, 'A');
        // If A record not found, try with CNAME record
        if (!$resp) {
            $resp = $resolver->query($domain, 'CNAME');
        }

        // Debug
        if (DEBUG) {
            print_r($resp);
        }

        // Get the A record
        $a_record_ip = $resp->answer[0]->address;
        if (!filter_var($a_record_ip, FILTER_VALIDATE_IP)) {
            $a_record_ip = $resp->answer[1]->address;
        }
        if (!filter_var($a_record_ip, FILTER_VALIDATE_IP)) {
            $a_record_ip = $resp->answer[2]->address;
        }
        if (!filter_var($a_record_ip, FILTER_VALIDATE_IP)) {
            $cname_name = $resp->answer[0]->name;
            $resp_2 = $resolver->query($cname_name, 'A');
            $a_record_ip = $resp_2->answer[0]->address;
        }
        
    }

    // Catch the exception
    catch (Net_DNS2_Exception $e) {
        return false;
    }
    
    // Validate if the detected_cn IP is a valid IP
    if (!filter_var($a_record_ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    return $a_record_ip;
}

// Querying the node API
function query_node_api($detect_node_api_url, $domain, $web_server_status) {
    $ch = curl_init();
    $query_url = $detect_node_api_url . '?domain='. $domain . '&web_server_status=' . $web_server_status;
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo '{"error":"Gateway Timeout: Node API timeout, try again","code":504}';
        exit;
    }
    curl_close($ch);

    // Debug
    if (DEBUG) {
        echo $result;
    }

    return $result;
}

// Get the website content using curl
function website_content_md5($domain) {
    // Convert domain name to a URL
    $url = 'http://' . $domain;
    // Get the content of the URL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36'); // Add user-agent
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Timeout after 5 seconds
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo '{"error":"Gateway Timeout: Website timeout, try again","code":504}';
        exit;
    }
    curl_close($ch);
    
    // Encode result to md5
    $result_md5 = md5($result);

    return $result_md5;
}

// Detect if the port is open for a domain, using localhost
function detect_port_open_localhost($ip_or_domain, $port) {
    $fp = @fsockopen($ip_or_domain, $port, $errno, $errstr, 3);
    if ($fp) {
        fclose($fp);
        return true;
    }
    return false;
}

// Detect if the port is open for a domain, using API
function detect_port_open_api($ip_or_domain) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PORT_CHECK_API_URL . $ip_or_domain);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36'); // Add user-agent
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout after 2 seconds
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return false;
    }
    curl_close($ch);

    // Decode JSON result
    $result_array = json_decode($result, true);
    if ($result_array['code'] !== 200) {
        return false;
    } else {
        // $port_443 = $result_array['port']['443'];
        // $port_80 = $result_array['port']['80'];
        return $result_array;
    }
    
}

// Detect web_server_status
function detect_web_server_status_localhost($domain) {
    $port_443 = detect_port_open_localhost($domain, 443);
    $port_80 = detect_port_open_localhost($domain, 80);
    if (!$port_80 && !$port_443) {
        $web_server_status = 'down';    
    } else {
        $web_server_status = 'up';
    }
    return $web_server_status;
}

// Detect web_server_status
function detect_web_server_status($domain) {
    // Detect if the port is open for a domain

    if (!PORT_CHECK_API_URL_ENABLED) {
        // Use localhost
        $web_server_status = detect_web_server_status_localhost($domain);
        return $web_server_status;
        
    } else {
        // Use remote API
        $result_array = detect_port_open_api($domain);
        if ($result_array) {
            $port_443 = $result_array['port']['443'];
            $port_80 = $result_array['port']['80'];
            if ($port_80 == "关闭" && $port_443 == "关闭") {
                $web_server_status = 'down';    
            } else {
                $web_server_status = 'up';
            }
            return $web_server_status;
        } else {
            // If API is down, fallback to use localhost
            $web_server_status = detect_web_server_status_localhost($domain);
            return $web_server_status;
        }
    }
}

function pastebin($api_paste_code, $api_paste_name) {

    // Load configuration
    require_once 'config.php';
    $api_dev_key = PASTEBIN_API_DEV_KEY; // your api_developer_key
    // $api_paste_code 		= 'just some random text you :)'; // your paste text
    $api_paste_private 		= '1'; // 0=public 1=unlisted 2=private
    // $api_paste_name			= 'justmyfilename.php'; // name or title of your paste
    $api_paste_expire_date 	= '6M';
    $api_paste_format 		= 'json';
    $api_user_key 			= ''; // if an invalid or expired api_user_key is used, an error will spawn. If no api_user_key is used, a guest paste will be created
    $api_paste_name			= urlencode($api_paste_name);
    $api_paste_code			= urlencode($api_paste_code);

    $url 				= 'https://pastebin.com/api/api_post.php';
    $ch 				= curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'api_option=paste&api_user_key='.$api_user_key.'&api_paste_private='.$api_paste_private.'&api_paste_name='.$api_paste_name.'&api_paste_expire_date='.$api_paste_expire_date.'&api_paste_format='.$api_paste_format.'&api_dev_key='.$api_dev_key.'&api_paste_code='.$api_paste_code.'');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36'); // Add user-agent
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Timeout after 5 seconds
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return '{"error":"Gateway Timeout: Pastebin.com timeout, try again","code":504}';
        exit;
    }
    curl_close($ch);
    return $response;
}