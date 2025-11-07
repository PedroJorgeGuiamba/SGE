<?php
// Lista avaliacoes
require_once __DIR__ . '/../../Conexao/db.php';

$query = "
SELECT ac.id_avaliacao, f.nome, f.apelido, m.descricao AS modulo, 
       c.id_competencia, ac.percentagem_atingida, ac.mencao, 
       tt.descricao AS tentativa, ac.data_avaliacao
FROM avaliacao_competencia ac
JOIN formando f ON ac.id_formando = f.id_formando
JOIN competencia c ON ac.id_competencia = c.id_competencia
JOIN modulo m ON c.id_modulo = m.id_modulo
JOIN tipo_tentativa tt ON ac.id_tentativa = tt.id_tentativa
ORDER BY ac.data_avaliacao DESC
";
$res = $mysqli->query($query);
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <title>Lista de Avaliações</title>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 text-primary mb-0"><i class="fa-solid fa-clipboard-list me-2"></i>Lista de Avaliações</h1>
      <a href="lancarNotas.php" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> Inserir Nova Avaliação
      </a>
    </div>

    <div class="table-responsive shadow-sm rounded bg-white p-4">
      <table class="table table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Formando</th>
            <th>Módulo</th>
            <th>Competência</th>
            <th>%</th>
            <th>Menção</th>
            <th>Tentativa</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id_avaliacao']) ?></td>
              <td><?= htmlspecialchars($row['nome'] . ' ' . $row['apelido']) ?></td>
              <td><?= htmlspecialchars($row['modulo']) ?></td>
              <td><?= htmlspecialchars($row['id_competencia']) ?></td>
              <td><?= htmlspecialchars($row['percentagem_atingida']) ?>%</td>
              <td><?= htmlspecialchars($row['mencao']) ?></td>
              <td><?= htmlspecialchars($row['tentativa']) ?></td>
              <td><?= htmlspecialchars($row['data_avaliacao']) ?></td>
              <td>
                <a href="detalhes.php?id=<?= urlencode($row['id_avaliacao']) ?>" class="btn btn-sm btn-info text-white">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <a href="editar.php?id=<?= urlencode($row['id_avaliacao']) ?>" class="btn btn-sm btn-warning text-white">
                  <i class="fa-solid fa-pen"></i>
                </a>
                <form action="../../Controller/Notas/apagarNotas.php" method="post" style="display:inline"
                  onsubmit="return confirm('Tem certeza que deseja eliminar esta avaliação?');">
                  <input type="hidden" name="id_avaliacao" value="<?= htmlspecialchars($row['id_avaliacao']) ?>">
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>