<?php
session_start();
require 'conexao.php';

// exige usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

// captura ID do pet
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID do pet não fornecido ou inválido.");
}

try {
    // admins podem editar qualquer pet, usuários normais só o próprio
    if ($_SESSION['usuario_tipo'] === 'admin') {
        $stmt = $pdo->prepare("SELECT * FROM pets WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM pets WHERE id = :id AND usuario_cpf = :cpf LIMIT 1");
        $stmt->execute([
            ':id' => $id,
            ':cpf' => preg_replace('/\D/', '', $_SESSION['usuario_cpf'])
        ]);
    }

    $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pet) {
        die("Pet não encontrado.");
    }

    // buscar primeira foto
    $stmtPhoto = $pdo->prepare("SELECT photo_path FROM pet_photos WHERE pet_id = :id ORDER BY id LIMIT 1");
    $stmtPhoto->execute([':id' => $id]);
    $firstPhoto = $stmtPhoto->fetchColumn() ?: 'img/cachorro.png';

    // buscar tipos e raças
    $tipos = $pdo->query("SELECT * FROM tipo ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);
    $racas = $pdo->query("SELECT * FROM raca ORDER BY descricao")->fetchAll(PDO::FETCH_ASSOC);

    // admins podem selecionar dono do pet
    if ($_SESSION['usuario_tipo'] === 'admin') {
        $usuarios = $pdo->query("SELECT cpf, nome FROM usuario ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Editar Pet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900">


    <main class="max-w-3xl mx-auto py-12 px-4">
        <h1 class="text-2xl font-bold mb-6">Editar Pet</h1>

        <form action="salvar_edicao_pet.php" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 rounded-xl shadow space-y-4">

            <input type="hidden" name="id" value="<?= htmlspecialchars($pet['id']); ?>">

            <!-- Dono do Pet (apenas admin) -->
            <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                <div>
                    <label class="block text-gray-700 mb-1" for="usuario_cpf">Dono do Pet</label>
                    <select id="usuario_cpf" name="usuario_cpf"
                        class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= htmlspecialchars($u['cpf']); ?>"
                                <?= ($pet['usuario_cpf'] == $u['cpf']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($u['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Nome -->
            <div>
                <label class="block text-gray-700 mb-1" for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($pet['nome'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-gray-700 mb-1" for="id_tipo">Tipo</label>
                <select id="id_tipo" name="id_tipo"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Selecione o tipo</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= $tipo['id_tipo']; ?>" <?= ($pet['id_tipo'] == $tipo['id_tipo']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($tipo['descricao']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Raça -->
            <div>
                <label class="block text-gray-700 mb-1" for="id_raca">Raça</label>
                <select id="id_raca" name="id_raca"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Selecione a raça</option>
                    <?php foreach ($racas as $raca): ?>
                        <option value="<?= $raca['id_raca']; ?>" <?= ($pet['id_raca'] == $raca['id_raca']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($raca['descricao']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Peso -->
            <div>
                <label class="block text-gray-700 mb-1" for="peso">Peso (kg)</label>
                <input type="number" step="0.1" min="0" id="peso" name="peso" value="<?= htmlspecialchars($pet['peso'] ?? ''); ?>"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Descrição -->
            <div>
                <label class="block text-gray-700 mb-1" for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="4"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"><?= htmlspecialchars($pet['descricao'] ?? ''); ?></textarea>
            </div>

            <!-- Foto -->
            <div>
                <label class="block text-gray-700 mb-1">Foto atual</label>
                <img src="<?= htmlspecialchars($firstPhoto); ?>" alt="Foto do pet"
                    class="w-32 h-32 object-cover rounded-lg mb-2">
                <input type="file" name="foto" accept="image/*"
                    class="w-full border px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Botões -->
            <div class="flex gap-4">
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">
                    Salvar
                </button>
                <a href="meus_pets.php" class="px-4 py-2 border rounded hover:bg-gray-100 transition">Cancelar</a>
            </div>
        </form>
    </main>
</body>

</html>