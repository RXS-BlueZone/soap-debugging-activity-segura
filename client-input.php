<?php
$response = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    $soapRequestXML = '<?xml version="1.0" encoding="UTF-8"?>
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
        <soapenv:Body>
            <name>' . htmlspecialchars($name) . '</name>
        </soapenv:Body>
    </soapenv:Envelope>';

    $url = "http://localhost/soap/server.php";
    
    $conn = curl_init($url);
    curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($conn, CURLOPT_HTTPHEADER, [
        "Content-Type: text/xml; charset=utf-8",
        "SOAPAction: ''"
    ]);
    curl_setopt($conn, CURLOPT_POST, true);
    curl_setopt($conn, CURLOPT_POSTFIELDS, $soapRequestXML);
    curl_setopt($conn, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($conn);

    if (curl_errno($conn)) {
        $response = "cURL Error: " . curl_error($conn);
    }

    curl_close($conn);
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
