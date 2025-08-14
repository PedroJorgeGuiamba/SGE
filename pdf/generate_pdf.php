<?php
// Ajuste o caminho relativo ao conector.php com base na estrutura do projeto
require_once __DIR__ . '/../Conexao/conector.php';  // Verifique se está correto

// Incluir a biblioteca FPDF (ajuste o caminho conforme necessário)
require_once __DIR__ . '/../fpdf/fpdf.php';  // Ajuste o caminho para onde o FPDF está

// Conexão com o banco de dados
$conexao = new Conector();
$conn = $conexao->getConexao();

// Receber o ID da URL
$id = isset($_GET['numero']) ? intval($_GET['numero']) : 0;

// Preparar a consulta com prepared statement
$sql = "SELECT nome, apelido, email, contactoPrincipal, contactoSecundario, qualificacao FROM pedido_carta WHERE numero = 1";
$stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $numero);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Pedido não encontrado.");
}

$pedido = $result->fetch_assoc();

// Inicializa o objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Cabeçalho da tabela no PDF
$pdf->Cell(40, 10, 'Nome', 1);
$pdf->Cell(50, 10, 'Apelido', 1);
$pdf->Cell(80, 10, 'Email', 1);
$pdf->Ln();


// Dados da tabela (apenas o pedido específico)
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, $pedido['nome'], 1);
$pdf->Cell(50, 10, $pedido['apelido'], 1);
$pdf->Cell(80, 10, $pedido['email'], 1);
$pdf->Ln();

$pdf->Cell(40, 10, 'Contacto', 1);
$pdf->Cell(40, 10, 'Contacto Alternativo', 1);
$pdf->Cell(80, 10, 'Qualificacao', 1);
$pdf->Ln();
$pdf->Cell(40, 10, $pedido['contactoPrincipal'], 1);
$pdf->Cell(40, 10, $pedido['contactoSecundario'], 1);
$pdf->Cell(80, 10, $pedido['qualificacao'], 1);
$pdf->Ln();

// Fecha a conexão e o statement
$stmt->close();
$conn->close();

// Gera o PDF
$pdf->Output('D', 'pedido_' . $id . '_report.pdf'); // Nome do arquivo inclui o ID