<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class NotificationHelper
{
    public static function sanitizeUserId($userId)
    {
        return max(0, (int)$userId);
    }

    public static function handleAction($conn, $userId, $postData)
    {
        $userId = self::sanitizeUserId($userId);

        if (!$userId || !isset($postData['action'])) {
            return;
        }

        $action = $postData['action'];

        $role = strtolower($_SESSION['role']);
        $painel = "";

        switch ($role) {
            case 'formando':
                $painel = "/estagio/formando";
                break;
            case 'supervisor':
                $painel = "/estagio/supervisor";
                break;
            case 'formador':
                $painel = "/estagio/formador";
                break;
            case 'admin':
                $painel = "/estagio/admin";
                break;
            case 'seguranca':
                $painel = "/estagio/seguranca";
                break;
            default:
                registrarAtividade($_SESSION['sessao_id'], "Tentativa de redirecionamento com role inválida: {$role}", "ERROR");
                break;
        }

        $redirectUrl = $postData['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? $painel;

        if ($action === 'mark_read' && !empty($postData['id_notificacao'])) {
            $notifId = (int) $postData['id_notificacao'];
            self::markAsRead($conn, $userId, $notifId);
        } elseif ($action === 'mark_all_read') {
            self::markAllAsRead($conn, $userId);
        } elseif ($action === 'clear_all') {
            self::clearAll($conn, $userId);
        }

        header("Location: " . $redirectUrl);
        exit;
    }

    public static function getUnreadCount($conn, $userId)
    {
        $userId = self::sanitizeUserId($userId);

        if (!$userId) {
            return 0;
        }

        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notificacao WHERE id_utilizador = ? AND (lida = 0 OR lida IS NULL)");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = 0;

        if ($result) {
            $row = $result->fetch_assoc();
            $count = isset($row['count']) ? (int) $row['count'] : 0;
        }

        $stmt->close();

        return $count;
    }

    public static function getNotifications($conn, $userId)
    {
        $userId = self::sanitizeUserId($userId);

        if (!$userId) {
            return [];
        }

        $stmt = $conn->prepare("SELECT * FROM notificacao WHERE id_utilizador = ? ORDER BY COALESCE(lida, 0) ASC, data DESC");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notifications = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
        }

        $stmt->close();

        return $notifications;
    }

    public static function markAsRead($conn, $userId, $notificationId)
    {
        $stmt = $conn->prepare("UPDATE notificacao SET lida = 1 WHERE id_notificacao = ? AND id_utilizador = ?");
        $stmt->bind_param('ii', $notificationId, $userId);
        $stmt->execute();
        $stmt->close();
    }

    public static function markAllAsRead($conn, $userId)
    {
        $stmt = $conn->prepare("UPDATE notificacao SET lida = 1 WHERE id_utilizador = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();
    }

    public static function clearAll($conn, $userId)
    {
        $stmt = $conn->prepare("DELETE FROM notificacao WHERE id_utilizador = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();
    }
}
