<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$usuario_id = $_SESSION['user_id'];
$ordenar = $_POST['ordenar'] ?? [];

if (is_array($ordenar)) {
    $stmt = $conn->prepare("UPDATE tarefas SET posicao = ? WHERE id = ? AND usuario_id = ?");

    foreach ($ordenar as $posicao => $id_tarefa) {
        $id_tarefa = (int) $id_tarefa;
        $stmt->bind_param("iii", $posicao, $id_tarefa, $usuario_id);
        $stmt->execute();
    }

    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode(['success' => true]);
