<?php

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ .'/../../middleware/auth.php';

$conexao = new Conector();
$conn = $conexao->getConexao();

// Receber o numero da URL
$numero = isset($_GET['numero']) ? intval($_GET['numero']) : 0;


$sql = "SELECT p.numero, p.nome, p.apelido, p.codigo_formando, p.qualificacao, p.codigo_turma, p.data_do_pedido, p.hora_do_pedido,
        p.empresa, p.contactoPrincipal, p.contactoSecundario, p.email
        FROM pedido_carta p
        WHERE p.numero = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $numero);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Pedido não encontrado.");
}

$pedido = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">Detalhes do Pedido #<?php echo htmlspecialchars($pedido['numero'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <table class="table table-bordered">
        <tr>
            <th>Nome</th>
            <td><?php echo htmlspecialchars($pedido['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Apelido</th>
            <td><?php echo htmlspecialchars($pedido['apelido'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Código Formando</th>
            <td><?php echo htmlspecialchars($pedido['codigo_formando'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Qualificação</th>
            <td><?php echo htmlspecialchars($pedido['qualificacao'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Turma</th>
            <td><?php echo htmlspecialchars($pedido['codigo_turma'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Data do Pedido</th>
            <td><?php echo date("d/m/Y", strtotime($pedido['data_do_pedido'])); ?></td>
        </tr>
        <tr>
            <th>Hora do Pedido</th>
            <td><?php echo htmlspecialchars($pedido['hora_do_pedido'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Empresa</th>
            <td><?php echo htmlspecialchars($pedido['empresa'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Contacto Principal</th>
            <td><?php echo htmlspecialchars($pedido['contactoPrincipal'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Contacto Secundário</th>
            <td><?php echo htmlspecialchars($pedido['contactoSecundario'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($pedido['email'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <div class="mt-3">
        <a href="formularioDeCartaDeEstagio.php" class="btn btn-primary">Novo Pedido</a>
        <a href="/pdf/generate_pdf.php?numero=<?php echo htmlspecialchars($pedido['numero'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success" target="_blank">Gerar PDF</a>
    </div>
</body>
</html>