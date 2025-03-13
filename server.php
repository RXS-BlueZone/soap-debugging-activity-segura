<?php

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

class BookSearch {
    // Mthod for accepting author name as input and searching for books.
    public function getBooks($name) {
        $name = trim(strtolower($name));
        
        // Load the library XML file which contains the books
        $xml_file = simplexml_load_file("library.xml");
        if ($xml_file === false) {
            throw new Exception("Failed to load library.xml");
        }
        
        // The library XML, must be registered with the namespaces defined within it.
        // It uses "soapenv" for the envelope and "bk" for the books namespace.
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
            // strpos utilization
            if (strpos($author, $name) !== false) {
                $books[] = [
                    'Title'  => (string)$bookChildren->Title,
                    'Author' => (string)$bookChildren->Author,
                    'Year'   => (string)$bookChildren->Year
                ];
            }
        }
        
        if (!empty($books)) {
            return $books;
        } else {
            return "No books found for author: " . $name;
        }
    }
}

// SoapServer in non-WSDL mode with the appropriate options
$options = array('uri' => 'http://localhost/soap');
$server = new SoapServer(null, $options);
$server->setClass("BookSearch");
$server->handle();


/*
 * ENHANCEMENTS MADE:
 * The original code used a direct XML parsing approach to extract the author name from the SOAP request.
 * Instead of manually parsing and constructing XML, PHP's SoapServer is used here to automatically handle the SOAP envelope,
 * namespaces, and response.
*/

?>