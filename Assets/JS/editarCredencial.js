$(document).ready(function() {
    $('#formEditarPedido').submit(function(e) {
        e.preventDefault();
        console.log('Dados enviados:', $(this).serialize()); // Log dos dados enviados
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '/estagio/credencial/listar';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText); // Log detalhado do erro
                alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
            }
        });
    });
});

$.validator.addMethod('telefone_mz', function(value, element) {
    if (this.optional(element)) return true;
    return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
}, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');


$("#formEditarPedido").validate({
    rules: {
        codigo_formando: {
            required: true,
            digits: true
        },
        nome: {
            required: true,
            minlength: 2
        },
        apelido: {
            required: true,
            minlength: 2
        },
        empresa: {
            required: true,
            minlength: 2
        },
        contactoPrincipal: {
            required: true,
            telefone_mz: true
        },
        contactoSecundario: {
            required: true,
            telefone_mz: true
        },
        email: {
            required: true,
            email: true
        }
    },
    messages: {
        codigo_formando: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        nome: {
            required: "Informe o nome",
            minlength: "O nome deve ter pelo menos 2 caracteres."
        },
        apelido: {
            required: "Informe o apelido",
            minlength: "O apelido deve ter pelo menos 2 caracteres."
        },
        empresa: {
            required: "Informe o nome da empresa.",
            minlength: "O nome da empresa deve ter pelo menos 2 caracteres."
        },
        contactoPrincipal: {
            required: "Campo obrigatório.",
            telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
        },
        contactoSecundario: {
            required: "Campo obrigatório.",
            telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
        },
        email: {
            required: "Informe o e-mail.",
            email: "Endereço de e-mail inválido."
        }
    },
    errorClass: "is-invalid",
    validClass: "is-valid",
    highlight: function(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
    },
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    }
});