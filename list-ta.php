<?php global $connection;
/**
 * This file lists all TAs in the database.
 */

require_once 'config.php'; // Include the configuration file
include DATA_ACCESS_PATH . 'connectToDatabase.php'; // Include the database connection
include COMPONENTS_PATH . 'modal.php' ?>

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

    <!-- Navigation Bar -->
    <?php include COMPONENTS_PATH . 'navigation-bar.php'; ?>

    <div class="add-ta-button-container">
        <a href="add-ta.php" class="add-ta-button">&#43; Add TA</a>
        <a class="delete-ta-button" onclick="toggleDeleteMode()">Enable Delete Mode</a>
    </div>

    <div class="ta-filter-section">
        <form action="list-ta.php" method="get" id="sortForm">
            <div class="ta-degree-filter">
                <label for="degreeFilter">Degree Type:
                    <select name="degreeFilter" onchange="this.form.submit()">
                        <option value="all" <?php echo($degreeFilter == 'all' ? 'selected' : ''); ?>>
                            All
                        </option>
                        <option value="Masters" <?php echo($degreeFilter == 'Masters' ? 'selected' : ''); ?>>
                            Masters
                        </option>
                        <option value="PhD" <?php echo($degreeFilter == 'PhD' ? 'selected' : ''); ?>>
                            PhD
                        </option>
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
                echo "<div class='ta-card' data-ta-id='" . htmlspecialchars($row["tauserid"]) . "' data-ta-name='" . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["lastname"]) . "'>";
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const url = new URL(window.location.href);
            const deleteModeActive = url.searchParams.get('deleteMode') === 'on';
            if (deleteModeActive) {
                updateDeleteModeUI(true);
            }
        });

        /**
         * Toggles delete mode.
         */
        function toggleDeleteMode() {
            const url = new URL(window.location.href);
            const deleteModeActive = url.searchParams.get('deleteMode') !== 'on';

            if (deleteModeActive) {
                url.searchParams.set('deleteMode', 'on');
            } else {
                url.searchParams.delete('deleteMode');
            }

            // Update the URL without reloading the page
            window.history.pushState({}, '', url);

            // Now call a function to update the UI based on the delete mode
            updateDeleteModeUI(deleteModeActive);
        }

        /**
         * Updates the UI based on the delete mode.
         * @param deleteModeActive - whether delete mode is active
         */
        function updateDeleteModeUI(deleteModeActive) {
            const deleteButton = document.querySelector('.delete-ta-button');
            deleteButton.textContent = deleteModeActive ? 'Disable Delete Mode' : 'Enable Delete Mode';

            const taCards = document.querySelectorAll('.ta-card');
            taCards.forEach(function (card) {
                if (deleteModeActive) {
                    card.classList.add('delete-hover');
                    card.parentNode.addEventListener('click', handleTACardClick); // Add event listener to the parent link
                } else {
                    card.classList.remove('delete-hover');
                    card.parentNode.removeEventListener('click', handleTACardClick); // Remove event listener from the parent link
                }
            });
        }

        /**
         * Deletes a TA from the database.
         * @param taId - the TA ID
         */
        function deleteTA(taId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "data-access/delete-ta.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Adding logging for debugging
            console.log("Sending request to delete TA with ID:", taId);

            xhr.onload = function () {
                console.log("Response received:", this.responseText); // Log the raw response
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            // Find the TA card and remove it from the DOM
                            const taCard = document.querySelector('.ta-card[data-ta-id="' + taId + '"]');
                            if (taCard) {
                                taCard.remove();
                            }
                            showModal('success', 'Success', response.message);
                        } else {
                            showModal('danger', 'Error', response.message);
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        showModal('danger', 'Error', 'Invalid response from the server.');
                    }
                } else {
                    console.error("Request failed with status:", this.status);
                    showModal('danger', 'Error', 'Request failed.');
                }
            };

            xhr.onerror = function () {
                console.error("Request error.");
                showModal('danger', 'Error', 'An error occurred during the request.');
            };

            xhr.send("taId=" + encodeURIComponent(taId));
        }


        /**
         * Handles a TA card click.
         * @param event - the click event
         */
        function handleTACardClick(event) {
            event.preventDefault();  // Prevent the default anchor action
            const taCard = event.currentTarget.querySelector('.ta-card');

            const taId = taCard.getAttribute('data-ta-id');
            const taName = taCard.getAttribute('data-ta-name');
            showModal(
                'danger',
                'Are you sure you want to permanently delete ' + taName + '?',
                'Once deleted, this TA cannot be recovered.',
                'deleteTA("' + taId + '")');
        }

    </script>

<?php $connection->close(); // Close the database connection ?>