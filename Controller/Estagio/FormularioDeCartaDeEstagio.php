<?php
session_start();

require_once __DIR__ . '/../../Model/PedidoDeCarta.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Conexao/conector.php';

class FormularioDeCartaDeEstagio
{

    public function cartaDeEstagio()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pedido = new PedidoDeCarta();

                $codigoFormando = isset($_POST['codigoFormando']) ? (int) $_POST['codigoFormando'] : null;
                $codigoTurma = isset($_POST['turma']) ? (int) $_POST['turma'] : null;
                $codigoQualificacao = isset($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;

                $dadosFormando = $pedido->buscarNomeEApelido($codigoFormando);

                $pedido->setCodigoFormando($codigoFormando);
                $pedido->setTurma($codigoTurma);
                $pedido->setQualificacao($codigoQualificacao);
                $pedido->setDataPedido($_POST['dataPedido'] ?? '');
                $pedido->setHoraPedido($_POST['horaPedido'] ?? '');
                $pedido->setEmpresa(trim($_POST['empresa'] ?? ''));
                $pedido->setContactoPrincipal(trim($_POST['contactoPrincipal'] ?? ''));
                $pedido->setContactoSecundario(trim($_POST['contactoSecundario'] ?? ''));
                $pedido->setEmail(trim($_POST['email'] ?? ''));


                if ($dadosFormando === null) {
                    echo "Formando não encontrado.";
                    return;
                }

                $nome = $dadosFormando['nome'];
                $apelido = $dadosFormando['apelido'];


                if ($pedido->salvar($nome, $apelido)) {
                    if (isset($_SESSION['sessao_id'])) {
                        registrarAtividade($_SESSION['sessao_id'], "Pedido de carta de estágio realizado", "CRIACAO");
                    }

                    $conexao = new Conector();
                    $conn = $conexao->getConexao();
                    $lastId = $conn->insert_id;

                    // Retornar HTML com a modal
                    // echo "<!DOCTYPE html>
                    // <html>
                    // <head>
                    //     <style>
                    //         .modal {
                    //             display: none;
                    //             position: fixed;
                    //             top: 50%;
                    //             left: 50%;
                    //             transform: translate(-50%, -50%);
                    //             background-color: white;
                    //             padding: 20px;
                    //             border: 1px solid #888;
                    //             box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    //             z-index: 1000;
                    //             text-align: center;
                    //         }
                    //         .overlay {
                    //             display: none;
                    //             position: fixed;
                    //             top: 0;
                    //             left: 0;
                    //             width: 100%;
                    //             height: 100%;
                    //             background-color: rgba(0,0,0,0.5);
                    //             z-index: 999;
                    //         }
                    //         .modal-buttons button {
                    //             margin: 0 10px;
                    //             padding: 10px 20px;
                    //             cursor: pointer;
                    //         }
                    //     </style>
                    // </head>
                    // <body>
                    //     <div id='overlay' class='overlay'></div>
                    //     <div id='modal' class='modal'>
                    //         <h2>Sucesso!</h2>
                    //         <p>Pedido de carta enviado com sucesso!</p>
                    //         <div class='modal-buttons'>
                    //             <button onclick=\"document.getElementById('modal').style.display='none'; document.getElementById('overlay').style.display='none';\">OK</button>
                    //             <button onclick=\"window.location.href='/View/estagio/detalhes_pedido.php?numero=" . $lastId . htmlspecialchars($lastId, ENT_QUOTES, 'UTF-8') ."'\">Detalhes</button>
                    //         </div>
                    //     </div>
                    //     <script>
                    //         document.addEventListener('DOMContentLoaded', function() {
                    //             document.getElementById('modal').style.display = 'block';
                    //             document.getElementById('overlay').style.display = 'block';
                    //         });
                    //     </script>
                    // </body>
                    // </html>";

                    echo "Pedido de carta enviado com sucesso!";
                } else {
                    echo "Erro ao enviar pedido.";
                }
            } catch (Exception $e) {
                echo "Erro no sistema: " . $e->getMessage();
            }
        } else {
            echo "Método inválido.";
        }
    }
}

// Executar processamento
$controller = new FormularioDeCartaDeEstagio();
$controller->cartaDeEstagio();
