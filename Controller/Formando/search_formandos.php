<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conexao = new Conector();
$conn    = $conexao->getConexao();
$criptografia = new Criptografia();

$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $sql = "
        SELECT f.*
        FROM formando f
        ORDER BY f.id_formando DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT f.*
        FROM formando f
        WHERE (f.nome LIKE ? OR f.apelido LIKE ? OR f.email LIKE ?)
        ORDER BY f.id_formando DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$formando = [];
while ($row = $result->fetch_assoc()) {
    $formando[] = [
        'id_formando' => $row['id_formando'],
        'nome' => $row['nome'],
        'apelido' => $row['apelido'],
        'codigo' => $row['codigo'],
        'nascimento' => $row['dataDeNascimento'],
        'naturalidade' => $row['naturalidade'],
        'tipoDocumento' => $row['tipoDeDocumento'],
        'numeroDocumento' => $criptografia->descriptografar($row['numeroDeDocumento']),
        'localEmitido' => $row['localEmitido'],
        'Emissao' => $row['dataDeEmissao'],
        'NUIT' => $criptografia->descriptografar($row['NUIT']),
        'telefone' => $criptografia->descriptografar($row['telefone']),
        'email' => $criptografia->descriptografar($row['email'])
    ];
}

header('Content-Type: application/json');
echo json_encode($formando);
$conn->close();
