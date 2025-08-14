<?php
require_once __DIR__ . '/../Helpers/Sessao.php';
require_once __DIR__ . '/../Conexao/conector.php';

class AuthMiddleware
{
    public function verificarAutenticacao()
    {
        if (!isset($_COOKIE["token_sessao"])) {
            return false;
        }

        $token = $_COOKIE["token_sessao"];
        $sessao = selecionarSessao($token, 1); // Usa a função já existente

        if (!$sessao) {
            return false;
        }

        // Identifica perfil e armazena na sessão
        if (!empty($sessao['id_cliente'])) {
            $_SESSION['cliente_id'] = $sessao['id_cliente'];
            $_SESSION['perfil'] = 'cliente';
        } elseif (!empty($sessao['id_utilizador'])) {
            $_SESSION['utilizador_id'] = $sessao['id_utilizador'];

            // Buscar perfil do utilizador
            $conexao = new Conector();
            $conn = $conexao->getConexao();

            $stmt = $conn->prepare("SELECT perfil FROM utilizador WHERE id = ?");
            $stmt->bind_param("i", $sessao['id_utilizador']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $perfil = $resultado->fetch_assoc()['perfil'] ?? null;

            $_SESSION['perfil'] = $perfil; // admin ou funcionario
        }

        $_SESSION['token'] = $token;

        return true;
    }
}
?>
