<?php
require_once __DIR__ . '/../Conexao/conector.php';

function registrarAtividade($sessaoId, $descricao, $tipo = 'LOGIN') {
    $conexao = new Conector();
    $conn = $conexao->getConexao();
    $conn->begin_transaction();

    if ($sessaoId === null) {
        $stmt = $conn->prepare("INSERT INTO actividade (descricao, tipo) VALUES (?, ?)");
        $stmt->bind_param("ss", $descricao, $tipo);
    } else {
        $stmt = $conn->prepare("INSERT INTO actividade (id_sessao, descricao, tipo) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $sessaoId, $descricao, $tipo);
    }

    if (!$stmt->execute()) {
        error_log("Erro ao registrar atividade: " . $stmt->error);
        $stmt->close();
        $conn->rollback();
    }
    
    $stmt->close();
    $conn->commit();
    $conn->close();
}