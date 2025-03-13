<?php

$rawPostData = file_get_contents("php://input");
header("Content-Type: text/xml");

$xml = simplexml_load_string($rawPostData);
if ($xml === false) {
    die("<error>Invalid XML format</error>");
}

/*
 * INITIAL PROBLEM ENCOUNTERED:
 * The original code attempted to use an XPath query with the prefix "soap" (//soap:Body/name)
 * to locate the <name> element in the SOAP body.
 * However, the incoming XML defines the SOAP envelope with the prefix "soapenv"
 * (as declared in: xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/").
 * This caused the XPath query to fail (returning false), leading to the error
 * "Undefined namespace prefix" and subsequent warnings.
 *
 * FIX:
 * Register the correct namespace prefix ("soapenv") from the incoming XML and update the XPath query accordingly.
 */

// Register the "soapenv" namespace from the XML
$xml->registerXPathNamespace("soapenv", "http://schemas.xmlsoap.org/soap/envelope/");

// Use the correct namespace prefix in the query to find the <name>
$nameNodes = $xml->xpath("//soapenv:Body/name");
if (!$nameNodes || !isset($nameNodes[0])) {
    die("<error>Missing name element in SOAP request</error>");
}

// Get the name value and convert to lowercase
$name = trim(strtolower((string)$nameNodes[0]));

// Load the library XML file which contains the books
$xml_file = simplexml_load_file("library.xml");
if ($xml_file === false) {
    die("<error>Failed to load library.xml</error>");
}

/*
 * For the library XML, we need to register the namespaces defined within it.
 * The library.xml uses "soapenv" for the envelope and "bk" for the books namespace.
 */
$xml_file->registerXPathNamespace("soapenv", "http://schemas.xmlsoap.org/soap/envelope/");
$xml_file->registerXPathNamespace("bk", "http://example.com/books");

/*
 * MAIN PROBLEM TO DEBUG:
 * The bug was that even when a valid author name is entered, no matching book was found.
 * This is because the <bk:Author> element is in the "bk" namespace.
 * Directly accessing $book->Author returns an empty string.
 *
 * FIX:
 * To fix this, I used the children() method with the "http://example.com/books" namespace to properly access the namespaced elements.
 * Additionally, instead of checking for an exact match, "strpos" is used to perform a case-insensitive substring search
 * so that searching for only the first or last name will still provide results.
 */
$books = [];
foreach ($xml_file->xpath("//soapenv:Body/bk:GetBooksResponse/bk:Book") as $book) {
    // Access the namespaced children to get the Author element
    $bookChildren = $book->children("http://example.com/books");
    // Get author name and convert to lowercase
    $author = strtolower(trim((string)$bookChildren->Author));
    // strpos utilzation
    if (strpos($author, $name) !== false) {
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
        // Added for accessing the namespaced children to get Title, Author, and Year
        $bookChildren = $book->children("http://example.com/books");
        $bookElement = $responseXML->createElement("bk:Book");

        $title = $responseXML->createElement("bk:Title", (string)$bookChildren->Title);
        $author = $responseXML->createElement("bk:Author", (string)$bookChildren->Author);
        $year = $responseXML->createElement("bk:Year", (string)$bookChildren->Year);

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