<?php
require_once __DIR__ . '/../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT id_tipo, descricao FROM tipo_avaliacao;";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione um Tipo de Avaliação</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['id_tipo']}'>{$row['descricao']}</option>";
}

echo $options;