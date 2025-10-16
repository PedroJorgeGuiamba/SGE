<?php
session_start();

require_once __DIR__ . '/../../Model/PedidoDeCarta.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Resposta.php';

class FormularioDeCartaDeEstagio
{
    public function cartaDeEstagio()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pedido = new PedidoDeCarta();

                $codigoFormando = isset($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
                $codigoTurma = isset($_POST['turma']) ? (int) $_POST['turma'] : null;
                $codigoQualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;

                $dadosFormando = $pedido->buscarNomeEApelido($codigoFormando);

                $pedido->setCodigoFormando($codigoFormando);
                $pedido->setTurma($codigoTurma);
                $pedido->setQualificacao($codigoQualificacao);
                $pedido->setDataPedido($_POST['dataPedido'] ?? '');
                $pedido->setHoraPedido($_POST['horaPedido'] ?? '');
                $pedido->setEmpresa(trim($_POST['empresa'] ?? ''));
                $pedido->setContactoPrincipal(trim($_POST['contactoPrincipal'] ?? ''));
                $pedido->setContactoSecundario(trim($_POST['contactoSecundario'] ?? ''));
                $pedido->setEmail(trim($_POST['email'] ?? ''));


                if ($dadosFormando === null) {
                    echo "Formando não encontrado.";
                    return;
                }

                $nome = $dadosFormando['nome'];
                $apelido = $dadosFormando['apelido'];

                $resposta = new Resposta();
                $resposta->setStatus('Pendente');

                $conexao = new Conector();
                $conn = $conexao->getConexao();
                if ($pedido->salvar($nome, $apelido)) {
                    if (isset($_SESSION['sessao_id'])) {
                        registrarAtividade($_SESSION['sessao_id'], "pedido de carta de estágio realizado", "CRIACAO");
                    }
                    $sql = "SELECT numero FROM pedido_carta ORDER BY numero DESC LIMIT 1";
                    $result = $conn->query($sql);
                    $lastIdFromQuery = $result && $result->num_rows > 0 ? $result->fetch_assoc()['numero'] : 0;

                    $resposta->setNumero($lastIdFromQuery);

                    if ($resposta->salvar()) {
                        if (isset($_SESSION['sessao_id'])) {
                            registrarAtividade($_SESSION['sessao_id'], "resposta de carta de estágio realizado", "CRIACAO");
                        }
                    }

                    header("Location: GerarPdfCarta.php?numero=$lastIdFromQuery");
                } else {
                    echo "Erro ao enviar pedido.";
                }
            } catch (Exception $e) {
                echo "Erro no sistema: " . $e->getMessage();
            }
        } else {
            echo "Método inválido.";
        }
    }
}

$controller = new FormularioDeCartaDeEstagio();
$controller->cartaDeEstagio();