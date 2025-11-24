<?php
// Inicia sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Verifica se é admin usando a sessão (mais eficiente)
$isAdmin = !empty($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adocão</title>
  <link rel="shortcut icon" href="img-site/logotipo.png" type="image/x-icon">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>

  </style>
</head>

<body class="bg-gray-50">

  <header>
    <nav class="bg-white w-full flex items-center justify-between px-6 py-3 shadow-md fixed top-0 z-50">
      <!-- Logo -->
      <a href="home.php" class="flex items-center">
        <img src="img-site/logotipo.png" alt="Logo" class="h-16">
      </a>

      <!-- Hamburger Menu para mobile -->
      <div class="md:hidden">
        <button id="menuButton" class="text-gray-700 focus:outline-none">
          <!-- ícone hamburger -->
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>

      <!-- Menu Links -->
      <div id="menu" class="hidden flex-col md:flex md:flex-row md:items-center md:space-x-6 absolute md:static top-full left-0 w-full md:w-auto bg-white md:bg-transparent shadow md:shadow-none">
        <a href="#pets_adotar" class="block px-4 py-2 text-gray-700 hover:text-purple-600 font-medium">Adotar</a>
        <a href="cadastro_pet.php" class="block px-4 py-2 text-gray-700 hover:text-purple-600 font-medium">Colocar um pet para adoção</a>
        <a href="localizar_dono.php" class="block px-4 py-2 text-gray-700 hover:text-purple-600 font-medium">Achar um pet próximo</a>

        <!-- Perfil -->
        <div class="relative">
          <button type="button" id="profileButton" class="flex items-center px-3 py-2 border rounded-full hover:shadow-lg m-2 md:m-0">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
            </svg>
          </button>

          <!-- Dropdown Menu -->
          <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 hidden">
            <a href="perfil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Meu Perfil</a>
            <a href="pets.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Meus Pets</a>
            <a href="favoritos.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Pets Favoritados</a>
            <?php if ($isAdmin): ?>
              <hr class="my-1">
              <a href="usuarios_admin.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 font-semibold text-violet-600">Administrador</a>
            <?php endif; ?>
            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Sair</a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <script>
    // Toggle Menu Mobile
    const menuButton = document.getElementById('menuButton');
    const menu = document.getElementById('menu');
    menuButton.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });

    // Dropdown Perfil
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');

    profileButton.addEventListener('click', (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.classList.add('hidden');
      }
    });
  </script>
</body>

</html>