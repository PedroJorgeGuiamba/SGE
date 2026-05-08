<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Modulo.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarResultadoDeAprendizagem
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
    public function cadastrarResultadoDeAprendizagem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/resultado-aprendizagem/criar?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/resultado-aprendizagem/criar?erros=" . urlencode($e));
            }

            $codigoResultado = $_POST['codigo_resultado'] ?? '';
            $descricao = $_POST['descricao_resultado'] ?? '';
            $tipoResultado = $_POST['tipo_resultado'] ?? '';
            $observacoes = $_POST['carga_horaria'] ?? '';

            // Validação
            if (empty($codigoResultado)) {
                header("LOCATION: /estagio/resultado-aprendizagem/criar?erros=" . urlencode("Erro: O código do modulo deve ser um número válido."));
                exit();
            }

            if(empty($descricao)){
                header("LOCATION: /estagio/resultado-aprendizagem/criar?erros=" . urlencode("Erro: A descrição do modulo é obrigatória."));
                exit();
            }

            $this->conn->begin_transaction();

            $this->modulo->setCodigoResultado($codigoResultado);
            $this->modulo->setDescricaoResultado($descricao);
            $this->modulo->setTipoResultado($tipoResultado);
            $this->modulo->setObservacoesResultado($observacoes);

            if (!$this->modulo->salvarResultadoAprend($this->conn)) {
                header("LOCATION: /estagio/resultado-aprendizagem/criar?erros=" . urlencode("Erro ao cadastrar a R.A. " . $this->conn->error));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou um R.A: " . $descricao, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O R.A :  $descricao foi registrado com sucesso e associado a qualificacao.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();
            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/resultado-aprendizagem/criar?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}

$modulo = new CadastrarResultadoDeAprendizagem();
$modulo->cadastrarResultadoDeAprendizagem();
