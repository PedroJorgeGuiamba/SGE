<?php
require_once __DIR__ . '/Helpers/Criptografia.php';
require_once __DIR__ . '/Conexao/conector.php';

$criptografia = new Criptografia();
$conexao = new Conector();
$conn = $conexao->getConexao();

// Buscar usuários com email em texto plano
$result = $conn->query("SELECT id, email FROM usuarios WHERE email LIKE '%@%'");

while ($row = $result->fetch_assoc()) {
    $email_original = $row['email'];
    $email_encrypted = $criptografia->criptografar($email_original);
    
    // Atualizar para o email criptografado
    $stmt = $conn->prepare("UPDATE usuarios SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $email_encrypted, $row['id']);
    $stmt->execute();
    
    echo "Atualizado: $email_original -> $email_encrypted\n";
}

echo "Correção concluída!";
?>