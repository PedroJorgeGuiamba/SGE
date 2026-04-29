<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
SecurityHeaders::setLogin();

// include_once '../../Controller/Auth/RegisterController.php';
?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="icon" href="https://www.itc.ac.mz/wp-content/uploads/2020/03/cropped-logobackgsite_ITC-2-32x32.png" sizes="32x32">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fazer link do CSS Global -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
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
            
            <h3 class="auth-title">Registo de Utilizador</h3>
            <p class="auth-subtitle">Crie uma conta para aceder ao sistema</p>

            <form method="post" action="/estagio/register/salvar">
                <?= CSRFProtection::getTokenField() ?>
                
                <?php if (isset($_GET['erros'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="form-group mb-3">
                    <label for="email" class="form-label text-muted small fw-bold">Endereço de Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="exemplo@dominio.com" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label text-muted small fw-bold">Palavra-passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" id="password" placeholder="**********" required>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="role" class="form-label text-muted small fw-bold">Tipo de Perfil</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user-tag text-muted"></i></span>
                        <select name="role" id="role" class="form-select border-start-0 ps-0" required>
                            <option value="">Selecione o perfil pretendido...</option>
                            <option value="admin">Administrador</option>
                            <option value="Formador">Formador</option>
                            <option value="Formando">Formando</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Seguranca">Segurança</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm text-white">Registrar Conta</button>
                </div>

                <div class="auth-links">
                    <p class="text-muted mb-0">Já possui uma conta? <a href="/estagio/login/">Inicie Sessão</a></p>
                    <p class="mt-2"><a href="/estagio/" class="text-secondary"><i class="fas fa-arrow-left me-1"></i> Voltar à Home</a></p>
                </div>
            </form>
        </div>
    </main>


</body>

</html>