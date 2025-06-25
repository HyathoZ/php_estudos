<?php
// salvar_filme.php
header('Content-Type: application/json');

// Permite apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Lê o corpo JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'JSON inválido.']);
    exit;
}

// Campos esperados
$campos = [
    'imdbID', 'Title', 'Year', 'Poster', 'Plot', 'Genre', 'Actors', 'Director', 'Language', 'Runtime', 'Type', 'exibirNaHome'
];
$filme = [];
foreach ($campos as $campo) {
    $filme[$campo] = isset($data[$campo]) ? trim($data[$campo]) : null;
}
$filme['exibirNaHome'] = isset($data['exibirNaHome']) && ($data['exibirNaHome'] === true || $data['exibirNaHome'] === 'true' || $data['exibirNaHome'] == 1) ? 1 : 0;

// Validação mínima
if (empty($filme['imdbID']) || empty($filme['Title'])) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes.']);
    exit;
}

// Conexão segura com PDO
require_once 'conexao.php';
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cadastro_filmes;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()]);
    exit;
}

// Cria tabela se não existir
$sqlTabela = "CREATE TABLE IF NOT EXISTS filmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imdbID VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(255),
    year VARCHAR(10),
    poster TEXT,
    plot TEXT,
    genre VARCHAR(255),
    actors TEXT,
    director VARCHAR(255),
    language VARCHAR(100),
    runtime VARCHAR(50),
    type VARCHAR(50),
    exibirNaHome BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$pdo->exec($sqlTabela);

// Previne duplicatas (imdbID)
$sql = "INSERT INTO filmes (imdbID, title, year, poster, plot, genre, actors, director, language, runtime, type, exibirNaHome)
        VALUES (:imdbID, :title, :year, :poster, :plot, :genre, :actors, :director, :language, :runtime, :type, :exibirNaHome)
        ON DUPLICATE KEY UPDATE
            title=VALUES(title), year=VALUES(year), poster=VALUES(poster), plot=VALUES(plot), genre=VALUES(genre),
            actors=VALUES(actors), director=VALUES(director), language=VALUES(language), runtime=VALUES(runtime),
            type=VALUES(type), exibirNaHome=VALUES(exibirNaHome)";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([
        ':imdbID' => $filme['imdbID'],
        ':title' => $filme['Title'],
        ':year' => $filme['Year'],
        ':poster' => $filme['Poster'],
        ':plot' => $filme['Plot'],
        ':genre' => $filme['Genre'],
        ':actors' => $filme['Actors'],
        ':director' => $filme['Director'],
        ':language' => $filme['Language'],
        ':runtime' => $filme['Runtime'],
        ':type' => $filme['Type'],
        ':exibirNaHome' => $filme['exibirNaHome']
    ]);
    echo json_encode(['success' => true, 'message' => 'Filme cadastrado com sucesso!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar filme: ' . $e->getMessage()]);
}
