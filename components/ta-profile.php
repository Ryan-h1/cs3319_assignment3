<?php
/**
 * @author 67
 * This file contains the HTML for the TA profile component.
 * It is used to display the TA's details.
 */
?>

<?php if (isset($taDetails)): ?>
    <div class="ta-detail">
        <div class="ta-detail-card">
            <!-- Check if the TA has an image URL and display it -->
            <?php if (!empty($taDetails["image"])): ?>
                <img class="ta-profile-picture"
                     src="<?php echo htmlspecialchars($taDetails["image"]); ?>"
                     alt="Photo of <?php echo htmlspecialchars($taDetails["firstname"]) . " " .
                         htmlspecialchars($taDetails["lastname"]); ?>">
            <?php else: ?>
                <img class="ta-profile-picture"
                     src="https://christopherscottedwards.com/wp-content/uploads/2018/07/Generic-Profile.jpg"
                     alt="Generic profile picture">
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($taDetails["firstname"]) . " " .
                    htmlspecialchars($taDetails["lastname"]); ?>
            </h2>
            <p>TA ID: <?php echo htmlspecialchars($taDetails["tauserid"]); ?></p>
            <p>Degree: <?php echo htmlspecialchars($taDetails["degreetype"]); ?></p>
            <p>Student Number: <?php echo htmlspecialchars($taDetails["studentnum"]); ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="ta-detail">
        <div class="ta-detail-card">
            <img class="ta-profile-picture"
                 src="https://christopherscottedwards.com/wp-content/uploads/2018/07/Generic-Profile.jpg"
                 alt="Generic profile picture">

            <h2><?php echo "TA Details Not Found" ?></h2>
        </div>
    </div>
<?php endif; ?>
