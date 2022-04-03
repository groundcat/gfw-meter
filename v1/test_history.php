<?php

$domain = $_GET['domain'];
$entries = $_GET['entries'];

// Validate if $domain is a valid domain name
if (!preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain) || !filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
    echo '{"error":"Invalid domain","code":400}';
    exit;
}

// Validate if $entries is an integer
if (!is_numeric($entries)) {
    echo '{"error":"Invalid entries","code":400}';
    exit;
}

// Cap entries at 100
if ($entries > 100) {
    $entries = 100;
}

// Floor entries at 1
if ($entries < 1) {
    $entries = 1;
}

// Select the latest 100 test results from database wm_tests where domain matches
include dirname(__FILE__) . '/../user/ASEngine/AS.php';
$test_result = app('db')->select(
    "SELECT * FROM `wm_tests` WHERE `domain` = '$domain' ORDER BY `timestamp` DESC LIMIT $entries"
);

// Validate if the test exists
if (count($test_result) == 0) {
    echo '{"error":"Test not found","code":404}';
    exit;
}

if (isset($_GET['html']) && $_GET['html'] == 1) {
    // Print the result in HTML format
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Test History</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        tr:nth-child(even){background-color: #f2f2f2}
        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<h2>Test History: '.$domain.'</h2>
<table>
    <tr>
        <th>Timestamp</th>
        <th>Test Report</th>
        <th>Test Score</th>
        <th>Evaluation</th>
    </tr>';
    
    // Loop through the results
    foreach ($test_result as $test) {
        $test_result_json = json_decode($test['test_result_json'], true);
        echo '<tr>
            <td>' . $test['timestamp'] . '</td>
            <td><a href="'.WEBSITE_DOMAIN.'/v1/test_data.php?id=' . $test['test_id'] . '" target="_blank">' . $test['test_id'] . '</a></td>
            <td>' . $test_result_json['percentage_blocking_score']*100 . '% blocked</td>
            <td>' . $test_result_json['evaluation'] . '</td>
        </tr>';

    }
    echo '</table>
</body>
</html>';

} else {
    // Print the result in JSON format with indentation
    header('Content-Type: application/json');
    // Encode the test results
    $test_result_json = json_encode($test_result, JSON_PRETTY_PRINT);
    echo $test_result_json;
}
