<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Curso.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class CadastrarCurso
{
    private mysqli $conn;
    private Notificacao $notificacao;
    private Curso $curso;

    public function __construct()
    {
        $this->curso = new Curso();
        $this->notificacao = new Notificacao();
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
    }
    public function cadastrarCurso()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                header("LOCATION: /estagio/cursos/criar?erros=" . urlencode($e));
            }

            $codigo = isset($_POST['codigoCurso']) ? (int) $_POST['codigoCurso'] : null;
            $nome = trim($_POST['nomeCurso'] ?? '');
            $descricao = trim($_POST['descricaoCurso'] ?? '');
            $sigla = trim($_POST['siglaCurso'] ?? '');
            $id_qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;

            // Validação
            if ($codigo === null || $codigo <= 0) {
                header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("Erro: O código do curso deve ser um número válido."));
                exit();
            }
            if (empty($nome)) {
                header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("Erro: O nome do curso é obrigatório."));
                exit();
            }
            if (empty($sigla)) {
                header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("Erro: A sigla do curso é obrigatória."));
                exit();
            }
            if ($id_qualificacao === null || $id_qualificacao <= 0) {
                header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("Erro: Selecione uma qualificação válida (valor recebido: " . ($_POST['qualificacao'] ?? 'indefinido') . ")."));
                exit();
            }

            $this->curso->setCodigo($codigo);
            $this->curso->setNome($nome);
            $this->curso->setDescricao($descricao);
            $this->curso->setSigla($sigla);
            $this->curso->setIdQualificacao($id_qualificacao);

            if (!$this->curso->salvar($this->conn)) {
                header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("Erro ao cadastrar o curso."));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou um curso: " . $nome, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O Curso $nome foi registrado com sucesso.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            header("Location: /estagio/admin");
            exit;
        } catch (Exception $e) {
            header("LOCATION: /estagio/cursos/criar?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}

$curso = new CadastrarCurso();
$curso->cadastrarCurso();
