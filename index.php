<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$status = $_GET['status'] ?? 'todas';

$sql = "SELECT * FROM tarefas WHERE usuario_id = ?";
$params = [$usuario_id];
$types = "i";

if ($status === 'pendente' || $status === 'concluida') {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY posicao ASC, created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$tarefas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Task Manager</h1>
            <a href="logout.php" class="btn logout">Sair</a>
        </header>

        <form action="adicionar.php" method="POST" class="add-form">
            <input type="text" name="titulo" placeholder="Nova tarefa..." required>
            <button type="submit" class="btn add">Adicionar</button>
        </form>

        <div class="filters">
            <a href="?status=todas" class="filter <?= $status === 'todas' ? 'active' : '' ?>">Todas</a>
            <a href="?status=pendente" class="filter <?= $status === 'pendente' ? 'active' : '' ?>">Pendentes</a>
            <a href="?status=concluida" class="filter <?= $status === 'concluida' ? 'active' : '' ?>">Concluídas</a>
        </div>

        <div class="task-grid" id="task-grid">
            <?php foreach ($tarefas as $t): ?>
                <div class="task-card <?= $t['status'] === 'concluida' ? 'done' : '' ?>" data-id="<?= $t['id'] ?>">
                    <div class="task-content">
                        <a href="toggle.php?id=<?= $t['id'] ?>" class="toggle-btn">
                            <?= $t['status'] === 'concluida' ? '✓' : '○' ?>
                        </a>
                        <span class="task-title"><?= htmlspecialchars($t['titulo']) ?></span>
                    </div>
                    <a href="excluir.php?id=<?= $t['id'] ?>" class="delete-btn" onclick="return confirm('Excluir tarefa?')">×</a>
                </div>
            <?php endforeach; ?>

            <?php if (empty($tarefas)): ?>
                <div class="empty-state">Nenhuma tarefa encontrada.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
