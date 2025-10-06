<?php
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Helpers/auth.php';

$auth = new AuthMiddleware();
if (!$auth->verificarAutenticacao()) {
    header("Location: /estagio/View/Login.php");
    exit();
}