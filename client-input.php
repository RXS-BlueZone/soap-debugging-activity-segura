<?php
$response = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    
    // SOAP client for connecting to the server, with the server's location and URI
    $options = array(
        'location' => "http://localhost/soap/server.php",
        'uri' => "http://localhost/soap",
        'trace' => 1
    );
    
    try {
        $client = new SoapClient(null, $options);
        $result = $client->getBooks($name);
        $response = print_r($result, true);
    } catch (Exception $e) {
        $response = "Error: " . $e->getMessage();
    }
}
?>

<html>
    <head>
        <title>SOAP Client</title>
    </head>
    <body>
        <h1>Enter Author Name</h1>
        <form method="post">
            <input type="text" name="name" required>
            <button type="submit">Submit SOAP Request</button>
        </form>
        
        <?php
        if (!empty($response)) {
            echo "<h3>Server Response</h3>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
        ?>
    </body>
</html>
