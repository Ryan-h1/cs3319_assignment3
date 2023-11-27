<?php global $connection;
/**
 * @author 67
 * This file displays the details of a TA and all the course offerings this TA has worked on.
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
if (!isset($_GET['taId'])) {
    echo "<script type='text/javascript'>";
    echo "showModal('danger', 'Error', 'TA ID not provided.');";
    echo "</script>";
}

$taId = $_GET['taId'];

// Ta results query
$ta_query_statement = $connection->prepare("SELECT * FROM ta WHERE tauserid = ?");
$ta_query_statement->bind_param("s", $taId);
$ta_query_statement->execute();
$ta_result = $ta_query_statement->get_result();

if ($ta_result->num_rows < 1) {
    echo "<script type='text/javascript'>";
    echo "showModal('danger', 'Error', 'TA not found with ID: $taId');";
    echo "</script>";
}

$taDetails = $ta_result->fetch_assoc();
$ta_query_statement->close();

$courses_worked_on_by_ta = $connection->prepare("SELECT hasworkedon.hours AS hours,
                                                       courseoffer.coid  AS coid,
                                                       courseoffer.year  AS year,
                                                       courseoffer.term  AS term,
                                                       course.coursenum  AS coursenum,
                                                       course.coursename AS coursename
                                                FROM TA
                                                         JOIN hasworkedon ON TA.tauserid = hasworkedon.tauserid
                                                         JOIN courseoffer ON hasworkedon.coid = courseoffer.coid
                                                         JOIN course ON courseoffer.whichcourse = course.coursenum
                                                WHERE TA.tauserid = ?");
$courses_worked_on_by_ta->bind_param("s", $taId);
$courses_worked_on_by_ta->execute();
$courses_worked_on_by_ta_result = $courses_worked_on_by_ta->get_result();
$courses_worked_on_by_ta_array = $courses_worked_on_by_ta_result->fetch_all(MYSQLI_ASSOC);
$courses_worked_on_by_ta->close();

if (empty($courses_worked_on_by_ta_array)) {
    echo "<script type='text/javascript'>";
    echo "showModal('success', 'Sorry!', 'Sorry, no courses found for TA with ID: $taId');";
    echo "</script>";
}

$courses_loved_by_ta = $connection->prepare("SELECT loves.lcoursenum AS loves_coursenum
                                            FROM TA
                                                     JOIN loves ON TA.tauserid = loves.ltauserid
                                            WHERE TA.tauserid = ?");
$courses_loved_by_ta->bind_param("s", $taId);
$courses_loved_by_ta->execute();
$courses_loved_by_ta_result = $courses_loved_by_ta->get_result();
$courses_loved_by_ta_array = $courses_loved_by_ta_result->fetch_all(MYSQLI_ASSOC);
$courses_loved_by_ta->close();

$courses_hated_by_ta = $connection->prepare("SELECT hates.hcoursenum AS hates_coursenum
                                            FROM TA
                                                     JOIN hates ON TA.tauserid = hates.htauserid
                                            WHERE TA.tauserid = ?");
$courses_hated_by_ta->bind_param("s", $taId);
$courses_hated_by_ta->execute();
$courses_hated_by_ta_result = $courses_hated_by_ta->get_result();
$courses_hated_by_ta_array = $courses_hated_by_ta_result->fetch_all(MYSQLI_ASSOC);
$courses_hated_by_ta->close();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Courses Worked On By TA</title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
    </head>
    <body>

    <?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

    <div class="courses-worked-on-by-ta-container">

        <?php include COMPONENTS_PATH . 'ta-profile.php'; ?>

        <div class="ta-catalog">
            <?php
            $loved_course_numbers = array_column($courses_loved_by_ta_array, 'loves_coursenum');
            $hated_course_numbers = array_column($courses_hated_by_ta_array, 'hates_coursenum');
            if (!empty($courses_worked_on_by_ta_array)) {
                foreach ($courses_worked_on_by_ta_array as $course_worked_on_by_ta) {
                    $courseNum = $course_worked_on_by_ta['coursenum'] ?? 'N/A';
                    $lovesCourse = in_array($courseNum, $loved_course_numbers);
                    $hatesCourse = in_array($courseNum, $hated_course_numbers);

                    echo "<div class='ta-card'>";
                    echo "<h3>" . htmlspecialchars($courseNum) . " - " .
                        htmlspecialchars($course_worked_on_by_ta['coursename'] ?? 'N/A') . "</h3>";
                    echo "<h4>" . htmlspecialchars($course_worked_on_by_ta['coid'] ?? 'N/A') . "</h4>";
                    echo "<p>Hours: " . htmlspecialchars($course_worked_on_by_ta['hours'] ?? '0') . "</p>";
                    echo "<p>Term: " . htmlspecialchars($course_worked_on_by_ta['term'] ?? 'N/A') . "</p>";
                    echo "<p>Year: " . htmlspecialchars($course_worked_on_by_ta['year'] ?? 'N/A') . "</p>";
                    if ($lovesCourse) {
                        echo "<p><b>Loves this course! &#128578;</b></p>";
                    }
                    if ($hatesCourse) {
                        echo "<p><b>Hates this course! &#128577;</b></p>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>This TA has not worked on any course offerings.</p>";
            }
            ?>
        </div>
    </div>

    </body>
    </html>

<?php $connection->close(); // Close the database connection ?>