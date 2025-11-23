<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/conexao.php
Summary (auto-generated):
PHP file; uses PDO for database access

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
    $host = 'localhost';
    $dbname = 'sistema_adocao';
    $user = 'root';
    $pass = '';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // Habilita erros do PDO
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Conexão bem-sucedida!";
    } catch (PDOException $e) {
        echo "Erro na conexão: " . $e->getMessage();
    }
?>