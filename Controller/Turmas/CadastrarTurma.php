<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Turma.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class CadastrarTurma
{
    private mysqli $conn;
    private Turma $turma;
    private Notificacao $notificacao;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->turma = new Turma();
        $this->notificacao = new Notificacao();
    }
    public function cadastrarTurma()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estagio/turma/criar?erros=" . "Método de de requisição inválido.");
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/turma/criar?erros=" . "Token de segurança inválido. Recarregue a página e tente novamente.");
            exit();
        }

        try {
            $this->conn->begin_transaction();

            $codigo = isset($_POST['codigoTurma']) ? (int) $_POST['codigoTurma'] : null;
            $nome = trim($_POST['nomeTurma'] ?? '');
            $id_qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $id_curso = isset($_POST['curso']) ? (int) $_POST['curso'] : null;

            $this->turma->setCodigo($codigo);
            $this->turma->setNome($nome);
            $this->turma->setCodigoQualificacao($id_qualificacao);
            $this->turma->setCodigoCurso($id_curso);

            if (!$this->turma->salvar($this->conn)) {
                $this->conn->rollback();
                header("Location: /estagio/turma/criar?erros=" . urldecode("Erro ao Registrar Turma.."));
                exit();
            }
            if (isset($_SESSION['sessao_id']) || $_SESSION['sessao_id']) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma turma: " . $nome, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "A turma $nome foi registrada com sucesso.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
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

            header("LOCATION: /estagio/turma/criar?erros=" . urlencode("Erro no sistema."));
            exit();
        }

        return $turma;
    }
}

$turma = new CadastrarTurma();
$turma->cadastrarTurma();
