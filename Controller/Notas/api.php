<?php
require_once __DIR__ . '/../../Conexao/db.php';
header('Content-Type: application/json; charset=utf-8');

$res = $mysqli->query("
SELECT ac.*, f.nome, f.apelido, m.descricao AS modulo, tt.descricao AS tentativa
FROM avaliacao_competencia ac
JOIN formando f ON ac.id_formando = f.id_formando
JOIN competencia c ON ac.id_competencia = c.id_competencia
JOIN modulo m ON c.id_modulo = m.id_modulo
JOIN tipo_tentativa tt ON ac.id_tentativa = tt.id_tentativa
ORDER BY ac.data_avaliacao DESC
");
$out = [];
while($r = $res->fetch_assoc()) $out[] = $r;
echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
