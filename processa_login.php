<?php
session_start();
require 'conexao.php';

// Aceita apenas POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

// Recebe dados
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($cpf) || empty($senha)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
    exit;
}

try {
    $sql = "SELECT * FROM usuario WHERE cpf = :cpf LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($senha, $usuario['senha'])) {
            // Salva dados na sessão
            $_SESSION['usuario_cpf']   = $usuario['cpf'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_tipo']  = $usuario['tipo']; // ESSENCIAL PARA ADMIN

            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'tipo' => $usuario['tipo']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Senha incorreta']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
