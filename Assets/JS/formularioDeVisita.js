$.validator.addMethod(
  "telefone_mz",
  function (value, element) {
    if (this.optional(element)) return true;
    return /^(\+258)?[ -]?[8][2-8][0-9]{7}$/.test(value);
  },
  "Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx",
);

// Validação do formulário
$("#formularioEstagio").validate({
  rules: {
    codigoFormando: {
      required: true,
      digits: true,
      minlength: 5,
    },
    contactoFormando: {
      required: true,
      telefone_mz: true,
    },
    empresa: {
      required: true,
      minlength: 2,
    },
    endereco: {
      required: true,
      minlength: 2,
    },
    nome_supervisor: {
      required: true,
      minlength: 2,
    },
    contacto_supervisor: {
      required: true,
      telefone_mz: true,
    },
    datahora: {
      required: true,
    },
  },
  messages: {
    codigoFormando: {
      required: "Campo obrigatório.",
      digits: "Apenas números são permitidos.",
      minlength: "O Código deve ter pelo menos 5 caracteres.",
    },
    contactoFormando: {
      required: "Campo obrigatório.",
      telefone_mz: "Número inválido. Ex: +258 84xxxxxxx",
    },
    empresa: {
      required: "Informe o nome da empresa.",
      minlength: "O nome deve ter pelo menos 2 caracteres.",
    },
    endereco: {
      required: "Informe o endereço da empresa.",
      minlength: "O endereço deve ter pelo menos 2 caracteres.",
    },
    nome_supervisor: {
      required: "Informe o nome do supervisor.",
      minlength: "O nome deve ter pelo menos 2 caracteres.",
    },
    contacto_supervisor: {
      required: "Campo obrigatório.",
      telefone_mz: "Número inválido. Ex: +258 84xxxxxxx",
    },
    datahora: {
      required: "Informe a data do pedido.",
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
