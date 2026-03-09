# SGE - Sistema de Gestao de Estagios

## Visao geral
O SGE (Sistema de Gestao de Estagios) e uma aplicacao web para gerir o ciclo completo de estagios numa instituicao de ensino.
O sistema cobre autenticacao, gestao academica, pedidos de carta de estagio, acompanhamento do estado do processo e operacoes administrativas.

## Funcionalidades principais
- Autenticacao e registo de utilizadores (login, logout e validacao de conta).
- Perfis por papel: Admin, Formador, Formando e Supervisor, com paineis dedicados.
- Gestao de cursos, modulos, qualificacoes e turmas.
- Cadastro de formandos, formadores e supervisores.
- Submissao, pesquisa, edicao e remocao de pedidos de carta de estagio.
- Emissao de cartas de estagio em PDF (individual e em lote/ZIP).
- Controlo de sessao, middleware de acesso e cabecalhos de seguranca.
- Views separadas por dominio funcional e perfil de utilizador.

## Atualizacoes recentes
- Reestruturacao do login para `View/Auth/Login.php`, com atualizacao dos redirecionamentos em `Index.php`, `middleware/auth.php` e helpers de autenticacao/sessao.
- Melhoria da pesquisa de pedidos (`Controller/Estagio/search_pedidos.php`) com filtro por empresa, nome, apelido e email, alem de dados de qualificacao e turma.
- Melhoria da pesquisa de respostas (`Controller/Estagio/search_respostas.php`) com associacao ao numero do pedido.
- Novo fluxo de geracao de documentos em `Controller/Estagio/GerarPdfCompleto.php`:
  - Geracao de PDF individual por pedido.
  - Geracao de varios PDFs em ficheiro ZIP (selecionando multiplos pedidos).
  - Logs tecnicos em `Temp/debug_gerar_pdf.log`.
  - Atualizacao da data de levantamento apos geracao do documento.
- Nova view consolidada `View/estagio/Estagio.php` e novos templates de carta em `View/estagio/Carta/`.
- Reutilizacao de scripts/rodape com `Includes/footer.php` e auto-fecho de alertas em `Assets/JS/info-message-close.js`.
- Ajustes de seguranca (CSP) em `Helpers/SecurityHeaders.php`.
- Atualizacao do `.gitignore` para ignorar artefactos temporarios como `Temp/` e `Tasks/`.

## Estrutura do projeto (resumo)
- `Index.php` - ponto de entrada principal.
- `Assets/` - recursos estaticos (JS, imagens, documentacao, estilos).
- `Config/` - configuracoes de ambiente (ex.: `.env`).
- `Conexao/` - ligacao a base de dados (ex.: `conector.php`, scripts SQL).
- `Controller/` - regras de negocio por modulo (Admin, Auth, Cursos, Estagio, Formador, Formando, Modulos, Supervisor, Turmas).
- `Helpers/` - utilitarios (seguranca, sessao, autenticacao, etc.).
- `Includes/` - componentes reutilizaveis de layout (header/footer).
- `middleware/` - verificacao de autenticacao e autorizacao.
- `Model/` - entidades e acesso a dados.
- `View/` - camadas de apresentacao por perfil e funcionalidade.
- `Style/` - estilos CSS.
- `Temp/` - ficheiros temporarios (PDF/ZIP e logs).
- `uploads/` - ficheiros enviados pelos utilizadores.

## Tecnologias e dependencias
- PHP (XAMPP/Apache + PHP).
- MySQL/MariaDB.
- HTML, CSS, JavaScript (jQuery + AJAX) e Bootstrap.
- `wkhtmltopdf` (para geracao de PDF).
- Extensao `php_zip` (para exportacao em ZIP).

## Como executar localmente
1. Instale o XAMPP e inicie os servicos Apache e MySQL.
2. Copie o projeto para `C:\xampp\htdocs\estagio`.
3. Crie a base de dados e importe o script SQL principal (ex.: `Conexao/bd.sql`, quando aplicavel).
4. Configure `Config/.env` com as credenciais corretas:
   - `DB_HOST`
   - `DB_USER`
   - `DB_PASS`
   - `DB_NAME_ITC`
   - `DB_PORT`
5. Garanta que o `wkhtmltopdf` esta instalado no caminho esperado:
   - `C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe`
6. Aceda a `http://localhost/estagio/`.

## Notas de seguranca
- Nao publique credenciais reais no repositorio.
- Em producao, desative `display_errors` e mantenha logs apenas em canais controlados.

## Contato
Para duvidas e contribuicoes, abra uma issue no repositorio ou contacte o mantenedor do projeto.
