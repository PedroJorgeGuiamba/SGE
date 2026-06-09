<?php
require_once __DIR__ . '/../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT id_competencia, descricao_elemento FROM competencia;";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione um RA</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['id_competencia']}'>{$row['descricao_elemento']}</option>";
}

echo $options;