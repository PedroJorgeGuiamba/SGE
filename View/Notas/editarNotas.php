<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
  die('ID inválido');
}

$stmt = $mysqli->prepare("SELECT * FROM avaliacao_competencia WHERE id_avaliacao = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) die('Avaliação não encontrada');

$formandos = $mysqli->query("SELECT id_formando, nome, apelido FROM formando ORDER BY nome");
$tentativas = $mysqli->query("SELECT id_tentativa, descricao FROM tipo_tentativa ORDER BY ordem");
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <title>Editar Avaliação</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- CSS personalizado -->
  <link rel="stylesheet" href="../../Style/home.css">
</head>

<body class="bg-light">

  <div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Avaliação #<?= htmlspecialchars($res['id_avaliacao']) ?></h3>
        <a href="visualizarNotas.php" class="btn btn-light btn-sm">
          <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
      </div>

      <div class="card-body">
        <form action="../../Controller/Notas/editarNotas.php" method="post" class="row g-3">
          <input type="hidden" name="id_avaliacao" value="<?= htmlspecialchars($res['id_avaliacao']) ?>">

          <!-- Formando -->
          <div class="col-md-6">
            <label class="form-label">Formando</label>
            <select name="id_formando" class="form-select" required>
              <?php while ($r = $formandos->fetch_assoc()): ?>
                <option value="<?= $r['id_formando'] ?>" <?= ($r['id_formando'] == $res['id_formando']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($r['nome'] . ' ' . $r['apelido']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <!-- Competência -->
          <div class="col-md-6">
            <label class="form-label">Competência (ID)</label>
            <input type="text" name="id_competencia" value="<?= htmlspecialchars($res['id_competencia']) ?>" class="form-control" required>
          </div>

          <!-- Tentativa -->
          <div class="col-md-6">
            <label class="form-label">Tentativa</label>
            <select name="id_tentativa" class="form-select" required>
              <?php while ($r = $tentativas->fetch_assoc()): ?>
                <option value="<?= $r['id_tentativa'] ?>" <?= ($r['id_tentativa'] == $res['id_tentativa']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($r['descricao']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <!-- Percentagem -->
          <div class="col-md-6">
            <label class="form-label">Percentagem Atingida (%)</label>
            <input type="number" step="0.01" name="percentagem_atingida" value="<?= htmlspecialchars($res['percentagem_atingida']) ?>" class="form-control" required>
          </div>

          <!-- Data -->
          <div class="col-md-6">
            <label class="form-label">Data da Avaliação</label>
            <input type="date" name="data_avaliacao" value="<?= htmlspecialchars($res['data_avaliacao']) ?>" class="form-control">
          </div>

          <!-- Observações -->
          <div class="col-md-12">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="3"><?= htmlspecialchars($res['observacoes']) ?></textarea>
          </div>

          <!-- Botões -->
          <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-success px-4">
              <i class="fa-solid fa-floppy-disk"></i> Guardar Alterações
            </button>
            <a href="visualizarNotas.php" class="btn btn-secondary px-4 ms-2">
              <i class="fa-solid fa-xmark"></i> Cancelar
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>