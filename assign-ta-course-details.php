<?php global $connection;
/**
 * This file displays the details of a TA and allows the user to assign course offerings to the TA.
 */
require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
/**
 * SQL
 */

// Check if the 'taid' GET parameter is set
if (isset($_GET['taid'])) {
    $taId = $_GET['taid'];

    // Ta results query
    $ta_query_statement = $connection->prepare("SELECT * FROM ta WHERE tauserid = ?");
    $ta_query_statement->bind_param("s", $taId);
    $ta_query_statement->execute();
    $ta_result = $ta_query_statement->get_result();
    if ($ta_result->num_rows > 0) {
        // Fetch associative array for the TA
        $taDetails = $ta_result->fetch_assoc();
        // Now we can echo out the TA details in HTML
    } else {
        echo "No TA found with ID: " . htmlspecialchars($taId);
    }
    $ta_query_statement->close();

    // Course offering query
    $course_offering_query_state = $connection->prepare("SELECT * FROM courseoffer");
    $course_offering_query_state->execute();
    $course_offering_result = $course_offering_query_state->get_result();
    $course_offering_array = $course_offering_result->fetch_all(MYSQLI_ASSOC);
    $course_offering_query_state->close();
} else {
    echo "No TA ID specified.";
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Assign TA To Course Detail</title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
    </head>
    <body>

    <?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

    <div class="course-offering-assignment-container">
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
            </div>
        </div>

        <div class="course-offering-list">
            <form action="<?php echo BASE_URL . 'data-access/update-ta-course-offering.php'; ?>" method="post">
                <label for="courseOffers">Select Course Offerings:</label>
                <select name="courseOffers[]" id="courseOffers" multiple>
                    <?php foreach ($course_offering_array as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['coid']); ?>">
                            <?php echo htmlspecialchars($course['coid']); ?>
                            : <?php echo htmlspecialchars($course['whichcourse']); ?>
                            - <?php echo htmlspecialchars($course['term']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="add-ta-form-item">
                    <label for="hours">Hours:
                        <input type="number" name="hours" id="hours" min="0" max="32766" value="0">
                    </label>
                </div>
                <input type="hidden" name="taId" value="<?php echo htmlspecialchars($taId); ?>">
                <input class="modal-proceed-button" type="submit" value="Assign Courses">
            </form>
        </div>
    </div>

    <script>

        /**
         * Display modal based on URL parameters
         */
        window.onload = function () {
            // Check if URL has message and type parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message') && urlParams.has('type')) {
                const message = urlParams.get('message');
                const type = urlParams.get('type');
                const modalType = type === 'success' ? 'success' : 'danger';
                const title = modalType === 'success' ? 'Success' : 'Error';

                showModal(modalType, title, message);
            }
        };
    </script>

    </body>
    </html>

<?php $connection->close(); // Close the database connection ?>