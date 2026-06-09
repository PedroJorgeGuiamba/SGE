<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Modulo.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarCriterioDesempenho
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
    public function cadastrarCriterioDesempenho()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/criterio-desempenho/criar?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/criterio-desempenho/criar?erros=" . urlencode($e));
            }

            $elemento = $_POST['elemento'] ?? '';
            $descricao_criterio = $_POST['descricao_criterio'] ?? '';
            $descricao_evidencia = $_POST['descricao_evidencia'] ?? '';

            // Validação
            if (empty($elemento)) {
                header("LOCATION: /estagio/criterio-desempenho/criar?erros=" . urlencode("Erro: O código do modulo deve ser um número válido."));
                exit();
            }

            $this->conn->begin_transaction();

            $this->modulo->setIdCompetencia($elemento);
            if(!empty($descricao_criterio)){
                $this->modulo->setDescricaoDesemp($descricao_criterio);
                
                if (!$this->modulo->salvarCriterioDesempenho($this->conn)) {
                    header("LOCATION: /estagio/criterio-desempenho/criar?erros=" . urlencode("Erro ao cadastrar o Criterio de Desempenho. " . $this->conn->error));
                    exit();
                }

                if (!empty($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Cadastrou um Criterio de Desempenho: " . $descricao_criterio, "CRIACAO");
                }

                if (!empty($_SESSION['usuario_id'])) {
                    $mensagem_criterio = "O Criterio de Desempenho:  $descricao_criterio foi registrado com sucesso e associado a Competencia.";

                    $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                    $this->notificacao->setMensagem($mensagem_criterio);
                    $this->notificacao->salvar($this->conn);
                }
            }

            if(!empty($descricao_evidencia)){
                $this->modulo->setDescricaoEvid($descricao_evidencia);
                
                if (!$this->modulo->salvarEvidenciaRequerida($this->conn)) {
                    header("LOCATION: /estagio/criterio-desempenho/criar?erros=" . urlencode("Erro ao cadastrar a Evidencia Requerida " . $this->conn->error));
                    exit();
                }

                if (!empty($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma Evidencia Requerida: " . $descricao_criterio, "CRIACAO");
                }

                if (!empty($_SESSION['usuario_id'])) {
                    $mensagem_evidencia = "A Evidencia Requerida :  $descricao_criterio foi registrada com sucesso e associado a Competencia.";

                    $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                    $this->notificacao->setMensagem($mensagem_evidencia);
                    $this->notificacao->salvar($this->conn);
                }
            }


            

            $this->conn->commit();
            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/criterio-desempenho/criar?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}


$modulo = new CadastrarCriterioDesempenho();
$modulo->cadastrarCriterioDesempenho();
