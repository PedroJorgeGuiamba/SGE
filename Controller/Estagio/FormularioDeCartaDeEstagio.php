<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../../Model/PedidoDeCarta.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Resposta.php';
require_once __DIR__ . '/../../Model/Empresa.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';

class FormularioDeCartaDeEstagio
{
    private Empresa $empresa;
    private PedidoDeCarta $pedido;
    private Notificacao $notificacao;
    private mysqli $conn;
    private Criptografia $criptografia;
    public function __construct()
    {
        $this->notificacao = new Notificacao();
        $this->pedido = new PedidoDeCarta();
        $this->empresa = new Empresa();
        $this->criptografia = new Criptografia();
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }
    public function cartaDeEstagio()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/estagio/criar?erros=" . urlencode("Metodo da Requisicao Invalido"));
            exit();
        }

        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/estagio/criar?erros=" . $e);
            }

            date_default_timezone_set('Africa/Maputo');

            $resposta = new Resposta($this->conn);

            $fromPreview = isset($_POST['fromPreview']) && $_POST['fromPreview'] === '1';

            if ($fromPreview) {
                if (empty($_SESSION['preview_pedido'])) {
                    header("LOCATION: /estagio/estagio/criar?erros="
                        . urlencode("Sessão expirada. Preencha o formulário novamente."));
                    exit();
                }
                $dados = $_SESSION['preview_pedido'];
                unset($_SESSION['preview_pedido']);
            } else {
                $dados = $_POST;
            }

            $codigoFormando      = isset($dados['codigoFormando']) && is_numeric($dados['codigoFormando']) ? (int) $dados['codigoFormando'] : null;
            $codigoTurma         = isset($dados['turma']) && is_numeric($dados['turma'])    ? (int) $dados['turma']          : null;
            $codigoQualificacao  = isset($dados['qualificacao']) && is_numeric($dados['qualificacao'])   ? (int) $dados['qualificacao']   : null;
            $empresa             = strtoupper(trim($dados['empresa'] ?? ''));
            $contactoPrincipal   = trim($dados['contactoPrincipal']);
            $contactoSecundario  = trim($dados['contactoSecundario']);
            $email               = trim(filter_var($dados['email'], FILTER_VALIDATE_EMAIL));

            // Coloque isso antes da validação
            error_log("Dados recebidos: " . print_r([
                'codigoFormando' => $codigoFormando,
                'turma' => $codigoTurma,
                'qualificacao' => $codigoQualificacao,
                'empresa' => $empresa,
                'contactoPrincipal' => $contactoPrincipal
            ], true));

            if (empty($codigoFormando) || empty($empresa) || empty($codigoQualificacao) || empty($codigoTurma) || empty($contactoPrincipal)) {
                header("Location: /estagio/estagio/criar?erros=" . htmlspecialchars("Por favor, certifique-se de preencher todos os campos."));
                exit();
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /estagio/estagio/criar?erros=" . htmlspecialchars("Por favor, insira um endereço de email válido."));
                exit();
            }

            $this->conn->begin_transaction();

            $sigla = $this->empresa->getSiglaEmpresa($empresa);

            $dadosFormando = $this->pedido->buscarNomeEApelido((int) $codigoFormando, $this->conn);
            if ($dadosFormando === null) {
                $this->conn->rollback();
                throw new RuntimeException("Formando nao encontrado");
            }

            $sqlLimite = "
                SELECT COUNT(*) AS total
                FROM pedido_carta
                WHERE codigo_formando = ?
                    AND MONTH(data_do_pedido) = MONTH(CURRENT_DATE())
                    AND YEAR(data_do_pedido) = YEAR(CURRENT_DATE())
            ";
            $stmtLimite = $this->conn->prepare($sqlLimite);
            $stmtLimite->bind_param("i", $codigoFormando);
            $stmtLimite->execute();
            $resultadoLimite = $stmtLimite->get_result()->fetch_assoc();

            if ((int) ($resultadoLimite['total'] ?? 0) >= 5) {
                $stmtLimite->close();
                $this->conn->rollback();
                throw new RuntimeException("Limite mensal de 5 pedidos atingido. Tente novamente no proximo mes.");
            }

            $anoAtual = (int) date('Y');
            $sql = "
                SELECT COALESCE(MAX(numero), 0) AS ultimo_numero
                FROM pedido_carta
                WHERE YEAR(data_do_pedido) = ?
                FOR UPDATE
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $anoAtual);
            $stmt->execute();
            $resultadoNumero = $stmt->get_result()->fetch_assoc();
            $novoSequencial = ((int) ($resultadoNumero['ultimo_numero'] ?? 0)) + 1;

            $this->pedido->setNumero($novoSequencial);
            $this->pedido->setCodigoFormando((int) $codigoFormando);
            $this->pedido->setTurma((int) $codigoTurma);
            $this->pedido->setQualificacao((int) $codigoQualificacao);
            $this->pedido->setEmpresa($empresa);
            $this->pedido->setContactoPrincipal($this->criptografia->criptografar($contactoPrincipal));
            $this->pedido->setContactoSecundario($this->criptografia->criptografar($contactoSecundario));
            $this->pedido->setEmail($this->criptografia->criptografar($email));
            $this->pedido->setHoraPedido(date("H:i:s"));
            $this->pedido->setDataPedido(date('Y-m-d'));

            $this->empresa->setNome($empresa);
            $this->empresa->setAbr($sigla);

            $nome = $dadosFormando['nome'];
            $apelido = $dadosFormando['apelido'];

            $resposta->setStatus('Pendente');
            $resposta->setStatusEstagio('Pendente');

            if (!$this->pedido->salvar($nome, $apelido, $this->conn)) {
                $this->conn->rollback();
                throw new RuntimeException("Erro ao Salvar Pedido");
            }

            // Captura o id_pedido_carta imediatamente apos inserir o pedido.
            $idPedidoCarta = (int) $this->conn->insert_id;
            if ($idPedidoCarta <= 0) {
                $this->conn->rollback();
                throw new RuntimeException("Nao foi possivel obter o ID do pedido");
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "pedido de carta de estagio realizado", "CRIACAO");
            }

            $sqlVerificarEmpresa = "SELECT id_empresa FROM empresa WHERE nome = ? OR abreviatura = ?";
            $stmtVerificar = $this->conn->prepare($sqlVerificarEmpresa);
            $stmtVerificar->bind_param("ss", $empresa, $empresa);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result();

            if ($resultVerificar->num_rows === 0) {
                if (!$this->empresa->salvar($this->conn)) {
                    $resultVerificar->close();
                    $this->conn->rollback();
                    throw new RuntimeException("Erro ao Registrar Empresa");
                }
                if (!empty($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Empresa registrada com sucesso", "CRIACAO");
                }
            }

            $resposta->setNumero($idPedidoCarta);
            if (!$resposta->salvarEstadoDoEstagio()) {
                $this->conn->rollback();
                throw new RuntimeException("Erro ao Salvar Resposta");
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "resposta de carta de estagio realizado", "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O seu pedido foi processado com sucesso. O numero da carta e #$novoSequencial. Ainda esta pendente de verificacao por um administrador.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();

            if (($_SESSION['role'] ?? '') !== 'formando') {
                header("Location: /estagio/estagio/listar");
            } else {
                header("Location: /estagio/estagio/criar");
            }
            exit();
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            header("LOCATION: /estagio/estagio/criar?erros=" . urlencode("Erro no sistema: " . $e->getMessage()));
            exit();
        }
    }
}

$controller = new FormularioDeCartaDeEstagio();
$controller->cartaDeEstagio();
