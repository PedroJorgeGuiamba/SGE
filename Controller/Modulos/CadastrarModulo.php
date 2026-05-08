<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Modulo.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarModulo
{
    private mysqli $conn;
    private Notificacao $notificacao;
    private Modulo $modulo;

    public function __construct()
    {
        $this->modulo = new Modulo();
        $this->notificacao = new Notificacao();
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }
    public function cadastrarModulo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/modulo/criar?erros=" . urlencode($e));
            }

            $codigoModulo = $_POST['codigo_modulo'] ?? '';
            $id_qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $descricao = $_POST['descricao_modulo'] ?? '';
            $cargaHoraria = isset($_POST['carga_horaria']) ? (int) $_POST['carga_horaria'] : null;

            // Validação
            if (empty($codigoModulo)) {
                header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("Erro: O código do modulo deve ser um número válido."));
                exit();
            }
            if ($cargaHoraria === null || $cargaHoraria <= 0) {
                header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("Erro: O carga horaria do modulo é obrigatória."));
                exit();
            }
            if(empty($descricao)){
                header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("Erro: A descrição do modulo é obrigatória."));
                exit();
            }

            $this->conn->begin_transaction();

            $this->modulo->setCodigoModulo($codigoModulo);
            $this->modulo->setDescricaoModulo($descricao);
            $this->modulo->setCargaHoraria($cargaHoraria);

            if (!$this->modulo->salvarModulo($this->conn)) {
                header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("Erro ao cadastrar a modulo. " . $this->conn->error));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou um modulo: " . $descricao, "CRIACAO");
            }

            $id_modulo = $this->conn->insert_id;

            $this->modulo->setIdModulo($id_modulo);
            $this->modulo->setIdQualificacao($id_qualificacao);

            if (!$this->modulo->salvarQualModulo($this->conn)){
                header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("Erro ao associar modulo a qualificacao. " . $this->conn->error));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Associou O módulo " . $id_modulo . " a qualificacao " . $id_qualificacao, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O módulo:  $descricao foi registrado com sucesso e associado a qualificacao.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();
            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/modulo/criar?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}

$modulo = new CadastrarModulo();
$modulo->cadastrarModulo();
