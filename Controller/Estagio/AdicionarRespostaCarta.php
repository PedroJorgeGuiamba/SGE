<?php
session_start();

require_once __DIR__ . '/../../Model/Resposta.php';
require_once __DIR__ . '/../../Model/Estagio.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class RespostaCarta
{

    public function respostaCarta()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $resposta = new Resposta();
                $estagio = new Estagio();

                $numeroRaw = $_POST['numero'] ?? '';
                $numero = filter_var($numeroRaw, FILTER_SANITIZE_NUMBER_INT);
                $numero = $numero !== false && $numero !== '' ? intval($numero) : 0;
                if ($numero <= 0) {
                    throw new Exception('Número inválido.');
                }
                $resposta->setNumero($numero);

                $statusRaw = trim($_POST['status'] ?? '');
                $allowedStatuses = ['Aceito', 'Recusado', 'Pendente'];
                $status = $statusRaw;
                if (!in_array($status, $allowedStatuses, true)) {
                    $status = 'Pendente';
                }
                $resposta->setStatus($status);

                $statusEstagioRaw = trim($_POST['statusEstagio'] ?? '');
                $allowedEstagioStatuses = ['Concluido', 'Nao Concluido'];
                $statusEstagio = $statusEstagioRaw;
                if (!in_array($statusEstagio, $allowedEstagioStatuses, true)) {
                    $$statusEstagio = '';
                }
                $resposta->setStatusEstagio($statusEstagio);

                $dataRespostaRaw = trim($_POST['dataResposta'] ?? '');
                $dataResposta = '';
                if ($dataRespostaRaw !== '') {
                    $d = DateTime::createFromFormat('Y-m-d', $dataRespostaRaw);
                    if ($d && $d->format('Y-m-d') === $dataRespostaRaw) {
                        $dataResposta = $dataRespostaRaw;
                    } else {
                        throw new Exception('Data de resposta inválida. Use: YYYY-MM-DD');
                    }
                }
                $resposta->setDataResposta($dataResposta);

                $contacto = trim($_POST['contactoResponsavel'] ?? '');
                $contacto = strip_tags($contacto);
                $contacto = htmlspecialchars($contacto, ENT_QUOTES, 'UTF-8');
                $resposta->setContactoResponsavel($contacto);

                $dataInicioRaw = trim($_POST['dataInicio'] ?? '');
                $dataFimRaw = trim($_POST['dataFim'] ?? '');

                $dataInicio = '';
                $dataFim = '';

                if ($dataInicioRaw !== '') {
                    $di = DateTime::createFromFormat('Y-m-d', $dataInicioRaw);
                    if ($di && $di->format('Y-m-d') === $dataInicioRaw) {
                        $dataInicio = $dataInicioRaw;
                    } else {
                        throw new Exception('Data de início inválida. Use: YYYY-MM-DD');
                    }
                }

                if ($dataFimRaw !== '') {
                    $df = DateTime::createFromFormat('Y-m-d', $dataFimRaw);
                    if ($df && $df->format('Y-m-d') === $dataFimRaw) {
                        $dataFim = $dataFimRaw;
                    } else {
                        throw new Exception('Data de fim inválida. Use: YYYY-MM-DD');
                    }
                }

                $resposta->setDataInicio($dataInicio);
                $resposta->setDataFim($dataFim);

                if ($resposta->salvar()) {
                    if (isset($_SESSION['sessao_id'])) {
                        registrarAtividade($_SESSION['sessao_id'], "resposta de carta de estágio realizado", "CRIACAO");
                    }

                    $sql = "SELECT id_resposta FROM resposta_carta ORDER BY numero DESC LIMIT 1";
                    $result = $conn->query($sql);
                    $lastIdFromQuery = $result && $result->num_rows > 0 ? $result->fetch_assoc()['id_resposta'] : 0;

                    if($status == 'Aceito'){
                        $sql = "SELECT
                                p.*,
                                s.id_supervisor as id_s,
                                e.id_empresa as id_e
                            FROM pedido_carta p
                            LEFT JOIN supervisor s ON s.id_qualificacao = p.qualificacao
                            LEFT JOIN empresa e ON e.nome = p.empresa
                            WHERE p.id_pedido_carta = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $numero);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 0) {
                            die("Pedido não encontrado.");
                        }

                        $dados = $result->fetch_assoc();
                        $empresa = htmlspecialchars($dados['id_e'] ?? '');
                        $codigo = htmlspecialchars($dados['codigo_formando'] ?? '');
                        $supervisor = $dados['id_s'];

                        $estagio->setStatus($status);
                        $estagio->setId_resposta($lastIdFromQuery);
                        $estagio->setDataI($dataInicio);
                        $estagio->setDataF($dataFim);
                        $estagio->setCodigo($codigo);
                        $estagio->setId_supervisor($supervisor);
                        $estagio->setId_empresa($empresa);

                        if (method_exists($estagio, 'salvarNoEdit')) {
                            if($estagio->salvar()){
                                $sqlEstagio = "SELECT id_estagio FROM estagio ORDER BY id_estagio DESC LIMIT 1";
                                $result = $conn->query($sqlEstagio);
                                $lastIdFromQueryINSERT = $result && $result->num_rows > 0 ? $result->fetch_assoc()['id_estagio'] : 0;

                                $sqlINSERT = "INSERT INTO supervisor_estagio(id_estagio, id_supervisor) VALUES(?,?)";
                                $stmtINSERT = $conn->prepare($sqlINSERT);
                                $stmtINSERT->bind_param("ii", $lastIdFromQueryINSERT, $supervisor);
                                $stmtINSERT->execute();
                            }
                        }
                        
                    }

                    $_SESSION['flash_success'] = 'resposta de carta enviado com sucesso!';
                    header("Location: /estagio/View/estagio/respostaCarta.php");
                    exit;
                } else {
                    echo "Erro ao enviar resposta.";
                }
            } catch (Exception $e) {
                echo "Erro no sistema: " . $e->getMessage();
            }
        } else {
            echo "Método inválido.";
        }
    }
}

// Executar processamento
$controller = new RespostaCarta();
$controller->respostaCarta();
