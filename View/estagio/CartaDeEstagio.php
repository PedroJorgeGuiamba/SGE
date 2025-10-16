<?php
require_once __DIR__ . '/../../Conexao/conector.php';
// Mostra erros do MySQLi (para debug — remova em produção)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Recebe o parâmetro GET (numero)
$id = 0;
if (isset($_GET['numero'])) {
    $id = (int) $_GET['numero'];
} elseif (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
}

if ($id <= 0) {
    die('ID do pedido (numero) não fornecido ou inválido. Verifique a URL: ?numero=1');
}

$conexao = new Conector();
$conn = $conexao->getConexao();

// --- Consulta principal com JOIN ajustado ---
$sql = "SELECT
            p.*,
            q.descricao AS qualificacao_descricao,
            q.nivel AS qualificacao_nivel
        FROM pedido_carta p
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        WHERE p.numero = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Erro no prepare: ' . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Se não houver correspondência, checa se o pedido existe na tabela principal
    $stmt2 = $conn->prepare("SELECT * FROM pedido_carta WHERE numero = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if ($res2->num_rows === 0) {
        die("Pedido não encontrado (não existe linha em pedido_carta com numero = $id).");
    } else {
        // Pedido existe, mas a qualificação não foi encontrada
        $dados = $res2->fetch_assoc();
        $dados['qualificacao_descricao'] = '';
        $dados['qualificacao_nivel'] = '';
        $joinWarning = "Aviso: a qualificação (ID {$dados['qualificacao']}) não existe na tabela qualificacao.";
    }
} else {
    $dados = $result->fetch_assoc();
    $joinWarning = '';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Carta de Estágio - <?= htmlspecialchars($dados['nome'] . ' ' . $dados['apelido']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 50px;
            font-size: 13pt;
            line-height: 1.5;
        }
        header img {
            width: 100px;
            height: auto;
        }
        
        header {
            text-align: left;
            border-bottom: 1px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
        }
        .ref { margin-top: 10px; }
        .assunto {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 30px;
        }
        .assinatura {
            margin-top: 50px;
            text-align: center;
        }

        footer{
            font-size: 9pt;
            border-top: 1px solid black;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
        <h3>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h3>
    </header>

    <section>
        <p>À<br>
            <b><?= htmlspecialchars($dados['empresa'] ?? '') ?></b><br>
            Ex.mo Sr. Direcção de Recursos Humanos<br>
            <b>MAPUTO</b>
        </p>

        <p class="ref">
            <b>N. Ref: </b><?= htmlspecialchars($dados['numero'] ?? null) ?> - 1032/GETFC/ITC/<?= date('Y') ?><br>
            <b>Maputo,</b> <?= htmlspecialchars(date('j \d\e F \d\e Y', strtotime($dados['data_do_pedido'] ?? date('Y-m-d')))) ?>
        </p>

        <p class="assunto">ASSUNTO: Estágio Profissional</p>

        <p>Ex.mo Senhor,</p>

        <p>
            O Instituto de Transportes e Comunicações é uma Instituição de Ensino Técnico-Profissional que lecciona as qualificações técnicas de nível III, IV e V.
        </p>

        <p>
            Sendo assim, vimos solicitar a aceitação do(a) nosso(a) aluno(a)
            <b><?= htmlspecialchars(($dados['nome'] ?? '') . ' ' . ($dados['apelido'] ?? '')) ?></b>,
            com o código <b><?= htmlspecialchars($dados['codigo_formando'] ?? '') ?></b>,
            para estagiar na área de
            <b><?= htmlspecialchars($dados['qualificacao_descricao'] ?? '') ?></b>
            (Nível <?= htmlspecialchars($dados['qualificacao_nivel'] ?? '') ?>).
        </p>

        <p>Os objectivos do Estágio Profissional são os seguintes:</p>
        <ul>
            <li>Proporcionar ao aluno a elaboração de Termos de Referência de uma experiência de trabalho a realizar na organização;</li>
            <li>Realizar as actividades de acordo com os termos de referência da organização;</li>
            <li>Elaborar o relatório da experiência de trabalho.</li>
        </ul>

        <p>O estágio profissional é de carácter individual.</p>

        <p>
            Cientes de que a nossa preocupação merecerá a atenção de V. Excia., agradecemos antecipadamente a colaboração e endereçamos os nossos melhores cumprimentos.
        </p>

        <div class="assinatura">
            <p>A Coordenadora de Estágios,</p>
            <p><b>(Dra. Sheila Momade)</b></p>
            <img src="http://localhost/estagio/Assets/img/Assinatura-removebg-preview.png"
                alt="Assinatura"
                style="width:100px; height:auto;">
        </div>

        <p><b>Contacto(s):</b> (+258) <?= htmlspecialchars($dados['contactoPrincipal'] ?? '') ?> / (+258) <?= htmlspecialchars($dados['contactoSecundario'] ?? '') ?><br>
        <b>Email:</b> <?= htmlspecialchars($dados['email'] ?? '') ?></p>

        <?php if (!empty($joinWarning)): ?>
            <hr>
            <small style="color:darkorange"><?= htmlspecialchars($joinWarning) ?></small>
        <?php endif; ?>
    </section>

    <footer>
        <p>Av. 24 de Julho, Nº 4707, Maputo</p>
        <p>Tel. 21 401110/823126370 Fax: 21 48 87 94</p>
        <a href="mailto:itc@itc.transcom.co.mz">itc@itc.transcom.co.mz</a>
    </footer>
</body>
</html>