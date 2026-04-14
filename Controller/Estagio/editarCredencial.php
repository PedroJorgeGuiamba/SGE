<?php
session_start();
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/PedidoDeCredencial.php';

class EditarPedido
{
    public function editarPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
        try {
            $conexao = new Conector();
            $conn = $conexao->getConexao();
            $pedido = new PedidoDeCredencial();

            $id_credencial = $_POST['id_credencial'];
            $nome = $_POST['nome'];
            $apelido = $_POST['apelido'];
            $codigo_formando = $_POST['codigo_formando'];
            $contactoFormando = $_POST['contactoFormando'];
            $empresa = $_POST['empresa'];
            $email = $_POST['email'];
            
            
            if ($pedido->actualizar($nome, $apelido, $codigo_formando, $contactoFormando, $empresa, $email, $id_credencial)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pedido: ' . $conn->error]);
            }
            
            $conn->close();
        } catch (Exception $e) {
            error_log('Erro em editarPedido.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarPedido();
$controller->editarPedido();