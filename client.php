<?php
// File: client.php is being used to test the SOAP server by sending a request directly without a form to display the response.
// Using PHP's built-in SoapClient for a more robust SOAP approach using an array
$options = array(
    'location' => "http://localhost/soap/server.php",
    'uri' => "http://localhost/soap",
    'trace' => 1
);
try {
    $client = new SoapClient(null, $options);
    // Call the getBooks method with the author name
    $result = $client->getBooks("George");
    echo "Server Response: <br>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>