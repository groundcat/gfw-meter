<?php

$test_id = $_GET['id'];

// Validate the test_id is a 32 digit UUID
if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $test_id)) {
    echo '{"error":"Invalid test_id","code":400}';
    exit;
}

// Select the test result from database wm_tests where test_id matches
include dirname(__FILE__) . '/../user/ASEngine/AS.php';
$test_result = app('db')->select(
    "SELECT test_result_json FROM `wm_tests` WHERE `test_id` = '$test_id'"
);

// Validate if the test_id exists
if (count($test_result) == 0) {
    echo '{"error":"Test not found","code":404}';
    exit;
}

// Print the result in JSON format with indentation
header('Content-Type: application/json');
echo json_encode(json_decode($test_result[0]['test_result_json'], true), JSON_PRETTY_PRINT);

