estagio
├── Conexao/
│   └── conector.php
│   │
├── Assets/
│   └── Estrutura/
│   │   └── SGE.md
│   └── img/
│   │
│   ├── Model/
│   │   └── Usuario.php
│   │   └── Estagio.php
│   │   └── Empresa.php
│   │   └── Curso.php
│   │   └── Formador.php
│   │   └── Formando.php
│   │   └── PedidoDeCarta.php
│   │   └── Pessoa.php
│   │   └── Turma.php
│   │   └── Supervisor.php
│   │
│   ├── Controller/
│   │   └── auth/
│   │   │   └── AuthController.php
│   │   │   └── RegisterController.php
│   │   │   └── .env
│   │   │   └── AuthMailSender.py
│   │   │   └── AuthConfirmation.php
│   │   │   └── LogoutController.php
│   │   └── Formando/
│   │   │   └── Home.php
│   │   │   └── CadastrarFormando.php
│   │   └── Formador/
│   │   │   └── Home.php
│   │   └── Supervisor/
│   │   │   └── Home.php
│   │   └── Admin/
│   │   │   └── Home.php
│   │   └── Curso/
│   │   │   └── CadastrarCuro.php
│   │   │   └── getCursos.php
│   │   └── Estagio/
│   │   │   └── editarPedido.php
│   │   │   └── GerarPdfCarta.php
│   │   │   └── FormularioDeCartaDeEstagio.php
│   │   │   └── remover_pedidos.php
│   │   │   └── search_pedidos.php
│   │   └── Modulos/
│   │   │   └── getModulos.php
│   │   └── Qualificacoes/
│   │   │   └── getQualificacoes.php
│   │   └── Turmas/
│   │   │   └── CadastrarTurmas.php
│   │   │   └── getTurmas.php
│   │
│   ├── Style/
│   │   └── Home.css
│   │   └── login.css
│   │
│   ├── Helpers/
│   │   └── Sessao.php
│   │   └── auth.php
│   │   └── Actividade.php
│   │   └── Criptografia.php
│   │
│   ├── middleware/
│   │   └── auth.php
│   │
│   ├── Temp/
│   │
│   ├── View/
│   │   └── Admin/
│   │   │   └── portalDoAdmin.php
│   │   └── Auth/
│   │   │   └── ValidarUser.php
│   │   │   └── Register.php
│   │   └── Cursos/
│   │   │   └── CadastrarCurso.php
│   │   └── estagio/
│   │   │   └── CartaDePedido.php
│   │   │   └── detalhes_pedido.php
│   │   │   └── editarPedido.php
│   │   │   └── formularioDeAvaliacaoDeEstagio.php
│   │   │   └── formularioDeCartaDeEstagio.php
│   │   │   └── listaDePedido.php
│   │   │   └── situacaoDeEstagio.php
│   │   └── Admin/
│   │   │   └── portalDoAdmin.php
│   │   └── Formando/
│   │   │   └── portalDoFormando.php
│   │   │   └── CadastrarFormando.php
│   │   └── Formador/
│   │   │   └── portalDoFormador.php
│   │   │   └── CadastrarFormador.php
│   │   └── Modulos/
│   │   │   └── CadastrarModulo.php
│   │   └── Qualificacao/
│   │   │   └── CadastrarQualificacao.php
│   │   └── Turmas/
│   │   │   └── CadastrarTurma.php
│   │   └── Supervisor/
│   │   │   └── portalDoSupervisor.php
│   │   └── Login.php
├── Index.php
└──.gitignore
└──.gitattributes