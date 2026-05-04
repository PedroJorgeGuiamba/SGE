<?php
session_start();
header('Content-Type: application/javascript');

// Calcular permissões do usuário
$isAdmin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor');

// Gerar JavaScript puro com os dados
echo "// Dados gerados pelo PHP\n";
echo "window.userPermissions = {\n";
echo "    isAdmin: " . json_encode($isAdmin) . ",\n";
echo "    role: " . json_encode($_SESSION['role'] ?? 'guest') . "\n";
echo "};\n";
echo "\n";
echo "console.log('Permissões carregadas:', window.userPermissions);\n";
?>