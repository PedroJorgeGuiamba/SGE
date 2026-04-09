<?php
session_start();

require_once __DIR__ . '/../../Model/PedidoDeVisita.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class FormularioDeVisita
{
    public function pedidoVisita()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/View/estagio/formularioDeVisita.php?erros=" . urlencode("Metodo da Requisicao Invalido"));
            exit();
        }
            
        $conn = null;
            
        try {
            $token = $_POST['csrf_token'] ?? '';
            CSRFProtection::validateToken($token);
            
            date_default_timezone_set('Africa/Maputo');

            $conexao = new Conector();
            $conn = $conexao->getConexao();
            $conn->begin_transaction();

            $pedido = new PedidoDeVisita($conn);
            
            $codigoFormando = isset($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
            $empresa = strtoupper(trim($_POST['empresa'] ?? ''));

            $dadosFormando = $pedido->buscarNomeEApelido((int) $codigoFormando);
            if ($dadosFormando === null) {
                throw new RuntimeException("Formando nao encontrado");
            }
            
            $anoAtual = (int) date('Y');
            $sql = "
                SELECT DISTINCT COALESCE(MAX(id_pedido_carta), 0) AS ultimo_id
                FROM pedido_carta
                WHERE YEAR(data_do_pedido) = ?
                AND codigo_formando = ?
                AND empresa = ?
                FOR UPDATE
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $anoAtual, $codigoFormando, $empresa);
            $stmt->execute();
            $resultadoNumero = $stmt->get_result()->fetch_assoc();

            if ($resultadoNumero['ultimo_id'] <= 0) {
                throw new RuntimeException("Nao foi possivel obter o ID do pedido");
            }

            $pedido->setIdPedido($resultadoNumero['ultimo_id']);
            $pedido->setCodigoFormando((int) $codigoFormando);
            $pedido->setContactoFormando(trim($_POST['contactoFormando'] ?? ''));
            $pedido->setEmpresa($empresa);
            $pedido->setEndereco(trim($_POST['endereco']));
            $pedido->setNomeSupervisor(trim($_POST['nome_supervisor']));
            $pedido->setContactoSupervisor(trim($_POST['contacto_supervisor'] ?? ''));
            $pedido->setDataHoraVisita(trim($_POST['datahora'] ?? ''));
            $pedido->setDataPedido(date('Y-m-d'));

            $nome = $dadosFormando['nome'];
            $apelido = $dadosFormando['apelido'];

            if (!$pedido->salvar($nome, $apelido)) {
                throw new RuntimeException("Erro ao Salvar Pedido");
            }

            if (isset($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "pedido de visita de estagio realizado", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O seu pedido de Visita foi processado com sucesso. Ainda esta pendente de verificacao por um administrador.";
                $stmtNotificacao = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
                $stmtNotificacao->bind_param("is", $_SESSION['usuario_id'], $mensagem);
                $stmtNotificacao->execute();
            }

            $conn->commit();

            if (($_SESSION['role'] ?? '') !== 'formando') {
                header("Location: /estagio/View/Estagio/listaDePedidos.php");
            } else {
                header("Location: /estagio/View/Estagio/formularioDeVisita.php");
            }
            exit();
        } catch (Throwable $e) {
            if ($conn instanceof mysqli) {
                try {
                    $conn->rollback();
                } catch (Throwable $rollbackError) {
                }
            }

            header("LOCATION: /estagio/View/estagio/formularioDeVisita.php?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$controller = new FormularioDeVisita();
$controller->pedidoVisita();