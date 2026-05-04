// Verificar se jQuery está carregado
if (typeof jQuery === 'undefined') {
    console.error('jQuery não foi carregado!');
} else {
    console.log('jQuery carregado com sucesso, versão:', jQuery.fn.jquery);
}

// Validação do formulário
$("#formularioAvaliacao").validate({
    rules: {
        resultado: {
            required: true
        }
    },
    messages: {
        resultado: {
            required: "Informe o resultado."
        }
    },
    errorClass: "is-invalid",
    validClass: "is-valid",
    highlight: function (element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function (element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    },
    submitHandler: function(form) {
        // Usar FormData para suportar upload de arquivos
        var formData = new FormData(form);
        
        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = response.redirect;
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
                alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
            }
        });
        return false;
    }
});
