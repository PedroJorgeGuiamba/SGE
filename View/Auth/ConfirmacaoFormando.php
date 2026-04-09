<?php
require_once __DIR__ . '/../../Controller/Auth/ConfirmacaoFormandoController.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

session_start();

// Verificar se o usuário está logado e é um formando
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'formando') {
    header("Location: /estagio/View/Auth/login.php");
    exit();
}

// Verificar se o usuário já confirmou o código do formando
if (isset($_SESSION['codigo_formando'])) {
    // Usuário já confirmou, redirecionar para o portal
    header("Location: /estagio/View/Formando/portalDeEstudante.php");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/home.css">
    <style>
        .confirmation-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirmation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .confirmation-header i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .confirmation-header h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .confirmation-header p {
            color: #666;
            font-size: 1.1rem;
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
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-confirm:disabled {
            background: #ccc;
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
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .attempts-info i {
            color: #ffc107;
        }

        .countdown {
            font-weight: bold;
            color: #dc3545;
        }

        .help-text {
            background: rgba(0, 123, 255, 0.1);
            border: 1px solid rgba(0, 123, 255, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .help-text h6 {
            color: #0056b3;
            margin-bottom: 10px;
        }

        .help-text ul {
            margin: 0;
            padding-left: 20px;
        }

        .help-text li {
            margin-bottom: 5px;
            color: #666;
        }

        @media (max-width: 576px) {
            .confirmation-card {
                padding: 30px 20px;
                margin: 10px;
            }

            .confirmation-header i {
                font-size: 3rem;
            }
        }
    </style>
</head>

<body>
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-header">
                <i class="fas fa-user-graduate"></i>
                <h2>Confirmação de Formando</h2>
                <p>Para acessar o sistema, confirme seu código de formando</p>
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
                <a href="/estagio/View/Auth/login.php" class="btn btn-outline-secondary">
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
                    window.location.href = '/estagio/View/Auth/Login.php';
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