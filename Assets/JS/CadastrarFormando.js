$.validator.addMethod(
  "telefone_mz",
  function (value, element) {
    if (this.optional(element)) return true;
    return /^(\+258)?[ -]?[8][2-8][0-9]{7}$/.test(value);
  },
  "Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx",
);

// Validação do formulário
$("#formularioFormando").validate({
  rules: {
    codigoformando: {
      required: true,
      digits: true,
      minlength: 5,
    },
    nomeformando: {
      required: true,
      minlength: 2,
    },
    apelidoformando: {
      required: true,
      minlength: 2,
    },
    dataNascimento: {
      required: true,
      date: true,
    },
    naturalidade: {
      required: true,
      minlength: 2,
    },
    tipoDeDocumento: {
      required: true,
      minlength: 2,
    },
    numeroDeDocumento: {
      required: true,
      minlength: 5,
    },
    localEmitido: {
      required: true,
      minlength: 2,
    },
    nuit: {
      required: true,
      digits: true,
      minlength: 9,
    },
    telefone: {
      required: true,
      telefone_mz: true,
    },
    email: {
      required: true,
      email: true,
    },
  },
  messages: {
    codigoformando: {
      required: "Campo obrigatório.",
      digits: "Apenas números são permitidos.",
      minlength: "O código deve ter pelo menos 5 digitos.",
    },
    nomeformando: {
      required: "Informe o nome do formando.",
      minlength: "O nome deve ter pelo menos 2 caracteres.",
    },
    apelidoformando: {
      required: "Informe o apelido do formando.",
      minlength: "O apelido deve ter pelo menos 2 caracteres.",
    },
    dataNascimento: {
      required: "Informe a data de nascimento.",
      date: "Formato inválido.",
    },
    naturalidade: {
      required: "Informe a Naturalidade.",
      minlength: "A naturalidade deve ter pelo menos 2 caracteres.",
    },
    tipoDeDocumento: {
      required: "Informe o O Tipo do Documento.",
      minlength: "O Tipo de Documento deve ter pelo menos 2 caracteres.",
    },
    numeroDeDocumento: {
      required: "Informe o Número do Documento.",
      minlength: "O Número do Documento deve ter pelo menos 5 caracteres.",
    },
    localEmitido: {
      required: "Informe o Local De Emissão do Documento.",
      minlength:
        "O Local De Emissão do Documento deve ter pelo menos 2 caracteres.",
    },
    nuit: {
      required: "Informe o nome da empresa.",
      digits: "Apenas números são permitidos.",
      minlength: "O NUIT deve ter 9 digitos exactos.",
    },
    telefone: {
      required: "Campo obrigatório.",
      telefone_mz: "Número inválido. Ex: +258 84xxxxxxx",
    },
    email: {
      required: "Informe o e-mail.",
      email: "Endereço de e-mail inválido.",
    },
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
});
