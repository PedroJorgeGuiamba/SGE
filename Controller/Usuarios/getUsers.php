<?php
require_once '../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';

$con = new Conector();
$mysqli = $con->getConexao();

$descriptografar = new Criptografia();

$query = "SELECT id, Email FROM usuarios";
$resultado = mysqli_query($mysqli, $query);

$email_descriptografado = $descriptografar->descriptografar($row['Email']);


$options = "<option value=''>Selecione um Utilizador</option>";
while ($row = mysqli_fetch_assoc($resultado)) {
    $emailCriptografado = $row['Email'];
    $emailDescriptografado = $descriptografar->descriptografar($emailCriptografado);
    
    // Protege contra emails inválidos ou corrompidos
    if ($emailDescriptografado === false || $emailDescriptografado === '') {
        $emailDescriptografado = "[Email inválido ou corrompido]";
    }

    $options .= "<option value='{$row['id']}'>" . htmlspecialchars($emailDescriptografado) . "</option>";
}

echo $options;