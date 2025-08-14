<?php
require_once '../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT codigo, nome FROM curso";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione um curso</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['codigo']}'>{$row['nome']}</option>";
}

echo $options;