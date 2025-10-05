# SGE - Sistema de Gestão de Estágios

## Visão geral
O SGE (Sistema de Gestão de Estágios) é uma aplicação web desenvolvida para gerenciar todo o ciclo de estágios em uma instituição de ensino. O sistema permite o cadastro e autenticação de usuários (administradores, formadores, formandos, supervisores), gestão de cursos, turmas e qualificações, submissão e acompanhamento de pedidos de carta de estágio, geração de PDFs (cartas), e funcionalidades administrativas para gerir o fluxo de estágios.

## Funcionalidades principais
- Autenticação e registro de usuários (login, logout, confirmação de conta).
- Papéis de usuário: Admin, Formador, Formando, Supervisor, cada um com painéis dedicados.
- Gestão de Cursos, Módulos, Qualificações e Turmas.
- Cadastro de Formandos, Formadores e Supervisores.
- Submissão de pedidos de carta de estágio pelos formandos.
- Edição, pesquisa e remoção de pedidos de estágio.
- Geração de PDF para cartas de estágio (exportar/baixar).
- Sistema de sessão e encriptação de dados sensíveis.
- Templates de visualização para diferentes tipos de usuários.

## Estrutura do projeto (resumo)
- `Index.php` - Ponto de entrada principal.
- `Assets/` - Recursos estáticos (imagens, documentação, estilos).
- `Conexao/` - Scripts de conexão com a base de dados (ex: `conector.php`, `bd.sql`).
- `Controller/` - Lógica de controle do sistema, dividido por áreas (Admin, Auth, Cursos, Estagio, Formador, Formando, Modulos, Supervisor, Turmas).
- `Helpers/` - Funções utilitárias (criptografia, sessão, etc.).
- `middleware/` - Middleware de autenticação e autorização.
- `Model/` - Modelos de dados representando entidades como `Curso`, `Formando`, `Estagio`, `Usuario`.
- `View/` - Arquivos de exibição/templating para várias funcionalidades e papéis.
- `Style/` - Arquivos CSS.
- `Temp/` - Pasta temporária para arquivos gerados.

## Tecnologias e dependências
- PHP (servidor: XAMPP/Apache + PHP)
- MySQL (base de dados)
- HTML/CSS/Javascript(Jquery + Ajax)/Bootstrap para as views

## Como executar localmente (resumo)
1. Instale o XAMPP e inicie Apache e MySQL.
2. Copie o projeto para a pasta `htdocs` do XAMPP (ex: `C:\xampp\htdocs\estagio`).
3. Crie a base de dados MySQL e importe `Conexao/bd.sql` se disponível.
4. Atualize as credenciais de conexão em `Conexao/conector.php` conforme seu ambiente.
5. Acesse `http://localhost/estagio/` no navegador e use as rotas/páginas de login.

## Contato
Para dúvidas e contribuições, abra uma issue no repositório ou contacte o mantenedor do projeto.
