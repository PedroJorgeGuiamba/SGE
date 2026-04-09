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
date_default_timezone_set('Africa/Maputo');
// $dataFormatada = date('j \\d\\e F \\d\\e Y', strtotime($dados['data_do_pedido'] ?? ''));
$meses = [
    1  => 'Janeiro',  2  => 'Fevereiro', 3  => 'Março',
    4  => 'Abril',    5  => 'Maio',      6  => 'Junho',
    7  => 'Julho',    8  => 'Agosto',    9  => 'Setembro',
    10 => 'Outubro',  11 => 'Novembro',  12 => 'Dezembro'
];

$ts          = strtotime($dados['data_do_pedido'] ?? date('Y-m-d'));
$dataFormatada = date('j', $ts) . ' de ' . $meses[(int) date('n', $ts)] . ' de ' . date('Y', $ts);

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


<?php
$pacotesPorCurso = [
    'Suporte Informático' => [
        'TermoDeReferenciaSuporte.php',
    ],
    'Suporte Informatico' => [
        'TermoDeReferenciaSuporte.php',
    ],
    'Administração de Redes' => [
        'TermoDeReferenciaSuporte.php',
        'TermoDeReferenciaRedes.php',
    ],
    'Administracao de Redes' => [
        'TermoDeReferenciaSuporte.php',
        'TermoDeReferenciaRedes.php',
    ],
    'Programação de Aplicações Web' => [
        'TermoDeReferenciaSuporte.php',
        'TermoDeReferenciaProgramacaoAplicacoesWeb.php',
    ],
    'Programação de Aplicações WEB' => [
        'TermoDeReferenciaSuporte.php',
        'TermoDeReferenciaProgramacaoAplicacoesWeb.php',
    ],
    'Programacao de Aplicacoes Web' => [
        'TermoDeReferenciaSuporte.php',
        'TermoDeReferenciaProgramacaoAplicacoesWeb.php',
    ],
    'Programacao de Aplicacoes WEB' => [
        'TermoDeReferenciaSuporte.php',
        'TermoDeReferenciaProgramacaoAplicacoesWeb.php',
    ],
    'Construção Cívil' => [
        'TermoDeReferenciaConstrucaoCivil.php',
    ],
    'Construção Civil' => [
        'TermoDeReferenciaConstrucaoCivil.php',
    ],
    'Construcao Civil' => [
        'TermoDeReferenciaConstrucaoCivil.php',
    ],
    'Contabilidade e Auditoria' => [
        'TermoDeReferenciaContabilidadeEAuditoria.php',
    ],
    'Contabilidade & Auditoria' => [
        'TermoDeReferenciaContabilidadeEAuditoria.php',
    ],
    'Electricidade Industrial' => [
        'TermoDeReferenciaElectricidadeIndustrial.php',
    ],
    'Eletricidade Industrial' => [
        'TermoDeReferenciaElectricidadeIndustrial.php',
    ],
];

if (isset($pacotesPorCurso[$curso])) {
    require_once __DIR__ . '/Carta/Credencial.php';

    foreach ($pacotesPorCurso[$curso] as $termoFile) {
        require_once __DIR__ . '/Carta/' . $termoFile;
    }

    require_once __DIR__ . '/Carta/Visita.php';
}
?>
</body>
</html>
