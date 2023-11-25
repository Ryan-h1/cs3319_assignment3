<?php global $connection;
/**
 * This file lists all TAs in the database.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection ?>

<?php
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'nameAsc';

$sql = match ($sortOption) {
    'degree' => "SELECT * FROM ta ORDER BY degreetype ASC, lastname ASC, firstname ASC",
    'nameDesc' => "SELECT * FROM ta ORDER BY lastname DESC, firstname DESC",
    default => "SELECT * FROM ta ORDER BY lastname ASC, firstname ASC",
};

$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List TAs</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/styles.css">
</head>
<body>

<?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

<form action="list-ta.php" method="get">
    <input type="radio" id="nameAsc" name="sort"
           value="nameAsc" <?php echo($sortOption == 'nameAsc' ? 'checked' : ''); ?>>
    <label for="nameAsc">Sort by Name Ascending</label>

    <input type="radio" id="nameDesc" name="sort"
           value="nameDesc" <?php echo($sortOption == 'nameDesc' ? 'checked' : ''); ?>>
    <label for="nameDesc">Sort by Name Descending</label>

    <input type="radio" id="degree" name="sort" value="degree"
        <?php echo($sortOption == 'degree' ? 'checked' : ''); ?>
    >

    <label for="degree">Sort by Degree</label>

    <input type="submit" value="Sort">
</form>


<div class="ta-catalog">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<a href='ta-details.php?taid=" . urlencode($row["tauserid"]) . "' class='ta-card-link'>";
            echo "<div class='ta-card'>";
            echo "<h3>" . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["lastname"]) . "</h3>";
            echo "<p>TA ID: " . htmlspecialchars($row["tauserid"]) . "</p>";
            echo "<p>Degree: " . htmlspecialchars($row["degreetype"]) . "</p>";
            echo "<p>Student Number: " . htmlspecialchars($row["studentnum"]) . "</p>";
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
