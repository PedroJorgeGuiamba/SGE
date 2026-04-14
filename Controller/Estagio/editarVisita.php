<?php
session_start();
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/PedidoDeVisita.php';

class EditarVisita
{
    public function editarVisita()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
        try {
            $conexao = new Conector();
            $conn = $conexao->getConexao();
            $pedido = new PedidoDeVisita();
            
            $id_visita = $_POST['id_visita'];
            $nome = $_POST['nome'];
            $apelido = $_POST['apelido'];
            $codigo_formando = $_POST['codigo_formando'];
            $contactoFormando = $_POST['contactoFormando'];
            $empresa = $_POST['empresa'];
            $endereco = $_POST['endereco'];
            $nomeSupervisor = $_POST['nomeSupervisor'];
            $contactoSupervisor = $_POST['contactoSupervisor'];
            $dataHoraDaVisita = $_POST['dataHoraDaVisita'];
            
            if ($pedido->actualizar($nome, $apelido, $codigo_formando, $contactoFormando, $empresa, $endereco, $nomeSupervisor, $contactoSupervisor, $dataHoraDaVisita, $id_visita)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pedido: ' . $conn->error]);
            }
            
            $conn->close();
        } catch (Exception $e) {
            error_log('Erro em editarVisita.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarVisita();
$controller->editarVisita();