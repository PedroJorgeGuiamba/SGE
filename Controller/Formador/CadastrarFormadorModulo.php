<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/FormadorModulo.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarFormadorModulo
{
    private mysqli $conn;
    private Notificacao $notificacao;
    private FormadorModulo $formador;

    public function __construct()
    {
        $this->formador = new FormadorModulo();
        $this->notificacao = new Notificacao();
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }
    public function cadastrarFormadorModulo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode($e));
            }

            $modulo = $_POST['modulo'] && is_numeric($_POST['modulo']) ? (int) $_POST['modulo'] : null;
            $formador = $_POST['formador'] && is_numeric($_POST['formador']) ? (int) $_POST['formador'] : null;

            // Validação
            if (empty($modulo) && empty($modulo)) {
                header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode("Erro: O código do modulo e do Formador devem ser um número válido."));
                exit();
            }
            
            if ($modulo < 0 && $formador < 0) {
                header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode("Erro: O código do modulo deve ser um número válido."));
                exit();
            }
            $this->conn->begin_transaction();
            
            $this->formador->setIdModulo($modulo);
            $this->formador->setIdFormador($formador);

            $resultado = $this->formador->buscarFormadorModulo($this->conn);

            if($resultado !== null){
                $this->conn->rollback();
                header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode("Erro: Formador e Módulo já associados."));
                exit();
            }
            
            if (!$this->formador->salvar($this->conn)) {
                $this->conn->rollback();
                header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode("Erro ao Associar Formador ao Modulo. " . $this->conn->error));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Associou um formador a um módulo", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem_criterio = "Associou com sucesso um formador a um módulo.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem_criterio);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();
            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/formador/associar-modulo?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}

$modulo = new CadastrarFormadorModulo();
$modulo->cadastrarFormadorModulo();