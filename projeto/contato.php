<?php
/*
AUTO-COMMENTED FILE
Original path: site-adocao1/projeto/contato.php
Summary (auto-generated):
PHP file; uses session authentication (session_start); uses PDO for database access; perpares and executes SQL statements (parameterized); performs SELECT queries (reads data); performs INSERT/UPDATE/DELETE (writes data); includes other PHP files (layout or helpers); contains HTML form(s); fetches DB results into arrays

Notes:
- This header was generated automatically to give a quick overview of the file.
- Inline, line-by-line commenting was NOT applied automatically to avoid changing behavior.
- If you want detailed line-by-line comments for specific files, ask and I'll produce them.
*/
?>
<?php
session_start();
require 'conexao.php'; // deve definir $pdo (PDO)

$pet_id = filter_input(INPUT_GET, 'pet_id', FILTER_VALIDATE_INT);
if (!$pet_id) {
    die("Pet inválido.");
}

try {
    // busca pet e dono (usuario)
    $sql = "SELECT p.id, p.nome AS pet_nome, u.cpf AS owner_cpf, u.nome AS owner_nome, u.email AS owner_email, u.telefone AS owner_tel
            FROM pets p
            LEFT JOIN usuario u ON u.cpf = p.usuario_cpf
            WHERE p.id = :id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $pet_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        die("Pet não encontrado.");
    }
} catch (PDOException $e) {
    error_log('contato.php fetch error: '.$e->getMessage());
    die("Erro ao carregar dados.");
}

// processamento do POST
$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_name  = trim($_POST['name'] ?? '');
    $sender_email = trim($_POST['email'] ?? '');
    $message_body = trim($_POST['message'] ?? '');

    if ($sender_name === '' || $sender_email === '' || $message_body === '') {
        $status = 'Por favor preencha todos os campos.';
    } elseif (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
        $status = 'Email inválido.';
    } else {
        try {
            // cria tabela de mensagens se não existir
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    pet_id INT NOT NULL,
                    owner_cpf VARCHAR(20) NULL,
                    sender_name VARCHAR(255) NOT NULL,
                    sender_email VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            $sqlIns = "INSERT INTO contact_messages (pet_id, owner_cpf, sender_name, sender_email, message)
                       VALUES (:pet_id, :owner_cpf, :sender_name, :sender_email, :message)";
            $stmtIns = $pdo->prepare($sqlIns);
            $stmtIns->execute([
                ':pet_id' => $row['id'],
                ':owner_cpf' => $row['owner_cpf'] ?? null,
                ':sender_name' => $sender_name,
                ':sender_email' => $sender_email,
                ':message' => $message_body
            ]);

            // tenta enviar email
            if (!empty($row['owner_email'])) {
                $to = $row['owner_email'];
                $subject = "Interesse no pet: " . $row['pet_nome'];
                $body  = "Olá " . ($row['owner_nome'] ?: 'Dono') . ",\n\n";
                $body .= "Você recebeu uma mensagem sobre o pet \"". $row['pet_nome'] ."\":\n\n";
                $body .= "Nome: {$sender_name}\n";
                $body .= "Email: {$sender_email}\n\n";
                $body .= "Mensagem:\n{$message_body}\n\n";
                $body .= "----\nMensagem enviada pelo site.";

                $headers = "From: {$sender_name} <{$sender_email}>\r\nReply-To: {$sender_email}\r\n";
                @mail($to, $subject, $body, $headers);
            }

            // sucesso
            header("Location: detalhe_pet.php?id=" . urlencode($pet_id) . "&contato=ok");
            exit;
        } catch (PDOException $e) {
            error_log('contato.php insert error: '.$e->getMessage());
            $status = 'Erro ao enviar a mensagem. Tente novamente mais tarde.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Contato — <?= htmlspecialchars($row['pet_nome']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
<main class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-2">Entrar em contato</h1>
        <p class="text-sm text-gray-600 mb-4">Pet: <strong><?= htmlspecialchars($row['pet_nome']) ?></strong></p>

        <div class="mb-4 text-sm">
            <p>Dono: <strong><?= htmlspecialchars($row['owner_nome'] ?? '—') ?></strong></p>
            <?php if (!empty($row['owner_email'])): ?>
                <p>Email: <?= htmlspecialchars($row['owner_email']) ?></p>
            <?php endif; ?>
            <?php if (!empty($row['owner_tel'])): ?>
                <p>Telefone: <?= htmlspecialchars($row['owner_tel']) ?></p>
                <p>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $row['owner_tel']) ?>?text=Olá, tenho interesse no pet <?= urlencode($row['pet_nome']) ?>" 
                       target="_blank" 
                       class="inline-block mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                       Entrar em contato via WhatsApp
                    </a>
                </p>
            <?php endif; ?>
        </div>

        <?php if ($status): ?>
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded">
                <?= htmlspecialchars($status) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Seu nome</label>
                <input name="name" required class="w-full p-2 border rounded" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Seu email</label>
                <input name="email" type="email" required class="w-full p-2 border rounded" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Mensagem</label>
                <textarea name="message" rows="6" required class="w-full p-2 border rounded"><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
            </div>

            <div class="flex items-center justify-between">
                <a href="detalhe_pet.php?id=<?= urlencode($pet_id) ?>" class="text-sm text-gray-600 hover:underline">Voltar</a>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Enviar mensagem</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
