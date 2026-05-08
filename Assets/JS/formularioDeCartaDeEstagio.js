$(document).ready(function () {
  carregarQualificacoes();
  carregarTurmas();
});

function carregarQualificacoes() {
  $.ajax({
    url: "/estagio/api/qualificacao",
    method: "GET",
    success: function (resposta) {
      $("#qualificacao").html(resposta);
    },
    error: function (xhr, status, error) {
      console.error("Erro ao carregar qualificações:", status, error);
      $("#qualificacao").html("<option>Erro ao carregar qualificações</option>");
    }
  });
}

function carregarTurmas(qualificacaoId = null) {
  $.ajax({
    url: "/estagio/api/turmas",
    method: "GET",
    data: { termo: qualificacaoId || "" },
    success: function (resposta) {
      $("#turma").html(resposta);
    },
    error: function (xhr, status, error) {
      console.error("Erro ao carregar turmas:", status, error);
      $("#turma").html("<option>Erro ao carregar turmas</option>");
    }
  });
}

$('#qualificacao').on('change', function() {
  carregarTurmas($(this).val());
});

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
      digits: true
    },
    qualificacao: {
      required: true
    },
    turma: {
      required: true
    },
    dataPedido: {
      required: true,
      date: true
    },
    horaPedido: {
      required: true
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
    },
  },
  messages: {
    codigoFormando: {
      required: "Campo obrigatório.",
      digits: "Apenas números são permitidos."
    },
    qualificacao: {
      required: "Selecione uma qualificação."
    },
    turma: {
      required: "Selecione uma turma."
    },
    dataPedido: {
      required: "Informe a data do pedido.",
      date: "Formato inválido."
    },
    horaPedido: {
      required: "Informe a hora do pedido."
    },
    empresa: {
      required: "Informe o nome da empresa.",
      minlength: "O nome deve ter pelo menos 2 caracteres."
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
