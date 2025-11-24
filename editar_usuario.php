<?php
session_start();
require 'conexao.php';

// apenas admin pode acessar
if (empty($_SESSION['usuario_cpf']) || $_SESSION['usuario_tipo'] !== 'admin') {
    die("Acesso negado.");
}

// pega CPF pela URL
$cpf = filter_input(INPUT_GET, 'cpf', FILTER_SANITIZE_STRING);
if (!$cpf) {
    die("CPF inválido.");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuário não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao buscar usuário: " . $e->getMessage());
}

// define tipo padrão caso esteja vazio
$tipo = $usuario['tipo'] ?: 'usuario';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Editar Usuário</title>
    <link rel="shortcut icon" href="img-site/logotipo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="max-w-xl mx-auto mt-10 bg-white shadow rounded p-6">

        <h2 class="text-2xl font-semibold mb-5">Editar Usuário</h2>

        <form action="salvar_edicao_usuario.php" method="post">

            <input type="hidden" name="cpf" value="<?= htmlspecialchars($usuario['cpf']) ?>">

            <label class="block mb-2 font-semibold">Nome</label>
            <input type="text" name="nome" required
                value="<?= htmlspecialchars($usuario['nome']) ?>"
                class="w-full border p-2 rounded mb-4">

            <label class="block mb-2 font-semibold">Email</label>
            <input type="email" name="email" required
                value="<?= htmlspecialchars($usuario['email']) ?>"
                class="w-full border p-2 rounded mb-4">

            <label class="block mb-2 font-semibold">Telefone</label>
            <input type="text" name="telefone"
                value="<?= htmlspecialchars($usuario['telefone']) ?>"
                class="w-full border p-2 rounded mb-4">

            <label class="block mb-2 font-semibold">CEP</label>
            <input type="text" name="cep"
                value="<?= htmlspecialchars($usuario['cep']) ?>"
                class="w-full border p-2 rounded mb-4">

            <label class="block mb-2 font-semibold">Data de Nascimento</label>
            <input type="date" name="data_nasc"
                value="<?= htmlspecialchars($usuario['data_nasc']) ?>"
                class="w-full border p-2 rounded mb-4">

            <label class="block mb-2 font-semibold">Tipo de Usuário</label>
            <select name="tipo" class="w-full border p-2 rounded mb-4">
                <option value="usuario" <?= $tipo === 'usuario' ? 'selected' : '' ?>>Usuário comum</option>
                <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>

            <label class="block mb-2 font-semibold">Senha (deixe vazio para não alterar)</label>
            <input type="password" name="senha"
                class="w-full border p-2 rounded mb-6">

            <button type="submit"
                class="w-full bg-violet-700 hover:bg-violet-800 text-white font-semibold py-2 rounded">
                Salvar Alterações
            </button>

        </form>

        <a href="usuario_admin.php"
            class="block mt-4 text-center text-gray-600 hover:underline">
            Voltar
        </a>

    </div>

</body>

</html>