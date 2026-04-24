<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id = 0;
if (isset($_GET['id_pedido_carta'])) {
    $id = (int)$_GET['id_pedido_carta'];
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if ($id <= 0) {
    http_response_code(404);
    require 'View/Erros/error.php';
    exit;
}

$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia();

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
$contacto1 = $criptografia->descriptografar(htmlspecialchars($dados['contactoPrincipal'] ?? ''));
$contacto2 = $criptografia->descriptografar(htmlspecialchars($dados['contactoSecundario'] ?? ''));
$email = $criptografia->descriptografar(htmlspecialchars($dados['email'] ?? ''));
$curso = htmlspecialchars($dados['curso']);
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="utf-8">
    <title>Carta de Estágio - <?= $nomeCompleto ?></title>
    <link rel="stylesheet" href="/estagio/Assets/CSS/carta.css">
</head>
<body>

<?php
    require_once __DIR__ . '/Carta/PedidoDeEstagio.php';
?>
</body>
</html>
