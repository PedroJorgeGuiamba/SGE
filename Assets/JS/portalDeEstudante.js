$(document).ready(function () {
  // Estado por seção
  let currentSection = "pedidos";
  let paginationState = {
    pedidos: { page: 1, data: [] },
    credenciais: { page: 1, data: [] },
    visitas: { page: 1, data: [] },
    avaliacoes: { page: 1, data: [] },
  };
  const rowsPerPage = 4;

  // Funções específicas para cada tipo
  const endpoints = {
    pedidos: "/estagio/api/historico-pedidos",
    credenciais: "/estagio/api/historico-credencial",
    visitas: "/estagio/api/visitas",
    avaliacoes: "/estagio/api/historico-avalicoes",
  };

  const renderFunctions = {
    pedidos: renderPedidoRow,
    credenciais: renderCredencialRow,
    visitas: renderVisitaRow,
    avaliacoes: renderAvaliacaoRow,
  };

  function renderPedidoRow(pedido) {
    return `
            <tr>
                <td><input type="checkbox" class="form-check-input select-checkbox" value="${pedido.id_pedido_carta}"></td>
                <td><span class="fw-semibold text-primary">${pedido.numero}</span></td>
                <td>${pedido.nome}</td>
                <td>${pedido.apelido}</td>
                <td><code style="color:#3a4c91;">${pedido.codigo_formando}</code></td>
                <td>${pedido.qualificacao_descricao ?? pedido.qualificacao}</td>
                <td>${pedido.turma}</td>
                <td>${pedido.data_do_pedido.split("-").reverse().join("/")}</td>
                <td>${pedido.hora_do_pedido}</td>
                <td>${pedido.empresa}</td>
                <td>${pedido.contactoPrincipal}</td>
                <td>${pedido.contactoSecundario}</td>
                <td>${pedido.email}</td>
            </tr>
        `;
  }

  function renderCredencialRow(credencial) {
    return `
            <tr>
              <td><input type="checkbox" class="select-checkbox" value="${credencial.id_credencial}"></td>
              <td>${credencial.id_credencial}</td>
              <td>${credencial.nome}</td>
              <td>${credencial.apelido}</td>
              <td>${credencial.codigo_formando}</td>
              <td>${credencial.contactoFormando}</td>
              <td>${credencial.email}</td>
              <td>${credencial.empresa}</td>
              <td>${credencial.data_do_pedido.split('-').reverse().join('/')}</td>
              <td>${credencial.id_pedido_carta}</td>
              <td>
                  ${credencial.carta_path
                      ? `<a href="${credencial.carta_path}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info" title="Visualizar Carta">
                          <i class="fas fa-file-alt"></i>
                      </a>`
                      : `<span class="text-muted" title="Nenhuma carta anexada">
                              <i class="fas fa-file-slash" style="font-size: 1.1rem; opacity: 0.4;"></i>
                          </span>`
                  }
              </td>
          </tr>
        `;
  }

  function renderVisitaRow(visita) {
    return `
            <tr>
              <td><input type="checkbox" class="select-checkbox" value="${visita.id_visita}" data-id="${visita.id_visita}"></td>
              <td><span class="fw-semibold">${visita.id_visita || 'N/A'}</span></td>
              <td>${visita.nome || '-'}</td>
              <td>${visita.apelido || '-'}</td>
              <td>${visita.codigo_formando || '-'}</td>
              <td>${visita.contactoFormando || '-'}</td>
              <td>${visita.empresa || '-'}</td>
              <td>${visita.endereco || '-'}</td>
              <td>${visita.nomeSupervisor || '-'}</td>
              <td>${visita.contactoSupervisor || '-'}</td>
              <td>${visita.dataHoraDaVisita}</td>
              <td>${visita.data_do_pedido}</td>
              <td>${visita.id_pedido_carta || '-'}</td>
              <td>
                  ${visita.status || 'Desconhecido'}
              </td>
            </tr>
        `;
  }

  function renderAvaliacaoRow(avaliacao) {
    return `
            <tr>
                <td><input type="checkbox" class="form-check-input select-checkbox" value="${avaliacao.id_avaliacao}"></td>
                <td>${avaliacao.nome}</td>
                <td>${avaliacao.apelido}</td>
                <td><code style="color:#3a4c91;">${avaliacao.codigo_formando}</code></td>
                <td>${avaliacao.qualificacao_descricao}</td>
                <td>${avaliacao.turma}</td>
                <td>${avaliacao.empresa}</td>
                <td>
                    <span class="badge bg-${avaliacao.resultado === "aprovado" ? "success" : avaliacao.resultado === "pendente" || !avaliacao.resultado ? "warning" : "danger"}">
                        ${avaliacao.resultado || "Pendente"}
                    </span>
                </td>
                <td>
                    ${avaliacao.doc_path ? `<a href="${avaliacao.doc_path}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file-pdf"></i> Ver</a>` : "N/A"}
                </td>
            </tr>
        `;
  }

  function renderTable(section) {
    const state = paginationState[section];
    const renderFunc = renderFunctions[section];
    const tbodyId = `#${section}Tbody`;

    $(tbodyId).empty();

    const start = (state.page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageData = state.data.slice(start, end);

    if (pageData.length === 0) {
      $(tbodyId).append(`
                <tr>
                    <td colspan="15">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p class="fw-semibold mt-2">Nenhum registro encontrado</p>
                            <small>Crie um novo registro para começar</small>
                        </div>
                    </td>
                </tr>
            `);
    } else {
      pageData.forEach((item) => {
        $(tbodyId).append(renderFunc(item));
      });
    }

    renderPagination(section);
  }

  function buscarDados(section, pesquisa = "") {
    $.ajax({
      url: endpoints[section],
      method: "GET",
      data: { termo: pesquisa },
      dataType: "json",
      success: function (data) {
        if (data.error) {
          $(`#${section}Tbody`).html(`
                        <tr><td colspan="15" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                            ${data.error}
                        </td></tr>
                    `);
          return;
        }

        paginationState[section].data = Array.isArray(data) ? data : [];
        paginationState[section].page = 1;
        renderTable(section);
      },
      error: function (xhr) {
        console.error(`Erro ao carregar ${section}:`, xhr.responseText);
        $(`#${section}Tbody`).html(`
                    <tr><td colspan="15" class="text-center text-danger py-4">
                        <i class="fas fa-database fa-2x mb-2 d-block"></i>
                        Erro ao carregar dados do servidor
                    </td></tr>
                `);
      },
    });
  }

  // Controle de abas
  $(".tab-btn").on("click", function () {
    const newSection = $(this).data("tab");
    if (currentSection === newSection) return;

    $(".tab-btn").removeClass("active");
    $(this).addClass("active");

    $(".tab-section").hide();
    $(`#${newSection}-section`).show();

    currentSection = newSection;

    // Carregar dados se ainda não foram carregados
    if (paginationState[currentSection].data.length === 0) {
      buscarDados(
        currentSection,
        $(`#${currentSection}Search`).val()?.trim() || "",
      );
    }

    // Recarregar paginação
    renderPagination(currentSection);
  });

  // Pesquisa por seção
  $(document).on("input", ".section-search", function () {
    const section = $(this).data("section");
    buscarDados(section, $(this).val().trim());
  });

  // Função de paginação (adaptada para a seção atual)
  function renderPagination(section) {
    const state = paginationState[section];
    const totalPages = Math.ceil(state.data.length / rowsPerPage);
    const paginationId = `#${section}Pagination`;

    $(paginationId).empty();
    if (totalPages <= 1) return;

    // [Código de paginação similar ao seu, mas com links adaptados]
    for (let i = 1; i <= Math.min(totalPages, 5); i++) {
      $(paginationId).append(`
                <li class="page-item ${i === state.page ? "active" : ""}">
                    <a class="page-link" href="#" data-section="${section}" data-page="${i}">${i}</a>
                </li>
            `);
    }

    $(paginationId).on("click", ".page-link", function (e) {
      e.preventDefault();
      const page = parseInt($(this).data("page"));
      const targetSection = $(this).data("section");

      if (
        !isNaN(page) &&
        page >= 1 &&
        page <= totalPages &&
        page !== paginationState[targetSection].page
      ) {
        paginationState[targetSection].page = page;
        renderTable(targetSection);
      }
    });
  }

  // Carregar dados iniciais
  buscarDados("pedidos", "");

  // Select All para cada tabela (adaptado)
  $(document).on("change", ".selectAll", function () {
    const section = $(this).data("section");
    $(`#${section}Tbody .select-checkbox`).prop("checked", this.checked);
  });
});
