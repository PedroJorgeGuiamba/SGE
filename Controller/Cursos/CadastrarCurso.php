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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            var_dump($_POST['qualificacao']); // Log para depuração
        
            $codigo = isset($_POST['codigoCurso']) ? (int) $_POST['codigoCurso'] : null;
            $nome = trim($_POST['nomeCurso'] ?? '');
            $descricao = trim($_POST['descricaoCurso'] ?? '');
            $sigla = trim($_POST['siglaCurso'] ?? '');
            // $id_qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $id_qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;

            // Validação
            if ($codigo === null || $codigo <= 0) {
                echo "Erro: O código do curso deve ser um número válido.";
                return;
            }
            if (empty($nome)) {
                echo "Erro: O nome do curso é obrigatório.";
                return;
            }
            if (empty($sigla)) {
                echo "Erro: A sigla do curso é obrigatória.";
                return;
            }
            if ($id_qualificacao === null || $id_qualificacao <= 0) {
                echo "Erro: Selecione uma qualificação válida (valor recebido: " . ($_POST['qualificacao'] ?? 'indefinido') . ").";
                return;
            }

            $curso = new Curso();
            $curso->setCodigo($codigo);
            $curso->setNome($nome);
            $curso->setDescricao($descricao);
            $curso->setSigla($sigla);
            $curso->setIdQualificacao($id_qualificacao);

            if ($curso->salvar()) {

                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Cadastrou um curso: " . $nome, "CRIACAO");
                }

                echo "Curso cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar o curso.";
            }
            
        
        } else {
            echo "Método inválido.";
        }

        return $curso;
    }
}

$erros = '';
$curso = new CadastrarCurso();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $curso->cadastrarCurso();
}

