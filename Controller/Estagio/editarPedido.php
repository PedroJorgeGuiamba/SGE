<?php
session_start();
require_once __DIR__ . '/../../Conexao/conector.php';

class EditarPedido
{
    public function editarPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $numero = $_POST['numero'];
                $nome = $_POST['nome'];
                $apelido = $_POST['apelido'];
                $codigo_formando = $_POST['codigo_formando'];
                $qualificacao = $_POST['qualificacao'];
                $codigo_turma = $_POST['codigo_turma'];
                $empresa = $_POST['empresa'];
                $contactoPrincipal = $_POST['contactoPrincipal'];
                $contactoSecundario = $_POST['contactoSecundario'];
                $email = $_POST['email'];
                
                $conexao = new Conector();
                $conn = $conexao->getConexao();
                
                $sql = "UPDATE pedido_carta SET
                        nome = ?,
                        apelido = ?,
                        codigo_formando = ?,
                        qualificacao = ?,
                        codigo_turma = ?,
                        empresa = ?,
                        contactoPrincipal = ?,
                        contactoSecundario = ?,
                        email = ?
                        WHERE numero = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssssi",
                    $nome,
                    $apelido,
                    $codigo_formando,
                    $qualificacao,
                    $codigo_turma,
                    $empresa,
                    $contactoPrincipal,
                    $contactoSecundario,
                    $email,
                    $numero
                );
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pedido: ' . $conn->error]);
                }
                
                $stmt->close();
                $conn->close();
            } catch (Exception $e) {
                error_log('Erro em editarPedido.php: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
    }
}

$controller = new EditarPedido();
$controller->editarPedido();