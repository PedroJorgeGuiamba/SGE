<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
SecurityHeaders::setLogin();

include_once __DIR__ . '/../../Controller/Auth/AuthController.php';

$env = parse_ini_file(__DIR__ . '/../../config/.env');

foreach ($env as $key => $value) {
    putenv("$key=$value");
}

$site = getenv("GOOGLE_DATA_SITE_KEY");
?>
<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Fazer link do CSS Global -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <!-- FontAwesome para icones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
</head>

<body>
    <main class="auth-wrapper">
        <div class="auth-card">
            <!-- Logo do ITC -->
            <div class="logo-container">
                <a href="../../Index.php">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="ITC Logo">
                </a>
            </div>

            <h3 class="auth-title">Bem-vindo</h3>
            <p class="auth-subtitle">Faça o seu login para acessar o SGE</p>

            <?php if (isset($_GET['erros'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_GET['erros']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post">
                <?= CSRFProtection::getTokenField() ?>

                <div class="form-group mb-3">
                    <label for="email" class="form-label text-muted small fw-bold">Endereço de Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="exemplo@dominio.com" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="password" class="form-label text-muted small fw-bold">Palavra-passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" id="password" placeholder="**********" required>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <div class="input-group">
                        <div class="g-recaptcha" data-sitekey="<?=$site ?>" data-action="LOGIN"></div>
                        <span class="error_form" id="recaptcha_error_message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm text-white">Entrar no Sistema</button>
                </div>

                <div class="auth-links">
                    <p class="text-muted mb-0">Ainda não tem conta? <a href="/estagio/register/">Criar Registo</a></p>
                    <p class="mt-2"><a href="/estagio/" class="text-secondary"><i class="fas fa-arrow-left me-1"></i> Voltar à Home</a></p>
                </div>
            </form>
        </div>
    </main>

    <script src="/estagio/Assets/JS/info-message-close.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>