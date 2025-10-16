<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
SecurityHeaders::setLogin();

include_once '../../Controller/Auth/RegisterController.php';
?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Style/login.css">
</head>

<body>

    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li>
                                <a class="nav-link" href="../../View/Login.php">Entrar</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container custom-container">
        <h2>
            REGISTER
        </h2>

        <hr />
        
        <?php if (isset($_GET['error']) || isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error'] ?? $error) ?></div>
        <?php endif; ?>

        <form method="post">
            <?= CSRFProtection::getTokenField() ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="pedrojorge@guiamba.com">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="**********">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="role" class="form-label">role</label>
                            <select name="role" id="name" class="form-control">
                                <option value="">Selecione a Role</option>
                                <option value="admin">admin</option>
                                <option value="Formador">Formador</option>
                                <option value="Formando">Formando</option>
                                <option value="Supervisor">Supervisor</option>
                                <option value="Seguranca">Segurança</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success form-control">LogIn</button>
                </div>
            </div>
        </form>
    </div>


</body>

</html>