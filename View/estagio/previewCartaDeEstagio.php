<?php
session_start();
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Conexao/conector.php';

SecurityHeaders::setFull();

CSRFProtection::validateToken($_POST['csrf_token'] ?? '');

$_SESSION['preview_pedido'] = [
    'codigoFormando'     => (int) ($_POST['codigoFormando'] ?? 0),
    'qualificacao'       => (int) ($_POST['qualificacao'] ?? 0),
    'turma'              => (int) ($_POST['turma'] ?? 0),
    'empresa'            => strtoupper(trim($_POST['empresa'] ?? '')),
    'contactoPrincipal'  => trim($_POST['contactoPrincipal'] ?? ''),
    'contactoSecundario' => trim($_POST['contactoSecundario'] ?? ''),
    'email'              => trim($_POST['email'] ?? ''),
];

$dados = $_SESSION['preview_pedido'];

// ── Queries corrigidas com base no schema real ────────────────────────────────
$conector = new Conector();
$conn     = $conector->getConexao();

$codigoFormando     = $dados['codigoFormando'];
$codigoQualificacao = $dados['qualificacao'];
$codigoTurma        = $dados['turma'];

// formando: PK é `codigo` (INT UNIQUE), não `codigo_formando`
$dadosFormando = null;
$stmtF = $conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
$stmtF->bind_param("i", $codigoFormando);
$stmtF->execute();
$dadosFormando = $stmtF->get_result()->fetch_assoc();
$stmtF->close();

// qualificacao: coluna de descrição chama-se `descricao`
$nomeQualificacao = '—';
$stmtQ = $conn->prepare("SELECT descricao FROM qualificacao WHERE id_qualificacao = ?");
$stmtQ->bind_param("i", $codigoQualificacao);
$stmtQ->execute();
$rowQ = $stmtQ->get_result()->fetch_assoc();
$stmtQ->close();
if ($rowQ) $nomeQualificacao = $rowQ['descricao'];

// turma: PK é `codigo` (INT), JOIN via turma.codigo_qualificacao = qualificacao.id_qualificacao
// Espelha exatamente o JOIN usado em listaDePedidos / search_pedidos
$nomeTurma = '—';
$stmtT = $conn->prepare("
    SELECT t.nome
    FROM turma t
    LEFT JOIN qualificacao q ON t.codigo_qualificacao = q.id_qualificacao
    WHERE t.codigo = ?
      AND q.id_qualificacao = ?
");
$stmtT->bind_param("ii", $codigoTurma, $codigoQualificacao);
$stmtT->execute();
$rowT = $stmtT->get_result()->fetch_assoc();
$stmtT->close();
if ($rowT) $nomeTurma = $rowT['nome'];
?>
<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <title>Revisão do Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: var(--bs-tertiary-bg); }

        .preview-card {
            max-width: 860px;
            margin: 2.5rem auto;
            background: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .preview-header {
            background: var(--bs-primary-bg-subtle);
            border-bottom: 1px solid var(--bs-border-color);
            padding: 1.25rem 1.5rem;
        }
        .preview-header h5 { margin: 0; font-size: 15px; font-weight: 500; color: var(--bs-primary); }
        .preview-header small { font-size: 12px; color: var(--bs-secondary-color); }

        .preview-body { padding: 1.5rem; }

        .section-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 500;
            color: var(--bs-secondary-color);
            letter-spacing: .05em;
            text-transform: uppercase;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--bs-border-color);
            margin-bottom: 12px;
        }

        .field-card {
            background: var(--bs-secondary-bg);
            border-radius: 8px;
            padding: 10px 14px;
        }
        .field-card .field-label {
            font-size: 11px;
            color: var(--bs-tertiary-color);
            margin: 0 0 2px;
        }
        .field-card .field-value {
            font-size: 15px;
            font-weight: 500;
            color: var(--bs-body-color);
            margin: 0;
            word-break: break-word;
        }

        .preview-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--bs-border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="preview-card">

    <div class="preview-header">
        <h5><i class="fas fa-file-alt me-2"></i>Revisão do pedido de carta de estágio</h5>
        <small>Verifique todos os dados antes de confirmar o envio</small>
    </div>

    <div class="preview-body">

        <?php if (!$dadosFormando): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                <i class="fas fa-exclamation-triangle"></i>
                Formando com código <strong class="ms-1 me-1"><?php echo htmlspecialchars($codigoFormando); ?></strong>
                não encontrado. Verifique o código e tente novamente.
            </div>

        <?php else: ?>

        <!-- Secção: Formando -->
        <div class="mb-4">
            <div class="section-label">
                <i class="fas fa-user" style="font-size:12px"></i> Dados do formando
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="field-card">
                        <p class="field-label">Código</p>
                        <p class="field-value"><?php echo htmlspecialchars($dados['codigoFormando']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card">
                        <p class="field-label">Nome</p>
                        <p class="field-value"><?php echo htmlspecialchars($dadosFormando['nome']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card">
                        <p class="field-label">Apelido</p>
                        <p class="field-value"><?php echo htmlspecialchars($dadosFormando['apelido']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secção: Académico -->
        <div class="mb-4">
            <div class="section-label">
                <i class="fas fa-graduation-cap" style="font-size:12px"></i> Dados académicos
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="field-card">
                        <p class="field-label">Qualificação</p>
                        <p class="field-value"><?php echo htmlspecialchars($nomeQualificacao); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-card">
                        <p class="field-label">Turma</p>
                        <p class="field-value"><?php echo htmlspecialchars($nomeTurma); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secção: Pedido -->
        <div class="mb-4">
            <div class="section-label">
                <i class="fas fa-briefcase" style="font-size:12px"></i> Dados do pedido
            </div>
            <div class="row g-2">
                <div class="col-12">
                    <div class="field-card">
                        <p class="field-label">Empresa</p>
                        <p class="field-value"><?php echo htmlspecialchars($dados['empresa']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card">
                        <p class="field-label">Contacto principal</p>
                        <p class="field-value"><?php echo htmlspecialchars($dados['contactoPrincipal']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card">
                        <p class="field-label">Contacto secundário</p>
                        <p class="field-value"><?php echo htmlspecialchars($dados['contactoSecundario']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="field-card">
                        <p class="field-label">Email pessoal</p>
                        <p class="field-value"><?php echo htmlspecialchars($dados['email']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aviso -->
        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-0">
            <i class="fas fa-info-circle" style="font-size:13px"></i>
            <small>Após confirmação, o pedido ficará <strong>pendente</strong> de aprovação por um administrador.</small>
        </div>

        <?php endif; ?>
    </div>

    <div class="preview-footer">
        <a href="formularioDeCartaDeEstagio.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar e corrigir
        </a>

        <?php if ($dadosFormando): ?>
        <form action="../../Controller/Estagio/FormularioDeCartaDeEstagio.php" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="fromPreview" value="1">
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-check me-1"></i> Confirmar e enviar
            </button>
        </form>
        <?php endif; ?>
    </div>

</div>
</body>
</html>