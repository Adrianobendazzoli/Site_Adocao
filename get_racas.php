<?php
require 'conexao.php'; // contém $pdo

header('Content-Type: application/json');

if (!isset($_GET['id_tipo']) || !is_numeric($_GET['id_tipo'])) {
    echo json_encode([]);
    exit;
}

$id_tipo = intval($_GET['id_tipo']);

try {
    $stmt = $pdo->prepare("SELECT id_raca, descricao FROM raca WHERE id_tipo = ?");
    $stmt->execute([$id_tipo]);
    $racas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($racas, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro na query: ' . $e->getMessage()]);
}
