<?php global $connection;
/**
 * This file deletes a TA from the database.
 */
require_once __DIR__ . '/../config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if (isset($_POST['taId'])) {
        $taId = $_POST['taId'];

        $deleteQuery = $connection->prepare("DELETE FROM ta WHERE tauserid = ?");
        $deleteQuery->bind_param("s", $taId);

        if ($deleteQuery->execute()) {
            $response['success'] = true;
            $response['message'] = 'TA deleted successfully.';
        } else {
            $response['message'] = 'Error: ' . $deleteQuery->error;
        }

        $deleteQuery->close();
    } else {
        $response['message'] = 'TA ID not provided.';
    }
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
$connection->close();

