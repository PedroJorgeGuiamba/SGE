<?php
include_once __DIR__ . '/../../Controller/Auth/AuthConfirmationController.php';
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
SecurityHeaders::setLogin();
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Fazer link do CSS Global -->
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <!-- FontAwesome para icones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            
            <h3 class="auth-title">Validação OTP</h3>
            <p class="auth-subtitle">Introduza o código numérico recebido</p>

            <form method="post">
                <?= CSRFProtection::getTokenField() ?>
                
                <?php if (isset($_GET['erros'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_GET['erros']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="form-group mb-4">
                    <label for="codigo" class="form-label text-muted small fw-bold">Código OTP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-key text-muted"></i></span>
                        <input type="number" name="codigo" class="form-control border-start-0 ps-0" id="codigo" placeholder="123456" required minlength="6" maxlength="6">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm text-white"><i class="fas fa-check-circle me-1"></i> Validar</button>
                    <a href="Login.php" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-redo me-1"></i> Reenviar Código</a>
                </div>

                <div class="auth-links">
                    <p class="mt-4"><a href="../../Index.php" class="text-secondary"><i class="fas fa-arrow-left me-1"></i> Voltar à Home</a></p>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>