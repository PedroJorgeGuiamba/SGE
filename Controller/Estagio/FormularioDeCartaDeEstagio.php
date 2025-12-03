<?php
session_start();

require_once __DIR__ . '/../../Model/PedidoDeCarta.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Resposta.php';
require_once __DIR__ . '/../../Model/Empresa.php';

class FormularioDeCartaDeEstagio
{
    public function cartaDeEstagio()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $conexao = new Conector();
            $conn = $conexao->getConexao();

            $conn->query("LOCK TABLES pedido_carta WRITE");

            try {
                $pedido = new PedidoDeCarta();
                $empresaM = new Empresa();

                $codigoFormando = isset($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
                $codigoTurma = isset($_POST['turma']) ? (int) $_POST['turma'] : null;
                $codigoQualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
                
                $dadosFormando = $pedido->buscarNomeEApelido($codigoFormando);
                $empresa = strtoupper(trim($_POST['empresa'] ?? ''));
                
                if ($dadosFormando === null) {
                    echo "Formando não encontrado.";
                    return;
                }

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

                $anoAtual = date('Y');;


                $sql = "SELECT COALESCE(MAX(numero), 0) AS ultimo_numero 
                        FROM pedido_carta 
                        WHERE YEAR(data_do_pedido) = ? 
                        FOR UPDATE";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $anoAtual);
                $stmt->execute();
                $resultado = $stmt->get_result()->fetch_assoc();

                $novoSequencial = $resultado['ultimo_numero'] + 1;


                $pedido->setNumero($novoSequencial);
                $pedido->setCodigoFormando($codigoFormando);
                $pedido->setTurma($codigoTurma);
                $pedido->setQualificacao($codigoQualificacao);
                $pedido->setEmpresa($empresa);
                $pedido->setContactoPrincipal(trim($_POST['contactoPrincipal'] ?? ''));
                $pedido->setContactoSecundario(trim($_POST['contactoSecundario'] ?? ''));
                $pedido->setEmail(trim($_POST['email'] ?? ''));
                $pedido->setHoraPedido(date("h:i"));
                $pedido->setDataPedido(date('Y-m-d'));
                $empresaM->setNome($empresa);
                $empresaM->setAbr($empresa);
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

                    // Verificar se a empresa já existe pelo nome OU abreviatura
                    $sqlVerificarEmpresa = "SELECT id_empresa FROM empresa WHERE nome = ? OR abreviatura = ?";
                    $stmtVerificar = $conn->prepare($sqlVerificarEmpresa);
                    $stmtVerificar->bind_param("ss", $empresa, $empresa);
                    $stmtVerificar->execute();
                    $resultVerificar = $stmtVerificar->get_result();
                    if ($resultVerificar->num_rows == 0) {

                        
                        if($empresaM->salvar()){
                            if (isset($_SESSION['sessao_id'])) {
                                registrarAtividade($_SESSION['sessao_id'], "Empresa registrada com sucesso", "CRIACAO");
                            }
                        }
                    }

                    $sql = "SELECT id_pedido_carta FROM pedido_carta ORDER BY numero DESC LIMIT 1";
                    $result = $conn->query($sql);
                    $lastIdFromQuery = $result && $result->num_rows > 0 ? $result->fetch_assoc()['id_pedido_carta'] : 0;

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

                    $conn->query("UNLOCK TABLES");

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