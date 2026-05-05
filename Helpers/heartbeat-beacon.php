<?php

require_once __DIR__ . '/Sessao.php';
require_once __DIR__ . '/../Conexao/conector.php';

header('Content-Type: application/json');

// Inicia sessão baseada no cookie token_sessao
if (!isset($_COOKIE['token_sessao'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sem sessão ativa']);
    exit;
}

$sessao = selecionarSessao($_COOKIE['token_sessao'], 1);

if (!$sessao) {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão inválida']);
    exit;
}

$conexao = new Conector();
$conn = $conexao->getConexao();

// Lê o corpo da requisição para diferenciar o tipo
$body = file_get_contents('php://input');
$data = json_decode($body, true) ?? [];
$requestType = $data['type'] ?? 'heartbeat';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($requestType === 'timeout') {
        // Timeout de inatividade - ENCERRA sessão
        $hora_fim = date('H:i:s');
        $stmt = $conn->prepare("UPDATE sessao SET se_valido = 0, hora_fim = ? WHERE id_sessao = ?");
        $stmt->bind_param("si", $hora_fim, $sessao['id_sessao']);
        $stmt->execute();

        if (!headers_sent()) {
            echo json_encode(['success' => true, 'action' => 'timeout']);
        }
    } elseif ($requestType === 'unload') {
        // Fechamento de aba / navegação - apenas registra último acesso
        // Não encerra a sessão ainda (pode ser navegação entre páginas)
        $stmt = $conn->prepare("UPDATE sessao SET ultima_atividade = NOW() WHERE id_sessao = ?");
        $stmt->bind_param("i", $sessao['id_sessao']);
        $stmt->execute();

        // Beacon não espera resposta, mas respondemos se fetch foi usado
        if (!headers_sent() && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => true, 'action' => 'unload']);
        }
    } else {
        // Heartbeat periódico (default) - apenas atualiza atividade
        $stmt = $conn->prepare("UPDATE sessao SET ultima_atividade = NOW() WHERE id_sessao = ?");
        $stmt->bind_param("i", $sessao['id_sessao']);
        $stmt->execute();

        if (!headers_sent()) {
            echo json_encode(['success' => true, 'action' => 'heartbeat']);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}
