$.validator.addMethod('telefone_mz', function(value, element) {
    if (this.optional(element)) return true;
    return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
}, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');

// Validação do formulário
$("#formularioEstagio").validate({
    rules: {
        codigoFormando: {
            required: true,
            digits: true
        },
        empresa: {
            required: true,
            minlength: 2
        },
        contactoFormando: {
            required: true,
            telefone_mz: true
        },
        email: {
            required: true,
            email: true
        }
    },
    messages: {
        codigoFormando: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        empresa: {
            required: "Informe o nome da empresa.",
            minlength: "O nome deve ter pelo menos 2 caracteres."
        },
        contactoFormando: {
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