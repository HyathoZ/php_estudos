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
    <link rel="stylesheet" href="../css/modal.css">
    <title>Unifilmes</title>
</head>
<<<<<<< HEAD

<body id="conteudo">
    <header>
        <p>Olá <?php echo $_SESSION["nome"]; ?></p>
        <div class="sair">
        <a href="logout.php">Sair</a>
        </div>
    </header>

    <div class="principal">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo Unifilmes">
        </div>
        <nav class="menu-principal">
            <ul>
                <li><a href="#">Início</a></li>
                <li><a href="#">Sobre</a></li>
                <li><a href="#">Contato</a></li>
            </ul>
        </nav>
    <div class="container">
    <div class="menu">
        <h2>Menu</h2>
        <p>Filme 1</p>
        <p>Filme 2</p>
        <p>Filme 3</p>
    </div>
        <h1>Em construção...</h1>
        
        <div class="filmes-lista">
            <h3>Filmes em catalago</h3>
            <p>Filme 1</p>
            <p>Filme 2</p>
            <p>Filme 3</p>
        </div>
    </div>
</div>
    <footer class="footer">
        <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
    </footer>
</div> 
=======
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

<main class="container">
    <section class="filmes filmes-destaque">
        <form method="GET" class="search-bar" style="margin-bottom: 24px;">
            <input type="text" name="q" placeholder="Pesquisar" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required>
            <button type="submit" class="btn-search">Pesquisar</button>
            <?php if (isset($_GET['q'])): ?>
                <a href="principal.php" class="btn-clear" style="margin-left:10px;">Limpar</a>
            <?php endif; ?>
        </form>
        <h3>Filmes em Destaque</h3>
        <?php
        $filmesParaExibir = $popularMovies;
        if (isset($_GET['q']) && trim($_GET['q']) !== '') {
            $busca = trim($_GET['q']);
            $filmesParaExibir = $tmdb->searchMovie($busca);
            echo '<h4>Resultados para: ' . htmlspecialchars($busca) . '</h4>';
        }
        ?>
        <div class="filmes-grid">
            <?php if (!empty($filmesParaExibir['results'])): ?>
                <?php foreach ($filmesParaExibir['results'] as $movie): ?>
                    <a href="description.php?id=<?php echo $movie['id']; ?>" class="filme-link">
                        <div class="filme-card">
                            <?php if ($movie['poster_path']): ?>
                                <img src="https://image.tmdb.org/t/p/w300<?php echo $movie['poster_path']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="filme-poster">
                            <?php else: ?>
                                <div class="filme-sem-poster">Sem imagem</div>
                            <?php endif; ?>
                            <div class="filme-info">
                                <h4><?php echo htmlspecialchars($movie['title']); ?></h4>
                                <p class="filme-data">Lançamento: <?php echo $movie['release_date']; ?></p>
                                <p class="filme-voto">Nota: <?php echo $movie['vote_average']; ?>/10</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center;">Nenhum filme encontrado.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
>>>>>>> 7d04a187b15dd1aa61baef81c2afb3f029245396

<footer>
    <p>&copy; 2025 Unifilmes. Todos os direitos reservados.</p>
</footer>
<script src="../scripts/dropdown.js"></script>
</body>
</html>