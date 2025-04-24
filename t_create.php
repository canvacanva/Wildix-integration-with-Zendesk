<?php
// Define Input variables
$num= str_replace('num=', '', $_SERVER["QUERY_STRING"]);

// Define variables
$subdomain = '';
$email = '';
$apiToken = '';

// Set the timezone to Italy (Rome)
date_default_timezone_set('Europe/Rome');

// Save date and time in European format
$europeanDateTime = date('d/m/Y H:i:s');

// Output the date and time
// echo $europeanDateTime;

// API URL
$url = "https://{$subdomain}.zendesk.com/api/v2/tickets.json";

// JSON data to send
$data = json_encode([
    "ticket" => [
        "subject" => "Chiamata in corso da {$num}",
        "comment" => [
            "body" => "Data creazione: {$europeanDateTime}"
        ]
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
curl_setopt($ch, CURLOPT_USERPWD, "$email/token:$apiToken");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

// Execute cURL and capture response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Decode and display response
    $responseDecoded = json_decode($response, true);

    // Check if 'id' exists and echo its value
    if (isset($responseDecoded['ticket']['id'])) {
        echo $responseDecoded['ticket']['id'];
    } else {
        echo "ID not found in the response.";
    }
}
// Close cURL
curl_close($ch);
?>

