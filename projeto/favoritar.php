<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/favoritar.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; perpares and executes SQL statements (parameterized); performs INSERT/UPDATE/DELETE (writes data); includes other PHP files (layout or helpers); related to favorites functionality

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
session_start();
require 'conexao.php';

if (empty($_SESSION['usuario_cpf'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$usuario_cpf = $_SESSION['usuario_cpf'];
$pet_id = $_POST['pet_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$pet_id || !in_array($action, ['add','remove'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_adocao", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO favoritos (usuario_cpf, pet_id) VALUES (?, ?)");
        $stmt->execute([$usuario_cpf, $pet_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_cpf = ? AND pet_id = ?");
        $stmt->execute([$usuario_cpf, $pet_id]);
    }

    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>