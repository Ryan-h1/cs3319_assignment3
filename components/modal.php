<?php
/**
 * This file constitutes the modal component for the application.
 */

require_once 'config.php'; // Include the configuration file
require_once STYLES_PATH . 'colours.php'; // Include the colours file
?>

<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h2 id="modal-title"></h2>
        <p id="modal-message"></p>
    </div>
</div>

<script>
    const rootStyle = getComputedStyle(document.documentElement);
    const errorColor = rootStyle.getPropertyValue('--danger-colour');
    const successColor = rootStyle.getPropertyValue('--success-colour');

    function showModal(type, title, message) {
        const modal = document.getElementById("modal");
        const titleElement = document.getElementById("modal-title");
        const messageElement = document.getElementById("modal-message");
        const modalContent = document.querySelector(".modal-content");
        const lightenedDanger = lightenColor('<?php echo DANGER_COLOUR; ?>', 30);
        const lightenedSuccess = lightenColor('<?php echo SUCCESS_COLOUR; ?>', 30);

        titleElement.innerText = title;
        messageElement.innerText = message;

        // Apply different styles based on the type
        modalContent.style.borderColor = type === 'error' ? '<?php echo DANGER_COLOUR; ?>'
            : '<?php echo SUCCESS_COLOUR; ?>';
        titleElement.style.color = type === 'error' ? '<?php echo DANGER_COLOUR; ?>'
            : '<?php echo SUCCESS_COLOUR; ?>';

        modal.style.display = "block";
    }

    function closeModal() {
        const modal = document.getElementById("modal");
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        const modal = document.getElementById("modal");
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

