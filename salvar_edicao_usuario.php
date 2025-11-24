<?php
session_start();
require 'conexao.php';

// apenas admin
if (empty($_SESSION['usuario_cpf']) || $_SESSION['usuario_tipo'] !== 'admin') {
    die("Acesso negado.");
}

$cpf = $_POST['cpf'] ?? null;
$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$telefone = $_POST['telefone'] ?? null;
$cep = $_POST['cep'] ?? null;
$data_nasc = $_POST['data_nasc'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$senha = $_POST['senha'] ?? null;

if (!$cpf || !$nome || !$email) {
    die("Dados inválidos.");
}

try {
    if (!empty($senha)) {
        // atualiza com senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET nome=?, email=?, telefone=?, cep=?, data_nasc=?, tipo=?, senha=? WHERE cpf=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $telefone, $cep, $data_nasc, $tipo, $hash, $cpf]);
    } else {
        // atualiza sem senha
        $sql = "UPDATE usuario SET nome=?, email=?, telefone=?, cep=?, data_nasc=?, tipo=? WHERE cpf=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $telefone, $cep, $data_nasc, $tipo, $cpf]);
    }

    header("Location: usuarios_admin.php?ok=1");
    exit;
} catch (PDOException $e) {
    die("Erro ao atualizar usuário: " . $e->getMessage());
}
