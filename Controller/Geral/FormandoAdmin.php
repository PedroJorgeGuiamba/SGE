<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || !in_array($_SESSION['role'], ['admin', 'formando', 'supervisor'])) {
    header("Location: ../../View/Login.php");
    exit();
}