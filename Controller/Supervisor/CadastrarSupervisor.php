<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Supervisor.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class CadastrarSupervisor
{
    private mysqli $conn;
    private Supervisor $supervisor;
    private Notificacao $notificacao;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->supervisor = new Supervisor();
        $this->notificacao = new Notificacao();
    }
    public function cadastrarSupervisor()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estagio/supervisor/criar?erros=" . "Método da Requisição Inválido.");
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/supervisor/criar?erros=" . "Token de segurança inválido. Recarregue a página e tente novamente.");
            exit();
        }

        try {
            
            $nome = trim($_POST['nomeSupervisor'] ?? '');
            $id_qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $user = isset($_POST['user']) ? (int) $_POST['user'] : null;
            $area = trim($_POST['area' ?? '']);

            $this->conn->begin_transaction();

            $result = $this->supervisor->getSupervisorByIdAndQual($this->conn, $user, $id_qualificacao);
            
            if($result && $result->num_rows > 0){
                $this->conn->rollback();
                header("Location: /estagio/supervisor/criar?erros=" . "Supervisor já registrado na qualificação selecionada.");
                exit();
            }

            $this->supervisor->setNome($nome);
            $this->supervisor->setId_Qualificacao($id_qualificacao);
            $this->supervisor->setUser($user);
            $this->supervisor->setArea($area);

            if (!$this->supervisor->salvar($this->conn)) {
                $this->conn->rollback();
                header("Location: /estagio/supervisor/criar?erros=" . "Erro ao cadastrar supervisor.");
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou um supervisor: " . $nome, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O Supervisor $nome foi cadastrado com sucesso.";
            
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();

            header("Location: /estagio/admin");
            exit();
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            header("LOCATION: /estagio/supervisor/criar?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$supervisor = new CadastrarSupervisor();
$supervisor->cadastrarSupervisor();
