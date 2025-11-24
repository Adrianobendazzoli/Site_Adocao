<?php
// Inicia a sessão para gerenciar autenticação e dados do usuário
session_start();

// Bloqueio de acesso: se não houver usuário logado, redireciona para a página de login
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

include 'pedaco.php';

// Conexão com o banco
require 'conexao.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_adocao", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT 
                p.*,
                t.descricao AS tipo,
                r.descricao AS raca,
                GROUP_CONCAT(pp.photo_path) AS photos
            FROM pets p
            LEFT JOIN tipo t ON p.id_tipo = t.id_tipo
            LEFT JOIN raca r ON p.id_raca = r.id_raca
            LEFT JOIN pet_photos pp ON p.id = pp.pet_id
            GROUP BY p.id";

    $stmt = $pdo->query($sql);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $favStmt = $pdo->prepare("SELECT pet_id FROM favoritos WHERE usuario_cpf = ?");
    $favStmt->execute([$_SESSION['usuario_cpf']]);
    $favoritos = $favStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Viu Meu Pet - Landing</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .titulo {
            font-size: 50px;
            text-align: center;
            font-weight: bold;
            padding-top: 10%;
            padding-bottom: 10%;
        }

        body {
            background: url('img-site/fundo.png') no-repeat center center fixed;
        }

        main {
            padding-top: 5%;
        }

        .botao_cadastrar {
            padding: 10%;
        }

        button,
        a {
            transition: all 0.3s ease;
        }

        button:active,
        a:active {
            transform: scale(0.95);
        }

        .depoimentos {
            background-color: transparent;
        }
    </style>
</head>

<body class="antialiased bg-white text-slate-800">

    <main class="max-w-7xl mx-auto px-6">
        <div class="card_inicio grid grid-cols-1 lg:grid-cols-2 gap-10 items-center pt-20">

            <section>
                <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-4">
                    Divulgue pets<br />para adoção
                </h1>

                <p class="text-slate-600 mb-6 max-w-xl text-white">
                    Utilize ferramentas de divulgação de pets com eficácia comprovada em todo o Brasil. Crie seu anúncio gratuito agora mesmo para ter acesso ao Painel de Adoção.
                </p>

                <div class="flex gap-4 mb-6">
                    <a href="cadastro_pet.php"
                        class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring">
                        Divulgar pet
                    </a>

                    <a href="#pets_adotar"
                        class="px-6 py-2 min-w-[120px] text-center text-violet-600 border border-violet-600 rounded hover:bg-violet-600 hover:text-white active:bg-indigo-500 focus:outline-none focus:ring">
                        Adotar um pet
                    </a>
                </div>

                <div class="mb-6">
                    <a href="faq.php"
                        class="px-6 py-2 min-w-[120px] text-left text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring">
                        Como o adocão funciona?
                    </a>
                </div>
            </section>

            <aside class="flex items-center justify-center">
                <div class="relative w-full max-w-lg">
                    <img src="https://www.viumeupet.com.br/images/website/adoption-house-bg.svg" class="w-full h-full object-cover" />
                    <img src="./img-site/fotinha.webp" class="absolute inset-0 w-full h-full object-cover" />
                </div>
            </aside>
        </div>

        <section class="py-12 bg-gray-50 depoimentos">
            <h1 class="titulo text-3xl font-bold text-center mb-8 text-gray-800">Depoimentos sobre o Adocão</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">

                <div class="w-full space-y-4 rounded-md border border-gray-200 bg-white p-6 text-gray-500 hover:-translate-y-1 hover:shadow-lg transition">
                    <p>“O Adocão mudou a forma como encontrei meu pet ideal. Super fácil de usar e muito confiável!”</p>
                    <div class="flex items-center gap-3 pt-3">
                        <img class="h-10 w-10 rounded-full object-cover"
                            src="img-site/547737_vinicius_junior_20250923225603.png" />
                        <div>
                            <p class="font-medium text-gray-800">Vinicius Junior</p>
                        </div>
                    </div>
                </div>

                <div class="w-full space-y-4 rounded-md border border-gray-200 bg-white p-6 text-gray-500 hover:-translate-y-1 hover:shadow-lg transition">
                    <p>“A experiência foi incrível! recomendo para todos, O site é intuitivo e encontrei exatamente o que procurava.”</p>
                    <div class="flex items-center gap-3 pt-3">
                        <img class="h-10 w-10 rounded-full object-cover"
                            src="img-site/fcd24742f_virginia-fonseca-1.jpg">
                        <div>
                            <p class="font-medium text-gray-800">Virginia</p>
                        </div>
                    </div>
                </div>

                <div class="w-full space-y-4 rounded-md border border-gray-200 bg-white p-6 text-gray-500 hover:-translate-y-1 hover:shadow-lg transition">
                    <p>“Recomendo o Adocão a todos! A plataforma é clara, rápida e confiável, ajudando muitos pets a encontrarem lares.”</p>
                    <div class="flex items-center gap-3 pt-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="img-site/images.webp" />
                        <div>
                            <p class="font-medium text-gray-800">Zé felipe</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section id="pets_adotar" class="mt-16">
            <h1 class="titulo">Pets Disponíveis para Adoção</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($pets as $pet):
                    $photoPaths = explode(',', $pet['photos'] ?? '');
                    $firstPhoto = !empty($photoPaths[0]) ? $photoPaths[0] : 'img/cachorro.png';
                    $isFavorito = in_array($pet['id'], $favoritos);
                ?>
                    <div class="relative group bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                        <a href="adotar.php?id=<?= $pet['id']; ?>" onclick="openModal(this.href); return false;">
                            <img src="<?= htmlspecialchars($firstPhoto); ?>" class="w-full aspect-square object-cover">
                        </a>

                        <button class="favorito-btn absolute top-2 right-2 text-2xl z-10">
                            <?= $isFavorito ? '❤️' : '🤍'; ?>
                        </button>

                        <div class="p-4">
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($pet['nome']); ?></h3>
                            <p class="text-gray-700">
                                <?= htmlspecialchars($pet['tipo'] ?? 'Tipo não informado'); ?> -
                                <?= htmlspecialchars($pet['raca'] ?? 'Raça não informada'); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="botao_cadastrar mt-6 text-center">
                <a href="cadastro_pet.php"
                    class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded hover:bg-transparent hover:text-violet-600">
                    Cadastrar Novo Pet
                </a>
            </div>
        </section>
    </main>

    <!-- MODAL -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="relative p-5 border w-11/12 max-w-6xl h-[90vh] shadow-lg rounded-xl bg-white">
            <div class="h-full">

                <button id="closeModal" class="absolute top-0 right-0 mt-4 mr-4 text-gray-400 hover:text-gray-600 z-60">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <iframe id="modalIframe" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openModal(url) {
            document.getElementById('modalIframe').src = url;
            document.getElementById('modal').classList.remove('hidden');
        }

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modalIframe').src = '';
        });

        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.getElementById('modalIframe').src = '';
            }
        });

        // ⭐ NOVO — Função chamada de dentro do iframe (adotar.php)
        function closeModalFromInside() {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modalIframe').src = '';
        }

        document.querySelectorAll('.favorito-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const petId = btn.closest('.group, .relative').querySelector('a').href.split('id=')[1];
                const isFavorito = btn.textContent === '❤️';
                const action = isFavorito ? 'remove' : 'add';

                fetch('favoritar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `pet_id=${petId}&action=${action}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            btn.textContent = isFavorito ? '🤍' : '❤️';
                        } else {
                            alert('Erro: ' + data.message);
                        }
                    });
            });
        });
    </script>

</body>

<?php include 'footer.php'; ?>

</html>