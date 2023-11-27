<?php global $connection;
/**
 * This file allows the user to add a new TA to the database.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
/**
 * SQL
 */
$courses_query = $connection->prepare("SELECT coursenum, coursename FROM course");
$courses_query->execute();
$courses_result = $courses_query->get_result();
$courses_array = $courses_result->fetch_all(MYSQLI_ASSOC);
$courses_query->close();

// Form Handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taUserId = $_POST['tauserid'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $studentNum = $_POST['studentnum'];
    $degreeType = $_POST['degreetype'];
    $image = $_POST['image'];
    $lovesCourses = $_POST['loves_courses'] ?? []; // Array of course numbers TA loves
    $hatesCourses = $_POST['hates_courses'] ?? []; // Array of course numbers TA hates

    // Prepare an INSERT statement to avoid SQL injection
    $insert_statement = $connection->prepare(
        "INSERT INTO ta (tauserid, firstname, lastname, studentnum, degreetype, image) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $insert_statement->bind_param(
        "ssssss", $taUserId, $firstName, $lastName, $studentNum, $degreeType, $image
    );

    // Check if the student number already exists
    $check_statement = $connection->prepare("SELECT * FROM ta WHERE studentnum = ?");
    $check_statement->bind_param("s", $studentNum);
    $check_statement->execute();
    $check_result = $check_statement->get_result();
    $check_statement->close();

    if ($check_result->num_rows > 0) {
        // Student number already exists
        $message = "A TA with this student number already exists.";
        $messageType = 'danger';
    } else {
        // Check if the student number doesn't already exist, run the insert statement
        $insert_statement->execute();
        // Check for errors
        if ($insert_statement->error || $insert_statement->affected_rows < 1) {
            // Custom error handling based on specific errors
            if ($insert_statement->errno == 1062) { // Duplicate entry error
                $message = "A TA with this User ID already exists.";
            } else {
                $message = "Error: " . $insert_statement->error;
            }
            $messageType = 'error';
        } else {
            if (is_array($lovesCourses)) {
                $loves_insert_statement = $connection->prepare("INSERT INTO loves VALUES (?, ?)");
                foreach ($lovesCourses as $courseNum) {
                    $loves_insert_statement->bind_param("ss", $taUserId, $courseNum);
                    $loves_insert_statement->execute();
                }
                $loves_insert_statement->close();
            }

            if (is_array($hatesCourses)) {
                $hates_insert_statement = $connection->prepare("INSERT INTO hates VALUES (?, ?)");
                foreach ($hatesCourses as $courseNum) {
                    $hates_insert_statement->bind_param("ss", $taUserId, $courseNum);
                    $hates_insert_statement->execute();
                }
                $hates_insert_statement->close();
            }

            $message = "New TA added successfully";
            $messageType = 'success';
        }

        // Close the statement
        $insert_statement->close();
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add New TA</title>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
    </head>
    <body>
    <!--    Navigation Bar-->
    <?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

    <!--    TA Form-->
    <div class="add-ta-container">
        <div class="add-ta-card">
            <form class="add-ta-form" action="add-ta.php" method="post">
                <div>
                    <img class="ta-profile-picture"
                         src="<?php echo DEFAULT_TA_IMAGE; ?>"
                         alt="Generic profile picture">
                </div>

                <div class="add-ta-form-items-container">
                    <div class="add-ta-form-item">
                        <label for="image">Image URL:</label>
                        <input type="text" id="image" name="image" oninput="updateImage(this.value)" maxlength="200">
                    </div>
                    <div class="add-ta-form-item">
                        <label for="tauserid">TA User ID*:</label>
                        <input type="text" id="tauserid" name="tauserid" required maxlength="8">
                    </div>
                    <div class="add-ta-form-item">
                        <label for="firstname">First Name*:</label>
                        <input type="text" id="firstname" name="firstname" required maxlength="30">
                    </div>
                    <div class="add-ta-form-item">
                        <label for="lastname">Last Name*:</label>
                        <input type="text" id="lastname" name="lastname" required maxlength="30">
                    </div>
                    <div class="add-ta-form-item">
                        <label for="studentnum">Student Number*:</label>
                        <input type="text" id="studentnum" name="studentnum" required maxlength="9">
                    </div>
                    <div class="add-ta-form-item">
                        <label for="degreetype">Degree Type:</label>
                        <input type="text" id="degreetype" name="degreetype" maxlength="7">
                    </div>
                    <div class="add-ta-form-item">
                        <label for="loves_courses">Courses TA Loves:</label>
                        <select multiple id="loves_courses" name="loves_courses[]">
                            <?php foreach ($courses_array as $row): ?>
                                <option value="<?php echo htmlspecialchars($row['coursenum']); ?>">
                                    <?php echo htmlspecialchars($row['coursename']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Courses TA Hates -->
                    <div class="add-ta-form-item">
                        <label for="hates_courses">Courses TA Hates:</label>
                        <select multiple id="hates_courses" name="hates_courses[]">
                            <?php foreach ($courses_array as $row): ?>
                                <option value="<?php echo htmlspecialchars($row['coursenum']); ?>">
                                    <?php echo htmlspecialchars($row['coursename']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input class="modal-proceed-button" type="submit" value="Add TA">
                </div>
            </form>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <?php if (!empty($message)): ?>
        <script>
            window.onload = function () {
                showModal(
                    '<?php echo $messageType; ?>',
                    '<?php echo $messageType === 'success' ? 'Insert Successful' : 'Insert Failed'; ?>',
                    '<?php echo $message; ?>'
                );
            };
        </script>
    <?php endif; ?>

    <!-- JS -->
    <script>
        function updateImage(src) {
            const imgElement = document.querySelector('.ta-profile-picture');
            const testImage = new Image();
            testImage.onload = function () {
                // If the image loads successfully, set the src to the entered URL
                imgElement.src = src;
            };
            testImage.onerror = function () {
                // If the image fails to load, set the src to the default image
                imgElement.src = "<?php echo DEFAULT_TA_IMAGE; ?>";
            };
            testImage.src = src;
        }
    </script>

    </body>
    </html>

<?php $connection->close(); // Close the database connection ?>