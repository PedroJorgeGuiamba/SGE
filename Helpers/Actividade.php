<?php
require_once __DIR__ . '/../Conexao/conector.php';

function registrarAtividade($sessaoId, $descricao, $tipo = 'LOGIN', $duracao = null)
{
    $conexao = new Conector();
    $conn = $conexao->getConexao();

    $stmt = $conn->prepare("INSERT INTO actividade (id_sessao, descricao, tipo, duracao)
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $sessaoId, $descricao, $tipo, $duracao);
    $stmt->execute();
}
