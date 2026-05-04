document.addEventListener('DOMContentLoaded', function () {
    const dateInputs = document.querySelectorAll('input[type=datetime-local]');
    dateInputs.forEach((input) => {
        if (!input.value) {
            input.value = '';
        }
    });
});
