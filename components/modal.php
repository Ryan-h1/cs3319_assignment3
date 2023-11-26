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
        <a class="modal-proceed-button" id="modal-proceed-button">Proceed</a>
    </div>
</div>


<script>
    /**
     * Shows a modal with the given type, title, and message.
     * @param type - either 'danger' or 'success'
     * @param title - the title of the modal
     * @param message - the message of the modal
     * @param buttonAction - the action to perform when the proceed button is clicked
     */
    function showModal(type, title, message, buttonAction = null) {
        const modal = document.getElementById("modal");
        const titleElement = document.getElementById("modal-title");
        const messageElement = document.getElementById("modal-message");
        const proceedButton = document.getElementById("modal-proceed-button");
        const modalContent = document.querySelector(".modal-content");

        titleElement.innerText = title;
        messageElement.innerText = message;

        // Apply different styles based on the type
        modalContent.style.borderColor = type === 'danger' ? '<?php echo DANGER_COLOUR; ?>'
            : '<?php echo SUCCESS_COLOUR; ?>';
        titleElement.style.color = type === 'danger' ? '<?php echo DANGER_COLOUR; ?>'
            : '<?php echo SUCCESS_COLOUR; ?>';

        // Handle the proceed button
        if (buttonAction) {
            proceedButton.style.display = 'inline-block';
            proceedButton.onclick = function () {
                eval(buttonAction);
                closeModal();
            };
        } else {
            proceedButton.style.display = 'none';
        }

        modal.style.display = "block";
    }


    /**
     * Closes the modal.
     */
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

