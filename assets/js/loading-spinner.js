 document.addEventListener('DOMContentLoaded', function() {
        const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.add('hidden');
    }
});

    window.addEventListener('beforeunload', function() {
        const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.remove('hidden');
    }
});

function showSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.remove('hidden');
}

function hideSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) spinner.classList.add('hidden');
}

// Tampilkan spinner saat submit form
document.querySelector('form').addEventListener('submit', function() {
    showSpinner();
});

// Atau untuk AJAX
fetch('/api/data')
    .then(res => res.json())
    .then(data => {
        hideSpinner();
        // proses data
    });
