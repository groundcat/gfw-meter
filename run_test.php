<?php

include dirname(__FILE__) . '/user/ASEngine/AS.php';

// Select an oldest domain that hasn't been tested
$domain = app('db')->select(
    "SELECT DISTINCT domain, latest_test_time FROM `wm_domains` WHERE `enabled` = 1 ORDER BY `latest_test_time` ASC LIMIT 1"
);
$domain = $domain[0]['domain'];
echo "Testing " . $domain;

// Load the functions
require_once 'functions.php';

// Print the result
$test_result = api_v1($domain);

// Get the percentage_blocking_score value from the test result JSON
$percentage_blocking_score = json_decode($test_result, true)['percentage_blocking_score'];
$percentage_blocking_score = $percentage_blocking_score * 100;
$percentage_blocking_score = round($percentage_blocking_score, 2);

// Insert the test result to database
app('db')->insert('wm_tests', array(
    "domain" => $domain,
    "test_result_json" => $test_result,
));

// Select the latest test_id from database
$test_id = app('db')->select(
    "SELECT test_id FROM `wm_tests` WHERE `domain` = '$domain' ORDER BY `test_id` DESC LIMIT 1"
);
$test_id = $test_id[0]['test_id'];

// Update to wm_domains
app('db')->update('wm_domains', array(
    "latest_test_time" => date('Y-m-d H:i:s'),
    "latest_test_id" => $test_id,
    "latest_score" => $percentage_blocking_score,
), "domain = '$domain'");






