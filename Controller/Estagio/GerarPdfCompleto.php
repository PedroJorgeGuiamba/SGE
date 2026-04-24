<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SafeUnlink.php';
// ─── LOG ──────────────────────────────────────────────────────────────────────
$logFile = __DIR__ . "/../../Temp/debug_gerar_pdf.log";

function gravarDataLevantamento(int $id): void
{
    try {
        $conexao = new Conector();
        $conn    = $conexao->getConexao();

        date_default_timezone_set('Africa/Maputo');
        $hoje = date('Y-m-d');

        $stmt = $conn->prepare(
            "UPDATE pedido_carta SET data_de_levantamento = ? WHERE id_pedido_carta = ?"
        );
        $stmt->bind_param("si", $hoje, $id);
        $stmt->execute();

        logMsg("data_de_levantamento gravada para ID $id: $hoje");
    } catch (Exception $e) {
        // Não interrompe o envio do PDF — apenas regista o erro
        logMsg("ERRO ao gravar data_de_levantamento para ID $id: " . $e->getMessage());
    }
}
function notificarFormando(int $idPedidoCarta){
    try {
        $conexao = new Conector();
        $conn    = $conexao->getConexao();
        // Notifica o formando
        $stmtFormando = $conn->prepare("
            SELECT f.usuario_id, p.id_pedido_carta
                FROM formando f
                JOIN pedido_carta p ON p.codigo_formando = f.codigo
                WHERE p.id_pedido_carta = ?
        ");
        $stmtFormando->bind_param('i', $idPedidoCarta);
        $stmtFormando->execute();
        $formando = $stmtFormando->get_result()->fetch_assoc();
        $stmtFormando->close();

        if ($formando && $formando['usuario_id']) {
            $mensagem = "A sua carta de estágio já foi gerada.";
            $stmtNotif = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
            $stmtNotif->bind_param('is', $formando['usuario_id'], $mensagem);
            $stmtNotif->execute();
            $stmtNotif->close();
        }
    }catch (Exception $e) {
        // Não interrompe o envio do PDF — apenas regista o erro
        logMsg("ERRO ao gravar data_de_levantamento para ID $idPedidoCarta: " . $e->getMessage());
    }
}

function logMsg(string $msg): void
{
    global $logFile;
    // Garante que a pasta Temp existe antes de escrever
    $dir = dirname($logFile);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

logMsg("=== NOVA REQUISIÇÃO ===");
logMsg("METHOD: " . $_SERVER['REQUEST_METHOD']);
logMsg("GET: "    . json_encode($_GET));
logMsg("POST: "   . json_encode($_POST));

// ─── CONFIGURAÇÕES ────────────────────────────────────────────────────────────
$env = parse_ini_file(__DIR__ . '/../../config/.env');

foreach ($env as $key => $value) {
    putenv("$key=$value");
}

$wkhtmltopdfPath = getenv('wkhtmltopdfPath');
$baseUrl         = "http://localhost/estagio/api/carta";
$pdfDir = __DIR__ . "/../../Temp";
if (!file_exists($pdfDir)) {
    mkdir($pdfDir, 0777, true);
}

if (!file_exists($wkhtmltopdfPath)) {
    logMsg("ERRO: wkhtmltopdf não encontrado: $wkhtmltopdfPath");
    die("<b>Erro:</b> wkhtmltopdf não encontrado em:<br><code>$wkhtmltopdfPath</code>");
}

logMsg("pdfDir: $pdfDir | existe: " . (file_exists($pdfDir) ? 'sim' : 'não'));

// ─── FUNÇÃO: gera PDF e devolve o conteúdo binário (string) ──────────────────
function gerarPdfBytes(int $id, string $pdfDir, string $wkhtmltopdfPath): ?string
{
    global $baseUrl;
    $safe = new SafeUnlink();

    $pdfFile = $pdfDir . "/tmp_estagio_$id.pdf";
    $url     = $baseUrl . "?id_pedido_carta=$id";
    $cmd     = "\"$wkhtmltopdfPath\" \"$url\" \"$pdfFile\"";

    logMsg("Gerando PDF ID $id | CMD: $cmd");

    $output     = [];
    $returnCode = null;
    exec($cmd . " 2>&1", $output, $returnCode);

    logMsg("exec ID $id → código=$returnCode | " . implode(" | ", $output));

    if ($returnCode === 0 && file_exists($pdfFile) && filesize($pdfFile) > 0) {
        $bytes = file_get_contents($pdfFile);
        $safe->safe_unlink($pdfFile, $pdfDir);
        // unlink($pdfFile); // apaga imediatamente após ler
        logMsg("PDF ID $id lido (" . strlen($bytes) . " bytes). Temp removido.");
        return $bytes;
    }

    logMsg("ERRO ao gerar PDF ID $id.");
    if (file_exists($pdfFile)) $safe->safe_unlink($pdfFile, $pdfDir);
    return null;
}

// ─── MÚLTIPLOS IDs via POST ───────────────────────────────────────────────────
if (!empty($_POST['ids']) && is_array($_POST['ids'])) {

    logMsg("Bloco POST: múltiplos IDs recebidos");

    $ids = array_values(
        array_filter(array_map('intval', $_POST['ids']), fn($v) => $v > 0)
    );
    logMsg("IDs válidos: " . implode(', ', $ids));

    if (empty($ids)) {
        logMsg("ERRO: array vazio após filtro.");
        die("<b>Erro:</b> Nenhum ID válido recebido.");
    }

    // ── 1 ID → devolve PDF direto ─────────────────────────────────────────────
    if (count($ids) === 1) {
        logMsg("Apenas 1 ID — PDF direto.");
        $bytes = gerarPdfBytes($ids[0], $pdfDir, $wkhtmltopdfPath);
        if ($bytes === null) {
            die("<b>Erro:</b> Não foi possível gerar o PDF para o ID {$ids[0]}. Verifique o log em Temp/debug_gerar_pdf.log");
        }

        gravarDataLevantamento($ids[0]);
        notificarFormando($ids[0]);

        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"Pacote_Estagio_Completo_{$ids[0]}.pdf\"");
        header('Content-Length: ' . mb_strlen($bytes, '8bit'));
        ob_end_clean();
        echo $bytes;
        exit;
    }

    // ── Múltiplos IDs → ZIP em memória (sem addFile, sem bug de ficheiro lazy) ─
    logMsg("Múltiplos IDs: a gerar ZIP...");

    if (!class_exists('ZipArchive')) {
        logMsg("ERRO: ZipArchive não disponível.");
        die("<b>Erro:</b> Extensão <code>php_zip</code> não está ativa no PHP. Ative-a no <code>php.ini</code> e reinicie o servidor.");
    }

    // Usa ficheiro temporário para o ZIP
    $zipFile = $pdfDir . "/zip_estagio_" . date('YmdHis') . "_" . rand(1000, 9999) . ".zip";
    $zip     = new ZipArchive();

    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        logMsg("ERRO: não foi possível criar o ZIP em $zipFile");
        die("<b>Erro:</b> Não foi possível criar o ficheiro ZIP temporário.");
    }

    $erros      = [];
    $adicionados = 0;

    $idsGeradosComSucesso = [];

    foreach ($ids as $id) {
        $bytes = gerarPdfBytes($id, $pdfDir, $wkhtmltopdfPath);
        if ($bytes !== null) {
            // addFromString: escreve o conteúdo diretamente no ZIP, sem depender do ficheiro em disco
            $zip->addFromString("Pacote_Estagio_Completo_$id.pdf", $bytes);
            $adicionados++;
            $idsGeradosComSucesso[] = $id;
            logMsg("ID $id adicionado ao ZIP via addFromString.");
        } else {
            $erros[] = $id;
            logMsg("ID $id FALHOU — não adicionado ao ZIP.");
        }
    }

    $zip->close();
    logMsg("ZIP fechado. Adicionados: $adicionados | Falhados: " . count($erros));

    if (!file_exists($zipFile) || filesize($zipFile) === 0) {
        logMsg("ERRO: ZIP vazio ou inexistente após fechar.");
        echo "<b>Erro:</b> O ZIP não foi gerado ou ficou vazio.<br>";
        if (!empty($erros)) {
            echo "<b>IDs que falharam:</b> " . implode(', ', $erros) . "<br>";
        }
        echo "<i>Verifique o log em <code>Temp/debug_gerar_pdf.log</code></i>";
        exit;
    }

    foreach ($idsGeradosComSucesso as $id) {
        gravarDataLevantamento($id);
        notificarFormando($id);
    }

    $zipName = "Pacotes_Estagio_" . date('Ymd_His') . ".zip";
    logMsg("Enviando ZIP ($zipName) com " . filesize($zipFile) . " bytes.");

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename=\"$zipName\"");
    header('Content-Length: ' . filesize($zipFile));
    ob_end_clean();
    readfile($zipFile);
    // unlink($zipFile);
    $safe->safe_unlink($pdfFile, $pdfDir);
    exit;
}

// ─── ID ÚNICO via GET ─────────────────────────────────────────────────────────
if (!empty($_GET['id_pedido_carta']) && (int)$_GET['id_pedido_carta'] > 0) {
    $id = (int) $_GET['id_pedido_carta'];
    logMsg("Bloco GET: ID único = $id");

    $bytes = gerarPdfBytes($id, $pdfDir, $wkhtmltopdfPath);
    if ($bytes === null) {
        die("<b>Erro:</b> Não foi possível gerar o PDF para o ID $id.<br><i>Verifique o log em <code>Temp/debug_gerar_pdf.log</code></i>");
    }

    gravarDataLevantamento($id);
    notificarFormando($id);

    
    header('Content-Type: application/pdf');
    header("Content-Disposition: inline; filename=\"Pacote_Estagio_Completo_$id.pdf\"");
    // header('Content-Length: ' . strlen($bytes));
    // ✅ Mais seguro para binários
    header('Content-Length: ' . mb_strlen($bytes, '8bit'));
    ob_end_clean();
    echo $bytes;
    exit;
}

// ─── NENHUM PARÂMETRO ─────────────────────────────────────────────────────────
logMsg("ERRO: nenhum parâmetro válido recebido.");
die("<b>Erro:</b> Número do pedido não informado ou inválido.");
