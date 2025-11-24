<?php
session_start();
require 'conexao.php';

// exige usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: home.php?status=invalid_id');
    exit;
}

$usuario_cpf = preg_replace('/\D/', '', $_SESSION['usuario_cpf']);
$isAdmin = !empty($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

try {
    // busca pet para verificar propriedade e obter caminhos de fotos
    $stmt = $pdo->prepare("SELECT id, usuario_cpf, foto FROM pets WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pet) {
        header('Location: home.php?status=not_found');
        exit;
    }

    // se não for admin e não for dono, bloqueia
    if (!$isAdmin && $pet['usuario_cpf'] !== $usuario_cpf) {
        header('Location: home.php?status=forbidden');
        exit;
    }

    // obtém fotos relacionadas
    $stmt = $pdo->prepare("SELECT photo_path FROM pet_photos WHERE pet_id = :id");
    $stmt->execute([':id' => $id]);
    $photos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $baseDir = realpath(__DIR__) . DIRECTORY_SEPARATOR;

    // transação: remove registros e arquivos
    $pdo->beginTransaction();

    // deleta registros de fotos
    $stmt = $pdo->prepare("DELETE FROM pet_photos WHERE pet_id = :id");
    $stmt->execute([':id' => $id]);

    // deleta registro do pet
    $stmt = $pdo->prepare("DELETE FROM pets WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $pdo->commit();

    // remove arquivos do sistema após commit
    $allFiles = $photos;
    if (!empty($pet['foto'])) {
        $allFiles[] = $pet['foto'];
    }
    foreach ($allFiles as $rel) {
        if (empty($rel)) continue;
        $path = $rel;
        if (!preg_match('#^(/|[A-Za-z]:\\\\)#', $path)) {
            $path = __DIR__ . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        }
        $real = @realpath($path);
        if ($real && strpos($real, $baseDir) === 0 && is_file($real)) {
            @unlink($real);
        }
    }

    header('Location: home.php?status=deleted');
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('excluir_pet.php error: ' . $e->getMessage());
    header('Location: home.php?status=error');
    exit;
}
