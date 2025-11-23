<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/salvar_edicao_perfil.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; perpares and executes SQL statements (parameterized); performs INSERT/UPDATE/DELETE (writes data); includes other PHP files (layout or helpers)

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
session_start();
require 'conexao.php';

// só permite acesso de usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

// captura dados do POST
$cpf = preg_replace('/\D/', '', $_SESSION['usuario_cpf']);
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$cep = $_POST['cep'] ?? '';
$data_nasc = $_POST['data_nasc'] ?? '';
$senha = $_POST['senha'] ?? '';
$conf_senha = $_POST['conf_senha'] ?? '';

// validações básicas
if (empty($nome)) {
    die("O campo nome é obrigatório.");
}

if (!empty($senha) && $senha !== $conf_senha) {
    die("As senhas não conferem.");
}

// montar query dinâmica para senha
$fields = "nome = :nome, email = :email, telefone = :telefone, cep = :cep, data_nasc = :data_nasc";
$params = [
    ':nome' => $nome,
    ':email' => $email,
    ':telefone' => $telefone,
    ':cep' => $cep,
    ':data_nasc' => $data_nasc ?: null,
    ':cpf' => $cpf
];

if (!empty($senha)) {
    $fields .= ", senha = :senha";
    $params[':senha'] = password_hash($senha, PASSWORD_DEFAULT);
}

try {
    $stmt = $pdo->prepare("UPDATE usuario SET $fields WHERE cpf = :cpf");
    $stmt->execute($params);

    // atualizar nome na sessão
    $_SESSION['usuario_nome'] = $nome;

    // redireciona para perfil
    header('Location: perfil.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar perfil: " . $e->getMessage());
}
?>
