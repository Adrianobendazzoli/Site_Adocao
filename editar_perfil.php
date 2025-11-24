<?php
session_start();
require 'conexao.php';

// só permite acesso de usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

$cpf = preg_replace('/\D/', '', $_SESSION['usuario_cpf']);

try {
    $stmt = $pdo->prepare("SELECT nome, email, telefone, cep, data_nasc FROM usuario WHERE cpf = :cpf LIMIT 1");
    $stmt->execute([':cpf' => $cpf]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Usuário não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Editar Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900">
    <?php include 'pedaco.php'; ?>

    <main class="max-w-3xl mx-auto py-12 px-4">
        <h1 class="text-2xl font-bold mb-6">Editar Perfil</h1>

        <form action="salvar_edicao_perfil.php" method="POST" class="bg-white p-6 rounded-xl shadow space-y-4">

            <!-- Nome -->
            <div>
                <label class="block text-gray-700 mb-1" for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($user['nome'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-700 mb-1" for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Telefone -->
            <div>
                <label class="block text-gray-700 mb-1" for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($user['telefone'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- CEP -->
            <div>
                <label class="block text-gray-700 mb-1" for="cep">CEP</label>
                <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($user['cep'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Data de nascimento -->
            <div>
                <label class="block text-gray-700 mb-1" for="data_nasc">Data de nascimento</label>
                <input type="date" id="data_nasc" name="data_nasc" value="<?= htmlspecialchars($user['data_nasc'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Nova Senha -->
            <div>
                <label class="block text-gray-700 mb-1" for="senha">Nova senha</label>
                <input type="password" id="senha" name="senha" placeholder="Deixe em branco para não alterar"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Confirmar Senha -->
            <div>
                <label class="block text-gray-700 mb-1" for="conf_senha">Confirmar nova senha</label>
                <input type="password" id="conf_senha" name="conf_senha" placeholder="Repita a nova senha"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Botões -->
            <div class="flex gap-4">
                <!-- Botão Salvar (envia o formulário) -->
                <button type="submit"
                    class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring">
                    Salvar
                </button>


                <a href="perfil.php"
                    class="px-6 py-2 min-w-[120px] text-center text-violet-600 border border-violet-600 rounded hover:bg-violet-600 hover:text-white active:bg-indigo-500 focus:outline-none focus:ring">
                    Cancelar
                </a>
            </div>
        </form>
    </main>
</body>

</html>