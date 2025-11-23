<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/ver_pet.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; perpares and executes SQL statements (parameterized); performs SELECT queries (reads data); includes other PHP files (layout or helpers); fetches DB results into arrays; styling (CSS) present; Contains JavaScript (DOM interactions)

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
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
    // Buscar dados do pet com tipo e raça
    $sql = "SELECT 
                p.*,
                t.descricao AS tipo,
                r.descricao AS raca
            FROM pets p
            LEFT JOIN tipo t ON p.id_tipo = t.id_tipo
            LEFT JOIN raca r ON p.id_raca = r.id_raca
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
  <style>
    
  </style>
</head>
<body class="bg-gray-50">

<div class="bg-white relative">
    <!-- Botão voltar maior e à direita -->
<a href="pets.php" class="absolute top-4 right-4 text-gray-700 text-4xl font-bold hover:text-gray-900 z-10">
    ×
</a>

    <div class="pt-6">
        <div class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:max-w-7xl lg:px-8 lg:pt-16 lg:pb-24">
            <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
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

                    <!-- Botões anterior/próximo -->
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

                <!-- Informações -->
                <div class="py-10 lg:pt-6 lg:pr-8 lg:pb-16">
                    <div>
                        <p class="text-base text-gray-900 mb-4"><?= nl2br(htmlspecialchars($pet['descricao'])) ?></p>

                        <ul class="list-disc space-y-2 pl-4 text-sm text-gray-600">
                            <li>Tipo: <?= htmlspecialchars($pet['tipo'] ?? '-') ?></li>
                            <li>Raça: <?= htmlspecialchars($pet['raca'] ?? '-') ?></li>
                            <li>Cor: <?= htmlspecialchars($pet['cor'] ?? '-') ?></li>
                            <li>Tamanho: <?= htmlspecialchars($pet['tamanho'] ?? '-') ?> cm</li>
                            <li>Peso: <?= htmlspecialchars($pet['peso'] ?? '-') ?> kg</li>
                            <li>Castrado: <?= htmlspecialchars($pet['castrado'] ? 'Sim' : 'Não') ?></li>
                        </ul>
                    </div>
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
