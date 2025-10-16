<?php
session_start();
require_once __DIR__ . '/../../Conexao/conector.php';

class EditarResposta
{
    public function editarResposta()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_resposta = filter_var( $_POST['id_resposta'], FILTER_SANITIZE_NUMBER_INT);
                $numero_carta = filter_var($_POST['numero_carta'], FILTER_SANITIZE_NUMBER_INT);
                $status_resposta = trim($_POST['status_resposta']);
                $data_resposta = trim($_POST['data_resposta']);
                $contato_responsavel = trim($_POST['contato_responsavel']);
                $data_inicio_estagio = trim($_POST['data_inicio_estagio']);
                $data_fim_estagio = trim($_POST['data_fim_estagio']);
                $status_estagio = trim($_POST['status_estagio']);
                
                $conexao = new Conector();
                $conn = $conexao->getConexao();
                
                $sql = "UPDATE resposta_carta SET
                        status_resposta = ?,
                        data_resposta = ?,
                        contato_responsavel = ?,
                        data_inicio_estagio = ?,
                        data_fim_estagio = ?,
                        status_estagio = ?
                        WHERE id_resposta = ? AND numero_carta = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssii",
                    $status_resposta,
                    $data_resposta,
                    $contato_responsavel,
                    $data_inicio_estagio,
                    $data_fim_estagio,
                    $status_estagio,
                    $id_resposta,
                    $numero_carta
                );
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Resposta atualizado com sucesso!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar Resposta: ' . $conn->error]);
                }
                
                $stmt->close();
                $conn->close();
            } catch (Exception $e) {
                error_log('Erro em editarResposta.php: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
    }
}

$controller = new EditarResposta();
$controller->editarResposta();