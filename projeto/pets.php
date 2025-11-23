<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/pets.php
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

// exige usuário logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

$usuario_cpf = preg_replace('/\D/', '', $_SESSION['usuario_cpf']);

try {
    // Consulta pets com tipo, raça e primeira foto
    $sql = "SELECT 
                p.*,
                t.descricao AS tipo,
                r.descricao AS raca,
                MIN(pp.photo_path) AS first_photo
            FROM pets p
            LEFT JOIN tipo t ON p.id_tipo = t.id_tipo
            LEFT JOIN raca r ON p.id_raca = r.id_raca
            LEFT JOIN pet_photos pp ON p.id = pp.pet_id
            WHERE p.usuario_cpf = :cpf
            GROUP BY p.id, t.descricao, r.descricao
            ORDER BY COALESCE(p.data_cadastro, NOW()) DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':cpf' => $usuario_cpf]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('meus_pets.php error: ' . $e->getMessage());
    $pets = [];
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Meus Pets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <?php include 'pedaco.php'; ?>

    <main class="max-w-6xl mx-auto py-12 px-4">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Meus Pets</h1>
            <a href="cadastro_pet.php" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Cadastrar novo pet</a>
        </div>

        <?php if (empty($pets)): ?>
            <div class="p-6 bg-white rounded shadow text-center">
                <p class="text-gray-700 mb-4">Você ainda não cadastrou nenhum pet.</p>
                <a href="cadastro_pet.php" class="text-purple-600 underline">Cadastre um agora</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($pets as $pet): ?>
                    <?php
                    $firstPhoto = $pet['first_photo'] ?? 'img/cachorro.png';
                    ?>
                    <div class="group bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition w-full max-w-md mx-auto">
                        <a href="ver_pet.php?id=<?= urlencode($pet['id']); ?>">
                            <img src="<?= htmlspecialchars($firstPhoto); ?>" alt="Foto do pet" class="w-full aspect-square object-cover">
                        </a>

                        <div class="p-4">
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($pet['nome'] ?? 'Nome não informado'); ?></h3>
                            <p class="text-gray-700"><?= htmlspecialchars($pet['tipo'] ?? 'Tipo não informado'); ?> - <?= htmlspecialchars($pet['raca'] ?? 'Raça não informada'); ?></p>

                            <!-- Botões menores -->
                            <div class="mt-4 flex gap-2 justify-between">
                                <a href="ver_pet.php?id=<?= urlencode($pet['id']); ?>"
                                   class="px-3 py-1 min-w-[80px] text-center text-violet-600 border border-violet-600 rounded hover:bg-violet-600 hover:text-white active:bg-indigo-500 focus:outline-none focus:ring text-sm">
                                    Ver
                                </a>
                                <a href="editar_pet.php?id=<?= urlencode($pet['id']); ?>"
                                   class="px-3 py-1 min-w-[80px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring text-sm">
                                    Editar
                                </a>
                                <a href="excluir_pet.php?id=<?= urlencode($pet['id']); ?>"
                                   onclick="return confirm('Tem certeza que deseja excluir este pet?');"
                                   class="px-3 py-1 min-w-[80px] text-center text-white bg-red-600 border border-red-600 rounded active:text-red-500 hover:bg-transparent hover:text-red-600 focus:outline-none focus:ring text-sm">
                                    Excluir
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>
