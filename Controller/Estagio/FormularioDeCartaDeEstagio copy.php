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

                // Verifica limite mensal de 5 pedidos
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
                    echo "<script>alert('Limite mensal de 5 pedidos atingido. Tente novamente no próximo mês.'); window.location.href='/estagio/View/Estagio/formularioDeCartaDeEstagio.php';</script>";
                    return;
                }

                // === CÁLCULO DO NÚMERO SEQUENCIAL DO ANO (REINICIA TODO ANO) ===
                // $anoAtual = date('Y');
                $anoAtual = 2028;

                $conn->query("LOCK TABLES pedido_carta WRITE");

                $sql = "SELECT COALESCE(MAX(numero), 0) AS ultimo_numero 
                        FROM pedido_carta 
                        WHERE YEAR(data_do_pedido) = ? 
                        FOR UPDATE";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $anoAtual);
                $stmt->execute();
                $resultado = $stmt->get_result()->fetch_assoc();
                
                $numeroCarta = $resultado['ultimo_numero'] + 1; // ← 1, 2, 3... reinicia todo ano

                // === PREPARA OS DADOS DO PEDIDO ===
                $pedido->setNumero($numeroCarta); // ← guarda o número sequencial inteiro
                $pedido->setCodigoFormando($codigoFormando);
                $pedido->setTurma($codigoTurma);
                $pedido->setQualificacao($codigoQualificacao);
                $pedido->setDataPedido($_POST['dataPedido'] ?? date('Y-m-d'));
                $pedido->setHoraPedido($_POST['horaPedido'] ?? date('H:i:s'));
                $pedido->setEmpresa($empresa);
                $pedido->setContactoPrincipal(trim($_POST['contactoPrincipal'] ?? ''));
                $pedido->setContactoSecundario(trim($_POST['contactoSecundario'] ?? ''));
                $pedido->setEmail(trim($_POST['email'] ?? ''));

                $nome = $dadosFormando['nome'];
                $apelido = $dadosFormando['apelido'];

                $resposta = new Resposta();
                $resposta->setStatus('Pendente');
                $resposta->setStatusEstagio('Pendente');

                // === SALVA O PEDIDO E CRIA RESPOSTA ===
                if ($pedido->salvar($nome, $apelido)) {
                    if (isset($_SESSION['sessao_id'])) {
                        registrarAtividade($_SESSION['sessao_id'], "pedido de carta de estágio realizado", "CRIACAO");
                    }

                    // CORREÇÃO DEFINITIVA: usa o ID real do registo inserido
                    $idPedidoReal = $conn->insert_id; // ← Este é o valor que a FK espera!

                    $resposta->setNumero($idPedidoReal); // ← Agora a foreign key aceita!

                    if ($resposta->salvarEstadoDoEstagio()) {
                        if (isset($_SESSION['sessao_id'])) {
                            registrarAtividade($_SESSION['sessao_id'], "resposta de carta de estágio realizado", "CRIACAO");
                        }
                    }

                    // Número bonito para mostrar ao utilizador e no PDF
                    $numeroBonito = $anoAtual . '/' . sprintf("%04d", $numeroCarta); // ex: 2025/0001

                    $mensagem = "O seu pedido foi processado com sucesso. Número da carta: <strong>$numeroBonito</strong>. Aguarde aprovação.";
                    $stmt_notificacao = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
                    $stmt_notificacao->bind_param("is", $_SESSION['usuario_id'], $mensagem);
                    $stmt_notificacao->execute();

                    // Liberta o lock
                    $conn->query("UNLOCK TABLES");

                    // Redireciona com o número bonito
                    if ($_SESSION['role'] != 'formando') {
                        header("Location: GerarPdfCarta.php?numero=" . urlencode($numeroBonito));
                    } else {
                        header("Location: /estagio/View/Estagio/formularioDeCartaDeEstagio.php?sucesso=1");
                    }
                    exit();

                } else {
                    $conn->query("UNLOCK TABLES");
                    echo "Erro ao enviar pedido.";
                }

            } catch (Exception $e) {
                $conn->query("UNLOCK TABLES");
                echo "Erro no sistema: " . $e->getMessage();
            }
        } else {
            echo "Método inválido.";
        }
    }
}

$controller = new FormularioDeCartaDeEstagio();
$controller->cartaDeEstagio();