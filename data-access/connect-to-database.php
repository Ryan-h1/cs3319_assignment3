<?php
/**
 * @author 67
 * This file establishes the connection to the SQL database using the credentials in the config file.
 */
require_once __DIR__ . '/../config.php'; // Include the configuration file

// Create connection
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}