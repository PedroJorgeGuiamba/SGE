<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Modulo.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarCriterioAvaliacao
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
    public function cadastrarCriterioAvaliacao()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/criterio-avaliacao/criar?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/criterio-avaliacao/criar?erros=" . urlencode($e));
            }

            $modulo = $_POST['modulo'] ?? (int)$_POST['modulo'];
            $tipo_avaliacao = $_POST['tipo_avaliacao'] ?? (int)$_POST['tipo_avaliacao'];
            $percentual = $_POST['percentual'] ?? (int)$_POST['percentual'];
            $observacoes = $_POST['observacoes'] ?? '';

            // Validação
            if (empty($modulo)) {
                header("LOCATION: /estagio/criterio-avaliacao/criar?erros=" . urlencode("Erro: O código do modulo deve ser um número válido."));
                exit();
            }
            
            if ($percentual < 0  && $percentual > 100) {
                header("LOCATION: /estagio/criterio-avaliacao/criar?erros=" . urlencode("Erro: O percentual do módulo deve ser superior a 0 e inferior a 100."));
                exit();
            }

            $this->conn->begin_transaction();

            $this->modulo->setIdModulo($modulo);
            $this->modulo->setTipoAvaliacao($tipo_avaliacao);
            $this->modulo->setPercentualAvaliacao($percentual);
            $this->modulo->setObservacaoAvaliacao($observacoes);
            
            if (!$this->modulo->salvarCriterioAvaliacao($this->conn)) {
                header("LOCATION: /estagio/criterio-avaliacao/criar?erros=" . urlencode("Erro ao cadastrar o Criterio de Avaliacao. " . $this->conn->error));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou um Criterio de Avaliação", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem_criterio = "O Criterio de Avaliação foi registrado com sucesso e associado a Competencia.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem_criterio);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();
            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/criterio-avaliacao/criar?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}


$modulo = new CadastrarCriterioAvaliacao();
$modulo->cadastrarCriterioAvaliacao();
