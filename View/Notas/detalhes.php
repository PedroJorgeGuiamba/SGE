<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../../Conexao/db.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../middleware/auth.php';

SecurityHeaders::setFull();
$conector = new Conector();
$conn = $conector->getConexao();
$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID inválido');

$stmt = $mysqli->prepare("
SELECT ac.*, f.nome, f.apelido, c.id_modulo, m.descricao AS modulo, tt.descricao AS tentativa
FROM avaliacao_competencia ac
JOIN formando f ON ac.id_formando = f.id_formando
JOIN competencia c ON ac.id_competencia = c.id_competencia
JOIN modulo m ON c.id_modulo = m.id_modulo
JOIN tipo_tentativa tt ON ac.id_tentativa = tt.id_tentativa
WHERE ac.id_avaliacao = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) die('Não encontrado');
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Detalhes</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Notifications CSS -->
  <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
</head>

<body>
  <!-- Notification Widget -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container-fluid">
      <span class="navbar-text me-auto">Notificações</span>
      <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
    </div>
  </nav>
  <h1>Detalhes Avaliação #<?= htmlspecialchars($row['id_avaliacao']) ?></h1>
  <ul>
    <li>Formando: <?= htmlspecialchars($row['nome'] . ' ' . $row['apelido']) ?></li>
    <li>Módulo: <?= htmlspecialchars($row['modulo']) ?></li>
    <li>Competência: <?= htmlspecialchars($row['id_competencia']) ?></li>
    <li>Tentativa: <?= htmlspecialchars($row['tentativa']) ?></li>
    <li>Percentagem: <?= htmlspecialchars($row['percentagem_atingida']) ?></li>
    <li>Menção: <?= htmlspecialchars($row['mencao']) ?></li>
    <li>Data: <?= htmlspecialchars($row['data_avaliacao']) ?></li>
    <li>Observações: <?= nl2br(htmlspecialchars($row['observacoes'])) ?></li>
  </ul>
  <p><a href="index.php">Voltar</a></p>
</body>

</html>