<?php
require_once __DIR__ . '/../Conexao/conector.php';

function registrarAtividade($sessaoId, $descricao, $tipo = 'LOGIN', $duracao = null) {
    // Verificar se pode aceitar NULL ou modificar estrutura da tabela
    $conexao = new Conector();
    $conn = $conexao->getConexao();
    if ($sessaoId === null) {
        // Usar query alternativa sem id_sessao
        $stmt = $conn->prepare("INSERT INTO actividade (descricao, tipo, duracao) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $descricao, $tipo, $duracao);
    } else {
        // Query normal com id_sessao
        $stmt = $conn->prepare("INSERT INTO actividade (id_sessao, descricao, tipo, duracao) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $sessaoId, $descricao, $tipo, $duracao);
    }
}