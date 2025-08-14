<?php
require_once '../../Conexao/conector.php';


$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT id_qualificacao, descricao FROM qualificacao";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione uma qualificacao</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['id_qualificacao']}'>{$row['descricao']}</option>";
}

echo $options;