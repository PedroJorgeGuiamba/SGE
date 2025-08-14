<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['email']) or $_SESSION['role'] !== 'supervisor') {
    header("Location: ../../View/Login.php");
    exit();
}