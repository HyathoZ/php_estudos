document.addEventListener('DOMContentLoaded', function () {
    const configIcon = document.getElementById('config-icon');
    const dropdownMenu = document.getElementById('dropdown-menu');

    configIcon.addEventListener('click', function () {
        // Alterna a exibição do menu
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    });

    // Fecha o menu ao clicar fora dele
    document.addEventListener('click', function (event) {
        if (!configIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });
});