<?php
include('tmdb_api.php');
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: index.php");
    exit;
}

$tmdb = new TMDB();
$popularMovies = $tmdb->getPopularMovies();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Unifilmes</title>
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
                        <li><a href="#">Tema</a></li>
                        <li><a href="#">Ajuda</a></li>
                    </ul>
                </div>
            </div>
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>
    </div>
</header>

<main class="container">
    <aside class="menu">
        <h3>Filmes Populares</h3>
        <ul>
            <?php foreach ($popularMovies['results'] as $movie): ?>
                <li><?php echo $movie['title']; ?></li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <section class="filmes">
        <h3>Detalhes do Filme</h3>
        <p>Selecione um filme para ver mais detalhes.</p>
    </section>
</main>

<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
<script src="../scripts/dropdown.js"></script>
</body>
</html>