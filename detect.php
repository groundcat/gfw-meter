<?php

// This is the API file to be deployed on the node server.

require_once 'vendor/autoload.php';

// Validate if the 'config.php' file exists
if (!file_exists('config.php')) {
    echo '{"error":"config.php file not found","code":500}';
    exit;
}

// Load configuration
require_once 'config.php';

// Disable all errors
if (!DEBUG) {
    error_reporting(0);
}

// Obtain $domain from the GET or POST requests
$domain = $_GET['domain'] ?? $_POST['domain'] ?? null;
$web_server_status_us = $_GET['web_server_status'] ?? $_POST['web_server_status'] ?? null;

// Validate if $domain is a valid domain name
if (!preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain) || !filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
    echo '{"error":"Invalid domain","code":400}';
    exit;
}

// Load the functions
require_once 'functions.php';

// Query the domain to get the detected_us IP and the detected_us AS number
$domain_result = query_geoip_api($domain);
$domain_result_json = json_decode($domain_result, true);
$ip_detected_us = $domain_result_json['query'];
$as_detected_us = $domain_result_json['as'];
$countryCode_detected_us = $domain_result_json['countryCode'];

// An array of polluted DNS servers' results
$polluted_dns_server_results = array();
$polluted_dns_server_results['domain'] = $domain;

// Pollution score
$score = 0.00;
$blocked_count = 0;
$web_server_mismatch_count = 0;

// Iterate through the polluted DNS servers
foreach (POLLUTED_DNS_SERVER_IPS as $key => $polluted_dns_server_ip) {

    // Query DNS server
    $ip_detected_cn = nslookup_A_record($domain, $polluted_dns_server_ip);
    // Validate the result
    if ($ip_detected_cn === false) {
        $polluted_dns_server_results[$key]["error"] = "Bad Gateway: The name server was unable to process this query due to a problem with the name server.";
        $polluted_dns_server_results[$key]["code"] = 502;
        continue; // Skip this iteration
    }
    $detected_cn_result = query_geoip_api($ip_detected_cn);
    $detected_cn_result_json = json_decode($detected_cn_result, true);
    $as_detected_cn = $detected_cn_result_json['as'];
    $countryCode_detected_cn = $detected_cn_result_json['countryCode'];
    
    // Debug
    if (DEBUG) {
        echo $key . ': ' . $ip_detected_cn . PHP_EOL;
    }

    // Compare the detected_cn and detected_us AS numbers
    if ($as_detected_cn == $as_detected_us) {
        $detection_result = 'not blocked';
        $blocked = 0;
    } elseif ($countryCode_detected_us == 'CN' || $countryCode_detected_cn == 'CN') {
        $detection_result = 'not blocked';
        $blocked = 0;
    } else {
        $detection_result = 'possible DNS poisoning';
        $blocked = 1;
    }

    // Detect web_server_status
    $web_server_status_cn = detect_web_server_status($ip_detected_cn);
    if ($web_server_status_us !== $web_server_status_cn) {
        $web_server_status_match = 0;
        $web_server_mismatch_count += 1;
    } else {
        $web_server_status_match = 1;
    }
    $polluted_dns_server_results[$key]['web_server_status_us'] = $web_server_status_us;
    $polluted_dns_server_results[$key]['web_server_status_cn'] = $web_server_status_cn;
    $polluted_dns_server_results[$key]['web_server_status_match'] = $web_server_status_match;

    // Compare web_server_status, 
    // if the DNS matches, but the web_server_status is not matched, then the website is still blocked
    if ($blocked == 0 && $web_server_status_us !== $web_server_status_cn) {
        $detection_result = 'possible TCP reset attack';
        $blocked = 1;
    } elseif ($blocked == 1 && $web_server_status_us !== $web_server_status_cn) {
        $detection_result = 'possible DNS poisoning and TCP reset attack';
        $blocked = 1;
    }

    // Add to pollution score
    $blocked_count += $blocked;

    // Create an array of the results
    $result = array(
        'blocked' => $blocked,
        'result' => $detection_result,
        'as_detected_cn' => $as_detected_cn,
        'as_detected_us' => $as_detected_us,
        'ip_detected_cn' => $ip_detected_cn,
        'ip_detected_us' => $ip_detected_us,
        'web_server_status_cn' => $web_server_status_cn,
        'web_server_status_us' => $web_server_status_us,
        'web_server_status_match' => $web_server_status_match,
    );

    // Add the result to the polluted_dns_server_results array with city names
    $polluted_dns_server_results[$key] = $result;
}

// Number of keys in the array of polluted DNS servers POLLUTED_DNS_SERVER_IPS
$polluted_dns_server_count = count(POLLUTED_DNS_SERVER_IPS);

// Calculate score
$score = $blocked_count / $polluted_dns_server_count;

// If the web server status is not matched for twice, the score is at least 0.5
if ($web_server_mismatch_count >= 2) {
    $score += 0.5;
}

// Cap the score at 1.0
if ($score > 1.0) {
    $score = 1.00;
}

// Round to 2 decimal places
$score = floatval(round($score, 2));

// Add pollution score to the polluted_dns_server_results array
$polluted_dns_server_results['percentage_blocking_score'] = $score;

// Summarize result based on the pollution score and add to the polluted_dns_server_results array
if ($score == 0) {
    $polluted_dns_server_results['evaluation'] = 'not blocked';
} elseif ($score <= 0.5) {
    $polluted_dns_server_results['evaluation'] = 'medium possibility of blocking';
} else {
    $polluted_dns_server_results['evaluation'] = 'high possibility of blocking';
}

// Add code 200 to the polluted_dns_server_results array
$polluted_dns_server_results['code'] = 200;

// Get current timestamp
$timestamp = time();

// Add timestamp to the polluted_dns_server_results array
$polluted_dns_server_results['timestamp'] = $timestamp;

// Encode the array as JSON
$polluted_dns_server_results_json = json_encode($polluted_dns_server_results, JSON_PRETTY_PRINT);

// Print the JSON
echo $polluted_dns_server_results_json;