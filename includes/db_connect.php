<?php
/**
 * Database Connection File
 * * This script connects to the MySQL database.
 * It uses constants for credentials for easy configuration.
 */

// Define database connection constants
// These are the default credentials for a standard XAMPP setup.
// If you have a different password, change it here.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'greenlife_db');

// Create a new MySQLi connection object
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
// The connect_error property will be non-null if an error occurred.
if ($conn->connect_error) {
    // If there is a connection error, stop the script immediately and show the error.
    // This is a critical security step to prevent the application from running with a broken database connection.
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to utf8mb4 for full Unicode support.
// This is good practice to ensure all languages and symbols are stored correctly.
if (!$conn->set_charset("utf8mb4")) {
    // Handle error if charset can't be set, though this is rare.
    // For simplicity, we can log this or just be aware of it.
    // printf("Error loading character set utf8mb4: %s\n", $conn->error);
}

// At this point, the connection is successful.
// The variable $conn can now be used in other scripts to interact with the database.
// No need to close the connection here; it's typically closed at the end of the script that includes this file.

?>