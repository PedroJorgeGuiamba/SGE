<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Helpers/auth.php';

$auth = new AuthMiddleware();

// Pega a URL requisitada
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

// Se for vazio, vai para Index.php (página inicial)
if ($url === '') {
    require 'Index.php';
    exit;
}

$partes = explode('/', $url);
$modulo = $partes[0] ?? '';
$acao = $partes[1] ?? '';
$id = $partes[2] ?? '';

// ================= ROTEAMENTO PRINCIPAL =================
switch ($modulo) {

    // ================= AUTH =================
    case 'login':
    case 'auth/login':
        switch ($acao) {
            case '':
                require 'View/Auth/Login.php';
                break;
            case 'logar':
                require 'Controller/Auth/AuthController.php';
                break;
            case 'confirmar-user':
                require 'View/Auth/ConfirmacaoFormando.php';
                break;
            case 'google-callback':
                require 'Controller/Auth/GoogleCallbackController.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    case 'register':
    case 'auth/register':
        switch ($acao) {
            case '':
                require 'View/Auth/Register.php';
                break;
            case 'salvar':
                require 'Controller/Auth/RegisterController.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    case 'logout':
    case 'auth/logout':
        require 'Controller/Auth/LogoutController.php';
        break;

    case 'validar':
    case 'auth/validar':
        require 'View/Auth/ValidarUser.php';
        break;

    // ================= PORTAIS =================
    case 'admin':
        $auth->verificarAutenticacao();
        require 'View/Admin/portalDoAdmin.php';
        break;

    case 'formando':
        switch ($acao) {
            case '':
                $auth->verificarAutenticacao();
                require 'View/Formando/portalDeEstudante.php';
                break;
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/Formando/listaDeFormandos.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/Formando/CadastrarFormando.php';
                break;
            case 'upload-json':
                $auth->verificarAutenticacao();
                require 'View/Formando/CadastrarFormandoJSON.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Formando/CadastrarFormando.php';
                break;
            case 'salvar-uploadJSON':
                $auth->verificarAutenticacao();
                require 'Controller/Formando/CadastrarFormandoJSON.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['id_formando'] = $id;
                require 'View/Formando/editarFormando.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Formando/editarFormando.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/getCursos.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    case 'formador':
        switch ($acao) {
            case '':
                $auth->verificarAutenticacao();
                require 'View/Formador/portalDoFormador.php';
                break;
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/Formador/listaDeFormadores.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/Formador/CadastrarFormador.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Formador/CadastrarFormador.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['id_formador'] = $id;
                require 'View/Formador/editarFormador.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Formador/editarFormador.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/getCursos.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    case 'supervisor':
        switch ($acao) {
            case '':
                $auth->verificarAutenticacao();
                require 'View/Supervisor/portalDoSupervisor.php';
                break;
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/Supervisor/listaDeSupervisores.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/Supervisor/CadastrarSupervisor.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Supervisor/CadastrarSupervisor.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['id_supervisor'] = $id;
                require 'View/Supervisor/editarSupervisor.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Supervisor/editarSupervisor.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/getCursos.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    // ================= CURSOS =================
    case 'curso':
    case 'cursos':
        switch ($acao) {
            case '':
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/Cursos/listaDeCursos.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/Cursos/CadastrarCurso.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/CadastrarCurso.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['id_curso'] = $id;
                require 'View/Cursos/editarCurso.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/editarCurso.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/getCursos.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    // ================= TURMAS =================
    case 'turma':
        switch ($acao) {
            case '':
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/Turmas/listaDeTurmas.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/Turmas/CadastrarTurma.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Turmas/CadastrarTurma.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['codigo'] = $id;
                require 'View/Turmas/editarTurma.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Turmas/editarTurma.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Turmas/getCursos.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;
    
    // ================= QUALIFICACAO =================
    case 'qualificacao':
        switch ($acao) {
            case '':
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/Qualificacao/listaDeQualificacoes.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/Qualificacao/CadastrarQualificacao.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Qualificacao/CadastrarQualificacao.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['id_qualificacao'] = $id;
                require 'View/Qualificacao/editarQualificacao.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Qualificacao/editarQualificacao.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Qualificacao/getCursos.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    // ================= ESTÁGIO =================
    case 'estagio':
    case 'estagios':
        switch ($acao) {
            case '':
                $auth->verificarAutenticacao();
                require 'View/estagio/situacaoDeEstagio.php';
                break;
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/estagio/listaDePedidos.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/estagio/formularioDeCartaDeEstagio.php';
                break;
            case 'preview':
                $auth->verificarAutenticacao();
                require 'View/estagio/previewCartaDeEstagio.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/FormularioDeCartaDeEstagio.php';
                break;
            case 'historico':
                $auth->verificarAutenticacao();
                require 'View/estagio/HistoricoDePedidos.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/remover_pedido.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['numero'] = $id;
                require 'View/estagio/editarPedido.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/editarPedido.php';
                break;
            case 'gerarPDF':
                $_GET['id_pedido_carta'] = $id;
                require 'Controller/Estagio/GerarPdfCompleto.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    // ================= CREDENCIAL =================
    case 'credencial':
    case 'credenciais':
        switch ($acao) {
            case '':
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/estagio/listaDePedidosCredencial.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/estagio/formularioDeCredencialDeEstagio.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/FormularioDeCredencialDeEstagio.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['numero'] = $id;
                require 'View/estagio/editarCredencial.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/editarCredencial.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                header('Content-Type: application/json');
                require 'Controller/Estagio/remover_credencial.php';
                break;
            case 'historico':
                $auth->verificarAutenticacao();
                require 'View/estagio/HistoricoDePedidosCredencial.php';
                break;
            case 'gerarPDF':
                $_GET['id_pedido_carta'] = $id;
                require 'Controller/Estagio/GerarPdfCredencialCompleto.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    // ================= VISITAS =================
    case 'visita':
    case 'visitas':
        switch ($acao) {
            case '':
            case 'listar':
                $auth->verificarAutenticacao();
                require 'View/estagio/listaDePedidosVisita.php';
                break;
            case 'criar':
                $auth->verificarAutenticacao();
                require 'View/estagio/formularioDeVisita.php';
                break;
            case 'salvar':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/FormularioDeVisita.php';
                break;
            case 'editar':
                $auth->verificarAutenticacao();
                $_GET['id_visita'] = $id;
                require 'View/estagio/editarVisita.php';
                break;
            case 'atualizar':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/editarVisita.php';
                break;
            case 'aprovar':
                $auth->verificarAutenticacao();
                header('Content-Type: application/json');
                require 'Controller/Estagio/aprovar_visita.php';
                break;
            case 'recusar':
                $auth->verificarAutenticacao();
                header('Content-Type: application/json');
                require 'Controller/Estagio/recusar_visita.php';
                break;
            case 'remover':
                $auth->verificarAutenticacao();
                header('Content-Type: application/json');
                require 'Controller/Estagio/remover_visita.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;

    case 'relatorio':
        switch ($acao) {
            case '':
                $auth->verificarAutenticacao();
                require 'View/estagio/relatorio.php';
                break;
            case 'gerarPDF':
                require 'Controller/Estagio/GerarPdfRelatorio.php';
                break;
            default:
                require 'View/Erros/error.php';
        }
        break;


    // ================= API / AJAX (se tiver) =================
    case 'api':
        // Para requisições AJAX
        switch ($acao) {
            case 'cursos':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/getCursos.php';
                break;
            case 'listar-cursos':
                $auth->verificarAutenticacao();
                require 'Controller/Cursos/search_cursos.php';
                break;
            case 'listar-qualificacoes':
                $auth->verificarAutenticacao();
                require 'Controller/Qualificacao/search_qualificacao.php';
                break;
            case 'listar-turmas':
                $auth->verificarAutenticacao();
                require 'Controller/Turmas/search_turmas.php';
                break;
            case 'listar-formandos':
                $auth->verificarAutenticacao();
                require 'Controller/Formando/search_formandos.php';
                break;
            case 'listar-formadores':
                $auth->verificarAutenticacao();
                require 'Controller/Formador/search_formadores.php';
                break;
            case 'listar-supervisores':
                $auth->verificarAutenticacao();
                require 'Controller/Supervisor/search_supervisores.php';
                break;
            case 'qualificacao':
                $auth->verificarAutenticacao();
                require 'Controller/Qualificacao/getQualificacoes.php';
                break;
            case 'users':
                $auth->verificarAutenticacao();
                require 'Controller/Usuarios/getUsers.php';
                break;
            case 'turmas':
                $auth->verificarAutenticacao();
                require 'Controller/Turmas/getTurmas.php';
                break;
            case 'pedidos':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/search_pedidos.php';
                break;
            case 'historico-pedidos':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/search_historico_pedidos.php';
                break;
            case 'credenciais':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/search_credencial.php';
                break;
            case 'historico-credencial':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/search_historico_credencial.php';
                break;
            case 'visitas':
                $auth->verificarAutenticacao();
                require 'Controller/Estagio/search_visita.php';
                break;
            case 'carta':
                require 'View/estagio/Estagio.php';
                break;
            case 'credencial':
                require 'View/estagio/Credencial.php';
                break;
            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Endpoint não encontrado']);
        }
        break;

    // ================= FALLBACK =================
    default:
        http_response_code(404);
        require 'View/Erros/error.php';
        break;
}
