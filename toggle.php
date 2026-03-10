<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$usuario_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE tarefas SET status = IF(status='pendente','concluida','pendente') WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$stmt->close();

header('Location: index.php');
exit;
