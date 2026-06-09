<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$con = new Conector();
$mysqli = $con->getConexao();

$query = "SELECT id_formador, nome, apelido FROM formador";
$resultado = mysqli_query($mysqli, $query);

$options = "<option value=''>Selecione um Formador</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $options .= "<option value='{$row['id_formador']}'>" . htmlspecialchars($row['nome'] .  ' ' . $row['apelido']) . "</option>";
}

echo $options;