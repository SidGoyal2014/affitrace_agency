/**
 * Toast Notification System - Affitrace
 * Modern, elegant toast notifications
 * 
 * Usage:
 *   Toast.success('Title', 'Message');
 *   Toast.error('Error', 'Something went wrong');
 *   Toast.warning('Warning', 'Please check your input');
 *   Toast.info('Info', 'Here is some information');
 * 
 * Options:
 *   Toast.success('Title', 'Message', { duration: 5000, position: 'top-right' });
 */

const Toast = (function () {
    const defaultOptions = {
        duration: 4000,          // Duration in ms (0 for no auto-dismiss)
        position: 'top-right',   // top-right, top-left, top-center, bottom-right, bottom-left, bottom-center
        showProgress: true,      // Show countdown progress bar
        showClose: true,         // Show close button
        pauseOnHover: true,      // Pause timer on hover
    };

    const icons = {
        success: '<i class="fas fa-check"></i>',
        error: '<i class="fas fa-times"></i>',
        warning: '<i class="fas fa-exclamation"></i>',
        info: '<i class="fas fa-info"></i>'
    };

    let containers = {};

    function getContainer(position) {
        if (!containers[position]) {
            const container = document.createElement('div');
            container.className = `toast-container ${position}`;
            document.body.appendChild(container);
            containers[position] = container;
        }
        return containers[position];
    }

    function createToast(type, title, message, options = {}) {
        const opts = { ...defaultOptions, ...options };
        const container = getContainer(opts.position);

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;

        // Build inner HTML
        let html = `
            <div class="toast-icon">
                ${icons[type]}
            </div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${escapeHtml(title)}</div>` : ''}
                ${message ? `<div class="toast-message">${escapeHtml(message)}</div>` : ''}
            </div>
        `;

        if (opts.showClose) {
            html += `<button class="toast-close" aria-label="Close">&times;</button>`;
        }

        if (opts.showProgress && opts.duration > 0) {
            html += `<div class="toast-progress" style="animation-duration: ${opts.duration}ms;"></div>`;
        }

        toast.innerHTML = html;

        // Add to container
        container.appendChild(toast);

        // Setup close button
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => dismissToast(toast));
        }

        // Setup auto-dismiss
        let timeout;
        let remainingTime = opts.duration;
        let startTime;

        function startTimer() {
            if (opts.duration > 0) {
                startTime = Date.now();
                timeout = setTimeout(() => dismissToast(toast), remainingTime);
            }
        }

        function pauseTimer() {
            if (timeout) {
                clearTimeout(timeout);
                remainingTime -= Date.now() - startTime;
                const progress = toast.querySelector('.toast-progress');
                if (progress) {
                    const computedStyle = window.getComputedStyle(progress);
                    progress.style.width = computedStyle.width;
                    progress.style.animationPlayState = 'paused';
                }
            }
        }

        function resumeTimer() {
            const progress = toast.querySelector('.toast-progress');
            if (progress) {
                progress.style.animationPlayState = 'running';
            }
            startTimer();
        }

        if (opts.pauseOnHover) {
            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', resumeTimer);
        }

        startTimer();

        // Return toast element for potential programmatic control
        return {
            element: toast,
            dismiss: () => dismissToast(toast)
        };
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('hiding')) return;

        toast.classList.add('hiding');

        // Remove after animation
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }

            // Clean up empty containers
            Object.keys(containers).forEach(position => {
                if (containers[position] && containers[position].children.length === 0) {
                    containers[position].remove();
                    delete containers[position];
                }
            });
        }, 250);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Public API
    return {
        success: (title, message, options) => createToast('success', title, message, options),
        error: (title, message, options) => createToast('error', title, message, options),
        warning: (title, message, options) => createToast('warning', title, message, options),
        info: (title, message, options) => createToast('info', title, message, options),

        // Dismiss all toasts
        dismissAll: () => {
            Object.values(containers).forEach(container => {
                Array.from(container.children).forEach(toast => dismissToast(toast));
            });
        },

        // Configure default options
        configure: (options) => {
            Object.assign(defaultOptions, options);
        }
    };
})();

// Also export for module systems if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Toast;
}
