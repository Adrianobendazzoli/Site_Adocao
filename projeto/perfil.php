<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/perfil.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; perpares and executes SQL statements (parameterized); performs SELECT queries (reads data); includes other PHP files (layout or helpers); fetches DB results into arrays; styling (CSS) present

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
session_start();

// só permite acesso de usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

require 'conexao.php';

// busca dados do usuário pelo CPF da sessão
$cpf = preg_replace('/\D/', '', $_SESSION['usuario_cpf']);

try {
    $stmt = $pdo->prepare("SELECT nome, email, data_nasc, cpf, telefone, cep FROM usuario WHERE cpf = :cpf LIMIT 1");
    $stmt->execute([':cpf' => $cpf]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = false;
}

// tenta carregar pets do usuário (se a coluna usuario_cpf existir)
$pets = [];
try {
    $stmt = $pdo->prepare("SELECT id, nome, tipo, idade FROM pets WHERE usuario_cpf = :cpf");
    $stmt->execute([':cpf' => $cpf]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pets = [];
}

function formatCpf($cpf) {
    $c = preg_replace('/\D/', '', $cpf);
    if (strlen($c) === 11) {
        return substr($c,0,3).'.'.substr($c,3,3).'.'.substr($c,6,3).'-'.substr($c,9,2);
    }
    return $cpf;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Perfil - <?= htmlspecialchars($user['nome'] ?? $_SESSION['usuario_nome']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            padding-top: 5%;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <?php include 'pedaco.php'; ?>

    <?php if (!$user): ?>
        <div class="max-w-4xl mx-auto mt-12 p-6 bg-yellow-50 border border-yellow-200 rounded">
            Não foi possível carregar os dados do usuário.
        </div>
    <?php else: ?>
        <section class="flex justify-center mt-12">
            <div class="bg-white overflow-hidden shadow rounded-lg border w-full max-w-lg">
                <div class="px-4 py-5 sm:px-6 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        User Profile
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Informações do seu perfil
                    </p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">

                        <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= htmlspecialchars($user['nome']) ?>
                            </dd>
                        </div>

                        <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= htmlspecialchars($user['email'] ?? '-') ?>
                            </dd>
                        </div>

                        <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= htmlspecialchars($user['telefone'] ?? '-') ?>
                            </dd>
                        </div>

                        <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">CEP</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= htmlspecialchars($user['cep'] ?? '-') ?>
                            </dd>
                        </div>

                        <div class="py-3 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Data de nascimento</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <?= !empty($user['data_nasc']) ? htmlspecialchars($user['data_nasc']) : '-' ?>
                            </dd>
                        </div>

                    </dl>
                </div>

                <!-- Botões centralizados dentro do card -->
                <div class="flex justify-center gap-4 px-4 py-5">
                    <a class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring"
                       href="editar_perfil.php">
                        Editar Perfil
                    </a>
                    <a class="px-6 py-2 min-w-[120px] text-center text-violet-600 border border-violet-600 rounded hover:bg-violet-600 hover:text-white active:bg-indigo-500 focus:outline-none focus:ring"
                       href="logout.php">
                        Sair
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>
</body>
</html>
