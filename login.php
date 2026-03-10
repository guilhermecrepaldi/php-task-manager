<?php
require_once 'config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'registrar') {
        $email    = trim($_POST['email']);
        $nome     = trim($_POST['nome']);
        $senha    = $_POST['senha'];

        if ($email && $senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (email, nome, senha_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $nome, $senha_hash);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                header('Location: index.php');
                exit;
            } else {
                $erro = 'Email já cadastrado.';
            }
            $stmt->close();
        } else {
            $erro = 'Preencha todos os campos.';
        }
    }

    if ($acao === 'login') {
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        $stmt = $conn->prepare("SELECT id, senha_hash FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($senha, $user['senha_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: index.php');
                exit;
            }
        }
        $erro = 'Email ou senha inválidos.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h1>Task Manager</h1>

        <?php if ($erro): ?>
            <div class="alert error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="auth-forms">
            <form method="POST" class="auth-form">
                <h2>Login</h2>
                <input type="hidden" name="acao" value="login">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>

            <form method="POST" class="auth-form">
                <h2>Registrar</h2>
                <input type="hidden" name="acao" value="registrar">
                <input type="text" name="nome" placeholder="Nome">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Criar Conta</button>
            </form>
        </div>
    </div>
</body>
</html>
