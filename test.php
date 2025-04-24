<?php

// Define Input variables
// Parse the query string into an associative array
$queryParams = [];
parse_str($_SERVER["QUERY_STRING"], $queryParams);

// Extract parameters into variables
$ticketId = isset($queryParams['ticketId']) ? $queryParams['ticketId'] : '';
$uniqueId = isset($queryParams['uniqueId']) ? $queryParams['uniqueId'] : '';
$tag = isset($queryParams['tag']) ? $queryParams['tag'] : '';
$num = isset($queryParams['num']) ? $queryParams['num'] : '';

//echo "Ticket ID: $ticketId\n";
//echo "Unique ID: $uniqueId\n";
// Check if the parameters are set
if (empty($ticketId) || empty($uniqueId)) {
    die("Error: Missing ticketId or uniqueId.");
}
$backgroundScript = "php background_script.php $ticketId $uniqueId $tag $num > /dev/null 2>&1 &";
exec($backgroundScript);

?>
