<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    $theme = $_POST['theme'] === 'dark' ? 'dark' : 'light';
    
    // Salva na sessão (persistente enquanto logado)
    $_SESSION['theme'] = $theme;
    
    // OPCIONAL: Salvar no banco de dados (para persistir após logout)
    // if (isset($_SESSION['user_id'])) {
    //     $conn->prepare("UPDATE usuarios SET theme = ? WHERE id = ?")->execute([$theme, $_SESSION['user_id']]);
    // }
    
    echo json_encode(['success' => true]);
    exit();
}
?>