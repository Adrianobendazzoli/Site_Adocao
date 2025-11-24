<?php
session_start();
require 'conexao.php'; // deve definir $pdo (PDO)

// Verifica se o usuário está logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID do pet não fornecido ou inválido.");
}

try {
    // Buscar dados do pet com tipo e raça + dono
    $sql = "SELECT 
                p.*,
                t.descricao AS tipo,
                r.descricao AS raca,
                u.telefone AS owner_tel,
                u.nome AS owner_nome
            FROM pets p
            LEFT JOIN tipo t ON p.id_tipo = t.id_tipo
            LEFT JOIN raca r ON p.id_raca = r.id_raca
            LEFT JOIN usuario u ON u.cpf = p.usuario_cpf
            WHERE p.id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pet) {
        die("Pet não encontrado.");
    }

    // Buscar fotos do pet
    $sqlPhotos = "SELECT photo_path FROM pet_photos WHERE pet_id = :id ORDER BY id";
    $stmtPhotos = $pdo->prepare($sqlPhotos);
    $stmtPhotos->execute([':id' => $id]);
    $photos = $stmtPhotos->fetchAll(PDO::FETCH_COLUMN);

    if (empty($photos)) {
        $photos = ['img/cachorro.png']; // imagem padrão
    }
    $totalSlides = count($photos);

    // Preparar link do WhatsApp
    $whatsappLink = '';

    if (!empty($pet['owner_tel'])) {
        $numero = preg_replace('/\D/', '', $pet['owner_tel']); // remove tudo que não for número

        // Garante código do Brasil se não tiver
        if (strlen($numero) == 11) {
            $numero = '55' . $numero;
        }

        $mensagem = "Olá {$pet['owner_nome']}, tenho interesse no pet {$pet['nome']} que vi no ViuMeuPet!";
        $whatsappLink = "https://wa.me/{$numero}?text=" . urlencode($mensagem);
    }

    $telefoneExibicao = '';

    if (!empty($pet['owner_tel'])) {
        $numero = preg_replace('/\D/', '', $pet['owner_tel']);

        if (strlen($numero) == 11) {
            $numero = '55' . $numero;
        }

        $telefoneExibicao = $pet['owner_tel'];

        $mensagem = "Olá {$pet['owner_nome']}, tenho interesse no pet {$pet['nome']} que vi no ViuMeuPet!";
        $whatsappLink = "https://wa.me/{$numero}?text=" . urlencode($mensagem);
    }
} catch (PDOException $e) {
    error_log('adotar.php error: ' . $e->getMessage());
    die("Erro ao carregar dados do pet.");
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Adotar - <?= htmlspecialchars($pet['nome']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="img-site/logotipo.png" type="image/x-icon">
</head>

<body class="bg-gray-50">
    <div class="bg-white">
        <div class="pt-6">
            <div class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:max-w-7xl lg:px-8 lg:pt-16 lg:pb-24">
                <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                    <!-- Nome do pet maior -->
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl mb-6">
                        <?= htmlspecialchars($pet['nome']) ?>
                    </h1>
                </div>

                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 mt-6">
                    <!-- Carrossel -->
                    <div class="relative">
                        <div class="carousel-container overflow-hidden rounded-lg">
                            <div class="carousel-wrapper flex transition-transform duration-300 ease-in-out" id="carousel">
                                <?php foreach ($photos as $index => $image): ?>
                                    <img src="<?= htmlspecialchars($image) ?>"
                                        alt="Foto do pet <?= $index + 1 ?>"
                                        class="carousel-slide w-full aspect-square object-cover flex-shrink-0" />
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Botões -->
                        <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 hover:bg-gray-200" id="prevBtn" type="button" aria-label="Anterior">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 hover:bg-gray-200" id="nextBtn" type="button" aria-label="Próximo">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <!-- Pontos -->
                        <div class="flex justify-center mt-4 space-x-2" id="dotsContainer">
                            <?php for ($i = 0; $i < $totalSlides; $i++): ?>
                                <button class="carousel-dot w-3 h-3 rounded-full bg-gray-300" data-slide="<?= $i ?>" type="button"></button>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Informações do pet maiores -->
                    <div class="py-10 lg:pt-6 lg:pr-8 lg:pb-16">
                        <div>
                            <p class="text-lg text-gray-900 mb-6"><?= nl2br(htmlspecialchars($pet['descricao'])) ?></p>

                            <ul class="list-disc space-y-3 pl-6 text-base text-gray-700">
                                <li>Tipo: <?= htmlspecialchars($pet['tipo'] ?? '-') ?></li>
                                <li>Raça: <?= htmlspecialchars($pet['raca'] ?? '-') ?></li>
                                <li>Cor: <?= htmlspecialchars($pet['cor'] ?? '-') ?></li>
                                <li>Tamanho: <?= htmlspecialchars($pet['tamanho'] ?? '-') ?> cm</li>
                                <li>Peso: <?= htmlspecialchars($pet['peso'] ?? '-') ?> kg</li>
                                <li>Castrado: <?= htmlspecialchars($pet['castrado'] ? 'Sim' : 'Não') ?></li>
                                <li>Telefone para Contato:<?= htmlspecialchars($telefoneExibicao) ?></li>
                            </ul>
                        </div>

                        <div class="mt-10">
                            <p class="text-base text-gray-600">Este pet está disponível para adoção. Entre em contato para mais informações.</p>
                        </div>

                        <?php if ($whatsappLink): ?>
                            <a href="<?= $whatsappLink ?>" target="_blank"
                                class="mt-6 inline-flex items-center justify-center gap-2 px-8 py-3 
                      bg-gradient-to-r from-green-600 to-purple-600 
                      text-white text-lg font-semibold 
                      rounded-full shadow-lg 
                      hover:from-purple-600 hover:to-green-700 
                      hover:scale-105 transition-all duration-300">
                                Entrar em contato via WhatsApp
                            </a>
                            <br>

                            <!-- BOTÃO VOLTAR QUE FECHA O MODAL -->
                            <a href="home.php"
                                onclick="window.parent.closeModalFromInside(); return false;"
                                class="mt-6 inline-flex items-center justify-center gap-2 px-8 py-3 
                      bg-gradient-to-r from-violet-600 to-purple-500 
                      text-white text-lg font-semibold 
                      rounded-full shadow-lg 
                      hover:from-violet-700 hover:to-purple-600 
                      hover:scale-105 transition-all duration-300">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Voltar
                            </a>

                        <?php else: ?>
                            <span class="mt-10 block w-full text-center px-6 py-3 min-w-[120px] text-white bg-gray-400 rounded cursor-not-allowed text-lg font-semibold">
                                Contato indisponível
                            </span>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const carousel = document.getElementById('carousel');
        const dots = document.querySelectorAll('.carousel-dot');
        let currentIndex = 0;
        const totalSlides = <?= $totalSlides ?>;

        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-gray-900', index === currentIndex);
                dot.classList.toggle('bg-gray-300', index !== currentIndex);
            });
        }

        document.getElementById('prevBtn').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateCarousel();
        });

        document.getElementById('nextBtn').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateCarousel();
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                updateCarousel();
            });
        });

        updateCarousel();
    </script>
</body>

</html>