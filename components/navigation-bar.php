<?php
/**
 * @author 67
 * This file contains the navigation bar for the Application.
 */

require_once 'config.php'; // Include the configuration file
?>

<nav class="navbar" id="navbar">
    <a href="<?php echo BASE_URL; ?>mainmenu.php" class="nav-button">
        Main Menu
    </a>
    <a href="<?php echo BASE_URL; ?>list-ta.php" class="nav-button">
        List & Edit TAs
    </a>
    <a href="<?php echo BASE_URL; ?>add-ta.php" class="nav-button">
        Add TA
    </a>
    <a href="<?php echo BASE_URL; ?>delete-ta.php" class="nav-button">
        Delete TA
    </a>
    <a href="<?php echo BASE_URL; ?>assign-ta-couresoffering.php" class="nav-button">
        Assign TAs
    </a>
    <a href="<?php echo BASE_URL; ?>list-courses-for-course-offerings.php" class="nav-button">
        Course Offerings By Course
    </a>
    <a href="<?php echo BASE_URL; ?>list-ta-course-offerings.php" class="nav-button">
        Course Offerings By TA
    </a>
    <a href="<?php echo BASE_URL; ?>list-course-offerings.php" class="nav-button">
        TAs By Course Offering
    </a>
</nav>


