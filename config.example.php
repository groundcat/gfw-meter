<?php

// Debug mode
define('DEBUG', false);

// Set timeout to 3 minutes
set_time_limit(180);

// Detect node API URL
// for example, 'https://api.example.com/path/to/detect.php'
define('DETECT_NODE_API_URL', 'REPLACE_ME');

// GeoIP API URL, with backslash at the end
// for example, 'http://ip-api.com/json/'
define('GEOIP_API_URL', 'http://ip-api.com/json/');

// List of polluted DNS servers
define('POLLUTED_DNS_SERVER_IPS', array(
    'beijing_chinatelecom' => '219.141.136.10',
    'shanghai_unicom' => '210.22.70.3',
    'beijing_unicom' => '123.123.123.123',
    'beijing_tencentcloud' => '119.29.29.29',
    'wulanchabu_aliyun' => '223.5.5.5',
));

// Port check API URL, with backslash and GET parameter at the end
// by default use 'https://yuanxiapi.cn/api/port/?ip='
// if the default API is not used, you must modify the function detect_web_server_status() in functions.php
define('PORT_CHECK_API_URL_ENABLED', true);
define('PORT_CHECK_API_URL', 'https://yuanxiapi.cn/api/port/?ip=');

// Pastebin sharing
// Documention: https://pastebin.com/doc_api
define('PASTEBIN_API_DEV_KEY', 'REPLACE_ME');
