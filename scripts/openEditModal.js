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

    // Corrige erro ao tentar atribuir onclick a elemento inexistente
    var closeModalBtn = document.getElementById('closeModal');
    var editModal = document.getElementById('editModal');
    if (closeModalBtn && editModal) {
        closeModalBtn.onclick = function () {
            editModal.classList.remove('show');
        };
    }

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });

    // Torna as funções globais para serem usadas inline no HTML
    window.openEditModal = openEditModal;
    window.openCreateModal = openCreateModal;
    // Torna a função de exclusão global
    window.openConfirmDeleteModal = function(userId) {
        document.getElementById('delete_id_modal').value = userId;
        document.getElementById('confirmDeleteModal').classList.add('show');
    };
    // Fecha o modal ao clicar no X ou em cancelar
    document.getElementById('closeConfirmDeleteModal').onclick = function() {
        document.getElementById('confirmDeleteModal').classList.remove('show');
    };
    document.getElementById('cancelDeleteBtn').onclick = function() {
        document.getElementById('confirmDeleteModal').classList.remove('show');
    };
    window.addEventListener('click', function (event) {
        const modal = document.getElementById('confirmDeleteModal');
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });

    // Funções para o modal de filmes
    window.openCreateMovieModal = function() {
        document.getElementById('movie_id').value = '';
        document.getElementById('movie_titulo').value = '';
        document.getElementById('movie_diretor').value = '';
        document.getElementById('movie_ano').value = '';
        document.getElementById('movie_genero').value = '';
        document.getElementById('movieModalTitle').textContent = 'Novo Filme';
        document.getElementById('movieModalSubmitButton').textContent = 'Cadastrar Filme';
        document.getElementById('movieModalSubmitButton').name = 'create_movie';
        document.getElementById('movieModal').classList.add('show');
    };
    window.openEditMovieModal = function(filme) {
        document.getElementById('movie_id').value = filme.id;
        document.getElementById('movie_titulo').value = filme.titulo;
        document.getElementById('movie_diretor').value = filme.diretor;
        document.getElementById('movie_ano').value = filme.ano;
        document.getElementById('movie_genero').value = filme.genero;
        document.getElementById('movieModalTitle').textContent = 'Editar Filme';
        document.getElementById('movieModalSubmitButton').textContent = 'Salvar Alterações';
        document.getElementById('movieModalSubmitButton').name = 'update_movie';
        document.getElementById('movieModal').classList.add('show');
    };
    document.getElementById('closeMovieModal').onclick = function() {
        document.getElementById('movieModal').classList.remove('show');
    };
    // Modal de exclusão de filme
    window.openConfirmDeleteMovieModal = function(id) {
        document.getElementById('delete_movie_id_modal').value = id;
        document.getElementById('confirmDeleteMovieModal').classList.add('show');
    };
    document.getElementById('closeConfirmDeleteMovieModal').onclick = function() {
        document.getElementById('confirmDeleteMovieModal').classList.remove('show');
    };
    document.getElementById('cancelDeleteMovieBtn').onclick = function() {
        document.getElementById('confirmDeleteMovieModal').classList.remove('show');
    };
    window.addEventListener('click', function (event) {
        const modal = document.getElementById('movieModal');
        if (event.target === modal) {
            modal.classList.remove('show');
        }
        const delModal = document.getElementById('confirmDeleteMovieModal');
        if (event.target === delModal) {
            delModal.classList.remove('show');
        }
    });
});