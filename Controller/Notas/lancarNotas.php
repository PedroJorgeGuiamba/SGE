<?php
// Recebe POST do form novo.php e cria tudo que for necessário e a avaliacao_competencia
require_once __DIR__ . '/../../Conexao/db.php';
function json_err($msg){ http_response_code(400); die($msg); }

$id_formando = intval($_POST['id_formando'] ?? 0);
$id_modulo = intval($_POST['id_modulo'] ?? 0);
$codigo_formador = intval($_POST['codigo_formador'] ?? 0);
$ra_codigo = trim($_POST['ra_codigo'] ?? '');
$ra_descricao = trim($_POST['ra_descricao'] ?? '');
$ra_tipo = $_POST['ra_tipo'] ?? 'Teórico';
$tipo_av_descricao = trim($_POST['tipo_av_descricao'] ?? '');
$tipo_av_tipo = $_POST['tipo_av_tipo'] ?? 'Teórica';
$percentual_minimo = intval($_POST['percentual_minimo'] ?? 0);
$criterio_obs = trim($_POST['criterio_obs'] ?? '');
$competencia_peso = floatval($_POST['competencia_peso'] ?? 1.00);
$id_tentativa = intval($_POST['id_tentativa'] ?? 0);
$percentagem_atingida = floatval($_POST['percentagem_atingida'] ?? 0.0);
$data_avaliacao = $_POST['data_avaliacao'] ?? date('Y-m-d');
$observacoes = trim($_POST['observacoes'] ?? '');

// 1) verifica inscricao_modulo
$stmt = $mysqli->prepare("SELECT 1 FROM inscricao_modulo WHERE id_formando=? AND id_modulo=? LIMIT 1");
$stmt->bind_param('ii',$id_formando, $id_modulo);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    die('Formando não está inscrito no módulo.');
}

// 2) verifica se o formador leciona o módulo (usa leciona_modulo -> modulo_turma)
$stmt = $mysqli->prepare("
SELECT 1 FROM leciona_modulo lm
JOIN modulo_turma mt ON lm.id_modulo_turma = mt.id_modulo
JOIN formador f ON lm.id_formador = f.id_formador
WHERE f.codigo = ? AND mt.id_turma IS NOT NULL AND mt.id_modulo = ?
LIMIT 1
");
// Observação: dependendo da tua modelagem, podes preferir verificar formador_modulo (codigo_formador,codigo_modulo)
$stmt->bind_param('ii', $codigo_formador, $id_modulo);
$stmt->execute();
$found = $stmt->get_result()->fetch_assoc();
if (!$found) {
    // tenta alternativa: verificar formador_modulo (mais simples)
    $stmt2 = $mysqli->prepare("SELECT 1 FROM formador_modulo WHERE codigo_formador = ? AND codigo_modulo = ? LIMIT 1");
    $stmt2->bind_param('ii',$codigo_formador,$id_modulo);
    $stmt2->execute();
    if (!$stmt2->get_result()->fetch_assoc()) {
        die('Formador não leciona o módulo (verifica leciona_modulo/formador_modulo).');
    }
    $stmt2->close();
}
$stmt->close();

// 3) Garantir que exista resultado_aprendizagem (por ra_codigo). Se não existe, insere.
$stmt = $mysqli->prepare("SELECT id_resultado FROM resultado_aprendizagem WHERE codigo = ? LIMIT 1");
$stmt->bind_param('s', $ra_codigo); $stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res) {
    $id_resultado = $res['id_resultado'];
} else {
    $ins = $mysqli->prepare("INSERT INTO resultado_aprendizagem (codigo, descricao, tipo) VALUES (?,?,?)");
    $ins->bind_param('sss', $ra_codigo, $ra_descricao, $ra_tipo);
    $ins->execute();
    $id_resultado = $ins->insert_id;
    $ins->close();
}
$stmt->close();

// 4) Garantir que exista competição (competencia) para (id_modulo,id_resultado_aprendizagem)
$stmt = $mysqli->prepare("SELECT id_competencia FROM competencia WHERE id_modulo = ? AND id_resultado_aprendizagem = ? LIMIT 1");
$stmt->bind_param('ii',$id_modulo, $id_resultado);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res) {
    $id_competencia = $res['id_competencia'];
} else {
    $ins = $mysqli->prepare("INSERT INTO competencia (id_modulo, id_resultado_aprendizagem, peso, obrigatoria) VALUES (?,?,?,1)");
    $ins->bind_param('iid',$id_modulo,$id_resultado,$competencia_peso);
    $ins->execute();
    $id_competencia = $ins->insert_id;
    $ins->close();
}
$stmt->close();

// 5) Garantir tipo_avaliacao
$stmt = $mysqli->prepare("SELECT id_tipo FROM tipo_avaliacao WHERE descricao = ? AND tipo = ? LIMIT 1");
$stmt->bind_param('ss',$tipo_av_descricao,$tipo_av_tipo);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res) {
    $id_tipo_avaliacao = $res['id_tipo'];
} else {
    $ins = $mysqli->prepare("INSERT INTO tipo_avaliacao (descricao, tipo) VALUES (?,?)");
    $ins->bind_param('ss',$tipo_av_descricao,$tipo_av_tipo);
    $ins->execute();
    $id_tipo_avaliacao = $ins->insert_id;
    $ins->close();
}
$stmt->close();

// 6) Garantir criterio_avaliacao para este modulo e tipo_avaliacao
$stmt = $mysqli->prepare("SELECT id_criterio FROM criterio_avaliacao WHERE id_modulo = ? AND id_tipo_avaliacao = ? LIMIT 1");
$stmt->bind_param('ii',$id_modulo,$id_tipo_avaliacao);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res) {
    $id_criterio = $res['id_criterio'];
} else {
    $ins = $mysqli->prepare("INSERT INTO criterio_avaliacao (id_modulo, id_tipo_avaliacao, percentual_minimo, observacoes) VALUES (?,?,?,?)");
    $ins->bind_param('iiis',$id_modulo,$id_tipo_avaliacao,$percentual_minimo,$criterio_obs);
    $ins->execute();
    $id_criterio = $ins->insert_id;
    $ins->close();
}
$stmt->close();

// 7) Se id_tentativa foi enviado, usa-o. Senão, erro (ou poderíamos criar)
if (!$id_tentativa) {
    die('Selecione uma tentativa.');
}

// 8) Inserir avaliacao_competencia (antes de inserir, checar única constraint unique_competencia_formando_tentativa)
$stmt = $mysqli->prepare("SELECT 1 FROM avaliacao_competencia WHERE id_competencia=? AND id_formando=? AND id_tentativa=? LIMIT 1");
$stmt->bind_param('iii',$id_competencia,$id_formando,$id_tentativa);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    $stmt->close();
    die('Já existe avaliação para essa combinação competência/formando/tentativa (unique).');
}
$stmt->close();

$ins = $mysqli->prepare("
INSERT INTO avaliacao_competencia (id_competencia, id_formando, id_tentativa, percentagem_atingida, data_avaliacao, observacoes)
VALUES (?,?,?,?,?,?)
");
$ins->bind_param('iiidss',$id_competencia,$id_formando,$id_tentativa,$percentagem_atingida,$data_avaliacao,$observacoes);
if ($ins->execute()) {
    // trigger/sto procs na BD atualizam menção final e mencao automaticamente
    header('Location: ../../view/notas/visualizarNotas.php');
    exit;
} else {
    die('Erro ao inserir avaliação: ' . $mysqli->error);
}
