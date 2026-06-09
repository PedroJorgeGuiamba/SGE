<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$con = new Conector();
$mysqli = $con->getConexao();

$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $query = "SELECT codigo, nome FROM curso";
    $resultado = mysqli_query($mysqli, $query);

    $options = "<option value=''>Selecione um curso</option>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        $options .= "<option value='{$row['codigo']}'>{$row['nome']}</option>";
    }
}else{
    $query = "SELECT codigo, nome 
    FROM curso
    WHERE codigo_qualificacao = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $options = "<option value=''>Selecione um curso</option>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        $options .= "<option value='{$row['codigo']}'>{$row['nome']}</option>";
    }
}
echo $options;