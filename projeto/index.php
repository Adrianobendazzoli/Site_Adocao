<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/index.php
Summary (auto-generated):
PHP file; uses PDO for database access; performs SELECT queries (reads data); includes other PHP files (layout or helpers); fetches DB results into arrays; contains modal/iframe UI logic; styling (CSS) present; Contains JavaScript (DOM interactions)

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
require 'conexao.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_adocao", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta pets com tipo, raça e fotos
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
        .titulo {
            font-size: 50px;
            text-align: center;
            font-weight: bold;
            padding-top: 10%;
            padding-bottom: 10%;
        }

        main {
            padding-top: 5%;
        }

        .botao_cadastrar {
            padding: 10%;
        }
    </style>
</head>


<header>
    <nav class="bg-white w-full flex items-center justify-between px-6 py-3 shadow-md fixed top-0 z-50">
        <!-- Logo -->
        <a href="home.php" class="flex items-center">
            <img src="img-site/logotipo.png" alt="Logo" class="h-16">
        </a>

        <!-- Hamburger Menu para mobile -->
        <div class="md:hidden">
            <button id="menuButton" class="text-gray-700 focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Menu Links -->
        <div id="menu" class="hidden flex-col md:flex md:flex-row md:items-center md:space-x-6 absolute md:static top-full left-0 w-full md:w-auto bg-white md:bg-transparent shadow md:shadow-none">
            <!-- Botões de Login/Cadastro -->
            <a href="login.html" class="block px-4 py-2 text-white bg-purple-600 rounded hover:bg-purple-700 font-medium text-center">Entrar</a>
            <a href="registro.html" class="block px-4 py-2 text-purple-600 border border-purple-600 rounded hover:bg-purple-50 font-medium text-center">Cadastrar</a>
        </div>
    </nav>
</header>



<body class="antialiased bg-white text-slate-800">

    <!-- HERO -->
    <main class="max-w-7xl mx-auto px-6">
        <div class="card_inicio grid grid-cols-1 lg:grid-cols-2 gap-10 items-center pt-20">

            <!-- LEFT -->
            <section>
                <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-4">
                    Divulgue pets<br />para adoção
                </h1>

                <p class="text-slate-600 mb-6 max-w-xl">
                    Utilize ferramentas de divulgação de pets com eficácia comprovada em todo o Brasil. Crie seu anúncio gratuito agora mesmo para ter acesso ao Painel de Adoção.
                </p>

                <div class="flex gap-4 mb-6">
                    <a href="cadastro_pet.php"
                        class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring">
                        Divulgar pet
                    </a>

                    <a href="home.php"
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

            <!-- RIGHT HERO IMAGE -->
            <aside class="flex items-center justify-center">
                <div class="relative w-full max-w-lg">
                    <img src="https://www.viumeupet.com.br/images/website/adoption-house-bg.svg" alt="Fundo da casa pet" class="w-full h-full object-cover" />
                    <img src="img-site/fotinha.webp" alt="pets para adoção" class="absolute inset-0 w-full h-full object-cover" />
                </div>
            </aside>
        </div>

        <!-- DEPOIMENTOS -->
        <section class="py-12">
            <h1 class="titulo ">Depoimentos sobre o Adocão</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">

                <!-- Depoimento 1 -->
                <div class="w-full space-y-4 rounded-md border border-gray-200 bg-white p-6 text-gray-500 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <p>“O Adocão mudou a forma como encontrei meu pet ideal. Super fácil de usar e muito confiável!”</p>
                    <div class="flex items-center gap-3 pt-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="img-site/547737_vinicius_junior_20250923225603.png" />
                        <div>
                            <p class="font-medium text-gray-800">Vinicius Junior</p>
                        </div>
                    </div>
                </div>

                <!-- Depoimento 2 -->
                <div class="w-full space-y-4 rounded-md border border-gray-200 bg-white p-6 text-gray-500 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <p>“A experiência foi incrível! recomendo para todos, O site é intuitivo e encontrei exatamente o que procurava.”</p>
                    <div class="flex items-center gap-3 pt-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="img-site/fcd24742f_virginia-fonseca-1.jpg" />
                        <div>
                            <p class="font-medium text-gray-800">Virginia</p>
                        </div>
                    </div>
                </div>

                <!-- Depoimento 3 -->
                <div class="w-full space-y-4 rounded-md border border-gray-200 bg-white p-6 text-gray-500 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
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

        <!-- PETS DISPONÍVEIS -->
        <section id="pets_adotar" class="mt-16">
            <h1 class="titulo">Pets Disponíveis para Adoção</h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($pets as $pet):
                    $photoPaths = explode(',', $pet['photos'] ?? '');
                    $firstPhoto = !empty($photoPaths[0]) ? $photoPaths[0] : 'img/cachorro.png';
                ?>
                    <div class="group bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition">
                        <a href="adotar.php?id=<?= $pet['id']; ?>" onclick="openModal(this.href); return false;">
                            <img src="<?= htmlspecialchars($firstPhoto); ?>" alt="Foto do pet"
                                class="w-full aspect-square object-cover">
                        </a>
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
                    class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring">
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
        const menuButton = document.getElementById('menuButton');
        const menu = document.getElementById('menu');
        menuButton.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Modal
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
    </script>

</body>
<?php include 'footer.php'; ?>

</html>