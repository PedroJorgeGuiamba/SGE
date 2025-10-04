<?php
// Verifica se o parâmetro 'numero' foi passado
if (!isset($_GET['numero']) || (int)$_GET['numero'] <= 0) {
    die("<b>Erro:</b> Número do pedido não informado ou inválido.");
}

// Recebe o ID do pedido
$id = (int) $_GET['numero'];

// URL da página HTML (a carta que será convertida em PDF)
$url = "http://localhost/estagio/View/estagio/CartaDeEstagio.php?numero=$id";

// Caminho para salvar o PDF temporário
$pdfDir = __DIR__ . "/../../Temp";
if (!file_exists($pdfDir)) {
    mkdir($pdfDir, 0777, true);
}
$pdfFile = "$pdfDir/Carta_Estagio_$id.pdf";

// Caminho do executável wkhtmltopdf
$wkhtmltopdfPath = "C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"; // Windows
// $wkhtmltopdfPath = "/usr/bin/wkhtmltopdf"; // Linux

// Verifica se o executável existe
if (!file_exists($wkhtmltopdfPath)) {
    die("<b>Erro:</b> O executável wkhtmltopdf não foi encontrado no caminho:<br>$wkhtmltopdfPath");
}

// Monta o comando
$cmd = "\"$wkhtmltopdfPath\" \"$url\" \"$pdfFile\"";

// Executa o comando e captura saída
exec($cmd . " 2>&1", $output, $returnCode);

// Verifica resultado
if ($returnCode === 0 && file_exists($pdfFile)) {
    // Cabeçalhos HTTP para exibir no navegador
    header('Content-Type: application/pdf');
    header("Content-Disposition: inline; filename=\"Carta_Estagio_$id.pdf\"");
    readfile($pdfFile);

    // (Opcional) Excluir o arquivo após o envio
    unlink($pdfFile);
} else {
    echo "<b>Erro ao gerar o PDF!</b><br>";
    echo "<b>Comando executado:</b> $cmd<br>";
    echo "<b>Código de retorno:</b> $returnCode<br>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
}
?>