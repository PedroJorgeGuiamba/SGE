<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['email']) or $_SESSION['role'] !== 'formador') {
    header("Location: ../../View/Auth/Login.php");
    exit();
}