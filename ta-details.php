<?php global $connection;
/**
 * This file displays the details of a TA.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection
?>

<?php
// Check if the 'taid' GET parameter is set
if (isset($_GET['taid'])) {
    $taId = $_GET['taid'];

    // Prepare a statement to prevent SQL injection
    $statement = $connection->prepare(query: "SELECT * FROM ta WHERE tauserid = ?");
    $statement->bind_param("s", $taId);

    // Execute the statement
    $statement->execute();

    // Get the result
    $result = $statement->get_result();

    if ($result->num_rows > 0) {
        // Fetch associative array for the TA
        $taDetails = $result->fetch_assoc();

        // Now we can echo out the TA details in HTML
    } else {
        echo "No TA found with ID: " . htmlspecialchars($taId);
    }

    // Close the statement
    $statement->close();
} else {
    echo "No TA ID specified.";
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>List TAs</title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
    </head>
    <body>

    <?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

    <div class="ta-details">
        <?php if (isset($taDetails)): ?>
            <!-- Check if the TA has an image URL and display it -->
            <?php if (!empty($taDetails["image"])): ?>
                <img src="<?php echo htmlspecialchars($taDetails["image"]); ?>"
                     alt="Photo of <?php echo htmlspecialchars($taDetails["firstname"]) . " " .
                         htmlspecialchars($taDetails["lastname"]); ?>">
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($taDetails["firstname"]) . " " . htmlspecialchars($taDetails["lastname"]); ?></h2>
            <p>TA ID: <?php echo htmlspecialchars($taDetails["tauserid"]); ?></p>
            <p>Degree: <?php echo htmlspecialchars($taDetails["degreetype"]); ?></p>
            <p>Student Number: <?php echo htmlspecialchars($taDetails["studentnum"]); ?></p>
            <!-- Add more details as needed -->
        <?php endif; ?>

    </div>


    </body>
    </html>

<?php $connection->close(); // Close the database connection ?>