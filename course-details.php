<?php global $connection;
/**
 * @author 67
 * This file displays the details of a course and allows the user to filter the course offerings by year.
 */
require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
// Check if the coursenum is set, else throw an error modal
if (!isset($_GET['coursenum'])) {
    echo "<script type='text/javascript'>";
    echo "showModal('danger', 'Error', 'Course number not provided.');";
    echo "</script>";
}

$coursenum = $_GET['coursenum'];
$startYear = $_GET['startYear'] ?? null;
$endYear = $_GET['endYear'] ?? null;

$course_query = $connection->prepare("SELECT * FROM course WHERE coursenum = ?");
$course_query->bind_param("s", $coursenum);
$course_query->execute();
$course_result = $course_query->get_result();

if ($course_result->num_rows < 1) {
    echo "<script type='text/javascript'>";
    echo "showModal('danger', 'Error', 'Course not found.');";
    echo "</script>";
}

$courseDetails = $course_result->fetch_assoc();
$course_query->close();

// Get the course offerings for this course
// Adjust the SQL query based on the provided years
if ($startYear && $endYear) {
    $course_offering_query = $connection->prepare("SELECT *
                                                    FROM courseoffer
                                                    WHERE whichcourse = ?
                                                      AND year BETWEEN ? AND ?");
    $course_offering_query->bind_param("sii", $coursenum, $startYear, $endYear);
} elseif ($startYear) {
    $course_offering_query = $connection->prepare("SELECT *
                                                    FROM courseoffer
                                                    WHERE whichcourse = ?
                                                      AND year >= ?");
    $course_offering_query->bind_param("si", $coursenum, $startYear);
} elseif ($endYear) {
    $course_offering_query = $connection->prepare("SELECT *
                                                    FROM courseoffer
                                                    WHERE whichcourse = ?
                                                      AND year <= ?");
    $course_offering_query->bind_param("si", $coursenum, $endYear);
} else {
    $course_offering_query = $connection->prepare("SELECT *
                                                    FROM courseoffer
                                                    WHERE whichcourse = ?");
    $course_offering_query->bind_param("s", $coursenum);
}
$course_offering_query->execute();
$course_offering_result = $course_offering_query->get_result();
$course_offering_array = $course_offering_result->fetch_all(MYSQLI_ASSOC);
$course_offering_query->close();

if ($course_offering_result->num_rows == 0 || empty($course_offering_array)) {
    echo "<script type='text/javascript'>";
    echo "showModal('error', 'Sorry!', 'Sorry, no course offerings found for this course.');";
    echo "</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Details</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
</head>
<body>

<!-- Navigation Bar -->
<?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

<!-- Sub Header -->
<div class="subheader-container">
    <h2>
        <?php echo "Course Offerings For " . htmlspecialchars($courseDetails['coursenum']) . " - " .
            htmlspecialchars($courseDetails['coursename']); ?>
    </h2>
</div>

<!-- Filter Form -->
<div class="ta-filter-section">
    <form id="courseOfferDetailForm" class="course-offering-detail-sort-filter" action="" method="get">
        <div>
            <input type="hidden" name="coursenum" value="<?php echo htmlspecialchars($coursenum); ?>">
            <label for="startYear">Start Year:</label>
            <input type="number" id="startYear" name="startYear" required min="0">
            <label for="endYear">End Year:</label>
            <input type="number" id="endYear" name="endYear" required min="0">
        </div>
        <div class="course-offering-filter-button-container">
            <a href="javascript:void(0);" onclick="document.getElementById('courseOfferDetailForm').submit();"
               class="nav-button">Filter</a>
            <a href="?coursenum=<?php echo htmlspecialchars($coursenum); ?>" class="nav-button">Reset Filters</a>
        </div>
    </form>
</div>

<div class="ta-catalog">
    <?php
    if (!empty($course_offering_array)) {
        foreach ($course_offering_array as $course_offer) {
            echo "<div class='ta-card'>";
            echo "<h3>" . htmlspecialchars($course_offer['coid']) . "</h3>";
            echo "<p>Students: " . htmlspecialchars($course_offer['numstudent']) . "</p>";
            echo "<p>Term: " . htmlspecialchars($course_offer['term']) . "</p>";
            echo "<p>Year: " . htmlspecialchars($course_offer['year']) . "</p>";
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
