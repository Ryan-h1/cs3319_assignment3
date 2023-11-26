<?php global $connection;
/**
 * This file allows the user to add a new TA to the database.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taUserId = $_POST['tauserid'];
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $studentNum = $_POST['studentnum'];
    $degreeType = $_POST['degreetype'];
    $image = $_POST['image'];

    // Prepare an INSERT statement to avoid SQL injection
    $insert_statement = $connection->prepare("INSERT INTO ta (tauserid, firstname, lastname, studentnum, degreetype, image) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_statement->bind_param("ssssss", $taUserId, $firstName, $lastName, $studentNum, $degreeType, $image);

    // Execute the statement
    $insert_statement->execute();

    // Check for errors
    if ($insert_statement->error) {
        echo "Error: " . $insert_statement->error;
    } else {
        echo "New TA added successfully";
    }

    // Close the statement
    $insert_statement->close();
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

    <?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

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
                    <input type="submit" value="Add TA">
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateImage(src) {
            showModal('success', 'Operation Successful', 'The TA has been added successfully.');
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