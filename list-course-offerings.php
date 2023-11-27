<?php global $connection;
/**
 * @author 67
 * This file displays the list of course offerings.
 */
require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
// Get the list of courses
$course_offerings_query = $connection->prepare("SELECT courseoffer.*, course.coursename
                                                FROM courseoffer
                                                         JOIN course ON courseoffer.whichcourse = course.coursenum");
$course_offerings_query->execute();
$course_offerings_result = $course_offerings_query->get_result();
$course_offerings_array = $course_offerings_result->fetch_all(MYSQLI_ASSOC);
$course_offerings_query->close();

if (empty($course_offerings_array)) {
    echo "<script type='text/javascript'>";
    echo "showModal('success', 'Sorry!', 'Sorry, no courses found.');";
    echo "</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Course Offerings</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
</head>
<body>

<!-- Navigation Bar -->
<?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

<!-- Sub Header -->
<div class="subheader-container">
    <h2>
        <?php echo "View the TAs Working on a Course Offering by Clicking On It"; ?>
    </h2>
</div>

<div class="ta-catalog">
    <?php
    if (!empty($course_offerings_array)) {
        foreach ($course_offerings_array as $course_offer) {
            echo "<a href='course-offering-details.php?coid=" .
                htmlspecialchars($course_offer['coid']) . "' class='ta-card-link'>";
            echo "<div class='ta-card'>";
            echo "<h3>" . htmlspecialchars($course_offer['whichcourse']) . " - " .
                htmlspecialchars($course_offer['coursename']) . "</h3>";
            echo "<h3>" . htmlspecialchars($course_offer['coid']) . "</h3>";
            echo "<p>Students: " . htmlspecialchars($course_offer['numstudent']) . "</p>";
            echo "<p>Term: " . htmlspecialchars($course_offer['term']) . "</p>";
            echo "<p>Year: " . htmlspecialchars($course_offer['year']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No course offerings found.</p>";
    }
    ?>
</div>


</body>
</html>

<?php $connection->close(); // Close the database connection ?>
