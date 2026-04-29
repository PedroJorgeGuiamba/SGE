<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Formando.php';
require_once __DIR__ . '/../../Model/Usuario.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();


class CadastrarFormando
{
    private $conn;
    private Criptografia $criptografia;
    private Usuario $usuario;
    private Notificacao $notificacao;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->usuario = new Usuario();
        $this->notificacao = new Notificacao();
    }

    public function cadastrarFormando()
    {
        $json = file_get_contents('php://input');
        if(empty($json)){
            $this->resposta([
                'erro' => 'Nenhum dado recebido'
            ], 400);
            return;
        }

        $dados = json_decode($json, true);

        if(json_last_error()!== JSON_ERROR_NONE){
            $this->resposta([
                'erro' => 'JSON Invalido', 
            ], 400);
            return;
        }

        try {
            $codigo = isset($dados['codigo']) ? (int) $dados['codigo'] : null;
            $nome = trim($dados['nome'] ?? '');
            $apelido = trim($dados['apelido'] ?? '');
            $dataNascimento = isset($dados['dataNascimento']) && !empty(trim($dados['dataNascimento']))
                ? new DateTime(trim($dados['dataNascimento']))
                : null;
            $naturalidade = trim($dados['naturalidade'] ?? '');
            $tipoDeDocumento = trim(strtoupper($dados['tipoDeDocumento']) ?? '');
            $numeroDeDocumento = trim(strtoupper($dados['numeroDeDocumento']) ?? '');

            if (strlen($numeroDeDocumento) < 5) {
                header("Location: /estagio/formando/upload?erros=" . htmlspecialchars("O Número de documento deve ter no mínimo 5 digitos."));
                exit();
            }

            $localEmitido = trim($dados['localEmitido'] ?? '');
            $dataEmissao = isset($dados['dataEmissao']) && !empty(trim($dados['dataEmissao']))
                ? new DateTime(trim($dados['dataEmissao']))
                : null;
            $nuit = isset($dados['nuit']) ? (int) $dados['nuit'] : null;

            if ($nuit <= 9) {
                header("Location: /estagio/formando/upload?erros=" . htmlspecialchars("O NUIT deve ter no mínimo 9 digitos."));
                exit();
            }

            $telefone = isset($dados['telefone']) ? (int) $dados['telefone'] : null;
            $email = trim(filter_var($dados['email'], FILTER_SANITIZE_EMAIL) ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /estagio/formando/upload?erros=" . htmlspecialchars("Por favor, insira um endereço de email válido."));
                exit();
            }

            $this->conn->begin_transaction();

            $email_hashed = hash('sha256', $email);

            $result = $this->usuario->getUsersByEmailHashed($this->conn, $email_hashed);
            if ($result  && $result->num_rows > 0) {
                $this->conn->rollback();
                registrarAtividade(null, "Email registrado: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
                header("Location: /estagio/formando/upload?erros=" . urlencode("Email já registrado."));
                exit();
            }

            $email_encripted = $this->criptografia->criptografar($email);
            $hashedPassword = password_hash($apelido, PASSWORD_DEFAULT);

            $nuit_encripted = $this->criptografia->criptografar($nuit);
            $numeroDeDocumento_encripted = $this->criptografia->criptografar($numeroDeDocumento);
            $telefone_encripted = $this->criptografia->criptografar($telefone);

            $this->usuario->setEmail($email_encripted);
            $this->usuario->setEmailHash(hash('sha256', $email));
            $this->usuario->setSenha($hashedPassword);
            $this->usuario->setRole('formando');

            $resultUser = $this->usuario->salvar($this->conn);
            if (!$resultUser) {
                $this->conn->rollback();
                error_log("Erro na execução da query: " . $this->conn->error);
            }
            $userId = $this->conn->insert_id;

            if (!$userId) {
                $this->conn->rollback();
                $this->resposta(['erro' => 'Erro ao obter ID do utilizador'], 500);
                return;
            }

            $formando = new Formando(
                $nome,
                $apelido,
                $codigo,
                $dataNascimento,
                $naturalidade,
                $tipoDeDocumento,
                $numeroDeDocumento_encripted,
                $localEmitido,
                $dataEmissao,
                $nuit_encripted,
                $telefone_encripted,
                $email_encripted,
                $userId
            );

            if (!$formando->salvar($this->conn)) {
                $this->conn->rollback();
                $this->resposta(['erro' => 'Erro ao salvar no banco'], 500);
            }

            $id = $formando->LastInsertId($this->conn);

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma formando: " . $nome, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O formando $nome foi cadastrado com sucesso.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();
        
            $this->resposta([
                'sucesso' => true,
                'mensagem' => 'Formando registrado com sucesso',
                'id' => $id
            ], 201);
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {

                }
            }

            $this->resposta(['erro' => 'Erro do Sitema'], 500);
        }
    }

    private function resposta($dados, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$formando = new CadastrarFormando();
$formando->cadastrarFormando();
