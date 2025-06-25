// scripts/atualizar_home.js
// Função global para atualizar o campo exibirNaHome via fetch
function atualizarExibirNaHome(el) {
    const imdbID = el.dataset.id;
    const ativo = el.checked;
    console.log('Atualizando exibirNaHome:', imdbID, ativo);
    fetch('atualizar_home.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ imdbID, ativo })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('Atualizado com sucesso!');
        } else {
            console.error('Erro ao atualizar:', data.message);
            alert('Erro ao atualizar destaque: ' + (data.message || 'Erro desconhecido.'));
        }
    })
    .catch(err => {
        console.error('Erro na requisição:', err);
        alert('Erro de comunicação com o servidor.');
    });
}
