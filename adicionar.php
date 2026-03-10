<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['titulo'])) {
    $usuario_id = $_SESSION['user_id'];
    $titulo = trim($_POST['titulo']);

    $stmt = $conn->prepare("INSERT INTO tarefas (titulo, usuario_id) VALUES (?, ?)");
    $stmt->bind_param("si", $titulo, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: index.php');
exit;
