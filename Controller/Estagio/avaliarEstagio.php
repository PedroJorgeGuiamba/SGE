<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/avaliarEstagio.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class AvaliarEstagioController
{
    private AvaliarEstagio $avaliarEstagio;
    private Notificacao $notificacao;
    private mysqli $conn;
    public function __construct() {
        $this->avaliarEstagio = new AvaliarEstagio();
        $this->notificacao = new Notificacao();
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }
    public function avaliarEstagio()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/avaliacao-estagio/criar?erros=" . urlencode('Método da Requisição Inválido'));
            exit;
        }
        try {
            
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/estagio/criar?erros=" . $e);
                exit();
            }

            $codigoFormando = isset($_POST['codigoFormando']) && is_numeric($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
            $codigoQualificacao  = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao'])   ? (int) $_POST['qualificacao']   : null;
            $codigoTurma         = isset($_POST['turma']) && is_numeric($_POST['turma'])    ? (int) $_POST['turma']          : null;
            $empresa = strtoupper(trim($_POST['empresa'] ?? ''));
            $anoTurma  = isset($_POST['anoTurma']) && is_numeric($_POST['anoTurma'])   ? (int) $_POST['anoTurma']   : null;
            $resultado = strtoupper(trim($_POST['resultado'] ?? ''));
            $comentario = strtoupper(trim($_POST['comentario'] ?? ''));

            $this->conn->begin_transaction();

            $uploadDirRelatorio = __DIR__ . "/../../uploads/Formando/$codigoFormando/Relatorio/";
            if (!file_exists($uploadDirRelatorio)) {
                mkdir($uploadDirRelatorio, 0777, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            // Processar Carta
            $RelatorioPath = null;
            if (isset($_FILES['relatorio_path']) && $_FILES['relatorio_path']['error'] == UPLOAD_ERR_OK) {
                $RelatorioName = basename($_FILES['relatorio_path']['name']);
                $CartaExt = strtolower(pathinfo($RelatorioName, PATHINFO_EXTENSION));
                if (!in_array($_FILES['relatorio_path']['type'], $allowedTypes)) {
                    throw new Exception("Tipo de arquivo do Relatório não permitido.");
                }
                $newRelatorioName = uniqid() . '.' . $CartaExt;
                $targetFileRelatorio = $uploadDirRelatorio . $newRelatorioName;
                if (move_uploaded_file($_FILES['relatorio_path']['tmp_name'], $targetFileRelatorio)) {
                    $RelatorioPath = "/estagio/uploads/Formando/$codigoFormando/Relatorio/" . $newRelatorioName;
                } else {
                    header("LOCATION: /estagio/avaliacao-estagio/criar?erros=" . urlencode("Erro ao fazer upload do documento da Carta."));
                }
            }

            $anoAtual = (int) date('Y');
            $sql = "
                SELECT DISTINCT COALESCE(MAX(id_pedido_carta), 0) AS ultimo_id
                FROM pedido_carta
                WHERE YEAR(data_do_pedido) = ?
                AND codigo_formando = ?
                AND empresa = ?
                FOR UPDATE
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iis", $anoAtual, $codigoFormando, $empresa);
            $stmt->execute();
            $resultadoNumero = $stmt->get_result()->fetch_assoc();

            if ($resultadoNumero['ultimo_id'] <= 0) {
                $this->conn->rollback();
                throw new RuntimeException("Nao foi possivel obter o ID do pedido");
            }

            $this->avaliarEstagio->setIdPedido($resultadoNumero['ultimo_id']);
            $this->avaliarEstagio->setCodigoFormando($codigoFormando);
            $this->avaliarEstagio->setEmpresa($empresa);
            $this->avaliarEstagio->setQualificacao($codigoQualificacao);
            $this->avaliarEstagio->setTurma($codigoTurma);
            $this->avaliarEstagio->setAnoTurma($anoTurma);
            $this->avaliarEstagio->setDocPath($RelatorioPath);
            $this->avaliarEstagio->setResultado($resultado);
            $this->avaliarEstagio->setComentario($comentario);

            if (!$this->avaliarEstagio->salvar($this->conn)) {
                header("LOCATION: /estagio/avaliacao-estagio/criar?erros=" . urlencode('Erro ao salvar a avaliação no banco de dados.'));
                exit;
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Avaliação de estágio registrada para resposta ID:", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "A sua avaliação foi processada com sucesso. Ainda esta pendente de verificacao por um supervisor.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();

            if (($_SESSION['role'] ?? '') !== 'formando') {
                header("Location: /estagio/avaliacao-estagio/listar");
            } else {
                header("Location: /estagio/avaliacao-estagio/criar");
            }
            exit();
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            header("LOCATION: /estagio/avaliacao-estagio/criar?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$controller = new AvaliarEstagioController();
$controller->avaliarEstagio();