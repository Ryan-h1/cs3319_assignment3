<?php global $connection;
/**
 * This file displays the details of a TA.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
/**
 * SQL
 */

// Check if the 'taid' GET parameter is set
if (isset($_GET['taid'])) {
    $taId = $_GET['taid'];

    // Prepare statements to prevent SQL injection
    $ta_query_statement = $connection->prepare("SELECT * FROM ta WHERE tauserid = ?");
    $ta_loves_courses_query = $connection->prepare("SELECT course.*
                                                    FROM ta
                                                             JOIN loves ON ta.tauserid = loves.ltauserid
                                                             JOIN course ON loves.lcoursenum = course.coursenum
                                                    WHERE ta.tauserid = ?");
    $ta_hates_courses_query = $connection->prepare("SELECT course.*
                                                    FROM ta
                                                             JOIN hates ON ta.tauserid = hates.htauserid
                                                             JOIN course ON hates.hcoursenum = course.coursenum
                                                    WHERE ta.tauserid = ?");
    $ta_query_statement->bind_param("s", $taId);
    $ta_loves_courses_query->bind_param("s", $taId);
    $ta_hates_courses_query->bind_param("s", $taId);

    // Execute the statement
    $ta_query_statement->execute();

    // Ta results
    $ta_result = $ta_query_statement->get_result();
    if ($ta_result->num_rows > 0) {
        // Fetch associative array for the TA
        $taDetails = $ta_result->fetch_assoc();

        // Now we can echo out the TA details in HTML
    } else {
        echo "No TA found with ID: " . htmlspecialchars($taId);
    }
    $ta_query_statement->close();

    // Ta loves
    $ta_loves_courses_query->execute();
    $ta_loves_courses_result = $ta_loves_courses_query->get_result();
    $ta_loves_courses_query->close();

    // Ta hates
    $ta_hates_courses_query->execute();
    $ta_hates_courses_result = $ta_hates_courses_query->get_result();
    $ta_hates_courses_query->close();
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

    <div class="ta-detail">
        <div class="ta-detail-card">
            <?php if (isset($taDetails)): ?>
                <!-- Check if the TA has an image URL and display it -->
                <?php if (!empty($taDetails["image"])): ?>
                    <img class="ta-profile-picture"
                         src="<?php echo htmlspecialchars($taDetails["image"]); ?>"
                         alt="Photo of <?php echo htmlspecialchars($taDetails["firstname"]) . " " .
                             htmlspecialchars($taDetails["lastname"]); ?>">
                <?php else: ?>
                    <img class="ta-profile-picture"
                         src="https://christopherscottedwards.com/wp-content/uploads/2018/07/Generic-Profile.jpg"
                         alt="Generic profile picture">
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($taDetails["firstname"]) . " " . htmlspecialchars($taDetails["lastname"]); ?></h2>
                <p>TA ID: <?php echo htmlspecialchars($taDetails["tauserid"]); ?></p>
                <p>Degree: <?php echo htmlspecialchars($taDetails["degreetype"]); ?></p>
                <p>Student Number: <?php echo htmlspecialchars($taDetails["studentnum"]); ?></p>
            <?php endif; ?>

            <!-- Display courses the TA loves -->
            <?php if ($ta_loves_courses_result->num_rows > 0): ?>
                <div class="ta-courses">
                    <h3>Courses Loved by TA</h3>
                    <ul>
                        <?php while ($row = $ta_loves_courses_result->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($row['coursename']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Display courses the TA hates -->
            <?php if ($ta_hates_courses_result->num_rows > 0): ?>
                <div class="ta-courses">
                    <h3>Courses Hated by TA</h3>
                    <ul>
                        <?php while ($row = $ta_hates_courses_result->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($row['coursename']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    </body>
    </html>

<?php $connection->close(); // Close the database connection ?>