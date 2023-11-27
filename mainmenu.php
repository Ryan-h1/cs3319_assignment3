<?php
/**
 * @author 67
 * This file is the main menu for the TA Application.
 */

require_once 'config.php'; // Include the configuration file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Menu</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
</head>
<body>

<?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

<div class="title-container">
    <h1 class="title">Welcome to the Main Menu</h1>
</div>

</body>
</html>
