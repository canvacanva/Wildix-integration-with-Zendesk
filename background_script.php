<?php
sleep(5); // Simulate processing delay
// Define Input variables
$ticketId=$argv[1];
$uniqueId=$argv[2];
$tag=$argv[3];
$num=$argv[4];

// Define variables
$subdomain = '';
$email = '';
$apiToken = '';
$priority = 'normal';

// Variables for database connection
$servername = "localhost";
$username = "";
$password = "";
$dbname = ""; // Database name

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize the input to avoid SQL injection
$uniqueId = $conn->real_escape_string($uniqueId);

// Dynamically construct the query, searching for the uniqueId
//$query = "SELECT * FROM cdr WHERE uniqueid = '$uniqueId' order by rowid desc LIMIT 1";

// Dynamically construct the query, searching for the tag
$query = "SELECT * FROM cdr WHERE lastdata like '%$tag%' order by rowid desc LIMIT 1";

// Execute the query
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Fetch the first row of the result
    $row = $result->fetch_assoc();

    // Save each column into a variable
    $uniqueId = $row['uniqueid']; // AUnique call ID
    $duration = $row['duration']; // Call total duratuion
    $billsec = $row['billsec']; // Call billable duration
    $c_from = $row['src']; // caller number
    $from_name = $row['from_name']; // caller name by gal
    $to_name = $row['to_name']; // Internale operator name that answer
    $c_to = $row['c_to']; // Internal operator number that answer
    $start = $row['answer']; // start time

    $dst = $row['dst']; // Destination number (external or internal)
        //fix number format if number is external
        if (strlen($dst) > 5) {
            $dst_fix = substr($dst, 1);
        } else  $dst_fix = $dst;
            
    $status = $row['disposition']; // ANSWER, NO ANSWER, BUSY, etc.

    // Check the status and set the message accordingly
    if ($status === "ANSWERED") {
        $subject_message = "Telefonata con risposta da: ";
    } else if ($status === "NO ANSWER") {
        $subject_message = "Telefonata persa da: ";
        $duration = '0'; // Set duration to 0 for unanswered calls
    } else {
        $subject_message = "Telefonata con stato non riconosciuto";
    }


// Close the connection
$conn->close();

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
            "from" => "{$num}", // only numbers
            "to" => "{$dst_fix}",
            "started_at" => "{$start}",
            "call_duration" => "{$billsec}",
            "answered_by_id" => "{$c_to}",
        ],
        "priority" => "{$priority}",
        "subject" => "{$subject_message} {$from_name} (+{$num})",
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

} else {
    echo "No results found for uniqueId: $uniqueId";
}
// Close the connection
$conn->close();
?>
