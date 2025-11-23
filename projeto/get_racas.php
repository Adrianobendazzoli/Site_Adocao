<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/get_racas.php
Summary (auto-generated):
PHP file; uses PDO for database access; perpares and executes SQL statements (parameterized); performs SELECT queries (reads data); includes other PHP files (layout or helpers); fetches DB results into arrays

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
require 'conexao.php'; // contém $pdo

header('Content-Type: application/json');

if (!isset($_GET['id_tipo']) || !is_numeric($_GET['id_tipo'])) {
    echo json_encode([]);
    exit;
}

$id_tipo = intval($_GET['id_tipo']);

try {
    $stmt = $pdo->prepare("SELECT id_raca, descricao FROM raca WHERE id_tipo = ?");
    $stmt->execute([$id_tipo]);
    $racas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($racas, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro na query: ' . $e->getMessage()]);
}
?>
