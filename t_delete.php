<?php

// Define Input variables
$ticketId= str_replace('ticketId=', '', $_SERVER["QUERY_STRING"]);


// Define variables zendesk
$subdomain = '';
$email = '';
$apiToken = '';

// API URL for soft delete
$softDeleteUrl = "https://{$subdomain}.zendesk.com/api/v2/tickets/{$ticketId}.json";

// Initialize cURL for soft delete
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $softDeleteUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_USERPWD, "{$email}/token:{$apiToken}");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

// Execute cURL for soft delete
$softDeleteResponse = curl_exec($ch);

// Check for errors during soft delete
if (curl_errno($ch)) {
    echo "cURL Error (Soft Delete): " . curl_error($ch);
} else {
    echo "Ticket soft-deleted successfully!\n";
    // Optionally display response
    print_r($softDeleteResponse);
}

// Close cURL for soft delete
curl_close($ch);

// API URL for permanent delete
$permanentDeleteUrl = "https://{$subdomain}.zendesk.com/api/v2/deleted_tickets/{$ticketId}.json";

// Initialize cURL for permanent delete
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $permanentDeleteUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_USERPWD, "{$email}/token:{$apiToken}");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

// Execute cURL for permanent delete
$permanentDeleteResponse = curl_exec($ch);

// Check for errors during permanent delete
if (curl_errno($ch)) {
    echo "cURL Error (Permanent Delete): " . curl_error($ch);
} else {
    echo "Ticket permanently deleted successfully!\n";
    // Optionally display response
    print_r($permanentDeleteResponse);
}

// Close cURL for permanent delete
curl_close($ch);

?>
