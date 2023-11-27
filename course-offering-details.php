<?php global $connection;
/**
 * @author 67
 * This file displays the TAs on a course offering.
 */
require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
// Check if the coursenum is set, else throw an error modal
if (!isset($_GET['coid'])) {
    echo "<script type='text/javascript'>";
    echo "showModal('danger', 'Error', 'Course offer id not provided.');";
    echo "</script>";
} else {

    $coid = $_GET['coid'];

    $course_query = $connection->prepare("SELECT courseoffer.coid  AS coid,
                                           course.coursenum  AS coursenum,
                                           course.coursename AS coursename
                                    FROM courseoffer
                                             JOIN course ON courseoffer.whichcourse = course.coursenum
                                    WHERE courseoffer.coid = ?");
    $course_query->bind_param("s", $coid);
    $course_query->execute();
    $course_result = $course_query->get_result();

    if ($course_result->num_rows < 1) {
        echo "<script type='text/javascript'>";
        echo "showModal('danger', 'Error', 'Course not found.');";
        echo "</script>";
    }

    $courseDetails = $course_result->fetch_assoc();
    $course_query->close();

    $tas_on_course_offering_query = $connection->prepare("SELECT TA.*, hasworkedon.hours AS hours
                                                    FROM TA
                                                             JOIN hasworkedon ON TA.tauserid = hasworkedon.tauserid
                                                    WHERE hasworkedon.coid = ?");
    $tas_on_course_offering_query->bind_param("s", $coid);
    $tas_on_course_offering_query->execute();
    $tas_on_course_offering_result = $tas_on_course_offering_query->get_result();
    $tas_on_course_offering_array = $tas_on_course_offering_result->fetch_all(MYSQLI_ASSOC);
    $tas_on_course_offering_query->close();

    if ($course_result->num_rows < 1) {
        echo "<script type='text/javascript'>";
        echo "showModal('success', 'Sorry!', 'Sorry, no TAs found for course offering.');";
        echo "</script>";
    }
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
        <?php echo "TAs on Course Offering " .
            htmlspecialchars($courseDetails['coid']) . " | " .
            htmlspecialchars($courseDetails['coursenum']) . " - " .
            htmlspecialchars($courseDetails['coursename']); ?>
    </h2>
</div>

<div class="ta-catalog">
    <?php
    if (!empty($tas_on_course_offering_array)) {
        foreach ($tas_on_course_offering_array as $row) {
            echo "<div class='ta-card'>";
            echo "<h3>" . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["lastname"]) . "</h3>";
            echo "<p>TA ID: " . htmlspecialchars($row["tauserid"]) . "</p>";
            echo "<p>Degree: " . htmlspecialchars($row["degreetype"]) . "</p>";
            echo "<p>Student Number: " . htmlspecialchars($row["studentnum"]) . "</p>";
            echo "<p>Hours: " . htmlspecialchars($row["hours"] ?? '0') . "</p>";
            echo "</div>";
            echo "</a>";
        }
    } else {
        echo "<p>No TAs found.</p>";
    }
    ?>
</div>


</body>
</html>

<?php $connection->close(); // Close the database connection ?>
