<?php
require_once __DIR__ . '/../../Conexao/db.php';
$id = intval($_POST['id_avaliacao'] ?? 0);
if (!$id) die('ID inválido');
$stmt = $mysqli->prepare("DELETE FROM avaliacao_competencia WHERE id_avaliacao = ?");
$stmt->bind_param('i',$id);
if ($stmt->execute()) {
    header('Location: ../../view/notas/index.php');
    exit;
} else {
    die('Erro ao eliminar: ' . $mysqli->error);
}
