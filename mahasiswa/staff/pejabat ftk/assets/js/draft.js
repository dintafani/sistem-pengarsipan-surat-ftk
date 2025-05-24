// draft.js
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('a.button, button.button');

    buttons.forEach(button => {
        button.addEventListener('mousedown', () => {
            button.style.transform = 'scale(0.95)';
            button.style.transition = 'transform 0.1s ease';
        });
        button.addEventListener('mouseup', () => {
            button.style.transform = 'scale(1)';
        });
        button.addEventListener('mouseleave', () => {
            button.style.transform = 'scale(1)';
        });
    });
});
