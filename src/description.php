<?php
include('tmdb_api.php');
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo '<p>Filme não encontrado.</p>';
    exit;
}

$tmdb = new TMDB();
$movieId = intval($_GET['id']);

// Buscar detalhes do filme
$movie = $tmdb->getMovieDetails($movieId);
// Buscar vídeos (trailers)
$videos = $tmdb->getMovieVideos($movieId);
$trailer = null;
if (isset($videos['results'])) {
    foreach ($videos['results'] as $video) {
        if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
            $trailer = $video['key'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title><?php echo htmlspecialchars($movie['title'] ?? 'Filme'); ?> - Detalhes</title>
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
        .descricao-trailer {
            margin-top: 24px;
        }
        .descricao-trailer iframe {
            width: 100%;
            max-width: 560px;
            height: 315px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
    </style>
</head>
<body>
<header>
    <div class="container-header">
        <h2>Bem-vindo, <?php echo $_SESSION["nome"]; ?>!</h2>
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
            <?php if (!empty($movie['poster_path'])): ?>
                <img src="https://image.tmdb.org/t/p/w500<?php echo $movie['poster_path']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
            <?php else: ?>
                <div class="filme-sem-poster">Sem imagem</div>
            <?php endif; ?>
        </div>
        <div class="descricao-info">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            <p><strong>Título original:</strong> <?php echo htmlspecialchars($movie['original_title']); ?></p>
            <p><strong>Lançamento:</strong> <?php echo $movie['release_date']; ?></p>
            <p><strong>Duração:</strong> <?php echo $movie['runtime']; ?> min</p>
            <p><strong>Nota:</strong> <?php echo $movie['vote_average']; ?>/10 (<?php echo $movie['vote_count']; ?> votos)</p>
            <p><strong>Gêneros:</strong> <?php echo implode(', ', array_map(function($g){return $g['name'];}, $movie['genres'])); ?></p>
            <p><strong>Sinopse:</strong> <?php echo htmlspecialchars($movie['overview']); ?></p>
            <?php if (!empty($movie['homepage'])): ?>
                <p><a href="<?php echo $movie['homepage']; ?>" target="_blank">Site oficial</a></p>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($trailer): ?>
    <div class="descricao-trailer" style="text-align:center;">
        <h2>Trailer</h2>
        <iframe src="https://www.youtube.com/embed/<?php echo $trailer; ?>" frameborder="0" allowfullscreen></iframe>
    </div>
    <?php endif; ?>
    <div style="text-align:center; margin: 32px;">
        <a href="principal.php" style="color:#007bff; text-decoration:underline; font-weight:bold;">&larr; Voltar para os filmes</a>
    </div>
</main>
<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
<script src="../scripts/dropdown.js"></script>
</body>
</html>