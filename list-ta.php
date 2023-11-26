<?php global $connection;
/**
 * This file lists all TAs in the database.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection ?>

<?php
$sortOption = $_GET['sort'] ?? 'nameAsc';
$degreeFilter = $_GET['degreeFilter'] ?? 'all';

// Construct the base SQL query
$sql = "SELECT * FROM ta";

// Apply degree filter if not 'all'
if ($degreeFilter !== 'all') {
    $sql .= " WHERE degreetype = '$degreeFilter'";
}

// Apply sorting
$sql .= match ($sortOption) {
    'degree' => " ORDER BY degreetype ASC",
    'nameDesc' => " ORDER BY lastname DESC",
    'nameAsc' => " ORDER BY lastname ASC",
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

<div class="add-ta-button-container">
    <a href="add-ta.php" class="add-ta-button">&#43; Add TA</a>
</div>

<div class="ta-filter-section">
    <form action="list-ta.php" method="get" id="sortForm">
        <div class="ta-degree-filter">
            <label for="degreeFilter">Degree Type:
                <select name="degreeFilter" onchange="this.form.submit()">
                    <option value="all" <?php echo($degreeFilter == 'all' ? 'selected' : ''); ?>>All</option>
                    <option value="Masters" <?php echo($degreeFilter == 'Masters' ? 'selected' : ''); ?>>Masters
                    </option>
                    <option value="PhD" <?php echo($degreeFilter == 'PhD' ? 'selected' : ''); ?>>PhD</option>
                </select>
            </label>
        </div>

        <div class="ta-sort-filter">
            <input type="radio" id="nameAsc" name="sort"
                   value="nameAsc"
                <?php echo($sortOption == 'nameAsc' ? 'checked' : ''); ?>
                   onchange="this.form.submit()"
            >
            <label for="nameAsc">Sort by Name Ascending</label>

            <input type="radio" id="nameDesc" name="sort"
                   value="nameDesc"
                <?php echo($sortOption == 'nameDesc' ? 'checked' : ''); ?>
                   onchange="this.form.submit()"
            >
            <label for="nameDesc">Sort by Name Descending</label>

            <input type="radio" id="degree" name="sort" value="degree"
                <?php echo($sortOption == 'degree' ? 'checked' : ''); ?>
                   onchange="this.form.submit()"
            >
            <label for="degree">Sort by Degree</label>
        </div>
    </form>
</div>

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
