document.addEventListener('DOMContentLoaded', function() {
    // Validare formular
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.querySelector('input[type="email"]');
            const linkedin = document.querySelector('input[name="linkedin_profile"]');

            if (email && !validateEmail(email.value)) {
                e.preventDefault();
                alert('Please enter a valid email address');
            }

            if (linkedin && !validateLinkedIn(linkedin.value)) {
                e.preventDefault();
                alert('Please enter a valid LinkedIn URL');
            }
        });
    }
});

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validateLinkedIn(url) {
    return url.includes('linkedin.com/');
}

document.getElementById('toggle-dark-mode').addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
});

// Stiluri pentru dark mode
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .dark-mode {
            background-color: #121212;
            color: #fff;
        }
        .dark-mode .navbar {
            background-color: #333;
        }
        .dark-mode .jumbotron{
            color: white;
            background-color: #333333;
        }
        .dark-mode .card-body{
            color: white;
            background-color: #333333;
        }
    </style>
`);
