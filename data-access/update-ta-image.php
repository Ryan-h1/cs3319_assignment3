<?php global $connection;
/**
 * @author 67
 * This file updates the image URL of a TA in the database.
 */
require_once __DIR__ . '/../config.php'; // Configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Database connection

if (isset($_POST['taId'], $_POST['newImageUrl'])) {
    $taId = $_POST['taId'];
    $newImageUrl = $_POST['newImageUrl'];

    // Update the image URL in the database
    $update_query = $connection->prepare("UPDATE ta SET image = ? WHERE tauserid = ?");
    $update_query->bind_param("ss", $newImageUrl, $taId);

    if ($update_query->execute()) {
        // Redirect back to the TA details page with the updated image
        header("Location: ../ta-details.php?taid=" . urlencode($taId));
    } else {
        // Error handling
        echo "Error updating record: " . $connection->error;
    }

    $update_query->close();
} else {
    echo "Required fields are missing.";
}

$connection->close();
