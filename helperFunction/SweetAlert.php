<style>
/* Enhanced Notification System */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
    width: 100%;
    pointer-events: none;
}

/* Base notification styles */
.danger-notify,
.success-notify,
.warning-notify {
    position: relative;
    background: linear-gradient(135deg, #fff5f5, #fed7d7);
    color: #c53030;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    border-left: 4px solid #e53e3e;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.5;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    margin-bottom: 0.75rem;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    pointer-events: auto;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(229, 62, 62, 0.2);
}

.danger-notify.show,
.success-notify.show,
.warning-notify.show {
    transform: translateX(0);
    opacity: 1;
}

/* Success notification */
.success-notify {
    background: linear-gradient(135deg, #f0fff4, #c6f6d5);
    color: #22543d;
    border-left-color: #38a169;
    border: 1px solid rgba(56, 161, 105, 0.2);
}

/* Warning notification */
.warning-notify {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    color: #92400e;
    border-left-color: #d69e2e;
    border: 1px solid rgba(214, 158, 46, 0.2);
}

/* Notification content */
.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.notification-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1rem;
}

.danger-notify .notification-icon {
    background: rgba(229, 62, 62, 0.1);
    color: #e53e3e;
}

.success-notify .notification-icon {
    background: rgba(56, 161, 105, 0.1);
    color: #38a169;
}

.warning-notify .notification-icon {
    background: rgba(214, 158, 46, 0.1);
    color: #d69e2e;
}

.notification-text {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.notification-message {
    font-size: 0.8rem;
    opacity: 0.9;
    line-height: 1.4;
}

/* Close button */
.notification-close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    opacity: 0.6;
    transition: opacity 0.2s ease;
    font-size: 1rem;
    line-height: 1;
}

.notification-close:hover {
    opacity: 1;
    background: rgba(0, 0, 0, 0.05);
}

/* Progress bar */
.notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    opacity: 0.3;
    border-radius: 0 0 12px 12px;
    animation: progress 5s linear forwards;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Responsive design */
@media (max-width: 768px) {
    .notification-container {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
    
    .danger-notify,
    .success-notify,
    .warning-notify {
        transform: translateY(-100%);
        margin-bottom: 0.5rem;
        padding: 0.875rem 1rem;
        font-size: 0.8rem;
    }
    
    .danger-notify.show,
    .success-notify.show,
    .warning-notify.show {
        transform: translateY(0);
    }
    
    .notification-content {
        gap: 0.5rem;
    }
    
    .notification-icon {
        width: 18px;
        height: 18px;
        font-size: 0.875rem;
    }
}

@media (max-width: 480px) {
    .notification-container {
        top: 5px;
        right: 5px;
        left: 5px;
    }
    
    .danger-notify,
    .success-notify,
    .warning-notify {
        padding: 0.75rem;
        font-size: 0.75rem;
        border-radius: 8px;
    }
    
    .notification-title {
        font-size: 0.8rem;
    }
    
    .notification-message {
        font-size: 0.7rem;
    }
}

/* Animation for multiple notifications */
.notification-container .danger-notify:nth-child(2),
.notification-container .success-notify:nth-child(2),
.notification-container .warning-notify:nth-child(2) {
    animation-delay: 0.1s;
}

.notification-container .danger-notify:nth-child(3),
.notification-container .success-notify:nth-child(3),
.notification-container .warning-notify:nth-child(3) {
    animation-delay: 0.2s;
}

/* Hover effects */
.danger-notify:hover,
.success-notify:hover,
.warning-notify:hover {
    transform: translateX(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .danger-notify:hover,
    .success-notify:hover,
    .warning-notify:hover {
        transform: translateY(-2px);
    }
}

/* Special styling for room not found messages */
.room-not-found-notify {
    background: linear-gradient(135deg, #fff5f5, #fed7d7);
    color: #c53030;
    border-left-color: #e53e3e;
    border: 1px solid rgba(229, 62, 62, 0.2);
}

.room-not-found-notify .notification-icon {
    background: rgba(229, 62, 62, 0.1);
    color: #e53e3e;
}

.room-not-found-notify .notification-title {
    color: #742a2a;
}

.room-not-found-notify .notification-message {
    color: #c53030;
}
</style>

<script>
class NotificationManager {
    constructor() {
        this.container = this.createContainer();
        this.notifications = [];
        this.init();
    }

    createContainer() {
        const container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
        return container;
    }

    init() {
        // Auto-hide existing notifications after 5 seconds
        this.autoHideNotifications();
    }

    show(message, type = 'danger', title = null, duration = 5000) {
        const notification = this.createNotification(message, type, title);
        this.container.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto-hide
        if (duration > 0) {
            setTimeout(() => {
                this.hide(notification);
            }, duration);
        }

        this.notifications.push(notification);
        return notification;
    }

    createNotification(message, type, title) {
        const notification = document.createElement('div');
        notification.className = `${type}-notify`;
        
        if (type === 'danger' && message.toLowerCase().includes('room') && message.toLowerCase().includes('found')) {
            notification.classList.add('room-not-found-notify');
        }

        const icon = this.getIcon(type);
        const closeBtn = this.createCloseButton(notification);
        const progressBar = this.createProgressBar();

        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">${icon}</div>
                <div class="notification-text">
                    ${title ? `<div class="notification-title">${title}</div>` : ''}
                    <div class="notification-message">${message}</div>
                </div>
            </div>
            ${progressBar}
        `;

        notification.appendChild(closeBtn);
        return notification;
    }

    getIcon(type) {
        const icons = {
            danger: '⚠️',
            success: '✅',
            warning: '⚠️'
        };
        return icons[type] || icons.danger;
    }

    createCloseButton(notification) {
        const closeBtn = document.createElement('button');
        closeBtn.className = 'notification-close';
        closeBtn.innerHTML = '×';
        closeBtn.onclick = () => this.hide(notification);
        return closeBtn;
    }

    createProgressBar() {
        return '<div class="notification-progress"></div>';
    }

    hide(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications = this.notifications.filter(n => n !== notification);
        }, 400);
    }

    autoHideNotifications() {
        const notifications = document.querySelectorAll('.danger-notify, .success-notify, .warning-notify');
        notifications.forEach(notification => {
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.add('show');
                    setTimeout(() => {
                        this.hide(notification);
                    }, 5000);
                }
            }, 100);
        });
    }

    // Special method for room not found messages
    showRoomNotFound(location, roomType = null) {
        let message, title;
        
        if (roomType) {
            title = 'Room Not Found';
            message = `No ${roomType} rooms found in ${location}. Try searching for different room types or locations.`;
        } else {
            title = 'No Rooms Available';
            message = `No rooms found in ${location}. Please try a different location or check back later.`;
        }

        return this.show(message, 'danger', title, 8000);
    }
}

// Initialize notification manager
const notificationManager = new NotificationManager();

// Legacy functions for backward compatibility
function clearFormError() {
    const notifications = document.querySelectorAll('.danger-notify');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.display = 'none';
        }, 4900);
    });
}

function clearSuccessAlert() {
    const notifications = document.querySelectorAll('.success-notify');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.display = 'none';
        }, 4900);
    });
}
</script>

<?php if (isset($form_error)): ?>
    <script>
        notificationManager.show('<?php echo addslashes($form_error); ?>', 'danger', 'Search Result', 8000);
    </script>
<?php endif; ?>

<?php if (isset($successfullyRoomAdded)): ?>
    <script>
        notificationManager.show('<?php echo addslashes($successfullyRoomAdded); ?>', 'success', 'Success', 5000);
    </script>
<?php endif; ?>