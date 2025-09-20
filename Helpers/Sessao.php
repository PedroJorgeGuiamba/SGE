<?php
require_once __DIR__ . '/../Conexao/conector.php';

function iniciarSessao($utilizador_id)
{
    $conexao = new Conector();
    $conn = $conexao->getConexao();

    $token = bin2hex(random_bytes(32));
    $data = date('Y-m-d');
    $hora_inicio = date('H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP desconhecido';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent desconhecido';

    if ($ip === '::1') {
        $ip = '127.0.0.1';
    }

    $stmt = $conn->prepare("INSERT INTO sessao (data, hora_inicio, token, se_valido, utilizador_id, ip_address, user_agent) VALUES (?, ?, ?, 1, ?, ?, ?)");
    $stmt->bind_param("sssiss", $data, $hora_inicio, $token, $utilizador_id, $ip, $userAgent);
    $stmt->execute();

    $sessaoId = $stmt->insert_id;

    $_SESSION['token'] = $token;
    $_SESSION['sessao_id'] = $sessaoId;
    $_SESSION['usuario_id'] = $utilizador_id;
    setcookie("token_sessao", $token, [
        'expires' => time() + 86400,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);


    return $sessaoId;
}

function terminarSessao()
{
    session_start();

    if (!isset($_SESSION['sessao_id'])) {
        header("Location: ../View/Login.php");
        exit();
    }

    $conexao = new Conector();
    $conn = $conexao->getConexao();

    $hora_fim = date('H:i:s');

    $stmt = $conn->prepare("UPDATE sessao SET se_valido = 0, hora_fim = ? WHERE id_sessao = ?");
    $stmt->bind_param("si", $hora_fim, $_SESSION['sessao_id']);
    $stmt->execute();

    require_once __DIR__ . '/Actividade.php';
    registrarAtividade($_SESSION['sessao_id'], "Logout do usuário", "LOGOUT");
    
    session_unset();
    session_destroy();
    
    setcookie("token_sessao", "", time() - 3600, "/");
    header("Location: /estagio/View/Login.php");
    exit();
}

function selecionarSessao($token, $seValido = 1)
{
    $conexao = new Conector();
    $conn = $conexao->getConexao();

    $stmt = $conn->prepare("SELECT * FROM sessao WHERE token = ? AND se_valido = ?");
    $stmt->bind_param("si", $token, $seValido);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $sessao = $resultado->fetch_assoc();

    $stmt->close();

    return $sessao ?: null;
}