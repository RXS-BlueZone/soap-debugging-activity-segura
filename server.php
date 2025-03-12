<?php

$rawPostData = file_get_contents("php://input");
header("Content-Type: text/xml");


$xml = simplexml_load_string($rawPostData);
if ($xml === false) {
    die("<error>Invalid XML format</error>");
}

$name = trim(strtolower((string)$xml->xpath("//soap:Body/name")[0]));

// Load sang library xml please check file
$xml_file = simplexml_load_file("library.xml");
if ($xml_file === false) {
    die("<error>Failed to load library.xml</error>");
}

$namespace = $xml_file->getNamespaces(true);
$xml_file->registerXPathNamespace("soapenv", "http://schemas.xmlsoap.org/soap/envelope/");
$xml_file->registerXPathNamespace("bk", "http://example.com/books");

// Need natun pangitaun ang book para maka loop
$books = [];
foreach ($xml_file->xpath("//soapenv:Body/bk:GetBooksResponse/bk:Book") as $book) {
    if (strtolower(trim((string)$book->Author)) === $name) {
        $books[] = $book;
    }
}

// Create SOAP response XML - Na include na diri ang header and body
$responseXML = new DOMDocument("1.0", "UTF-8");
$responseXML->formatOutput = true;

$envelope = $responseXML->createElement("soapenv:Envelope");
$envelope->setAttribute("xmlns:soapenv", "http://schemas.xmlsoap.org/soap/envelope/");
$responseXML->appendChild($envelope);

$body = $responseXML->createElement("soapenv:Body");
$envelope->appendChild($body);


if (!empty($books)) {
    foreach ($books as $book) {
        $bookElement = $responseXML->createElement("bk:Book");

        $title = $responseXML->createElement("bk:Title", (string) $book->Title);
        $author = $responseXML->createElement("bk:Author", (string) $book->Author);
        $year = $responseXML->createElement("bk:Year", (string) $book->Year);

        $bookElement->appendChild($title);
        $bookElement->appendChild($author);
        $bookElement->appendChild($year);

        $body->appendChild($bookElement);
    }
} else {
    $response = $responseXML->createElement("response", "No books found for author: $name");
    $body->appendChild($response);
}

echo $responseXML->saveXML();
?>
