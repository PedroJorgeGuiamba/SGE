<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/PedidoDeCredencial.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarPedidoCredencial
{
    private mysqli $conn;
    private Criptografia $criptografia;
    private PedidoDeCredencial $pedido;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->pedido = new PedidoDeCredencial();
    }
    public function editarPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                echo json_encode(['success' => false, 'message' => $e]);
            }

            $this->conn->begin_transaction();

            $id_credencial = isset($_POST['id_credencial']) && is_numeric($_POST['id_credencial']) ? (int)$_POST['id_credencial'] : null;
            $nome = trim($_POST['nome'] ?? '');
            $apelido = trim($_POST['apelido'] ?? '');
            $codigo_formando = isset($_POST['codigo_formando']) && is_numeric($_POST['codigo_formando']) ? (int)$_POST['codigo_formando'] : null;
            $contactoFormando = trim($_POST['contactoFormando']) ?? '';
            $empresa = trim(strtoupper($_POST['empresa']) ?? '');
            $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) ?? '');

            if (empty($id_credencial) || empty($codigo_formando) || empty($empresa) || empty($nome) || empty($apelido) || empty($contactoFormando)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            if (empty($email)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor, insira um endereço de email válido']);
            }

            if ($this->pedido->actualizar($nome, $apelido, $codigo_formando, $this->criptografia->criptografar($contactoFormando), $empresa, $this->criptografia->criptografar($email), $id_credencial, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pedido: ' . $this->conn->error]);
            }

            $this->conn->commit();
            $this->conn->close();
        } catch (Exception $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            error_log('Erro em editarPedido.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarPedidoCredencial();
$controller->editarPedido();
