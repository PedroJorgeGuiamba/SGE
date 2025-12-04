<?php
session_start();
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Estagio.php';

class EditarResposta
{
    public function editarResposta()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $estagio = new Estagio();

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

                if (!$stmt->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar resposta: ' . $stmt->error]);
                    return;
                }
                $stmt->close();

                if($status_resposta = "Aceito"){
                    $sql = "SELECT
                                p.*,
                                s.id_supervisor as id_s,
                                e.id_empresa as id_e
                            FROM pedido_carta p
                            LEFT JOIN supervisor s ON s.id_qualificacao = p.qualificacao
                            LEFT JOIN empresa e ON e.nome = p.empresa
                            WHERE p.id_pedido_carta = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $numero_carta);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 0) {
                        die("Pedido não encontrado.");
                    }

                    $dados = $result->fetch_assoc();
                    $empresa = htmlspecialchars($dados['id_e'] ?? '');
                    $codigo = htmlspecialchars($dados['codigo_formando'] ?? '');
                    $supervisor = $dados['id_s'];

                    $estagio->setStatus($status_estagio);
                    $estagio->setId_resposta($id_resposta);
                    $estagio->setDataI($data_inicio_estagio);
                    $estagio->setDataF($data_fim_estagio);
                    $estagio->setCodigo($codigo);
                    $estagio->setId_supervisor($supervisor);
                    $estagio->setId_empresa($empresa);

                    if (method_exists($estagio, 'salvarNoEdit')) {
//                        $estagio->salvar();

                        if($estagio->salvar()){
                            $sqlEstagio = "SELECT id_estagio FROM estagio ORDER BY id_estagio DESC LIMIT 1";
                            $result = $conn->query($sqlEstagio);
                            $lastIdFromQuery = $result && $result->num_rows > 0 ? $result->fetch_assoc()['id_estagio'] : 0;

                            $sqlINSERT = "INSERT INTO supervisor_estagio(id_estagio, id_supervisor) VALUES(?,?)";
                            $stmtINSERT = $conn->prepare($sqlINSERT);
                            $stmtINSERT->bind_param("ii", $lastIdFromQuery, $supervisor);
                            $stmtINSERT->execute();
                        }
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Resposta atualizado com sucesso!']);
                
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