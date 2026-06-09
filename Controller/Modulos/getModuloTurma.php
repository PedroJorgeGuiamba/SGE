<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$conn = new Conector();
$mysqli = $conn->getConexao();


$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $query = "SELECT 
        mt.id_modulo_turma,
        mt.id_turma, 
        mt.id_modulo, 
        m.sigla AS modulo_sigla, 
        t.nome AS turma_nome
    FROM itc_v3.modulo_turma mt
    LEFT JOIN itc_v3.modulo m 
        ON mt.id_modulo = m.id_modulo
    LEFT JOIN itc_v3.turma t 
        ON mt.id_turma = t.codigo;";

    $resultado = mysqli_query($mysqli, $query);

    $options = "<option value=''>Selecione uma Turma associada ao Módulo</option>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        $options .= "<option value='{$row['id_modulo_turma']}'>" . htmlspecialchars($row['turma_nome'] .  ' - ' . $row['modulo_sigla']) . "</option>";
    }
}else{
    $query = "SELECT 
        mt.id_modulo_turma,
        mt.id_turma, 
        mt.id_modulo, 
        m.sigla AS modulo_sigla, 
        t.nome AS turma_nome
    FROM itc_v3.modulo_turma mt
    LEFT JOIN itc_v3.modulo m 
        ON mt.id_modulo = m.id_modulo
    LEFT JOIN itc_v3.turma t 
        ON mt.id_turma = t.codigo
        WHERE mt.id_modulo = ?;";
    // $resultado = mysqli_query($mysqli, $query);
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $options = "<option value=''>Selecione uma Turma associada ao Módulo</option>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        $options .= "<option value='{$row['id_modulo_turma']}'>" . htmlspecialchars($row['turma_nome'] .  ' - ' . $row['modulo_sigla']) . "</option>";
    }
}
echo $options;