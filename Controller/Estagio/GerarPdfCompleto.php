<?php
if (!isset($_GET['id_pedido_carta']) || (int)$_GET['id_pedido_carta'] <= 0) {
    die("<b>Erro:</b> Número do pedido não informado ou inválido.");
}

$id = (int) $_GET['id_pedido_carta'];

$url = "http://localhost/estagio/View/estagio/PacoteEstagioCompleto.php?id_pedido_carta=$id";

// Caminho para salvar o PDF temporário
$pdfDir = __DIR__ . "/../../Temp";
if (!file_exists($pdfDir)) {
    mkdir($pdfDir, 0777, true);
}
$pdfFile = "$pdfDir/Pacote_Completo_Estagio_$id.pdf";

// Caminho do executável wkhtmltopdf
$wkhtmltopdfPath = "C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe";

if (!file_exists($wkhtmltopdfPath)) {
    die("<b>Erro:</b> O executável wkhtmltopdf não foi encontrado no caminho:<br>$wkhtmltopdfPath");
}

// Monta o comando
$cmd = "\"$wkhtmltopdfPath\" \"$url\" \"$pdfFile\"";

// Executa o comando e captura saída
exec($cmd . " 2>&1", $output, $returnCode);

if ($returnCode === 0 && file_exists($pdfFile)) {
    header('Content-Type: application/pdf');
    header("Content-Disposition: inline; filename=\"Pacote_Estagio_Completo_$id.pdf\"");
    readfile($pdfFile);

    unlink($pdfFile);
} else {
    echo "<b>Erro ao gerar o PDF!</b><br>";
    echo "<b>Comando executado:</b> $cmd<br>";
    echo "<b>Código de retorno:</b> $returnCode<br>";
    echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
}
?>

