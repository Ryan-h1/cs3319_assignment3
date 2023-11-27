<?php global $connection;


require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connect-to-database.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php';
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

</body>
</html>

<?php $connection->close(); // Close the database connection ?>
