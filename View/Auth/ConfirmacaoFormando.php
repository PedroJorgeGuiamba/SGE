<?php
require_once __DIR__ . '/../../Controller/Auth/ConfirmacaoFormandoController.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado e é um formando
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'formando') {
    header("Location: /estagio/login");
    exit();
}

// Verificar se o usuário já confirmou o código do formando
if (isset($_SESSION['codigo_formando'])) {
    // Usuário já confirmou, redirecionar para o portal
    header("Location: /estagio/formando");
    exit();
}

$controller = new ConfirmacaoFormandoController();
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $error = $controller->verificar();
    if (!empty($error)) {
        // Limpar variáveis de sessão em caso de erro
        unset($_SESSION['confirmation_attempts']);
        unset($_SESSION['confirmation_last_attempt_time']);
        unset($_SESSION['codigo_formando']);
    }
    if (empty($error)) {
        $success = "Confirmação realizada com sucesso! Redirecionando...";
    }
}

$remainingAttempts = $controller->getRemainingAttempts();
$lockoutTime = $controller->getLockoutRemainingTime();
?>
<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Formando - SGE</title>
    <link rel="icon" href="https://www.itc.ac.mz/wp-content/uploads/2020/03/cropped-logobackgsite_ITC-2-32x32.png" sizes="32x32">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <style>
        /* ====== PALETA DO PROJECTO SGE ====== */
        :root {
            --primary: #3a4c91;
            --secondary: #3c9bff;
            --accent: #0d6efd;
        }

        .confirmation-container {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirmation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(58, 76, 145, 0.25);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .confirmation-header .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(58, 76, 145, 0.3);
        }

        .confirmation-header .icon-wrapper i {
            font-size: 2.2rem;
            color: #ffffff;
        }

        .confirmation-header h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .confirmation-header p {
            color: #64748b;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(58, 76, 145, 0.18);
        }

        .btn-confirm {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 10px;
            padding: 13px 30px;
            font-size: 1.05rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(58, 76, 145, 0.3);
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(58, 76, 145, 0.4);
            color: white;
        }

        .btn-confirm:disabled {
            background: #adb5bd;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .attempts-info {
            background: rgba(255, 193, 7, 0.08);
            border: 1px solid rgba(255, 193, 7, 0.35);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .attempts-info i {
            color: #e6a817;
        }

        .countdown {
            font-weight: bold;
            color: #dc3545;
        }

        .help-text {
            background: rgba(58, 76, 145, 0.06);
            border: 1px solid rgba(58, 76, 145, 0.18);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .help-text h6 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .help-text ul {
            margin: 0;
            padding-left: 20px;
        }

        .help-text li {
            margin-bottom: 5px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .btn-outline-secondary {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-outline-secondary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        @media (max-width: 576px) {
            .confirmation-card {
                padding: 30px 20px;
                margin: 10px;
            }

            .confirmation-header .icon-wrapper {
                width: 65px;
                height: 65px;
            }

            .confirmation-header .icon-wrapper i {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-header">
                <div class="icon-wrapper">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h2>Confirmação de Formando</h2>
                <p>Para aceder ao sistema, confirme o seu código de formando</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($remainingAttempts === 0 && $lockoutTime > 0): ?>
                <div class="attempts-info">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Tentativas esgotadas!</strong><br>
                    Aguarde <span class="countdown" id="countdown"><?php echo floor($lockoutTime / 60); ?>m <?php echo $lockoutTime % 60; ?>s</span> para tentar novamente.
                </div>
            <?php elseif ($remainingAttempts < 3 && $remainingAttempts > 0): ?>
                <div class="attempts-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Tentativas restantes: <strong><?php echo $remainingAttempts; ?></strong>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="confirmationForm">
                <?php CSRFProtection::generateToken(); ?>

                <div class="form-group">
                    <label for="codigo_formando" class="form-label">
                        <i class="fas fa-id-card me-2"></i>Código do Formando
                    </label>
                    <input type="number"
                        class="form-control"
                        id="codigo_formando"
                        name="codigo_formando"
                        placeholder="Digite seu código de formando"
                        required
                        min="1"
                        max="9999999999"
                        <?php echo ($remainingAttempts === 0 && $lockoutTime > 0) ? 'disabled' : ''; ?>>
                    <div class="form-text">
                        Digite o código numérico fornecido pela instituição.
                    </div>
                </div>

                <button type="submit"
                    class="btn btn-confirm"
                    id="submitBtn"
                    <?php echo ($remainingAttempts === 0 && $lockoutTime > 0) ? 'disabled' : ''; ?>>
                    <i class="fas fa-check me-2"></i>Confirmar Formando
                </button>
            </form>
            <div class="text-center mt-3">
                <a href="/estagio/login" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Login
                </a>
            </div>
            <div class="help-text">
                <h6><i class="fas fa-question-circle me-2"></i>Precisa de ajuda?</h6>
                <ul>
                    <li>O código do formando foi fornecido quando você se matriculou</li>
                    <li>Caso não lembre, entre em contato com a coordenação do curso</li>
                    <li>Você tem até 3 tentativas antes de um bloqueio temporário</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown timer
        <?php if ($remainingAttempts === 0 && $lockoutTime > 0): ?>
            let timeLeft = <?php echo $lockoutTime; ?>;
            const countdownElement = document.getElementById('countdown');
            const submitBtn = document.getElementById('submitBtn');
            const inputField = document.getElementById('codigo_formando');

            const timer = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = minutes + 'm ' + seconds + 's';

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    countdownElement.textContent = '0m 0s';
                    // Redirect to login after lockout period
                    window.location.href = '/estagio/login';
                } else {
                    timeLeft--;
                }
            }, 1000);
        <?php endif; ?>

        // Form validation
        document.getElementById('confirmationForm').addEventListener('submit', function(e) {
            const codigoInput = document.getElementById('codigo_formando');
            const codigo = codigoInput.value.trim();

            if (!codigo || codigo.length === 0) {
                e.preventDefault();
                alert('Por favor, digite seu código de formando.');
                codigoInput.focus();
                return false;
            }

            if (codigo.length > 10) {
                e.preventDefault();
                alert('Código de formando muito longo. Máximo 10 dígitos.');
                codigoInput.focus();
                return false;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
            submitBtn.disabled = true;
        });

        // Auto-focus on input
        document.addEventListener('DOMContentLoaded', function() {
            const inputField = document.getElementById('codigo_formando');
            if (!inputField.disabled) {
                inputField.focus();
            }
        });
    </script>
</body>

</html>