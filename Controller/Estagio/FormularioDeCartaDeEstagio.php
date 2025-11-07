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
                $empresa = strtoupper(trim($_POST['empresa'] ?? ''));
                
                if ($dadosFormando === null) {
                    echo "Formando não encontrado.";
                    return;
                }

                $conexao = new Conector();
                $conn = $conexao->getConexao();

                $sqlLimite = "
                    SELECT COUNT(*) AS total 
                    FROM pedido_carta 
                    WHERE codigo_formando = ? 
                    AND MONTH(data_do_pedido) = MONTH(CURRENT_DATE()) 
                    AND YEAR(data_do_pedido) = YEAR(CURRENT_DATE())
                ";

                $stmtLimite = $conn->prepare($sqlLimite);
                $stmtLimite->bind_param("i", $codigoFormando);
                $stmtLimite->execute();
                $resultado = $stmtLimite->get_result()->fetch_assoc();

                if ($resultado['total'] >= 5) {
                    echo "<script>alert('⚠️ Limite mensal de 5 pedidos atingido. Tente novamente no próximo mês.'); window.location.href='/estagio/View/Estagio/formularioDeCartaDeEstagio.php';</script>";
                    return;
                }

                $pedido->setCodigoFormando($codigoFormando);
                $pedido->setTurma($codigoTurma);
                $pedido->setQualificacao($codigoQualificacao);
                $pedido->setDataPedido($_POST['dataPedido'] ?? '');
                $pedido->setHoraPedido($_POST['horaPedido'] ?? '');
                $pedido->setEmpresa($empresa);
                $pedido->setContactoPrincipal(trim($_POST['contactoPrincipal'] ?? ''));
                $pedido->setContactoSecundario(trim($_POST['contactoSecundario'] ?? ''));
                $pedido->setEmail(trim($_POST['email'] ?? ''));


                $nome = $dadosFormando['nome'];
                $apelido = $dadosFormando['apelido'];

                $resposta = new Resposta();
                $resposta->setStatus('Pendente');
                $resposta->setStatusEstagio('Pendente');

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
                    
                    if ($resposta->salvarEstadoDoEstagio()) {
                        if (isset($_SESSION['sessao_id'])) {
                            registrarAtividade($_SESSION['sessao_id'], "resposta de carta de estágio realizado", "CRIACAO");
                        }
                    }
                    
                    $mensagem = "O seu pedido foi processado com sucesso. O pedido possui o #$lastIdFromQuery. Ainda está pendente de verificação por um administrador, Aguarde pela nootificação avisando que está disponível.";
                    $stmt_notificacao = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
                    $stmt_notificacao->bind_param("is", $_SESSION['usuario_id'], $mensagem);
                    $stmt_notificacao->execute();

                    if($_SESSION['role'] != 'formando'){
                        header("Location: GerarPdfCarta.php?numero=$lastIdFromQuery");
                    }else{
                        header("Location: /estagio/View/Estagio/formularioDeCartaDeEstagio.php");
                    }

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