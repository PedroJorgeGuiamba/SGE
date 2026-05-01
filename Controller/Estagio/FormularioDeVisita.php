<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../../Model/PedidoDeVisita.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';

class FormularioDeVisita
{
    private Notificacao $notificacao;
    private mysqli $conn;
    private Criptografia $criptografia;
    private PedidoDeVisita $pedido;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->notificacao = new Notificacao();
        $this->criptografia = new Criptografia();
        $this->pedido = new PedidoDeVisita();
    }
    public function pedidoVisita()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/visita/criar?erros=" . urlencode("Metodo da Requisicao Invalido"));
            exit();
        }

        try {
            $token = $_POST['csrf_token'] ?? '';
            CSRFProtection::validateToken($token);

            date_default_timezone_set('Africa/Maputo');

            $this->conn->begin_transaction();

            $codigoFormando = isset($_POST['codigoFormando']) && is_numeric($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
            $empresa = strtoupper(trim($_POST['empresa'] ?? ''));

            if (empty($codigoFormando) || empty($empresa)) {
                header("Location: /estagio/visita/criar?erros=" . htmlspecialchars("Por favor, certifique-se de preencher todos os campos."));
                exit();
            }

            $dadosFormando = $this->pedido->buscarNomeEApelido((int) $codigoFormando, $this->conn);
            if ($dadosFormando === null) {
                $this->conn->rollback();
                throw new RuntimeException("Formando nao encontrado");
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

            $sqlData = "
                SELECT COUNT(*)
                FROM visita_estagio
                WHERE codigo_formando = ?
                AND empresa = ?
                AND id_pedido_carta = ?
                AND MONTH(data_do_pedido) = MONTH(CURRENT_DATE())
                AND YEAR(data_do_pedido) = YEAR(CURRENT_DATE())
            ";

            $stmtData = $this->conn->prepare($sqlData);
            $stmtData->bind_param("isi", $codigoFormando, $empresa, $resultadoNumero['ultimo_id']);
            $stmtData->execute();
            $resultadoData = $stmtData->get_result();

            $row = $resultadoData->fetch_row();
            $totalVisitas = $row[0];

            if ($totalVisitas >= 2) {
                $this->conn->rollback();
                throw new RuntimeException("Nao foi possivel registrar visita. Limite de Pedidos excedidos.");
            }

            $this->pedido->setIdPedido($resultadoNumero['ultimo_id']);
            $this->pedido->setCodigoFormando((int) $codigoFormando);
            $this->pedido->setContactoFormando($this->criptografia->criptografar(trim($_POST['contactoFormando'] ?? '')));
            $this->pedido->setEmpresa($empresa);
            $this->pedido->setEndereco(trim($_POST['endereco']) ?? '');
            $this->pedido->setNomeSupervisor(trim($_POST['nome_supervisor']) ?? '');
            $this->pedido->setContactoSupervisor($this->criptografia->criptografar(trim($_POST['contacto_supervisor'] ?? '')));
            $this->pedido->setDataHoraVisita(trim($_POST['datahora'] ?? ''));
            $this->pedido->setDataPedido(date('Y-m-d'));

            $nome = $dadosFormando['nome'];
            $apelido = $dadosFormando['apelido'];

            if (!$this->pedido->salvar($nome, $apelido, $this->conn)) {
                $this->conn->rollback();
                throw new RuntimeException("Erro ao Salvar Pedido");
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "pedido de visita de estagio realizado", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O seu pedido de Visita foi processado com sucesso. Ainda esta pendente de verificacao por um administrador.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();

            if (($_SESSION['role'] ?? '') !== 'formando') {
                header("Location: /estagio/visita/listar");
            } else {
                header("Location: /estagio/visita/criar");
            }
            exit();
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                }
            }

            header("LOCATION: /estagio/visita/criar?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$controller = new FormularioDeVisita();
$controller->pedidoVisita();
