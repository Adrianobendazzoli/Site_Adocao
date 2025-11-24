<?php
session_start();
require 'conexao.php';

// exige usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

// captura dados do formulário
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome'] ?? '');
$id_tipo = $_POST['id_tipo'] ?? null;
$id_raca = $_POST['id_raca'] ?? null;
$peso = $_POST['peso'] ?? null;
$descricao = trim($_POST['descricao'] ?? '');

if (!$id || empty($nome)) {
    die("ID do pet ou nome inválido.");
}

$usuario_cpf = preg_replace('/\D/', '', $_SESSION['usuario_cpf']);
$isAdmin = !empty($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';

// se admin, permite alterar dono
$novo_cpf = $isAdmin && !empty($_POST['usuario_cpf']) ? preg_replace('/\D/', '', $_POST['usuario_cpf']) : $usuario_cpf;

try {
    // prepara query de atualização
    $sql = "UPDATE pets SET 
                nome = :nome,
                id_tipo = :id_tipo,
                id_raca = :id_raca,
                peso = :peso,
                descricao = :descricao,
                usuario_cpf = :novo_cpf
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':id_tipo' => $id_tipo ?: null,
        ':id_raca' => $id_raca ?: null,
        ':peso' => $peso ?: null,
        ':descricao' => $descricao,
        ':novo_cpf' => $novo_cpf,
        ':id' => $id
    ]);

    // se o usuário enviou uma nova foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['foto']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $filePath)) {
            $stmtPhoto = $pdo->prepare("INSERT INTO pet_photos (pet_id, photo_path) VALUES (:id, :path)");
            $stmtPhoto->execute([':id' => $id, ':path' => $filePath]);
        }
    }

    header('Location: pets.php?status=updated');
    exit;
} catch (PDOException $e) {
    die("Erro ao atualizar o pet: " . $e->getMessage());
}
