$(document).ready(function () {
  $("#formEditarPedido").submit(function (e) {
    e.preventDefault();
    console.log("Dados enviados:", $(this).serialize()); // Log dos dados enviados
    $.ajax({
      url: $(this).attr("action"),
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          alert(response.message);
          window.location.href = "/estagio/avaliacao-estagio/listar";
        } else {
          alert(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.log("Erro AJAX:", xhr.status, status, error, xhr.responseText); // Log detalhado do erro
        alert(
          "Erro ao processar a requisição: " +
            (xhr.responseText || "Verifique o console para mais detalhes."),
        );
      },
    });
  });
});

$("#formEditarPedido").validate({
  rules: {
    codigo_formando: {
      required: true,
      digits: true,
    },
    empresa: {
      required: true,
      minlength: 2,
    }
  },
  messages: {
    codigo_formando: {
      required: "Campo obrigatório.",
      digits: "Apenas números são permitidos.",
    },
    empresa: {
      required: "Informe o nome da empresa.",
      minlength: "O nome da empresa deve ter pelo menos 2 caracteres.",
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
});
