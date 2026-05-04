// Countdown timer
function initializeCountdownTimer() {
    const countdownElement = document.getElementById('countdown');
    const submitBtn = document.getElementById('submitBtn');
    const inputField = document.getElementById('codigo_formando');
    
    if (!countdownElement) return;
    
    let timeLeft = parseInt(countdownElement.textContent);
    if (isNaN(timeLeft)) return;

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
}

// Form validation
function initializeFormValidation() {
    const form = document.getElementById('confirmationForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
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
}

// Auto-focus on input
function initializeAutoFocus() {
    document.addEventListener('DOMContentLoaded', function() {
        const inputField = document.getElementById('codigo_formando');
        if (inputField && !inputField.disabled) {
            inputField.focus();
        }
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCountdownTimer();
    initializeFormValidation();
    initializeAutoFocus();
});
