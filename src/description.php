<?php
session_start();
$id = $_GET['id'] ?? null;
if (!$id) {
    exit('ID não informado.');
}
require_once 'tmdb_api.php';
$tmdb = new TMDB();
// Tenta buscar detalhes do filme pelo imdbID na TMDb
$tmdbResult = file_get_contents("https://api.themoviedb.org/3/find/" . urlencode($id) . "?api_key=f2fbd21b14ebbd5a6594f797ddff2613&language=pt-BR&external_source=imdb_id");
$tmdbData = $tmdbResult ? json_decode($tmdbResult, true) : null;
$filme = null;
if ($tmdbData && isset($tmdbData['movie_results'][0])) {
    $filme = $tmdbData['movie_results'][0];
}
if (!$filme) {
    exit('Filme não encontrado na TMDb.');
}
$poster = $filme['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $filme['poster_path'] : '../imagens/sem-imagem.png';
$title = $filme['title'] ?? 'Título não disponível';
$original_title = $filme['original_title'] ?? 'Título original não disponível';
$year = isset($filme['release_date']) ? substr($filme['release_date'], 0, 4) : 'Ano não informado';
$overview = $filme['overview'] ?? 'Sinopse indisponível';
$rating = $filme['vote_average'] ?? 'N/A';
$runtime = $filme['runtime'] ?? null; // runtime não vem nesse endpoint, só no /movie/{id}
// Busca runtime e outros detalhes
$details = $tmdb->getMovieDetails($filme['id']);
if ($details) {
    $runtime = $details['runtime'] ?? $runtime;
    $genres = isset($details['genres']) && is_array($details['genres']) ? implode(', ', array_map(fn($g) => $g['name'], $details['genres'])) : 'Gêneros não informados';
    $homepage = $details['homepage'] ?? null;
} else {
    $genres = 'Gêneros não informados';
    $homepage = null;
}
// Busca elenco
$credits = file_get_contents("https://api.themoviedb.org/3/movie/{$filme['id']}/credits?api_key=f2fbd21b14ebbd5a6594f797ddff2613&language=pt-BR");
$creditsData = $credits ? json_decode($credits, true) : null;
$actors = '';
if ($creditsData && isset($creditsData['cast'])) {
    $actors = implode(', ', array_map(fn($a) => $a['name'], array_slice($creditsData['cast'], 0, 6)));
}
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title><?= htmlspecialchars($title) ?> - Detalhes</title>
    <style>
        .descricao-filme-container {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 32px;
            margin: 32px auto;
            max-width: 1000px;
        }
        .descricao-poster {
            flex: 0 0 300px;
            max-width: 300px;
        }
        .descricao-poster img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .descricao-info {
            flex: 1;
            min-width: 250px;
        }
        .descricao-info h1 {
            margin-top: 0;
            color: #007bff;
        }
        .descricao-info p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
<header>
    <div class="container-header">
        <h2>Bem-vindo, <?= htmlspecialchars($_SESSION["nome"] ?? '') ?>!</h2>
        <div class="sair">
            <div class="config">
                <img src="../icones/config.svg" alt="Configurações" class="config-icon" id="config-icon">
                <div class="dropdown" id="dropdown-menu">
                    <ul>
                        <li><a href="./usuarios.php">Usuarios</a></li>
                        <li><a href="./movies.php">Filmes</a></li>
                        <li><a href="#">Ajuda</a></li>
                    </ul>
                </div>
            </div>
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>
    </div>
</header>
<div style="margin: 20px; text-align: right;">
    <a href="principal.php" class="btn-voltar" style="
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    ">&larr; Voltar</a>
</div>
<main>
    <div class="descricao-filme-container">
        <div class="descricao-poster">
            <img src="<?= htmlspecialchars($poster) ?>" alt="<?= htmlspecialchars($title) ?>">
        </div>
        <div class="descricao-info">
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><strong>Título original:</strong> <?= htmlspecialchars($original_title) ?></p>
            <p><strong>Lançamento:</strong> <?= htmlspecialchars($year) ?></p>
            <p><strong>Duração:</strong> <?= $runtime ? htmlspecialchars($runtime) . ' min' : 'Não informado' ?></p>
            <p><strong>Nota:</strong> <?= htmlspecialchars($rating) ?>/10</p>
            <p><strong>Gêneros:</strong> <?= htmlspecialchars($genres) ?></p>
            <p><strong>Elenco:</strong> <?= htmlspecialchars($actors) ?></p>
            <p><strong>Sinopse:</strong> <?= htmlspecialchars($overview) ?></p>
            <?php if ($homepage): ?>
                <p><a href="<?= htmlspecialchars($homepage) ?>" target="_blank">Site oficial</a></p>
            <?php endif; ?>
        </div>
    </div>
    <div style="text-align:center; margin: 32px;">
        <a href="principal.php" style="color:#007bff; text-decoration:underline; font-weight:bold;">&larr; Voltar para os filmes</a>
    </div>
</main>
<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
</body>
</html>