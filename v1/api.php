<?php

// This is the API file to be deployed on the client-facing server.

// Obtain $domain from the GET or POST requests
$domain = $_GET['domain'] ?? $_POST['domain'] ?? null;

// Load the functions
require_once '../functions.php';

// Print the result
header('Content-Type: application/json');
echo api_v1($domain);