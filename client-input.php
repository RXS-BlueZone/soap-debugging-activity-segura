<?php
$response = "";
$name = ""; // initialize name variable
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    
    // SOAP client for connecting to the server, with the server's location and URI
    $options = array(
        'location' => "http://localhost/soap/server.php",
        'uri'      => "http://localhost/soap",
        'trace'    => 1
    );
    
    try {
        $client = new SoapClient(null, $options);
        // Call getBooks using an associative array
        $result = $client->getBooks(['name' => $name]);
        $response = $result;
    } catch (Exception $e) {
        $response = "Error: " . $e->getMessage();
    }
}
?>
<html>
    <head>
        <title>Book Search by Author</title>
        <style>
            * {
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', sans-serif;
                background: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 900px;
                margin: 50px auto;
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }
            h1 {
                text-align: center;
                color: #333;
                margin-bottom: 20px;
            }
            form {
                text-align: center;
                margin-bottom: 30px;
            }
            input[type="text"] {
                padding: 12px 15px;
                width: 60%;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 16px;
            }
            button {
                padding: 12px 20px;
                background-color: #28a745;
                color: #fff;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                cursor: pointer;
                margin-left: 10px;
            }
            button:hover {
                background-color: #218838;
            }
            .response h3 {
                text-align: center;
                color: #1D7732FF;
            }
            .cards {
                display: block;
            }
            .card {
                width: 100%;
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 15px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s, box-shadow 0.3s;
            }
            .card:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                cursor: pointer;
            }
            .card h3 {
                margin: 0 0 10px;
                color: #333;
            }
            .card p {
                margin: 5px 0;
                color: #555;
            }
            .error {
                color: red;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Search for Books by Author</h1>
            <form method="post">
                <input type="text" name="name" placeholder="Enter Author Name" required value="<?php echo htmlspecialchars($name); ?>">
                <button type="submit">Search</button>
            </form>
            
            <div class="response">
                <?php
                if (!empty($response)) {
                    echo '<h3>Search Results</h3>';
                    // For displaying the search results in a card layout from an array response
                    if (is_array($response)) {
                        echo '<div class="cards">';
                        foreach ($response as $book) {
                            echo '<div class="card">';
                            echo '<h3>' . htmlspecialchars($book['Title']) . '</h3>';
                            echo '<p><strong>Author:</strong> ' . htmlspecialchars($book['Author']) . '</p>';
                            echo '<p><strong>Year:</strong> ' . htmlspecialchars($book['Year']) . '</p>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p class="error">' . htmlspecialchars($response) . '</p>';
                    }
                }
                ?>
            </div>
        </div>
    </body>
</html>
