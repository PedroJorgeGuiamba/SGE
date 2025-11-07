<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../middleware/auth.php'; // Verifica autenticação, se necessário
$conexao = new Conector();
$conn = $conexao->getConexao();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['numeros']) && is_array($_POST['numeros'])) {
        $ids = array_filter(array_map('intval', $_POST['numeros']));
        if (empty($ids)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Números das respostas não fornecidos ou inválidos']);
            exit;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM resposta_carta WHERE id_resposta IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Respostas removidas com sucesso']);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Erro ao remover as respostas']);
        }
        $stmt->close();
    } elseif (isset($_POST['numero'])) {
        $numero = (int)$_POST['numero'];
        if ($numero > 0) {
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
            echo json_encode(['error' => 'Número da resposta inválido']);
        }
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