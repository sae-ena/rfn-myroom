// Select elements
const toggleButton = document.getElementById('toggle-button');
const loginForm = document.getElementById('login-form');
const signupForm = document.getElementById('signup-form');

// Initially, display login form
loginForm.classList.add('visible');

// Toggle between login and signup forms
toggleButton.addEventListener('click', () => {
    if (loginForm.classList.contains('visible')) {
        // Switch to signup form with animation
        loginForm.classList.remove('visible');
        signupForm.classList.add('visible');
        toggleButton.textContent = "Go to Login";
    } else {
        // Switch to login form with animation
        signupForm.classList.remove('visible');
        loginForm.classList.add('visible');
        toggleButton.textContent = "Go to Sign Up";
    }
});
