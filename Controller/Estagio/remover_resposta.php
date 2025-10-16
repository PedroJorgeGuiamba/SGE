<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../middleware/auth.php'; // Verifica autenticação, se necessário

$conexao = new Conector();
$conn = $conexao->getConexao();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = isset($_POST['numero']) ? (int)$_POST['numero'] : null;

    if ($numero) {
        $sql = "DELETE FROM resposta_carta WHERE id_resposta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $numero);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Resposta removida com sucesso']);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Erro ao remover a resposta']);
        }

        $stmt->close();
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Número da resposta não fornecido']);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Método não permitido']);
}

$conn->close();
?>