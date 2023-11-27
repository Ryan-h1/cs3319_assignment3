<?php
global $connection;
/**
 * @author 67
 * This file assigns a TA to a course offering.
 */
require_once __DIR__ . '/../config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection

// Initialize a message variable to provide feedback to the user
$message = '';
$messageType = 'success';
$taId = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['taId'], $_POST['courseOffers'], $_POST['hours'])) {
    $taId = $_POST['taId'];
    $courseOffers = $_POST['courseOffers']; // This will be an array of 'coid' values
    $hours = $_POST['hours'];

    // Begin a transaction
    $connection->begin_transaction();

    try {
        // Prepare the insert statement for hasworkedon
        $insert_statement = $connection->prepare("INSERT INTO hasworkedon (tauserid, coid, hours) VALUES (?, ?, ?)");

        // Loop through each course offering and insert a record into hasworkedon
        foreach ($courseOffers as $coid) {
            $insert_statement->bind_param("sss", $taId, $coid, $hours);
            $insert_statement->execute();
        }

        // If no errors, commit the transaction
        $connection->commit();
        $message = "TA assigned to selected course offerings successfully!";
    } catch (mysqli_sql_exception $exception) {
        // Roll back the transaction
        $connection->rollback();

        // Check for duplicate entry error code
        if ($exception->getCode() == 1062) {
            $message = "Duplicate entry: the TA is already assigned to one of the selected course offerings.";
            $messageType = 'danger';
        } else {
            $message = "Error assigning TA to course offerings: " . $exception->getMessage();
            $messageType = 'danger';
        }
    }


    // Close the prepared statement
    $insert_statement->close();
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['taId'])) {
        $taId = $_POST['taId'];
    }
    $message = "No TA ID or course offerings specified.";
    $messageType = 'danger';
}

// After closing the prepared statement and before closing the connection
if ($messageType == 'success') {
    // Redirect back to the assign page with a success message
    header('Location: ../assign-ta-course-details.php?taid=' . urlencode($taId) . '&message=' . urlencode($message) . '&type=success');
} else {
    // Redirect back to the assign page with an error message
    header('Location: ../assign-ta-course-details.php?taid=' . urlencode($taId) . '&message=' . urlencode($message) . '&type=error');
}


$connection->close();