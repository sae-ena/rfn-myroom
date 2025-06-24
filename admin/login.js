// Enhanced Login/Signup Form Management
class AuthFormManager {
    constructor() {
        this.toggleButton = document.getElementById('toggle-button');
        this.loginForm = document.getElementById('login-form');
        this.signupForm = document.getElementById('signup-form');
        this.registerForm = document.getElementById('register-form');
        this.alertBox = document.getElementById('alert-box');
        this.alertMessage = document.getElementById('alert-message');
        
        this.init();
    }

    init() {
        this.setupToggleButton();
        this.setupPasswordToggles();
        this.setupFormValidation();
        this.setupAutoHideAlerts();
        this.setupResponsiveBehavior();
    }

    setupToggleButton() {
        // Initially, display login form
        this.loginForm.classList.add('visible');
        
        this.toggleButton.addEventListener('click', () => {
            this.toggleForms();
        });
    }

    toggleForms() {
        if (this.loginForm.classList.contains('visible')) {
            // Switch to signup form with animation
            this.loginForm.classList.remove('visible');
            setTimeout(() => {
                this.signupForm.classList.add('visible');
            }, 200);
            this.toggleButton.textContent = "Go to Login";
        } else {
            // Switch to login form with animation
            this.signupForm.classList.remove('visible');
            setTimeout(() => {
                this.loginForm.classList.add('visible');
            }, 200);
            this.toggleButton.textContent = "Go to Sign Up";
        }
    }

    setupPasswordToggles() {
        // Password toggle functionality for both fields
        const togglePassword = document.getElementById('toggle-password');
        const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
        const passwordField = document.getElementById('user_password');
        const confirmPasswordField = document.getElementById('confirmPassword');

        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', () => {
                this.togglePasswordVisibility(passwordField, togglePassword);
            });
        }

        if (toggleConfirmPassword && confirmPasswordField) {
            toggleConfirmPassword.addEventListener('click', () => {
                this.togglePasswordVisibility(confirmPasswordField, toggleConfirmPassword);
            });
        }
    }

    togglePasswordVisibility(field, button) {
        const isPassword = field.type === 'password';
        field.type = isPassword ? 'text' : 'password';
        button.innerHTML = isPassword ? '&#128065;&#8205;&#127787;' : '&#128065;';
        
        // Add visual feedback
        button.style.transform = 'scale(1.1)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 150);
    }

    setupFormValidation() {
        if (this.registerForm) {
            this.registerForm.addEventListener('submit', (event) => {
                event.preventDefault();
                if (this.validateForm()) {
                    this.submitForm();
                }
            });

            // Real-time validation
            this.setupRealTimeValidation();
        }
    }

    setupRealTimeValidation() {
        const fields = {
            'user_name': {
                pattern: /^[A-Za-z\s]+$/,
                message: "Full name must only contain letters and spaces."
            },
            'user_email': {
                pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                message: "Please enter a valid email address."
            },
            'user_number': {
                pattern: /^[98|97]\d{9}$/,
                message: "Phone number must be 10 digits and start with 98 or 97."
            },
            'user_password': {
                pattern: /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/,
                message: "Password must be at least 8 characters long, contain one uppercase letter, one number, and one symbol."
            }
        };

        Object.keys(fields).forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                // Add input event for real-time validation
                field.addEventListener('input', (e) => {
                    this.clearFieldError(field);
                    
                    // Special handling for phone number field
                    if (fieldId === 'user_number') {
                        this.formatPhoneNumber(e.target);
                    }
                });
                
                field.addEventListener('blur', () => {
                    this.validateField(field, fields[fieldId]);
                });
            }
        });

        // Confirm password validation
        const confirmPassword = document.getElementById('confirmPassword');
        const password = document.getElementById('user_password');
        
        if (confirmPassword && password) {
            confirmPassword.addEventListener('blur', () => {
                this.validateConfirmPassword(password, confirmPassword);
            });
            
            password.addEventListener('input', () => {
                if (confirmPassword.value) {
                    this.validateConfirmPassword(password, confirmPassword);
                }
            });
        }
    }

    // Format phone number as user types
    formatPhoneNumber(input) {
        // Remove all non-digit characters
        let value = input.value.replace(/\D/g, '');
        
        // Limit to 10 digits
        if (value.length > 10) {
            value = value.slice(0, 10);
        }
        
        // Update the input value
        input.value = value;
        
        // Add visual feedback for valid format
        if (value.length === 10 && /^[98|97]/.test(value)) {
            input.style.borderColor = 'var(--success-color)';
            input.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
        } else if (value.length > 0) {
            input.style.borderColor = 'var(--error-color)';
            input.style.backgroundColor = 'rgba(239, 68, 68, 0.05)';
        } else {
            input.style.borderColor = 'var(--border-color)';
            input.style.backgroundColor = 'var(--bg-primary)';
        }
    }

    validateField(field, validation) {
        const value = field.value.trim();
        const formGroup = field.closest('.form-group') || field.parentElement;
        
        if (!validation.pattern.test(value)) {
            this.showFieldError(field, validation.message);
            return false;
        } else {
            this.showFieldSuccess(field);
            return true;
        }
    }

    validateConfirmPassword(password, confirmPassword) {
        const formGroup = confirmPassword.closest('.form-group') || confirmPassword.parentElement;
        
        if (password.value !== confirmPassword.value) {
            this.showFieldError(confirmPassword, "Passwords do not match.");
            return false;
        } else {
            this.showFieldSuccess(confirmPassword);
            return true;
        }
    }

    showFieldError(field, message) {
        const formGroup = field.closest('.form-group') || field.parentElement;
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        
        // Remove existing error message
        const existingError = formGroup.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        formGroup.appendChild(errorDiv);
    }

    showFieldSuccess(field) {
        const formGroup = field.closest('.form-group') || field.parentElement;
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        
        // Remove error message
        const errorMessage = formGroup.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }

    clearFieldError(field) {
        const formGroup = field.closest('.form-group') || field.parentElement;
        formGroup.classList.remove('error', 'success');
        
        const errorMessage = formGroup.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }

    validateForm() {
        const fullName = document.getElementById('user_name').value.trim();
        const email = document.getElementById('user_email').value.trim();
        const phoneNumber = document.getElementById('user_number').value.trim();
        const password = document.getElementById('user_password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        let isValid = true;

        // Validate Full Name
        if (!/^[A-Za-z\s]+$/.test(fullName)) {
            this.showAlert("Full name must only contain letters and spaces.", 'error');
            isValid = false;
        }

        // Validate Email
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            this.showAlert("Please enter a valid email address.", 'error');
            isValid = false;
        }

        // Validate Phone Number
        if (!/^[98|97]\d{9}$/.test(phoneNumber)) {
            this.showAlert("Phone number must be 10 digits and start with 98 or 97.", 'error');
            isValid = false;
        }

        // Validate Password
        const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;
        if (!passwordPattern.test(password)) {
            this.showAlert("Password must be at least 8 characters long, contain one uppercase letter, one number, and one symbol.", 'error');
            isValid = false;
        }

        // Confirm Password match
        if (password !== confirmPassword) {
            this.showAlert("Passwords do not match.", 'error');
            isValid = false;
        }

        return isValid;
    }

    submitForm() {
        const submitButton = this.registerForm.querySelector('button[type="submit"]');
        submitButton.classList.add('loading');
        submitButton.textContent = 'Creating Account...';
        
        // Simulate loading delay (remove this in production)
        setTimeout(() => {
            this.registerForm.submit();
        }, 1000);
    }

    showAlert(message, type = 'error') {
        if (this.alertBox && this.alertMessage) {
            this.alertMessage.textContent = message;
            this.alertBox.className = `alert-box ${type}`;
            this.alertBox.classList.add('show');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                this.hideAlert();
            }, 5000);
        }
    }

    hideAlert() {
        if (this.alertBox) {
            this.alertBox.classList.remove('show');
        }
    }

    setupAutoHideAlerts() {
        // Auto-hide server-side notifications
        const notifications = document.querySelectorAll('.danger-notify, .success-notify');
        notifications.forEach(notification => {
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(-50%) translateY(-100%)';
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }, 5000);
        });
    }

    setupResponsiveBehavior() {
        // Handle responsive behavior
        const handleResize = () => {
            const isMobile = window.innerWidth <= 480;
            
            if (isMobile) {
                document.body.classList.add('mobile');
            } else {
                document.body.classList.remove('mobile');
            }
        };

        // Initial check
        handleResize();
        
        // Listen for resize events
        window.addEventListener('resize', handleResize);
    }
}

// Enhanced password strength indicator
class PasswordStrengthIndicator {
    constructor() {
        this.passwordField = document.getElementById('user_password');
        this.strengthBar = null;
        this.init();
    }

    init() {
        if (this.passwordField) {
            this.createStrengthBar();
            this.passwordField.addEventListener('input', () => {
                this.updateStrength();
            });
        }
    }

    createStrengthBar() {
        const formGroup = this.passwordField.closest('.form-group') || this.passwordField.parentElement;
        
        this.strengthBar = document.createElement('div');
        this.strengthBar.className = 'password-strength';
        this.strengthBar.innerHTML = `
            <div class="strength-bar">
                <div class="strength-fill"></div>
            </div>
            <div class="strength-text"></div>
        `;
        
        formGroup.appendChild(this.strengthBar);
    }

    updateStrength() {
        const password = this.passwordField.value;
        const strength = this.calculateStrength(password);
        
        const fill = this.strengthBar.querySelector('.strength-fill');
        const text = this.strengthBar.querySelector('.strength-text');
        
        fill.style.width = `${strength.percentage}%`;
        fill.className = `strength-fill ${strength.level}`;
        text.textContent = strength.message;
        text.className = `strength-text ${strength.level}`;
    }

    calculateStrength(password) {
        let score = 0;
        let feedback = [];

        if (password.length >= 8) score += 25;
        if (/[A-Z]/.test(password)) score += 25;
        if (/[a-z]/.test(password)) score += 25;
        if (/[0-9]/.test(password)) score += 25;
        if (/[^A-Za-z0-9]/.test(password)) score += 25;

        if (score < 50) {
            return { level: 'weak', percentage: score, message: 'Weak password' };
        } else if (score < 75) {
            return { level: 'medium', percentage: score, message: 'Medium strength' };
        } else {
            return { level: 'strong', percentage: score, message: 'Strong password' };
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AuthFormManager();
    new PasswordStrengthIndicator();
});

// Add CSS for password strength indicator
const style = document.createElement('style');
style.textContent = `
    .password-strength {
        margin-top: 0.5rem;
    }
    
    .strength-bar {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }
    
    .strength-fill {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }
    
    .strength-fill.weak {
        background: #ef4444;
    }
    
    .strength-fill.medium {
        background: #f59e0b;
    }
    
    .strength-fill.strong {
        background: #10b981;
    }
    
    .strength-text {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .strength-text.weak {
        color: #ef4444;
    }
    
    .strength-text.medium {
        color: #f59e0b;
    }
    
    .strength-text.strong {
        color: #10b981;
    }
    
    .form-group {
        position: relative;
    }
    
    .form-group.error input {
        border-color: var(--error-color);
        animation: shake 0.3s ease;
    }
    
    .form-group.success input {
        border-color: var(--success-color);
    }
    
    .form-group .error-message {
        color: var(--error-color);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none;
    }
    
    .form-group.error .error-message {
        display: block;
    }
`;
document.head.appendChild(style);
