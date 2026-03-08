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
