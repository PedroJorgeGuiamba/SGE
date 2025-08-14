<?php
// Ajuste o caminho relativo ao conector.php com base na estrutura do projeto
require_once __DIR__ . '/../Conexao/conector.php';  // Verifique se está correto

// Incluir a biblioteca FPDF (ajuste o caminho conforme necessário)
// Para suportar UTF-8, usaremos uma versão modificada ou adicionaremos suporte manual
require_once __DIR__ . '/../fpdf/fpdf.php';

// Adicionar suporte a UTF-8 (se necessário, baixe uma versão como FPDF com UTF-8)
class PDF extends FPDF {
    function __construct() {
        parent::__construct();
        $this->AddFont('Arial', '', 'arial.php'); // Ajuste se usar fonte personalizada
    }
}

// Conexão com o banco de dados
$conexao = new Conector();
$conn = $conexao->getConexao();

// Receber o ID da URL
$id = isset($_GET['numero']) ? intval($_GET['numero']) : 0;

// Dados fixos do documento (baseado na imagem)
$nome = "Pedro Jorge";
$qualificacoes = [
    "Contabilidade",
    "Gestão de Empresas",
    "Gestão Patrimonial e Financeira",
    "Gestão de Recursos Humanos",
    "Electricidade Industrial",
    "Administração de Redes",
    "Programação WEB: e-commerce",
    "Técnico de Suporte Informático",
    "Técnico de Electromecânica"
];
$data = "Maputo, 10 de agosto de 2025"; // Atualizado para a data atual
$ref = "2405 - 1032GETFC/ITC/2025";
$assunto = "Estágio Profissional";
$coordenadora = "Dra. Sheila Momade";
$contacto = "+258 850731919";
$email = "pedrojorgeguilamba@gmail.com";

// Inicializa o objeto FPDF com UTF-8
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Cabeçalho
$pdf->Cell(0, 10, utf8_decode('INSTITUTO DE TRANSPORTES E COMUNICAÇÕES'), 0, 1, 'C');
$pdf->Ln(5);
$pdf->Cell(0, 10, utf8_decode('Ministério das Finanças'), 0, 1);
$pdf->Cell(0, 10, utf8_decode('Ex.mo Sr. Direção de Recursos Humanos.'), 0, 1);
$pdf->Ln(5);
$pdf->Cell(0, 10, utf8_decode('MAPUTO'), 0, 0);
$pdf->Cell(0, 10, utf8_decode($data), 0, 1, 'R');
$pdf->Ln(5);
$pdf->Cell(0, 10, utf8_decode('N. Ref.: ' . $ref), 0, 1);
$pdf->Ln(5);
$pdf->Cell(0, 10, utf8_decode('Assunto: ' . $assunto), 0, 1);
$pdf->Ln(10);

// Corpo do documento
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, utf8_decode('Ex.mo Senhor,' . "\n\n" . 
    'O Instituto de Transportes e Comunicações e uma Instituição de Ensino Técnico-Profissional que leciona, as qualificações técnicas do Nivel III, IV e V.' . "\n\n" . 
    '- ' . implode("\n- ", $qualificacoes) . "\n\n" . 
    'Em conformidade com o plano do processo docente da qualificação que enviamos em anexo, o Estágio Profissional é uma componente muito importante das actividades práticas e é realizado ao fim de cada nível da qualificação.' . "\n\n" . 
    'Sendo assim, vimos através desta solicitar a aceitação de (a) nosso(a) aluno(a) ' . $nome . ' na área de Electricidade Industrial' . "\n\n" . 
    'Guiamba do Estágio Profissional são os seguintes:' . "\n\n" . 
    'Os objectivos do Estágio a elaboração de Termos de Referência de uma experiência de trabalho a realizar na organização:' . "\n\n" . 
    '✓ Proporcionar ao aluno a organização;' . "\n" . 
    '✓ trabalho a realizar de acordo com os termos de referência da organização;' . "\n" . 
    '✓ Realizar as actividades da experiência de trabalho;' . "\n" . 
    '✓ Elaborar o Relatório de carácter individual.' . "\n\n" . 
    'O Estágio Profissional é de carácter mercearia a atenção de V. Excia., agradecemos' . "\n\n" . 
    'Clientes de que a colaboração e endereçamos os nossos melhores cumprimentos,' . "\n\n" . 
    'A Coordenadora de Estágios para TIC' . "\n\n" . 
    'Contacto: ' . $contacto . ' | email: ' . $email . "\n\n" . 
    $coordenadora));
$pdf->Ln(10);

// Gera o PDF
$pdf->Output('D', 'estagio_profissional_' . $id . '_report.pdf'); // Nome do arquivo inclui o ID

// Fecha a conexão
$conn->close();
?>