<?php
require_once __DIR__ . '/../../Conexao/db.php';
$id_avaliacao = intval($_POST['id_avaliacao'] ?? 0);
$id_formando = intval($_POST['id_formando'] ?? 0);
$id_competencia = intval($_POST['id_competencia'] ?? 0);
$id_tentativa = intval($_POST['id_tentativa'] ?? 0);
$percentagem_atingida = floatval($_POST['percentagem_atingida'] ?? 0.0);
$data_avaliacao = $_POST['data_avaliacao'] ?? date('Y-m-d');
$observacoes = trim($_POST['observacoes'] ?? '');

if (!$id_avaliacao) die('ID inválido');
$stmt = $mysqli->prepare("UPDATE avaliacao_competencia SET id_formando=?, id_competencia=?, id_tentativa=?, percentagem_atingida=?, data_avaliacao=?, observacoes=? WHERE id_avaliacao=?");
$stmt->bind_param('iiiidsi',$id_formando,$id_competencia,$id_tentativa,$percentagem_atingida,$data_avaliacao,$observacoes,$id_avaliacao);
if ($stmt->execute()) {
    header('Location: ../../View/Notas/detalhes.php?id=' . $id_avaliacao);
    exit;
} else {
    die('Erro ao atualizar: ' . $mysqli->error);
}
