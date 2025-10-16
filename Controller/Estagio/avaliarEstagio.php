<?php
session_start();

require_once __DIR__ . '/../../Model/avaliarEstagio.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class AvaliarEstagioController
{
    public function avaliarEstagio()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $avaliarEstagio = new AvaliarEstagio();

                $id_resposta = isset($_GET['numero']) ? filter_var($_GET['numero'], FILTER_SANITIZE_NUMBER_INT) : 0;
                if ($id_resposta <= 0) {
                    throw new Exception('ID da resposta inválido.');
                }
                $avaliarEstagio->setId_resposta(intval($id_resposta));

                $conexao = new Conector();
                $conn = $conexao->getConexao();
                $stmt = $conn->prepare("SELECT numero_carta FROM resposta_carta WHERE id_resposta = ?");
                $stmt->bind_param("i", $id_resposta);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception('Resposta não encontrada.');
                }
                $row = $result->fetch_assoc();
                $numero_pedido = $row['numero_carta'];
                $avaliarEstagio->setNumPedido($numero_pedido);
            
                $resultadoRaw = trim($_POST['resultado'] ?? '');
                $allowedEstagioStatuses = ['A', 'NA'];
                $resultado = $resultadoRaw;
                if (!in_array($resultado, $allowedEstagioStatuses, true)) {
                    throw new Exception('Resultado inválido. Use A ou NA.');
                }
                $avaliarEstagio->setResultado($resultado);

                $stmt = $conn->prepare("SELECT qualificacao FROM pedido_carta WHERE numero = ?");
                $stmt->bind_param("i", $numero_pedido);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    throw new Exception('Pedido não encontrado.');
                }
                $row = $result->fetch_assoc();
                $qualificacao = $row['qualificacao'];
                $avaliarEstagio->setQualificacao($qualificacao);
                
                $uploadDirDoc = "../../uploads/avaliacaoEstagios/";
                if (!file_exists($uploadDirDoc)) {
                    mkdir($uploadDirDoc, 0777, true);
                }
                // Processar relatorio de estagio.
                $docFilePath = null;
                if (isset($_FILES['imagem_doc_path']) && $_FILES['imagem_doc_path']['error'] == UPLOAD_ERR_OK) {
                    $docFileName = basename($_FILES['imagem_doc_path']['name']);
                    $docFileExt = strtolower(pathinfo($docFileName, PATHINFO_EXTENSION));
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    if (!in_array($_FILES['imagem_doc_path']['type'], $allowedTypes)) {
                        throw new Exception("Tipo de arquivo do relatório não permitido.");
                    }
                    $newdocFileName = uniqid() . '.' . $docFileExt;
                    $targetFileAlvarDoc = $uploadDirDoc . $newdocFileName;
                    if (move_uploaded_file($_FILES['imagem_doc_path']['tmp_name'], $targetFileAlvarDoc)) {
                        $docFilePath = "/estagio/uploads/avaliacaoEstagios/" . $newdocFileName;
                        $avaliarEstagio->setDocPath($docFilePath);
                    } else {
                        throw new Exception("Erro ao fazer upload do documento.");
                    }
                }

                if ($avaliarEstagio->salvar()) {
                    if (isset($_SESSION['sessao_id'])) {
                        registrarAtividade($_SESSION['sessao_id'], "Avaliação de estágio registrada para resposta ID: $id_resposta", "CRIACAO");
                    }
                    $response = [
                        'success' => true,
                        'message' => 'Avaliação registrada com sucesso!',
                        'redirect' => '/estagio/View/estagio/respostaCarta.php'
                    ];
                } else {
                    throw new Exception('Erro ao salvar a avaliação no banco de dados.');
                }
            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => 'Erro ao processar a avaliação: ' . $e->getMessage()
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $_SESSION['flash_error'] = 'Método de requisição inválido.';
            header("Location: /estagio/View/estagio/respostaCarta.php");
            exit;
        }
    }
}

$controller = new AvaliarEstagioController();
$controller->avaliarEstagio();