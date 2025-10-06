<?php
require_once '../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT numero FROM pedido_carta";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione um pedido</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['numero']}'>{$row['numero']}</option>";
}

echo $options;