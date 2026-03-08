<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Curso.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';

class CadastrarCurso
{
    public function cadastrarCurso()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("Método da Requisição Inválido"));
            exit();
        }
        try{
            $curso = new Curso();

            $codigo = isset($_POST['codigoCurso']) ? (int) $_POST['codigoCurso'] : null;
            $nome = trim($_POST['nomeCurso'] ?? '');
            $descricao = trim($_POST['descricaoCurso'] ?? '');
            $sigla = trim($_POST['siglaCurso'] ?? '');
            // $id_qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $id_qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;

            // Validação
            if ($codigo === null || $codigo <= 0) {
                header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("Erro: O código do curso deve ser um número válido."));
                exit();
            }
            if (empty($nome)) {
                header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("Erro: O nome do curso é obrigatório."));
                exit();
            }
            if (empty($sigla)) {
                header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("Erro: A sigla do curso é obrigatória."));
                exit();
            }
            if ($id_qualificacao === null || $id_qualificacao <= 0) {
                header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("Erro: Selecione uma qualificação válida (valor recebido: " . ($_POST['qualificacao'] ?? 'indefinido') . ")."));
                exit();
            }

            $curso->setCodigo($codigo);
            $curso->setNome($nome);
            $curso->setDescricao($descricao);
            $curso->setSigla($sigla);
            $curso->setIdQualificacao($id_qualificacao);

            if (!$curso->salvar()) {
                header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("Erro ao cadastrar o curso."));
                exit();
            }

            if (isset($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou um curso: " . $nome, "CRIACAO");
            }

            header("Location: /estagio/View/Admin/portalDoAdmin.php");
            exit;
        }catch(Exception $e){
            header("LOCATION: /estagio/View/Cursos/CadastrarCurso.php?erros=" . urlencode("ERRO DO SISTEMA: $e"));
            exit();
        }
    }
}

$curso = new CadastrarCurso();
$curso->cadastrarCurso();