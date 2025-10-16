<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/Formando.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';

class CadastrarFormando
{
    public function cadastrarFormando()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $codigo = isset($_POST['codigoformando']) ? (int) $_POST['codigoformando'] : null;
            $nome = trim($_POST['nomeformando'] ?? '');
            $apelido = trim($_POST['apelidoformando'] ?? '');
            $dataNascimento = isset($_POST['dataNascimento']) && !empty(trim($_POST['dataNascimento']))
                ? new DateTime(trim($_POST['dataNascimento']))
                : null;
            $naturalidade = trim($_POST['naturalidade'] ?? '');
            $tipoDeDocumento = trim($_POST['tipoDeDocumento'] ?? '');
            $numeroDeDocumento = trim($_POST['numeroDeDocumento'] ?? '' );
            $localEmitido = trim($_POST['localEmitido'] ??'');
            $dataEmissao = isset($_POST['dataEmissao']) && !empty(trim($_POST['dataEmissao']))
                ? new DateTime(trim($_POST['dataEmissao']))
                : null;
            $nuit = isset($_POST['nuit']) ? (int) $_POST['nuit'] : null;
            $telefone = isset($_POST['telefone']) ? (int) $_POST['telefone'] : null;
            $email = trim($_POST['email'] ??'');

            $formando = new Formando(
                $nome,
                $apelido,
                $codigo,
                $dataNascimento,
                $naturalidade,
                $tipoDeDocumento,
                $numeroDeDocumento,
                $localEmitido,
                $dataEmissao,
                $nuit,
                $telefone,
                $email
            );
            // $formando->setCodigo($codigo);
            // $formando->setNome($nome);
            // $formando->setApelido($apelido);
            // $formando->setDataDeNascimento($dataNascimento);
            // $formando->setNaturalidade($naturalidade);
            // $formando->setTipoDeDocumento($tipoDeDocumento);
            // $formando->setNumeroDeDocumento($numeroDeDocumento);
            // $formando->setLocalEmitido($localEmitido);
            // $formando->setDataDeEmissao($dataEmissao);
            // $formando->setNUIT($nuit);
            // $formando->setTelefone($telefone);
            // $formando->setEmail($email);

            

            if ($formando->salvar()) {

                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma formando: " . $nome, "CRIACAO");
                }

                header("Location: /estagio/View/Admin/portalDoAdmin.php");
            } else {
                echo "Erro ao cadastrar formando.";
            }
        } else {
            echo "Método inválido.";
        }

        return $formando;
    }
}

$erros = '';
$formando = new CadastrarFormando();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $formando->cadastrarFormando();
}

