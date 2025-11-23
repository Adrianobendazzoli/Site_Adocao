<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/processa_login.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; perpares and executes SQL statements (parameterized); performs SELECT queries (reads data); includes other PHP files (layout or helpers); fetches DB results into arrays

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
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