<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/cadastro_pet.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; uses MySQLi or legacy mysql_* functions for DB access; perpares and executes SQL statements (parameterized); performs SELECT queries (reads data); performs INSERT/UPDATE/DELETE (writes data); includes other PHP files (layout or helpers); contains HTML form(s); fetches DB results into arrays; styling (CSS) present; Contains JavaScript (DOM interactions)

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
session_start();

// Redireciona se não estiver logado
if (empty($_SESSION['usuario_cpf'])) {
    header('Location: login.html');
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $cor = $_POST['cor'];
    $tamanho = $_POST['tamanho'];
    $peso = $_POST['peso'];
    $castrado = $_POST['castrado'];
    $detalhes_castracao = $_POST['detalhes_castracao'] ?? '';
    $id_tipo = $_POST['id_tipo'];
    $id_raca = $_POST['id_raca'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=sistema_adocao", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Criar pasta uploads se não existir
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        // Salvar a primeira foto
        $primeiraFoto = '';
        if (!empty($_FILES['fotos']['name'][0])) {
            $filename = time() . '_' . basename($_FILES['fotos']['name'][0]);
            $primeiraFoto = $uploadDir . $filename;
            move_uploaded_file($_FILES['fotos']['tmp_name'][0], $primeiraFoto);
        }

        // Inserir pet
        $stmt = $pdo->prepare("
            INSERT INTO pets 
            (nome, descricao, cor, tamanho, peso, castrado, detalhes_castracao, id_tipo, id_raca, usuario_cpf, foto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nome, $descricao, $cor, $tamanho, $peso, $castrado, $detalhes_castracao, $id_tipo, $id_raca, $_SESSION['usuario_cpf'], $primeiraFoto]);

        $id_pet = $pdo->lastInsertId();

        // Salvar todas as fotos na tabela pet_photos
        if (!empty($_FILES['fotos']['name'][0])) {
            foreach ($_FILES['fotos']['tmp_name'] as $key => $tmpName) {
                $filename = time() . '_' . basename($_FILES['fotos']['name'][$key]);
                $targetFile = $uploadDir . $filename;
                move_uploaded_file($tmpName, $targetFile);

                $stmtFoto = $pdo->prepare("INSERT INTO pet_photos (pet_id, photo_path) VALUES (?, ?)");
                $stmtFoto->execute([$id_pet, $targetFile]);
            }
        }

        // Redireciona para home.php após sucesso
        header("Location: home.php");
        exit;
    } catch (PDOException $e) {
        $mensagem = "Erro ao cadastrar pet: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: url('img-site/fundo.png') no-repeat center center fixed;
        }
    </style>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <?php if (!empty($mensagem)): ?>
        <div class="max-w-4xl w-full mt-4 p-4 bg-purple-100 text-purple-900 rounded-md text-center">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <section class="max-w-4xl w-full bg-purple-500/90 p-6 sm:p-8 rounded-2xl shadow-xl text-white">
        <h1 class="text-3xl font-bold mb-8 text-center">FICHA DO PET!</h1>

        <form class="space-y-6" method="POST" enctype="multipart/form-data">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Nome:</label>
                    <input type="text" name="nome" required class="w-full rounded-md p-2 text-gray-800 bg-white" />
                </div>
                <div>
                    <label class="block font-semibold mb-1">Descrição:</label>
                    <input type="text" name="descricao" required class="w-full rounded-md p-2 text-gray-800 bg-white" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Cor:</label>
                    <input type="text" name="cor" required class="w-full rounded-md p-2 text-gray-800 bg-white" />
                </div>
                <div>
                    <label class="block font-semibold mb-1">Tamanho (cm):</label>
                    <input type="number" name="tamanho" required class="w-full rounded-md p-2 text-gray-800 bg-white" />
                </div>
                <div>
                    <label class="block font-semibold mb-1">Peso:</label>
                    <input type="number" name="peso" step="0.01" required class="w-full rounded-md p-2 text-gray-800 bg-white" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                <div>
                    <label class="block font-semibold mb-1">Castrado?</label>
                    <div class="flex gap-3 text-white">
                        <label><input type="radio" name="castrado" value="nao" required> Não</label>
                        <label><input type="radio" name="castrado" value="sim" required> Sim</label>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold mb-1">Detalhes:</label>
                    <input type="text" name="detalhes_castracao" class="w-full rounded-md p-2 text-gray-800 bg-white" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Tipo:</label>
                    <select name="id_tipo" id="tipo" required class="w-full rounded-md p-2 text-gray-800 bg-white">
                        <option value="">Selecione...</option>
                        <?php
                        $conn = new mysqli("localhost", "root", "", "sistema_adocao");
                        $tipos = $conn->query("SELECT * FROM tipo");
                        while ($t = $tipos->fetch_assoc()):
                        ?>
                            <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['descricao']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Raça:</label>
                    <select name="id_raca" id="raca" required class="w-full rounded-md p-2 text-gray-800 bg-white">
                        <option value="">Selecione o tipo primeiro...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1">Fotos:</label>
                <input type="file" name="fotos[]" multiple accept="image/*" required class="w-full rounded-md p-2 text-gray-800 bg-white" />
            </div>

            <div class="flex justify-center">
                <button type="submit"
                    class="px-6 py-2 min-w-[120px] text-center text-white bg-violet-600 border border-violet-600 rounded active:text-violet-500 hover:bg-transparent hover:text-violet-600 focus:outline-none focus:ring w-full sm:w-auto rounded-full">
                    CADASTRAR PET
                </button>
            </div>
            <a href="home.php"
                class="mt-6 inline-flex items-center justify-center gap-2 px-8 py-3 
                      bg-gradient-to-r from-violet-600 to-violet-600
                      text-white text-lg font-semibold 
                      rounded-full  shadow-lg 
                      hover:from-violet-700 hover:to-purple-600 
                      hover:scale-105 transition-all duration-300">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Voltar
            </a>
        </form>
    </section>

    <script>
        document.getElementById("tipo").addEventListener("change", function() {
            const tipoId = this.value;
            const racaSelect = document.getElementById("raca");
            racaSelect.innerHTML = "<option value=''>Carregando...</option>";

            if (!tipoId) {
                racaSelect.innerHTML = "<option value=''>Selecione o tipo primeiro...</option>";
                return;
            }

            fetch("get_racas.php?id_tipo=" + tipoId)
                .then(response => response.json())
                .then(data => {
                    racaSelect.innerHTML = "";
                    if (data.length === 0) {
                        racaSelect.innerHTML = "<option value=''>Sem raças</option>";
                    } else {
                        data.forEach(raca => {
                            const option = document.createElement("option");
                            option.value = raca.id_raca;
                            option.textContent = raca.descricao;
                            racaSelect.appendChild(option);
                        });
                    }
                })
                .catch(() => {
                    racaSelect.innerHTML = "<option value=''>Erro ao carregar raças</option>";
                });
        });
    </script>

</body>

</html>