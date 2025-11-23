<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Supervisor.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';

class CadastrarSupervisor
{
    public function cadastrarSupervisor()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nome = trim($_POST['nomeSupervisor'] ?? '');
            $id_qualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $user = isset($_POST['user']) ? (int) $_POST['user'] : null;
            $area = trim($_POST['area' ?? '']);

            $supervisor = new Supervisor();
            $supervisor->setNome($nome);
            $supervisor->setId_Qualificacao($id_qualificacao);
            $supervisor->setUser($user);
            $supervisor->setArea($area);

            if ($supervisor->salvar()) {

                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Cadastrou um supervisor: " . $nome, "CRIACAO");
                }

                header("Location: /estagio/View/Admin/portalDoAdmin.php");
            } else {
                echo "Erro ao cadastrar supervisor.";
            }
        } else {
            echo "Método inválido.";
        }

        return $supervisor;
    }
}

$erros = '';
$supervisor = new CadastrarSupervisor();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $supervisor->cadastrarSupervisor();
}

