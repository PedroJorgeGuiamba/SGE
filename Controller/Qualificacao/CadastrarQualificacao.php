<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Qualificacao.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarQualificacao
{
    private mysqli $conn;
    private Notificacao $notificacao;
    private Qualificacao $qualificacao;

    public function __construct()
    {
        $this->qualificacao = new Qualificacao();
        $this->notificacao = new Notificacao();
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }
    public function cadastrarQualificacao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/qualificacao/criar?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/qualificacao/criar?erros=" . urlencode($e));
            }

            $qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $descricao = trim($_POST['descricao'] ?? '');
            $nivel = trim($_POST['nivel'] ?? '');

            // Validação
            if ($qualificacao === null || $qualificacao <= 0) {
                header("LOCATION: /estagio/qualificacao/criar?erros=" . urlencode("Erro: O código da qualificacao deve ser um número válido."));
                exit();
            }
            if (empty($nivel)) {
                header("LOCATION: /estagio/qualificacao/criar?erros=" . urlencode("Erro: O nivel da qualificacao é obrigatória."));
                exit();
            }

            $this->qualificacao->setQualificacao($qualificacao);
            $this->qualificacao->setNivel($nivel);
            $this->qualificacao->setDescricao($descricao);

            if (!$this->qualificacao->salvar($this->conn)) {
                header("LOCATION: /estagio/qualificacao/criar?erros=" . urlencode("Erro ao cadastrar a qualificacao."));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma qualificacao: " . $descricao, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "A qualificação de $descricao foi registrada com sucesso.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/qualificacao/criar?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}

$qualificacao = new CadastrarQualificacao();
$qualificacao->cadastrarQualificacao();
