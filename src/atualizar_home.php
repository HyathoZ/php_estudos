<?php
// atualizar_home.php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['imdbID']) || !isset($data['ativo'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    exit;
}
$imdbID = $data['imdbID'];
$exibirNaHome = $data['ativo'] ? 1 : 0;
require_once 'conexao.php';
try {
    $stmt = $conn->prepare('UPDATE filmes SET exibirNaHome = ? WHERE imdbID = ?');
    $stmt->bind_param('is', $exibirNaHome, $imdbID);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhuma linha alterada.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
