<?php
// Form para criar nova avaliação
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../middleware/auth.php';

SecurityHeaders::setFull();

$conexao = new Conector();
$conn = $conexao->getConexao();

// Carrega listas para selects
$formandos = $conn->query("SELECT id_formando, nome, apelido FROM formando ORDER BY nome");
$modulos = $conn->query("SELECT id_modulo, descricao, codigo FROM modulo ORDER BY descricao");
$tentativas = $conn->query("SELECT id_tentativa, descricao FROM tipo_tentativa ORDER BY ordem");
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <title>Nova Avaliação</title>
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
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fa-solid fa-pen-to-square me-2"></i> Inserir Nova Avaliação</h3>
        <a href="visualizarNotas.php" class="btn btn-light btn-sm">
          <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
      </div>

      <div class="card-body">
        <form action="../../Controller/Notas/lancarNotas.php" method="post" class="row g-3">

          <!-- Formando -->
          <div class="col-md-6">
            <label class="form-label">Formando</label>
            <select name="id_formando" class="form-select" required>
              <option value="">-- escolha --</option>
              <?php while ($r = $formandos->fetch_assoc()): ?>
                <option value="<?= $r['id_formando'] ?>">
                  <?= htmlspecialchars($r['nome'] . ' ' . $r['apelido']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <!-- Módulo -->
          <div class="col-md-6">
            <label class="form-label">Módulo</label>
            <select name="id_modulo" class="form-select" required>
              <option value="">-- escolha --</option>
              <?php while ($r = $modulos->fetch_assoc()): ?>
                <option value="<?= $r['id_modulo'] ?>">
                  <?= htmlspecialchars($r['codigo'] . ' - ' . $r['descricao']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <!-- Formador -->
          <div class="col-md-6">
            <label class="form-label">Formador (código - número)</label>
            <input type="text" name="codigo_formador" class="form-control" required>
          </div>

          <!-- Resultado de Aprendizagem -->
          <div class="col-md-6">
            <label class="form-label">Código do Resultado (ex: RA-001)</label>
            <input type="text" name="ra_codigo" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Descrição Resultado de Aprendizagem</label>
            <input type="text" name="ra_descricao" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tipo de Resultado</label>
            <select name="ra_tipo" class="form-select" required>
              <option value="Teórico">Teórico</option>
              <option value="Prático">Prático</option>
            </select>
          </div>

          <!-- Tipo de Avaliação -->
          <div class="col-md-6">
            <label class="form-label">Descrição Tipo de Avaliação</label>
            <input type="text" name="tipo_av_descricao" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tipo de Avaliação</label>
            <select name="tipo_av_tipo" class="form-select" required>
              <option value="Teórica">Teórica</option>
              <option value="Prática">Prática</option>
            </select>
          </div>

          <!-- Critério -->
          <div class="col-md-4">
            <label class="form-label">Percentual Mínimo (%)</label>
            <input type="number" name="percentual_minimo" class="form-control" required>
          </div>

          <div class="col-md-8">
            <label class="form-label">Descrição Critério (opcional)</label>
            <input type="text" name="criterio_obs" class="form-control">
          </div>

          <!-- Competência -->
          <div class="col-md-6">
            <label class="form-label">Peso da Competência (default 1.00)</label>
            <input type="number" step="0.01" name="competencia_peso" value="1.00" class="form-control">
          </div>

          <!-- Tentativa -->
          <div class="col-md-6">
            <label class="form-label">Tentativa</label>
            <select name="id_tentativa" class="form-select" required>
              <option value="">-- escolha --</option>
              <?php while ($r = $tentativas->fetch_assoc()): ?>
                <option value="<?= $r['id_tentativa'] ?>">
                  <?= htmlspecialchars($r['descricao']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <!-- Percentagem, Data e Observações -->
          <div class="col-md-4">
            <label class="form-label">Percentagem Atingida</label>
            <input type="number" step="0.01" name="percentagem_atingida" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Data da Avaliação</label>
            <input type="date" name="data_avaliacao" value="<?= date('Y-m-d') ?>" class="form-control">
          </div>

          <div class="col-md-12">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="3"></textarea>
          </div>

          <!-- Botão -->
          <div class="col-12 text-center mt-4">
            <button id="btnSubmit" type="submit" class="btn btn-success px-4">
              <i class="fa-solid fa-check"></i> Inserir Avaliação
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script para mostrar 4 campos de cada vez -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.querySelector("form");
      const inputs = form.querySelectorAll(".col-md-6, .col-md-4, .col-md-8, .col-md-12");
      const total = inputs.length;
      const step = 4; // Mostra 4 campos por vez
      let current = 0;

      const navDiv = document.createElement("div");
      navDiv.className = "col-12 text-center mt-3";

      const btnPrev = document.createElement("button");
      const btnNext = document.createElement("button");

      btnPrev.type = "button";
      btnNext.type = "button";
      btnPrev.className = "btn btn-secondary me-2";
      btnNext.className = "btn btn-primary";

      btnPrev.textContent = "← Anterior";
      btnNext.textContent = "Próximo →";

      navDiv.appendChild(btnPrev);
      navDiv.appendChild(btnNext);
      form.appendChild(navDiv);

      const btnSubmit = document.getElementById("btnSubmit");

      function showStep() {
        inputs.forEach((div, i) => {
          div.style.display = i >= current && i < current + step ? "block" : "none";
        });

        btnPrev.style.display = current === 0 ? "none" : "inline-block";
        btnNext.style.display = current + step >= total ? "none" : "inline-block";
        btnSubmit.style.display = current + step >= total ? "inline-block" : "none";
      }

      btnPrev.addEventListener("click", () => {
        current = Math.max(0, current - step);
        showStep();
      });

      btnNext.addEventListener("click", () => {
        current = Math.min(total - step, current + step);
        showStep();
      });

      showStep();
    });
  </script>

</body>

</html>