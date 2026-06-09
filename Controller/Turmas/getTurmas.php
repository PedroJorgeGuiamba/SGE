<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$con = new Conector();
$conn = $con->getConexao();

$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $query = "SELECT codigo, nome, ano_lectivo FROM turma";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $options = "<option value=''>Selecione uma Turma</option>";
    while ($row = $resultado->fetch_assoc()) {
        $options .= "<option value='{$row['codigo']}'>{$row['nome']} - {$row['ano_lectivo']}</option>";
    }
}
else{
    $query = "SELECT codigo, nome, ano_lectivo FROM turma WHERE codigo_qualificacao = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $options = "<option value=''>Selecione uma Turma</option>";
    while ($row = $resultado->fetch_assoc()) {
        $options .= "<option value='{$row['codigo']}'>{$row['nome']} - {$row['ano_lectivo']}</option>";
    }
}
echo $options;