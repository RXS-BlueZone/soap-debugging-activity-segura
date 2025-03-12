<?php
libxml_use_internal_errors(true); 

$xml = simplexml_load_file("library.xml");

if ($xml === false) {
    echo "Error: Cannot load XML. Details:<br>";
    foreach (libxml_get_errors() as $error) {
        echo htmlentities($error->message) . "<br>";
    }
    exit;
}

echo "XML Loaded Successfully!";


// Dear Class use this to test kung sakto inyo XML
?>
