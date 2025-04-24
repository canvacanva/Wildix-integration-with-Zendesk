<?php

// Parse the query string into an associative array
$queryParams = [];
parse_str($_SERVER["QUERY_STRING"], $queryParams);

// Extract parameters into variables
$ticketId = isset($queryParams['ticketId']) ? $queryParams['ticketId'] : '';
$call_from = isset($queryParams['call_from']) ? $queryParams['call_from'] : '';
$call_to = isset($queryParams['call_to']) ? $queryParams['call_to'] : '';
$call_answer_at = isset($queryParams['call_answer_at']) ? $queryParams['call_answer_at'] : '';
$call_answer_by = isset($queryParams['call_answer_by']) ? $queryParams['call_answer_by'] : '';
$call_duration = isset($queryParams['call_duration']) ? $queryParams['call_duration'] : '';
$call_location = isset($queryParams['call_location']) ? $queryParams['call_location'] : '';

// Define variables zendesk
$subdomain = '';
$email = '';
$apiToken = '';
$priority = 'normal';

//Define Timestamp
// Set the timezone to Rome
date_default_timezone_set('Europe/Rome');

// Generate a timestamp in European format
$timestamp = date('Y-m-d\TH:i:s\Z');

// API URL
$url = "https://{$subdomain}.zendesk.com/api/v2/tickets/{$ticketId}.json";

// JSON data to send
$data = json_encode([
    "ticket" => [
        "voice_comment" => [
            "from" => "{$call_from}",
            "to" => "{$call_to}",
            "started_at" => "{$timestamp}",
            "call_duration" => 100,
        ],
        "priority" => "{$priority}",
        "subject" => "Ticket da telefonata di {$call_from}",
        "tags" => ["ticket_da_telefonata", "new_flow"]
    ]
]);

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_USERPWD, "{$email}/token:{$apiToken}");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

// Execute cURL and capture response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Decode and display response
    $responseDecoded = json_decode($response, true);
    print_r($responseDecoded);
}

// Close cURL
curl_close($ch);

?>

