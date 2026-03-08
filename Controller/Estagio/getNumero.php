<?php
require_once '../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT id_pedido_carta, numero FROM pedido_carta ORDER BY id_pedido_carta DESC";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione um pedido</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $idPedido = (int) $row['id_pedido_carta'];
    $numeroPedido = (int) $row['numero'];
    $options .= "<option value='{$idPedido}'>Pedido #{$numeroPedido} (ID {$idPedido})</option>";
}

echo $options;
