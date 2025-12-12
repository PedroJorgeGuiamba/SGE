document.addEventListener('DOMContentLoaded', function () {
    const html = document.documentElement;
    const toggleBtn = document.getElementById('themeToggle');
    const icon = toggleBtn.querySelector('i');

    // Função para atualizar o ícone
    function updateIcon() {
        if (html.getAttribute('data-bs-theme') === 'dark') {
            icon.classList.replace('fa-moon', 'fa-sun');
            toggleBtn.setAttribute('title', 'Modo Claro');
        } else {
            icon.classList.replace('fa-sun', 'fa-moon');
            toggleBtn.setAttribute('title', 'Modo Escuro');
        }
    }

    // Troca o tema e envia para o PHP salvar
    toggleBtn.addEventListener('click', function () {
        const current = html.getAttribute('data-bs-theme');
        const newTheme = current === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-bs-theme', newTheme);
        updateIcon();

        // Envia para o PHP salvar a preferência (via AJAX)
        fetch('../../Controller/Geral/TrocarTema.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'theme=' + newTheme
        });
    });

    // Atualiza ícone ao carregar
    updateIcon();
});