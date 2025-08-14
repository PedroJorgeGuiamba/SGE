<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Turma.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';

class CadastrarTurma
{
    public function cadastrarTurma()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $codigo = isset($_POST['codigoTurma']) ? (int) $_POST['codigoTurma'] : null;
            $nome = trim($_POST['nomeTurma'] ?? '');
            $id_qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $id_curso = isset($_POST['curso']) ? (int) $_POST['curso'] : null;

            $turma = new Turma();
            $turma->setCodigo($codigo);
            $turma->setNome($nome);
            $turma->setCodigoQualificacao($id_qualificacao);
            $turma->setCodigoCurso($id_curso);

            if ($turma->salvar()) {

                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma turma: " . $nome, "CRIACAO");
                }

                echo "Turma cadastrada com sucesso!";
            } else {
                echo "Erro ao cadastrar turma.";
            }
        } else {
            echo "Método inválido.";
        }

        return $turma;
    }
}

$erros = '';
$turma = new CadastrarTurma();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $turma->cadastrarTurma();
}

