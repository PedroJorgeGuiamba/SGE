<?php
session_start();

require_once __DIR__ . '/../../Model/Resposta.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class RespostaCarta
{

    public function respostaCarta()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $resposta = new Resposta();

                $numeroRaw = $_POST['numero'] ?? '';
                $numero = filter_var($numeroRaw, FILTER_SANITIZE_NUMBER_INT);
                $numero = $numero !== false && $numero !== '' ? intval($numero) : 0;
                if ($numero <= 0) {
                    throw new Exception('Número inválido.');
                }
                $resposta->setNumero($numero);

                // Status: validar por whitelist
                $statusRaw = trim($_POST['status'] ?? '');
                $allowedStatuses = ['Aceito', 'Recusado', 'Pendente'];
                $status = '';
                $status = strtolower($statusRaw);
                if (!in_array($status, $allowedStatuses, true)) {
                    $status = 'Pendente';
                }
                $resposta->setStatus($status);

                // Data de resposta: aceitar formato YYYY-MM-DD ou vazio
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

                // Contacto do responsável: remover tags e escapar
                $contacto = trim($_POST['contactoResponsavel'] ?? '');
                $contacto = strip_tags($contacto);
                $contacto = htmlspecialchars($contacto, ENT_QUOTES, 'UTF-8');
                $resposta->setContactoResponsavel($contacto);

                // Datas de inicio/fim: validar formato YYYY-MM-DD ou vazio
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

                    // Registrar atividade já foi feito acima
                    // Definir mensagem de sucesso em sessão e redirecionar
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
