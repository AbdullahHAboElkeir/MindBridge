// assets/js/main.js

// General utility functions
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('button[type="submit"][class*="btn-danger"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});

// AJAX helper function
function ajaxRequest(url, method = 'GET', data = null, callback = null) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    if (method === 'POST' && data) {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                if (callback) {
                    callback(JSON.parse(xhr.responseText));
                }
            } else {
                console.error('AJAX Error:', xhr.status, xhr.responseText);
            }
        }
    };

    if (data && typeof data === 'object') {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        xhr.send(formData);
    } else {
        xhr.send(data);
    }
}

// Mood tracking functionality
function updateMoodDisplay(value) {
    const moodValue = document.getElementById('mood-value');
    if (moodValue) {
        moodValue.textContent = value;
    }
}

// Session management
function confirmSession(sessionId, action) {
    if (confirm(`Are you sure you want to ${action} this session?`)) {
        ajaxRequest(`/sessions/${sessionId}/${action}`, 'POST', null, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    }
}