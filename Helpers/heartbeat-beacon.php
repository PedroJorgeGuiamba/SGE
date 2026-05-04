<?php

require_once __DIR__ . '/Sessao.php';
require_once __DIR__ . '/../Conexao/conector.php';

// Inicia sessão baseada no cookie token_sessao
if (isset($_COOKIE['token_sessao'])) {
    $sessao = selecionarSessao($_COOKIE['token_sessao'], 1);
    
    if ($sessao) {
        $conexao = new Conector();
        $conn = $conexao->getConexao();
        
        // Se for heartbeat normal (tem dados POST e NÃO é beacon)
        // Beacon não espera resposta, então não precisa de lógica extra
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Para requisições normais (fetch), atualiza atividade
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
                $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
                // É beacon - apenas encerra sessão
                $hora_fim = date('H:i:s');
                $stmt = $conn->prepare("UPDATE sessao SET se_valido = 0, hora_fim = ? WHERE id_sessao = ?");
                $stmt->bind_param("si", $hora_fim, $sessao['id_sessao']);
                $stmt->execute();
            } else {
                // É heartbeat normal - apenas atualiza atividade
                $stmt = $conn->prepare("UPDATE sessao SET ultima_atividade = NOW() WHERE id_sessao = ?");
                $stmt->bind_param("i", $sessao['id_sessao']);
                $stmt->execute();
            }
            
            // Para requisições normais, retorna JSON
            if (!headers_sent()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            }
        }
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Sem sessão ativa']);
}