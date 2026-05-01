<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/PedidoDeVisita.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarVisita
{
    private mysqli $conn;
    private Criptografia $criptografia;
    private PedidoDeVisita $pedido;

    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->pedido = new PedidoDeVisita();
        $this->criptografia = new Criptografia();
    }
    public function editarVisita()
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

            $id_visita = isset($_POST['id_visita']) && is_numeric($_POST['id_visita']) ? (int)$_POST['id_visita'] : null;
            $nome = trim($_POST['nome']) ?? '';
            $apelido = trim($_POST['apelido']) ?? '';
            $codigo_formando = isset($_POST['codigo_formando'])  && is_numeric($_POST['codigo_formando']) ? (int)$_POST['codigo_formando'] : null;
            $contactoFormando = trim($_POST['contactoFormando']) ?? '';
            $empresa = trim(strtoupper($_POST['empresa'])) ?? '';
            $endereco = trim($_POST['endereco']) ?? '';
            $nomeSupervisor = trim($_POST['nomeSupervisor']) ?? '';
            $contactoSupervisor = trim($_POST['contactoSupervisor']) ?? '';
            $dataHoraDaVisita = isset($_POST['dataHoraDaVisita']) && !empty(trim($_POST['dataHoraDaVisita']))
                ? trim($_POST['dataHoraDaVisita']) 
                : null;

            if (empty($id_visita) || empty($codigo_formando) || empty($empresa) || empty($nomeSupervisor) || empty($dataHoraDaVisita) || empty($contactoFormando) || empty($contactoSupervisor) || empty($nome) || empty($apelido) || empty($endereco)) {
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor, preencha todos os campos']);
            }

            $this->conn->begin_transaction();

            if ($this->pedido->actualizar($nome, $apelido, $codigo_formando, $this->criptografia->criptografar($contactoFormando), $empresa, $endereco, $nomeSupervisor, $this->criptografia->criptografar($contactoSupervisor), $dataHoraDaVisita, $id_visita, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pedido: ' . $this->conn->error]);
            }

            $this->conn->commit();
            $this->conn->close();
        } catch (Exception $e) {
            error_log('Erro em editarVisita.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarVisita();
$controller->editarVisita();