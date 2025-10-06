<?php
include_once __DIR__ . '/../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';

SecurityHeaders::setLogin();

// Capturar o código de erro da URL ou definir um padrão
$error_code = isset($_GET['code']) ? (int)$_GET['code'] : 500;

// Definir mensagens de erro personalizadas
$error_messages = [
    404 => [
        'title' => 'Página Não Encontrada',
        'message' => 'A página que você está procurando não foi encontrada. Verifique o endereço ou tente novamente.'
    ],
    500 => [
        'title' => 'Erro Interno do Servidor',
        'message' => 'Ocorreu um erro interno no servidor. Nossa equipe já foi notificada. Por favor, tente novamente mais tarde.'
    ],
    'default' => [
        'title' => 'Erro Desconhecido',
        'message' => 'Ocorreu um erro inesperado. Por favor, tente novamente ou entre em contato com o suporte.'
    ]
];

// Selecionar mensagem com base no código de erro
$error_info = isset($error_messages[$error_code]) ? $error_messages[$error_code] : $error_messages['default'];

// Definir o código de status HTTP
http_response_code($error_code);
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($error_info['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../Style/login.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="ITC Logo">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li><a class="nav-link" href="../Login.php">Voltar ao Login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container custom-container">
        <h2><?php echo htmlspecialchars($error_info['title']); ?></h2>
        <hr />
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error_info['message']); ?>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <button type="button" class="btn btn-primary form-control" onclick="location.href='../Login.php';">Voltar ao Login</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>