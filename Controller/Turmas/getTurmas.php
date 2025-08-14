<?php
require_once '../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT codigo, nome FROM turma";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione uma Turma</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['codigo']}'>{$row['nome']}</option>";
}

echo $options;