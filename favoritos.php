<?php
session_start();
require 'conexao.php';
include 'pedaco.php';

if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

$usuario_cpf = $_SESSION['usuario_cpf'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_adocao", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar pets favoritos + tipo + raça + fotos
    $sql = "
        SELECT 
            p.id,
            p.nome,
            p.descricao,
            p.cor,
            p.tamanho,
            p.peso,
            p.castrado,
            p.detalhes_castracao,
            t.descricao AS tipo,
            r.descricao AS raca,
            GROUP_CONCAT(pp.photo_path) AS photos
        FROM favoritos f
        INNER JOIN pets p ON p.id = f.pet_id
        LEFT JOIN tipo t ON t.id_tipo = p.id_tipo
        LEFT JOIN raca r ON r.id_raca = p.id_raca
        LEFT JOIN pet_photos pp ON p.id = pp.pet_id
        WHERE f.usuario_cpf = ?
        GROUP BY p.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_cpf]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <style>
        body {
            padding-top: 5%;
        }

        .card_pets {
            padding-bottom: 5%;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="mx-auto max-w-7xl px-4 py-16">
        <h2 class="text-3xl font-bold mb-8">Meus Pets Favoritos</h2>

        <?php if (empty($pets)): ?>
            <p class="text-gray-700">Você ainda não favoritou nenhum pet.</p>
        <?php else: ?>

            <div class="card_pets grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($pets as $pet): ?>
                    <?php
                    $photoPaths = explode(',', $pet['photos'] ?? '');
                    $firstPhoto = $photoPaths[0] ?: 'img/cachorro.png';
                    ?>
                    <div class="relative group bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                        <a href="adotar.php?id=<?= urlencode($pet['id']); ?>" class="block abrir-modal">
                            <img src="<?= htmlspecialchars($firstPhoto); ?>" alt="Foto do pet"
                                class="w-full aspect-square object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold"><?= htmlspecialchars($pet['nome']); ?></h3>
                                <p class="text-gray-700">
                                    <?= htmlspecialchars($pet['tipo'] ?? 'Tipo não informado'); ?> -
                                    <?= htmlspecialchars($pet['raca'] ?? 'Raça não informada'); ?>
                                </p>
                            </div>
                        </a>

                        <button class="remover-btn absolute top-2 right-2 text-2xl z-10"
                            data-pet-id="<?= htmlspecialchars($pet['id']); ?>" aria-label="Remover dos favoritos">
                            ❤️
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="home.php"
                class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring">
                Voltar à lista de pets
            </a>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="relative p-5 border w-11/12 max-w-6xl h-[90vh] shadow-lg rounded-xl bg-white">
            <div class="h-full">
                <button id="closeModal"
                    class="absolute top-0 right-0 mt-4 mr-4 text-gray-400 hover:text-gray-600 z-60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <iframe id="modalIframe" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.abrir-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('modalIframe').src = this.href;
                document.getElementById('modal').classList.remove('hidden');
            });
        });

        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modalIframe').src = '';
        });

        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.getElementById('modalIframe').src = '';
            }
        });

        // Remover favorito
        document.querySelectorAll('.remover-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();

                const petId = btn.dataset.petId;

                fetch('favoritar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `pet_id=${petId}&action=remove`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            btn.closest('.group').remove();

                            if (document.querySelectorAll('.group').length === 0) {
                                document.querySelector('.mx-auto').insertAdjacentHTML(
                                    'beforeend',
                                    '<p class="text-center text-gray-700 mt-4">Você ainda não favoritou nenhum pet.</p>'
                                );
                            }
                        } else {
                            alert('Erro ao remover favorito!');
                        }
                    });
            });
        });
    </script>

</body>
<?php include 'footer.php'; ?>

</html>