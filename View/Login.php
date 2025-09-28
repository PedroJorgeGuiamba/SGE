<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? '';
    switch (strtolower($role)) {
        case 'admin': header("Location: Auth/Admin/PainelAdmin.php"); break;
        case 'formando': header("Location: Auth/Formando/PainelFormando.php"); break;
        case 'formador': header("Location: Auth/Formador/PainelFormador.php"); break;
        case 'supervisor': header("Location: Auth/Supervisor/PainelSupervisor.php"); break;
        default: header("Location: Login.php?error=role_invalida"); break;
    }
    exit();
}
include_once __DIR__ . '/../Controller/Auth/AuthController.php';
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                            <li><a class="nav-link" href="Auth/Register.php">Registrar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container custom-container">
        <h2>LOGIN</h2>
        <hr />
        <?php if (isset($_GET['error']) || isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error'] ?? $error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="exemplo@dominio.com" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="**********" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success form-control">Login</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>