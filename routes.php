<?php

require_once __DIR__ . '/App/Controllers/AuthController.php';
require_once __DIR__ . '/App/Controllers/UsuariosController.php';
require_once __DIR__ . '/App/Middleware/auth.php';
require_once __DIR__ . '/App/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/App/Controllers/AtendimentosController.php';
require_once __DIR__ . '/App/Controllers/PessoasController.php';
require_once __DIR__ . '/App/Controllers/FrontendController.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';


switch ($controller) {
    case 'frontend':
        exigirAutenticacao();

        $frontendController = new FrontendController();
        switch ($action) {
            case 'pessoas':
                $frontendController->pessoas();
                break;
            case 'tipos':
                $frontendController->tipos();
                break;
            case 'atendimentos':
                $frontendController->atendimentos();
                break;
            default:
                http_response_code(404);
                echo 'Ação de frontend não encontrada';

        }
        break;
    case 'tipos':
        exigirAutenticacao();

        $tiposController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;
            case 'buscarPorId':
                $tiposController->buscarPorId();
                break;
            case 'criar':
                $tiposController->criar();
                break;
            case 'atualizar':
                $tiposController->atualizar();
                break;
            case 'inativar':
                $tiposController->inativar();
                break;
            default:
                http_response_code(404);
                echo 'Ação de tipos de atendimento não encontrada';

        }
        break;
    case 'pessoas':
        exigirAutenticacao();

        $PessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $PessoasController->listar();
                break;
            case 'buscarPorId':
                $PessoasController->buscarPorId();
                break;
            case 'criar':
                $PessoasController->criar();
                break;
            case 'atualizar':
                $PessoasController->atualizar();
                break;
            case 'inativar':
                $PessoasController->inativar();
                break;
            default:
                http_response_code(404);
                echo 'Ação de pessoas não encontrada';

        }
        break;


    case 'atendimentos':
        exigirAutenticacao();

        $atendimentosController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'visualizar':
                $atendimentosController->visualizar();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'alterarStatus':
                $atendimentosController->alterarStatus();
                break;
            case 'opcoesFormulario':
                $atendimentosController->opcoesFormulario();
                break;
            default:
                http_response_code(404);
                echo 'Ação de atendimento não encontrada';

        }
        break;

    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Ação de autenticação não encontrada.';

        }

        break;

    case 'usuarios':
        exigirAutenticacao();
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;

            case 'buscar':
                $usuariosController->buscarPorId();
                break;
            case 'criar':
                $usuariosController->criar();
                break;
            case 'atualizar':
                $usuariosController->atualizar();
                break;
            case 'excluir':
                $usuariosController->excluir();
                break;
            default:
                http_response_code(404);
                echo 'Ação de usuarios não encontrada';
        }
        break;
    default:
        http_response_code(404);
        echo 'Controller não encontrado';

}
