// Select all navigation links and content divs
const contentSections = document.querySelectorAll('.content');
const confirmLogoutBtn = document.getElementById('confirm-logout');
const cancelLogoutBtn = document.getElementById('cancel-logout');

// Add click event listener to each nav link
navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        // Remove 'active' class from all links
        navLinks.forEach(link => link.classList.remove('active'));

        // Hide all content sections
        contentSections.forEach(section => section.classList.add('hidden'));

        // Add 'active' class to the clicked link
        this.classList.add('active');

        // Get the target section from data attribute and show the corresponding content
        const target = this.getAttribute('data-target');
        document.getElementById(target).classList.remove('hidden');
    });
});

// Initially show the dashboard content
document.getElementById('dashboard').classList.remove('hidden');
navLinks[0].classList.add('active');

// Logout confirmation functionality
confirmLogoutBtn.addEventListener('click', () => {
    alert('You have logged out!');
    // Simulate a logout action
    window.location.reload(); // For simplicity, reload the page after logout
});

cancelLogoutBtn.addEventListener('click', () => {
    // Cancel logout, show the dashboard again
    navLinks.forEach(link => link.classList.remove('active'));
    contentSections.forEach(section => section.classList.add('hidden'));
    
    // Show the dashboard again and make it active
    document.getElementById('dashboard').classList.remove('hidden');
    navLinks[0].classList.add('active');
});
