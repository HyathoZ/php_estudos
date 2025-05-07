document.addEventListener('DOMContentLoaded', function () {
    function openEditModal(user) {
        // Preenche os campos do modal com os dados do usuário
        document.getElementById('edit_id').value = user.id;
        document.getElementById('edit_nome').value = user.nome;
        document.getElementById('edit_cpf').value = user.cpf;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_senha').value = user.senha;

        // Ajusta o título e o botão do modal
        document.getElementById('modalTitle').textContent = 'Editar Usuário';
        document.getElementById('modalSubmitButton').textContent = 'Salvar Alterações';
        document.getElementById('modalSubmitButton').name = 'update_user';

        // Exibe o modal
        const modal = document.getElementById('editModal');
        modal.classList.add('show');
    }

    function openCreateModal() {
        // Limpa os campos do modal
        document.getElementById('edit_id').value = '';
        document.getElementById('edit_nome').value = '';
        document.getElementById('edit_cpf').value = '';
        document.getElementById('edit_email').value = '';
        document.getElementById('edit_senha').value = '';

        // Ajusta o título e o botão do modal
        document.getElementById('modalTitle').textContent = 'Novo Usuário';
        document.getElementById('modalSubmitButton').textContent = 'Criar Usuário';
        document.getElementById('modalSubmitButton').name = 'create_user';

        // Exibe o modal
        const modal = document.getElementById('editModal');
        modal.classList.add('show');
    }

    document.getElementById('closeModal').addEventListener('click', function () {
        const modal = document.getElementById('editModal');
        modal.classList.remove('show');
    });

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });

    // Torna as funções globais para serem usadas inline no HTML
    window.openEditModal = openEditModal;
    window.openCreateModal = openCreateModal;
});