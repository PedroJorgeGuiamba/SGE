<?php
// bag/config/db.php
// Edita as credenciais conforme o teu ambiente
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'Familiaguiamba1';
$DB_NAME = 'itc_v3';
$DB_PORT = 3306;

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($mysqli->connect_errno) {
    http_response_code(500);
    die("Erro ao conectar à base de dados: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
