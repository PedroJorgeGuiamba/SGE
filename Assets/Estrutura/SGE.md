estagio
├── .git/
├── .gitignore
├── .gitattributes
├── .htaccess
├── .idea/
├── .venv/
├── Index.php
├── router.php
├── README.md
│
├── Assets/
│ ├── CSS/
│ │ ├── chart.css
│ │ ├── formadores.css
│ │ ├── global.css
│ │ └── notifications.css
│ ├── JS/
│ │ ├── info-message-close.js
│ │ └── tema.js
│ ├── img/
│ └── Estrutura/
│ ├── README_DOCKER.md
│ ├── README_NGINX.md
│ ├── README.md
│ ├── SGE.md
│ ├── Dockerfile
│ └── nginx/
│
├── Conexao/
│ ├── conector.php
│ ├── bd.sql
│ └── bd_v2.sql
│
├── config/
│
├── Controller/
│ ├── Admin/
│ │ ├── Home.php
│ │ └── GerarPdfRelatorioEstagios.php
│ ├── Auth/
│ │ ├── .env
│ │ ├── AuthController.php
│ │ ├── AuthConfirmationController.php
│ │ ├── AuthMailSender.py
│ │ ├── ConfirmacaoFormandoController.php
│ │ ├── encriptionKey.php
│ │ └── LogoutController.php
│ ├── Cursos/
│ │ ├── CadastrarCurso.php
│ │ └── getCursos.php
│ ├── Estagio/
│ │ ├── AdicionarRespostaCarta.php
│ │ ├── FormularioDeCartaDeEstagio.php
│ │ ├── FormularioDeCredencialDeEstagio.php
│ │ ├── FormularioDeVisita.php
│ │ ├── GerarPdfCompleto.php
│ │ ├── GerarPdfCredencialCompleto.php
│ │ ├── GerarPdfRelatorio.php
│ │ ├── aprovar_visita.php
│ │ ├── avaliarEstagio.php
│ │ ├── editarCredencial.php
│ │ ├── editarPedido.php
│ │ ├── editarResposta.php
│ │ ├── editarVisita.php
│ │ ├── getNumero.php
│ │ ├── recusar_visita.php
│ │ ├── remover_pedido.php
│ │ ├── remover_resposta.php
│ │ ├── remover_visita.php
│ │ ├── search_credencial.php
│ │ ├── search_historico_credencial.php
│ │ ├── search_historico_pedidos.php
│ │ ├── search_historico_visita.php
│ │ ├── search_pedidos.php
│ │ ├── search_respostas.php
│ │ └── search_visita.php
│ ├── Formador/
│ │ └── Home.php
│ ├── Formando/
│ │ ├── Home.php
│ │ └── CadastrarFormando.php
│ ├── Geral/
│ │ ├── FormandoAdmin.php
│ │ ├── SupervisorAdmin.php
│ │ └── TrocarTema.php
│ ├── Modulos/
│ │ └── getModulos.php
│ ├── Notas/
│ │ ├── apagarNotas.php
│ │ ├── api.php
│ │ ├── editarNotas.php
│ │ └── lancarNotas.php
│ ├── Qualificacao/
│ │ └── getQualificacoes.php
│ ├── Seguranca/
│ │ ├── Home.php
│ │ └── RegistroDeEntrada.php
│ ├── Supervisor/
│ │ ├── Home.php
│ │ └── CadastrarSupervisor.php
│ ├── Turmas/
│ │ ├── CadastrarTurma.php
│ │ └── getTurmas.php
│ └── Usuarios/
│ └── getUsers.php
│
├── Helpers/
│ ├── Actividade.php
│ ├── Criptografia.php
│ ├── CSRFProtection.php
│ ├── NotificationHelper.php
│ ├── SecurityHeaders.php
│ ├── SecurityLogger.php
│ ├── Sessao.php
│ └── auth.php
│
├── Includes/
│ ├── footer.php
│ ├── footer-admin.php
│ ├── header-admin.php
│ ├── header-estagio-admin.php
│ ├── header-estagio-situacao-admin.php
│ ├── notification-widget.php
│ └── notification_init.php
│
├── middleware/
│ └── auth.php
│
├── Model/
│ ├── Admin.php
│ ├── Curso.php
│ ├── Empresa.php
│ ├── Estagio.php
│ ├── Formador.php
│ ├── Formando.php
│ ├── PedidoDeCarta.php
│ ├── PedidoDeCredencial.php
│ ├── PedidoDeVisita.php
│ ├── Pessoa.php
│ ├── RegistroAcesso.php
│ ├── Resposta.php
│ ├── Supervisor.php
│ ├── Turma.php
│ ├── Usuario.php
│ ├── Viatura.php
│ └── avaliarEstagio.php
│
├── Scripts/
│ └── gerar_relatorio_pdf.py
│
├── Tasks/
│ ├── corrigir_relacao_pedido_resposta.sql
│ ├── estagio_backup_e_truncate_relacionados.sql
│ ├── estagio_contagem_relacionados.sql
│ ├── estagio_truncate_relacionados.sql
│ ├── reunião-24-10-2025.txt
│ └── reunião-26-03-2026.txt
│
├── Temp/
│
├── View/
│ ├── Admin/
│ │ ├── Home.php
│ │ └── portalDoAdmin.php
│ ├── Auth/
│ │ ├── ConfirmacaoFormando.php
│ │ ├── Login.php
│ │ ├── Register.php
│ │ └── ValidarUser.php
│ ├── Cursos/
│ │ └── CadastrarCurso.php
│ ├── Erros/
│ │ └── error.php
│ ├── Formador/
│ │ ├── CadastrarFormador.php
│ │ ├── Home.php
│ │ └── portalDoFormador.php
│ ├── Formando/
│ │ ├── CadastrarFormando.php
│ │ ├── portalDeEstudante.php
│ │ └── portalDoFormando.php
│ ├── Modulos/
│ │ └── CadastrarModulo.php
│ ├── Notas/
│ │
│ ├── Qualificacao/
│ │ └── CadastrarQualificacao.php
│ ├── Seguranca/
│ │ └── portalDoSeguranca.php
│ ├── Supervisor/
│ │ ├── CadastrarSupervisor.php
│ │ ├── Home.php
│ │ └── portalDoSupervisor.php
│ ├── Turmas/
│ │ └── CadastrarTurma.php
│ └── estagio/
│ ├── Carta/
│ ├── PacoteEstagioCompleto.php
│ ├── PacotesCredencial.php
│ ├── adicionarRespostaCarta.php
│ ├── avaliarEstagio.php
│ ├── Credencial.php
│ ├── detalhes_pedido.php
│ ├── editarCredencial.php
│ ├── editarPedido.php
│ ├── editarResposta.php
│ ├── editarVisita.php
│ ├── Estagio.php
│ ├── formularioDeAvaliacaoDeEstagio.php
│ ├── formularioDeCartaDeEstagio.php
│ ├── formularioDeCredencialDeEstagio.php
│ ├── formularioDeVisita.php
│ ├── HistoricoDePedidos.php
│ ├── HistoricoDePedidosCredencial.php
│ ├── HistoricoDePedidosVisita.php
│ ├── listaDePedidos.php
│ ├── listaDePedidosCredencial.php
│ ├── listaDePedidosVisita.php
│ ├── previewCartaDeEstagio.php
│ ├── relatorio.php
│ ├── relatorio_impressao.php
│ ├── respostaCarta.php
│ └── situacaoDeEstagio.php
│
└── uploads/
└── avaliacaoEstagios/
