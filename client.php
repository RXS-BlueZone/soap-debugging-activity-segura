<?php

$soapRequestXML = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <getMessage>
            <name>Paul BSIT - 2028</name>
        </getMessage>
    </soap:Body>
</soap:Envelope>';

$url = "http://localhost/soap/server.php";

$conn = curl_init($url);
curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
curl_setopt($conn, CURLOPT_HTTPHEADER, [
    "Content-Type: text/xml; charset=utf-8",
    "SOAPAction: 'getMessage'"
]);
curl_setopt($conn, CURLOPT_POST, true);
curl_setopt($conn, CURLOPT_POSTFIELDS, $soapRequestXML);

$response = curl_exec($conn);

curl_close($conn);

echo "Server Response: <br>";
echo "<pre>" . $response . "</pre>";

?>