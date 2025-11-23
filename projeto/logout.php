<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/logout.php
Summary (auto-generated):
PHP file; uses session authentication (session_start)

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
// Inicia sessão (sem saída antes disto)
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Remove cookie de sessão, se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona para a tela de login
header('Location: index.php');
exit;
?>