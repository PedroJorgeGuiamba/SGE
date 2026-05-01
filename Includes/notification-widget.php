<?php
$unreadLabel = $unreadCount > 99 ? '99+' : $unreadCount;

$currentUrl = $_SERVER['REQUEST_URI'] ?? '/estagio/formando';
?>
<li class="nav-item dropdown">
    <a href="#" class="nav-link notification-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Notificações" style="padding: 0; margin: 0 15px;">
        <span style="position: relative; display: inline-block;">
            <i class="fas fa-bell fs-4" style="color: #3a4c91;"></i>
            <?php if ($unreadCount > 0): ?>
                <span class="notification-count"><?php echo $unreadLabel; ?></span>
            <?php endif; ?>
        </span>
    </a>
    <ul class="dropdown-menu notifications dropdown-menu-end">
        <?php if (empty($notifications)): ?>
            <li class="notification-item text-center">Nenhuma notificação.</li>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <li class="notification-item <?php echo ($notif['lida'] == 0 || $notif['lida'] === null ? 'unread' : ''); ?>">
                    <p><?php echo htmlspecialchars($notif['mensagem']); ?></p>
                    <small class="text-muted"><?php echo htmlspecialchars($notif['data']); ?></small>
                    <?php if ($notif['lida'] == 0 || $notif['lida'] === null): ?>
                        <form method="POST" style="display:inline; margin-top: 5px;" class="notification-form">
                            <input type="hidden" name="action" value="mark_read">
                            <input type="hidden" name="id_notificacao" value="<?php echo (int)$notif['id_notificacao']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-success">Marcar como lida</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li class="px-3 py-2 text-center">
            <form method="POST" style="display:inline;" class="d-inline-block me-2">
                <input type="hidden" name="action" value="mark_all_read">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($currentUrl); ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary">Marcar todas como lidas</button>
            </form>
            <form method="POST" style="display:inline;" class="d-inline-block">
                <input type="hidden" name="action" value="clear_all">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($currentUrl); ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Limpar todas</button>
            </form>
        </li>
    </ul>
</li>