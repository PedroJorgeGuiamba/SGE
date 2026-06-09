<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/FormadorModulo.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

class CadastrarLecionarModulo
{
    private mysqli $conn;
    private Criptografia $criptografia;
    private FormadorModulo $lecionar;
    private Notificacao $notificacao;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->lecionar = new FormadorModulo();
        $this->notificacao = new Notificacao();
    }

    public function cadastrarLecionarModulo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estagio/formador/lecionar?erros=" . urlencode("Método inválido."));
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/formador/lecionar?erros=" . "Token de segurança inválido. Recarregue a página e tente novamente.");
            exit();
        }

        try {

            $id_formador = isset($_POST['formador']) ? (int) $_POST['formador'] : null;
            $id_modulo_turma = isset($_POST['modulo_turma']) ? (int) $_POST['modulo_turma'] : null;
            $data_inicio = $_POST['data_inicio'] ?? '';
            $data_fim = $_POST['data_fim'] ?? '';
            $carga_horaria = isset($_POST['carga_horaria']) ? (int) $_POST['carga_horaria'] : null;

            $this->conn->begin_transaction();

            $this->lecionar->setIdFormador($id_formador);
            $this->lecionar->setIdModuloTurma($id_modulo_turma);
            $this->lecionar->setDataI($data_inicio);
            $this->lecionar->setDataF($data_fim);
            $this->lecionar->setCargaHoraria($carga_horaria);

            if(!$this->lecionar->salvarLecionarModulo($this->conn)){
                header("LOCATION: /estagio/formador/lecionar?erros=" . urlencode("Erro no sistema: " . $this->conn->error));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma formador: ", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "Módulo/Horário e formador associado.";

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

            header("LOCATION: /estagio/formador/lecionar?erros=" . urlencode("Erro no sistema." . $e->getMessage()));
            exit();
        }
    }
}

$formador = new CadastrarLecionarModulo();
$formador->cadastrarLecionarModulo();
