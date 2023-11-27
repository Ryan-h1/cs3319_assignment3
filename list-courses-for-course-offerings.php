<?php global $connection;
/**
 * @author 67
 * This file displays the list of courses and displays all course offerings for that course when
 * a user clicks on the course.
 */
require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
// Get the list of courses
$course_query = $connection->prepare("SELECT * FROM course");
$course_query->execute();
$course_result = $course_query->get_result();
$course_array = $course_result->fetch_all(MYSQLI_ASSOC);
$course_query->close();
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
        <?php echo "View The Course Offerings For a Course By Clicking On It"; ?>
    </h2>
</div>

<div class="ta-catalog">
    <?php
    if (!empty($course_array)) {
        foreach ($course_array as $course) {
            echo "<a href='course-details.php?coursenum=" . htmlspecialchars($course['coursenum']) . "' class='ta-card-link'>";
            echo "<div class='ta-card'>";
            echo "<h2>" . htmlspecialchars($course['coursenum']) . "</h2>";
            echo "<h3>" . htmlspecialchars($course['coursename']) . "</h3>";
            echo "<p>Level: " . htmlspecialchars($course['level']) . "</p>";
            echo "<p>Year: " . htmlspecialchars($course['year']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No courses found.</p>";
    }
    ?>
</div>


</body>
</html>

<?php $connection->close(); // Close the database connection ?>
