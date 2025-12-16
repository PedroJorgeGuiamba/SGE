<?php
require_once __DIR__ . '/../../Conexao/conector.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = 0;
if (isset($_GET['id_pedido_carta'])) {
    $id = (int)$_GET['id_pedido_carta'];
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if ($id <= 0) {
    die('ID do pedido não fornecido ou inválido.');
}

$conexao = new Conector();
$conn = $conexao->getConexao();

$sql = "SELECT
            p.*,
            q.descricao AS qualificacao_descricao,
            q.nivel AS qualificacao_nivel,
            s.nome_supervisor as nomeS,
            s.area as area,
            c.codigo_qualificacao as cq,
            c.nome as curso
        FROM pedido_carta p
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN supervisor s ON s.id_qualificacao = p.qualificacao
        LEFT JOIN curso c ON c.codigo_qualificacao = q.id_qualificacao
        WHERE p.id_pedido_carta = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pedido não encontrado.");
}

$dados = $result->fetch_assoc();

$nomeCompleto = htmlspecialchars($dados['nome'] . ' ' . $dados['apelido']);
$qualificacao = htmlspecialchars($dados['qualificacao_descricao'] ?? '');
$nivel = htmlspecialchars($dados['qualificacao_nivel'] ?? '');
$empresa = htmlspecialchars($dados['empresa'] ?? '');
$codigo = htmlspecialchars($dados['codigo_formando'] ?? '');
$dataFormatada = date('j \\d\\e F \\d\\e Y', strtotime($dados['data_do_pedido'] ?? ''));

// Formata a data em português: "15 de dezembro de 2025"
// $dataFormatada = $dados['data_do_pedido'] ?? date('Y-m-d');
// // Formata a data em português: "15 de dezembro de 2025"
// $dt = new DateTime($dataFormatada);
// $formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
// $formatter->setPattern('d \'de\' MMMM \'de\' yyyy');
// $dataPedido = ucfirst($formatter->format($dt));

$dataCurta = date('j de F de Y', strtotime($dados['data_do_pedido'] ?? ''));
$ano = date('Y', strtotime($dados['data_do_pedido'] ?? ''));
$ref = $dados['numero'];
$coordenador = $dados['nomeS'];
$area = $dados['area'];
$contacto1 = htmlspecialchars($dados['contactoPrincipal'] ?? '');
$contacto2 = htmlspecialchars($dados['contactoSecundario'] ?? '');
$email = htmlspecialchars($dados['email'] ?? '');
$curso = htmlspecialchars($dados['curso']);
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="utf-8">
    <title>Pacote Completo Estágio - <?= $nomeCompleto ?></title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 13pt; 
            line-height: 1.6; 
            margin: 50px 70px 50px 80px; 
        }
        header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid black;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        header img { width: 100px; }
        .ref { margin: 20px 0; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .assinatura { margin-top: 60px; text-align: center; }
        footer { 
            position: fixed; bottom: 40px; left: 0; right: 0; font-size: 9pt; text-align: center; border-top: 1px solid black; padding-top: 8px; }
        .page-break { page-break-after: always; }
        ul { padding-left: 40px; }
        li { margin-bottom: 8px; }
    </style>
</head>
<body>

<!-- PÁGINA 1 - CREDENCIAL -->
<header>
    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
    <h3>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h3>
</header>

<p>À<br><b><?= $empresa ?></b><br><span class="underline">Maputo</span></p>

<p class="ref">
    <b>N. Ref:</b> <?= $ref ?>/ITC/<?= $ano ?><br>
    <b>Maputo,</b> <?= $dataFormatada?>
</p>

<p class="center bold">CREDENCIAL</p>

<p>Sirvo-me pela presente, para enviar o senhor <b><?= $nomeCompleto ?></b>, estudante do curso de <b><?= $curso ?></b>, neste instituto para se apresentar ao Departamento de Recursos Humanos da Empresa Supracitada, a fim de realizar o estágio académico no departamento de TIC.</p>

<p>Sem outro assunto de momento, agradecemos o privilégio que a V. Exas. nos concedem em receber os nossos estudantes, permitindo assim adquirir conhecimentos e contribuindo para a melhoria de suas qualidades profissionais.</p>

<div class="assinatura">
    <p>O coordenador do Estágio Curricular em <?= $area ?></p>
    <p><b>(Dr. <?= $coordenador  ?>)</b></p>
    <img src="http://localhost/estagio/Assets/img/Assinatura-removebg-preview.png" style="width:120px; margin-top:10px;">
</div>

<div class="page-break"></div>

<!-- PÁGINA 2 - Termos de Referência - Administração de Sistemas e Redes -->
<header>
    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
    <h3>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h3>
</header>

<p>Ao <span class="bold">Supervisor</span><br>Do Estágio Curricular<br>Do Estudante <b><?= $nomeCompleto ?></b><br>Na Instituição <?= $empresa ?></p>

<p class="center bold">Termos de referência de estágio curricular para Técnico de Administração de Sistemas e Redes</p>

<p>Exmo. senhor supervisor, enviamos o estudante <b><?= $nomeCompleto ?></b>, para um estágio de 160 horas na vossa instituição, e pedimos que a vossa supervisão oriente-o a ganhar e/ou fortificar competências técnicas dentro das seguintes directrizes:</p>

<ul>
    <li>Instalação, configuração, exploração, manutenção e resolução de problemas com componentes de sistemas de computadores e redes empresariais, nomeadamente servidores, equipamentos de rede, serviços web, sistemas de armazenamento, etc, assegurando as necessidades de comunicação e requisitos de serviço da organização.</li>
    <li>Instalação, configuração, exploração, manutenção e resolução de problemas com software comum (sistema operativo, serviços de email, serviços web, sistemas de armazenamento, etc), assegurando as necessidades de comunicação e requisitos de serviço da organização.</li>
</ul>

<p>Sem mais, agradecemos vossa colaboração.</p>

<div class="assinatura">
    <p>O coordenador do Estágio Curricular em <?= $area ?></p>
    <p><b>(Dr. <?= $coordenador  ?>)</b></p>
    <img src="http://localhost/estagio/Assets/img/Assinatura-removebg-preview.png" style="width:120px; margin-top:10px;">
</div>

<div class="page-break"></div>

<!-- PÁGINA 3 - Termos de Referência - Suporte Informático -->
<header>
    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
    <h3>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h3>
</header>

<p>Ao Supervisor<br>Do Estágio Curricular<br>Do Estudante <b><?= $nomeCompleto ?></b><br>Na Instituição <?= $empresa ?></p>

<p class="center bold">Termos de referência de estágio curricular em Técnico de Suporte Informático</p>

<p>Exmo. senhor supervisor, enviamos o estudante <b><?= $nomeCompleto ?></b>, para um estágio curricular de 160 horas na vossa empresa, e pedimos que a vossa supervisão oriente-o a ganhar e/ou fortificar competências técnicas dentro das seguintes directrizes:</p>

<ul>
    <li>Instalação, configuração, exploração, manutenção e resolução de problemas com hardware (computadores pessoais, redes, impressoras, dispositivos de armazenamento, periféricos e outros) em ambiente de escritório;</li>
    <li>Instalação, configuração, exploração, manutenção e resolução de problemas com software comum (sistema operativo, email, antivirus, Office, etc.) em ambiente de escritório;</li>
    <li>Exploração de recursos de Internet (criação de páginas da Internet);</li>
    <li>Programação na óptica de utilização (automação de tarefas com Macros e desenvolvimento de Base de Dados em Microsoft Office Access).</li>
</ul>

<p>Sem mais, agradecemos vossa colaboração.</p>

<div class="assinatura">
    <p>O coordenador do Estágio Curricular em <?= $area ?></p>
    <p><b>(Dr. <?= $coordenador  ?>)</b></p>
    <img src="http://localhost/estagio/Assets/img/Assinatura-removebg-preview.png" style="width:120px; margin-top:10px;">
</div>

<div class="page-break"></div>

<!-- PÁGINA 4 - Solicitação de visita de supervisão -->
<header>
    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
    <h3>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h3>
</header>

<p>À Direcção de Recursos Humanos<br>Da empresa <?= $empresa ?><br>Maputo</p>

<p class="assunto"><b>Assunto:</b> Solicitação de agendamento de visita de Supervisão de formando em estágio</p>

<p><b>N. Ref:</b> <?= $ref ?> -1032/GETFC/ITC/<?= $ano ?></p>

<p>Prezado(a) Senhor(a)</p>

<p>Vimos por meio desta, em nome do Instituto de Transportes e Comunicações (ITC), solicitar a possibilidade de agendamento de uma visita de supervisão do formando em estágio, a ser realizada com o objectivo de supervisionar o <b><?= $nomeCompleto ?></b> durante o seu período de estágio na instituição acolhedora.</p>

<p>A visita será realizada pelo(a) coordenador(a) e supervisor(a) de estágios da instituição de ensino, <b>Dr. <?= $coordenador  ?></b> e tem como área alvo o departamento de <?= $area ?>.</p>

<p>Agradecer a vossa atenção, oferecer nossos votos da mais elevada estima e consideração e colocar-nos à disposição para quaisquer esclarecimentos pelos contactos abaixo listados. Atenciosamente.</p>

<div class="assinatura">
    <p>O coordenador do Estágio Curricular em <?= $area ?></p>
    <p><b>(Dr. <?= $coordenador  ?>)</b></p>
    <img src="http://localhost/estagio/Assets/img/Assinatura-removebg-preview.png" style="width:120px; margin-top:10px;">
</div>

<div class="page-break"></div>

<!-- PÁGINA 5 - Carta de Solicitação de Estágio Profissional (a que você já tinha) + lista de cursos -->
<header>
    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
    <h3>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h3>
</header>

<p>À<br><b><?= $empresa ?></b><br>Ex.mo Sr. Direcção de Recursos Humanos<br><b>MAPUTO</b></p>

<p class="ref">
    <b>N. Ref: <?= $ref ?> - GETFC</b>/ITC/<?= $ano ?><br>
    <b>Maputo,</b> <?= $dataFormatada ?>
</p>

<p><span class="assunto bold">ASSUNTO:</span> <span class="underline">Estágio Profissional</span></p>

<p>Ex.mo Senhor,</p>

<p>O Instituto de Transportes e Comunicações é uma Instituição de Ensino Técnico-Profissional que leciona as qualificações técnicas de nível III, IV e V.</p>

<ul>
    <li>Contabilidade;</li>
    <li>Gestão de Empresas;</li>
    <li>Gestão Patrimonial e Financeira;</li>
    <li>Gestão de Recursos Humanos;</li>
    <li>Electricidade Industrial;</li>
    <li>Administração de Redes;</li>
    <li>Programação WEB, e</li>
    <li>Técnico de Suporte Informático;</li>
    <li>Técnico de Electromecânica.</li>
</ul>

<p>Em conformidade com o plano do processo docente da qualificação que enviamos em anexo, o Estágio Profissional é um componente muito importante das actividades práticas e é realizado no fim de cada nível da qualificação.</p>

<p>Sendo assim, vimos solicitar a aceitação do nosso aluno <b><?= $nomeCompleto ?></b>, com o código <b><?= $codigo ?></b>, para estagiar na área de <b><?= $curso ?>.</b></p>

<p>Os objectivos do Estágio Profissional são os seguintes:</p>

<ul>
    <li>Proporcionar ao aluno a elaboração de Termos de Referência de uma experiência de trabalho a realizar na organização;</li>
    <li>Realizar as actividades de acordo com os termos de referência da organização;</li>
    <li>Elaboração do relatório da experiência de trabalho.</li>
</ul>

<p>O estágio profissional é de carácter individual.</p>

<p>Cientes de que a nossa preocupação merecerá a atenção de V. Excia., agradecemos antecipadamente a colaboração e endereçamos os nossos melhores cumprimentos.</p>

<div class="assinatura">
    <p>O Coordenador de Estágios,</p>
    <p><b>(Dr. <?= $coordenador  ?>)</b></p>
    <img src="http://localhost/estagio/Assets/img/Assinatura-removebg-preview.png" style="width:120px; margin-top:10px;">
</div>

<p><b>Contacto(s):</b> (+258) <?= $contacto1 ?> / (+258) <?= $contacto2 ?><br>
<b>Email:</b> <?= $email ?></p>

</body>
</html>