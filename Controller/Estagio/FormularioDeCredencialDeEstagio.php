<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../../Model/PedidoDeCredencial.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';

class FormularioDeCredencialDeEstagio
{
    private Notificacao $notificacao;
    private mysqli $conn;
    private Criptografia $criptografia;
    private PedidoDeCredencial $pedido;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->notificacao = new Notificacao();
        $this->criptografia = new Criptografia();
        $this->pedido = new PedidoDeCredencial();
    }
    public function pedidoCredencial()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/credencial/criar?erros=" . urlencode("Metodo da Requisicao Invalido"));
            exit();
        }

        try {
            $token = $_POST['csrf_token'] ?? '';
            CSRFProtection::validateToken($token);

            date_default_timezone_set('Africa/Maputo');

            $this->conn->begin_transaction();

            $codigoFormando = isset($_POST['codigoFormando']) && is_numeric($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
            $empresa = strtoupper(trim($_POST['empresa'] ?? ''));
            $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) ?? '');

            if (empty($codigoFormando) || empty($empresa)) {
                header("Location: /estagio/credencial/criar?erros=" . htmlspecialchars("Por favor, certifique-se de preencher todos os campos."));
                exit();
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /estagio/credencial/criar?erros=" . htmlspecialchars("Por favor, insira um endereço de email válido."));
                exit();
            }


            // Configuração do diretório de upload
            $uploadDirCarta = __DIR__ . "/../../uploads/Formando/$codigoFormando/CartaResposta/";
            if (!file_exists($uploadDirCarta)) {
                mkdir($uploadDirCarta, 0777, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            // Processar Carta
            $CartaPath = null;
            if (isset($_FILES['carta_path']) && $_FILES['carta_path']['error'] == UPLOAD_ERR_OK) {
                $CartaName = basename($_FILES['carta_path']['name']);
                $CartaExt = strtolower(pathinfo($CartaName, PATHINFO_EXTENSION));
                if (!in_array($_FILES['carta_path']['type'], $allowedTypes)) {
                    throw new Exception("Tipo de arquivo da Carta não permitido.");
                }
                $newCartaName = uniqid() . '.' . $CartaExt;
                $targetFileCarta = $uploadDirCarta . $newCartaName;
                if (move_uploaded_file($_FILES['carta_path']['tmp_name'], $targetFileCarta)) {
                    $CartaPath = "/estagio/uploads/Formando/$codigoFormando/CartaResposta/" . $newCartaName;
                    
                } else {
                    throw new Exception("Erro ao fazer upload do documento da Carta.");
                }
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

            $this->pedido->setIdPedido($resultadoNumero['ultimo_id']);
            $this->pedido->setCodigoFormando((int) $codigoFormando);
            $this->pedido->setContactoFormando($this->criptografia->criptografar(trim($_POST['contactoFormando'] ?? '')));
            $this->pedido->setEmail($this->criptografia->criptografar($email));
            $this->pedido->setEmpresa($empresa);
            $this->pedido->setDataPedido(date('Y-m-d'));
            $this->pedido->setCartaPath($CartaPath);

            $nome = trim($dadosFormando['nome']) ?? '';
            $apelido = trim($dadosFormando['apelido']) ?? '';

            if (!$this->pedido->salvar($nome, $apelido, $this->conn)) {
                $this->conn->rollback();
                throw new RuntimeException("Erro ao Salvar Pedido");
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "pedido de visita de estagio realizado", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O seu pedido de Credencial foi processado com sucesso. Ainda esta pendente de verificacao por um administrador.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();

            if (($_SESSION['role'] ?? '') !== 'formando') {
                header("Location: /estagio/credencial/listar");
            } else {
                header("Location: /estagio/credencial/criar");
            }
            exit();
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                }
            }

            header("LOCATION: /estagio/credencial/criar?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$controller = new FormularioDeCredencialDeEstagio();
$controller->pedidoCredencial();
