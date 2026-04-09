<?php
require_once __DIR__ . '/../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../Conexao/conector.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);
