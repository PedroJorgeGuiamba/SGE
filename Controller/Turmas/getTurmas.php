<?php
require_once __DIR__ . '/../../Conexao/conector.php';


$con = new Conector();
$conn = $con->getConexao();

$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $query = "SELECT codigo, nome FROM turma";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->get_result();
    // $resultado = mysqli_query($mysqli, $query);

    $options = "<option value=''>Selecione uma Turma</option>";
    while ($row = $resultado->fetch_assoc()) {
        $options .= "<option value='{$row['codigo']}'>{$row['nome']}</option>";
    }
}
else{
    $query = "SELECT codigo, nome FROM turma WHERE codigo_qualificacao = ?";
    // $resultado = mysqli_query($conn, $query);
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $options = "<option value=''>Selecione uma Turma</option>";
    // while ($row = mysqli_fetch_assoc($resultado)) {
    while ($row = $resultado->fetch_assoc()) {
        $options .= "<option value='{$row['codigo']}'>{$row['nome']}</option>";
    }
}
echo $options;