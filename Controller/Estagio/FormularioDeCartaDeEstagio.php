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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/View/estagio/formularioDeCartaDeEstagio.php?erros=" . urlencode("Metodo da Requisicao Invalido"));
            exit();
        }

        $conn = null;

        try {
            date_default_timezone_set('Africa/Maputo');

            $conexao = new Conector();
            $conn = $conexao->getConexao();
            $conn->begin_transaction();

            $pedido = new PedidoDeCarta($conn);
            $empresaM = new Empresa();
            $resposta = new Resposta($conn);

            $codigoFormando = isset($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
            $codigoTurma = isset($_POST['turma']) ? (int) $_POST['turma'] : null;
            $codigoQualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $empresa = strtoupper(trim($_POST['empresa'] ?? ''));

            $dadosFormando = $pedido->buscarNomeEApelido((int) $codigoFormando);
            if ($dadosFormando === null) {
                throw new RuntimeException("Formando nao encontrado");
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
            $resultadoLimite = $stmtLimite->get_result()->fetch_assoc();

            if ((int) ($resultadoLimite['total'] ?? 0) >= 5) {
                throw new RuntimeException("Limite mensal de 5 pedidos atingido. Tente novamente no proximo mes.");
            }

            $anoAtual = (int) date('Y');
            $sql = "
                SELECT COALESCE(MAX(numero), 0) AS ultimo_numero
                FROM pedido_carta
                WHERE YEAR(data_do_pedido) = ?
                FOR UPDATE
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $anoAtual);
            $stmt->execute();
            $resultadoNumero = $stmt->get_result()->fetch_assoc();
            $novoSequencial = ((int) ($resultadoNumero['ultimo_numero'] ?? 0)) + 1;

            $pedido->setNumero($novoSequencial);
            $pedido->setCodigoFormando((int) $codigoFormando);
            $pedido->setTurma((int) $codigoTurma);
            $pedido->setQualificacao((int) $codigoQualificacao);
            $pedido->setEmpresa($empresa);
            $pedido->setContactoPrincipal(trim($_POST['contactoPrincipal'] ?? ''));
            $pedido->setContactoSecundario(trim($_POST['contactoSecundario'] ?? ''));
            $pedido->setEmail(trim($_POST['email'] ?? ''));
            $pedido->setHoraPedido(date("H:i:s"));
            $pedido->setDataPedido(date('Y-m-d'));

            $empresaM->setNome($empresa);
            $empresaM->setAbr($empresa);

            $nome = $dadosFormando['nome'];
            $apelido = $dadosFormando['apelido'];

            $resposta->setStatus('Pendente');
            $resposta->setStatusEstagio('Pendente');

            if (!$pedido->salvar($nome, $apelido)) {
                throw new RuntimeException("Erro ao Salvar Pedido");
            }

            // Captura o id_pedido_carta imediatamente apos inserir o pedido.
            $idPedidoCarta = (int) $conn->insert_id;
            if ($idPedidoCarta <= 0) {
                throw new RuntimeException("Nao foi possivel obter o ID do pedido");
            }

            if (isset($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "pedido de carta de estagio realizado", "CRIACAO");
            }

            $sqlVerificarEmpresa = "SELECT id_empresa FROM empresa WHERE nome = ? OR abreviatura = ?";
            $stmtVerificar = $conn->prepare($sqlVerificarEmpresa);
            $stmtVerificar->bind_param("ss", $empresa, $empresa);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result();

            if ($resultVerificar->num_rows === 0) {
                if (!$empresaM->salvar($conn)) {
                    throw new RuntimeException("Erro ao Registrar Empresa");
                }
                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Empresa registrada com sucesso", "CRIACAO");
                }
            }

            $resposta->setNumero($idPedidoCarta);
            if (!$resposta->salvarEstadoDoEstagio()) {
                throw new RuntimeException("Erro ao Salvar Resposta");
            }

            if (isset($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "resposta de carta de estagio realizado", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O seu pedido foi processado com sucesso. O numero da carta e #$novoSequencial. Ainda esta pendente de verificacao por um administrador.";
                $stmtNotificacao = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
                $stmtNotificacao->bind_param("is", $_SESSION['usuario_id'], $mensagem);
                $stmtNotificacao->execute();
            }

            $conn->commit();

            if (($_SESSION['role'] ?? '') !== 'formando') {
                header("Location: /estagio/View/Estagio/listaDePedidos.php");
            } else {
                header("Location: /estagio/View/Estagio/formularioDeCartaDeEstagio.php");
            }
            exit();
        } catch (Throwable $e) {
            if ($conn instanceof mysqli) {
                try {
                    $conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            header("LOCATION: /estagio/View/estagio/formularioDeCartaDeEstagio.php?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$controller = new FormularioDeCartaDeEstagio();
$controller->cartaDeEstagio();
