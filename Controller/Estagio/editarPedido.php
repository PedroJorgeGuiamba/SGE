<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/PedidoDeCarta.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';

class EditarPedido
{
    private mysqli $conn;
    private Criptografia $criptografia;
    private PedidoDeCarta $pedido;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->pedido = new PedidoDeCarta();
    }
    public function editarPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
        try {
            $this->conn->begin_transaction();

            $numero = isset($_POST['numero']) && is_numeric($_POST['numero']) ? (int)$_POST['numero'] : null;
            $nome = trim($_POST['nome'] ?? '');
            $apelido = trim($_POST['apelido'] ?? '');
            $codigo_formando = isset($_POST['codigo_formando']) && is_numeric($_POST['codigo_formando']) ? (int)$_POST['codigo_formando'] : null;
            $qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $codigo_turma = isset($_POST['codigo_turma']) && is_numeric($_POST['codigo_turma']) ? (int) $_POST['codigo_turma'] : null;
            $empresa = trim(strtoupper($_POST['empresa'])) ?? '';
            $contactoPrincipal = trim($_POST['contactoPrincipal']) ?? '';
            $contactoSecundario = trim($_POST['contactoSecundario']) ?? '';
            $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) ?? '';

            if (empty($numero) || empty($codigo_formando) || empty($empresa) || empty($qualificacao) || empty($codigo_turma) || empty($contactoPrincipal) || empty($contactoSecundario) || empty($nome) || empty($apelido)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor, insira um endereço de email válido']);
            }

            if ($this->pedido->actualizar($nome, $apelido, $codigo_formando, $qualificacao, $codigo_turma, $empresa, $this->criptografia->criptografar($contactoPrincipal), $this->criptografia->criptografar($contactoSecundario), $this->criptografia->criptografar($email), $numero, $this->conn)) {
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

$controller = new EditarPedido();
$controller->editarPedido();
