document.addEventListener('DOMContentLoaded', function () {
  const botaoPesquisar = document.getElementById('botaoPesquisar');
  const modalPesquisa = document.getElementById('modalPesquisa');
  const fecharModalPesquisa = document.getElementById('fecharModalPesquisa');
  const input = document.getElementById('inputPesquisaFilme');
  const btnBuscar = document.getElementById('btnBuscarFilme');
  const resultados = document.getElementById('resultadosPesquisaFilme');

  let filmesEncontrados = [];
  let loading = false;

  // Abrir modal
  if (botaoPesquisar && modalPesquisa) {
    botaoPesquisar.addEventListener('click', function () {
      modalPesquisa.classList.add('show');
      modalPesquisa.style.display = 'block';
      if (input) input.focus();
      if (resultados) resultados.innerHTML = '';
    });
  } else {
    console.error('Botão ou modal de pesquisa não encontrados no DOM.');
  }

  // Fechar modal pelo X
  if (fecharModalPesquisa && modalPesquisa) {
    fecharModalPesquisa.addEventListener('click', function () {
      fecharModal();
    });
  }

  // Fechar modal ao clicar fora do conteúdo
  window.addEventListener('click', function (event) {
    if (event.target === modalPesquisa) {
      fecharModal();
    }
  });

  function fecharModal() {
    modalPesquisa.classList.remove('show');
    modalPesquisa.style.display = 'none';
  }

  // Realiza busca de filmes
  function buscarFilmes() {
    if (!input || !resultados) return;
    const termo = input.value.trim();

    if (termo.length < 2) {
      resultados.innerHTML = '<p style="color:#888;text-align:center;">Digite pelo menos 2 caracteres.</p>';
      return;
    }

    resultados.innerHTML = '<div style="color:#007bff;text-align:center;padding:20px;">Carregando...</div>';
    loading = true;

    console.log('Botão clicado, buscando por:', termo); // DEBUG

    fetch('movies.php?imdb_search=' + encodeURIComponent(termo))
      .then(r => r.json())
      .then(data => {
        loading = false;
        console.log('Resposta da API:', data); // DEBUG
        if (data && data.Search && data.Search.length) {
          filmesEncontrados = data.Search;
          resultados.innerHTML = filmesEncontrados.map(filme => renderCard(filme)).join('');
        } else {
          resultados.innerHTML = '<p style="color:#888;text-align:center;">Nenhum filme encontrado com esse título.</p>';
        }
      })
      .catch(err => {
        loading = false;
        console.error('Erro ao buscar filmes:', err);
        resultados.innerHTML = '<p style="color:#d00;text-align:center;">Erro ao buscar filmes. Tente novamente.</p>';
      });
  }

  // Gera HTML de cada filme
  function renderCard(filme) {
    const img = filme.Poster && filme.Poster !== 'N/A'
      ? `<img src="${filme.Poster}" alt="Poster de ${filme.Title}" style="width:60px;height:90px;object-fit:cover;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">`
      : `<div style="width:60px;height:90px;background:#e3e7ed;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#b0b8c1;font-size:13px;">Sem imagem</div>`;

    const filmeString = encodeURIComponent(JSON.stringify(filme));

    return `
      <div style="display:flex;align-items:center;gap:18px;padding:12px 0;border-bottom:1px solid #e3e7ed;">
        ${img}
        <div style="flex:1;">
          <div style="font-size:17px;font-weight:600;color:#222;">${filme.Title}</div>
          <div style="color:#007bff;font-size:14px;">${filme.Year}</div>
          <div style="color:#444;font-size:13px;margin:4px 0 8px 0;">
            ${filme.Type === 'movie' ? 'Filme' : filme.Type}
          </div>
          <button
            onclick='window.selecionarFilmeModal && window.selecionarFilmeModal(decodeURIComponent("${filmeString}"))'
            style="background:#222e3a;color:#fff;padding:7px 16px;border:none;border-radius:5px;font-size:15px;font-weight:500;cursor:pointer;transition:background 0.2s;"
          >
            Selecionar
          </button>
        </div>
      </div>`;
  }

  // Dispara busca ao clicar no botão
  if (btnBuscar) {
    btnBuscar.addEventListener('click', buscarFilmes);
  } else {
    console.error('Botão de buscar filme não encontrado no DOM.');
  }

  // Dispara busca ao pressionar Enter
  if (input) {
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        buscarFilmes();
      }
    });
  }

  // Selecionar filme (recebe o objeto completo via JSON serializado)
  window.selecionarFilmeModal = function (filmeJson) {
    let filme;
    try {
      filme = JSON.parse(filmeJson);
    } catch (e) {
      console.error('Erro ao decodificar filme selecionado:', e);
      return;
    }

    fetch('movies.php?imdb_search=' + encodeURIComponent(filme.imdbID) + '&by_id=1')
      .then(r => r.json())
      .then(data => {
        // Aqui você pode abrir um segundo modal com os dados detalhados e o toggle switch
        alert('Filme selecionado: ' + (data.Title || ''));
      })
      .catch(err => {
        console.error('Erro ao buscar detalhes do filme:', err);
        alert('Erro ao buscar dados do filme selecionado.');
      });
  };
});
