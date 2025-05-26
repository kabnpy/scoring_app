// public/js/ui_utils.js
//
// this file contains utility functions forui components,
// specifically for displaying dynamic toast messages.

/**
 * displays a toast message on the screen.
 * @param {string} message - the text message to display.
 * @param {string} type - the type of message ('success', 'error', 'info', 'warning').
 * @param {number} duration - how long the toast should be visible in milliseconds (0 for permanent until dismissed).
 */
function showToast(message, type = 'info', duration = 5000) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        console.error('Toast container not found. Please add <div id="toast-container"></div> to your HTML.');
        return;
    }

    const toast = document.createElement('div');
    toast.classList.add('toast', `toast-${type}`);
    toast.innerHTML = `
        <span>${message}</span>
        <button class="toast-dismiss">&times;</button>
    `;

    // append toast to container
    toastContainer.appendChild(toast);

    // force reflow to ensure transition works
    void toast.offsetWidth;

    // add 'show' class to trigger fade-in animation
    toast.classList.add('show');

    // add dismiss functionality
    const dismissButton = toast.querySelector('.toast-dismiss');
    dismissButton.addEventListener('click', () => {
        dismissToast(toast);
    });

    // auto-dismiss if duration is set
    if (duration > 0) {
        setTimeout(() => {
            dismissToast(toast);
        }, duration);
    }
}

/**
 * dismisses a given toast element with a fade-out animation.
 * @param {HTMLElement} toastElement - the toast dom element to dismiss.
 */
function dismissToast(toastElement) {
    if (toastElement && toastElement.classList.contains('show')) {
        toastElement.classList.remove('show');
        toastElement.addEventListener('transitionend', () => {
            toastElement.remove();
        }, { once: true });
    }
}

// this handles messages that might have been rendered directly by php before js loads
document.addEventListener('DOMContentLoaded', () => {
    const oldMessages = document.querySelectorAll('.message.auto-fade');
    oldMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.addEventListener('transitionend', () => message.remove(), { once: true });
        }, 5000); // fade out after 5 seconds
    });
});
