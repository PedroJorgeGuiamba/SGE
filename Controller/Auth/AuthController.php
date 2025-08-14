<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';

class AuthController
{
    public function verificar()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['password'] ?? '';
        $erros = '';

        $conexao = new Conector();
        $conn = $conexao->getConexao();

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($senha === $row['password']) {
                session_start();
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['usuario_id'] = $row['id'];

                // setcookie('user_email', $email, time() + 3600, "/");

                $sessaoId = iniciarSessao($row['id']);
                registrarAtividade($sessaoId, "Login realizado com sucesso", "LOGIN");

                $role = strtolower($row['role']);

                if ($role === 'formando') {
                    header("Location: /estagio/View/Formando/portalDeEstudante.php");
                    exit();
                } elseif ($role === 'supervisor') {
                    header("Location: /estagio/View/Supervisor/portalDoSupervisor.php");
                    exit();
                } elseif ($role === 'Formador') {
                    header("Location: /estagio/View/Formador/portalDoFormador.php");
                    exit();
                } elseif ($role === 'admin') {
                    header("Location: /estagio/View/Admin/portalDoAdmin.php");
                    exit();
                }elseif ($role === 'Admin'){
                    header("Location: /estagio/View/Admin/portalDoAdmin.php");
                }
                else {
                    $erros .= "Tipo de usuário desconhecido.<br>";
                }
            } else {
                $erros .= "Senha incorreta.<br>";
            }
        } else {
            $erros .= "Email não encontrado.<br>";
        }

        return $erros;
    }
}

$erros = '';
$login = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $login->verificar();
}
