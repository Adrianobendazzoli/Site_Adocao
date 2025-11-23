<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/usuarios_admin.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; performs SELECT queries (reads data); includes other PHP files (layout or helpers); fetches DB results into arrays; styling (CSS) present

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
session_start();
require 'conexao.php';

// apenas admin
if (empty($_SESSION['usuario_cpf']) || $_SESSION['usuario_tipo'] !== 'admin') {
    die("Acesso negado.");
}

try {
    $stmt = $pdo->query("SELECT cpf, nome, email, tipo FROM usuario ORDER BY nome ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Todos Usuários - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        padding-top: 5%;
    }
</style>
</head>
<?php include 'pedaco.php'; ?>
<body class="bg-gray-50 text-gray-800">


<div class="max-w-6xl mx-auto mt-12 p-4">
    <h2 class="text-2xl font-semibold mb-6">Todos os Usuários</h2>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($u['nome']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($u['tipo']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="editar_usuario.php?cpf=<?= urlencode($u['cpf']) ?>"
                               class="px-3 py-1 text-sm bg-violet-600 text-white rounded hover:bg-violet-700">
                               Editar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($usuarios)): ?>
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum usuário encontrado</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
