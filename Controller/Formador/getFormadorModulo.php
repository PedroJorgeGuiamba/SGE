<?php
require_once __DIR__ . '/../../Conexao/conector.php';


$con = new Conector();
$conn = $con->getConexao();

$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $query = "SELECT id_formador, nome, apelido FROM formador";
    $resultado = mysqli_query($conn, $query);

    $options = "<option value=''>Selecione um Formador</option>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        $options .= "<option value='{$row['id_formador']}'>" . htmlspecialchars($row['nome'] .  ' ' . $row['apelido']) . "</option>";
    }
}
else{
    $query = "SELECT 
        fm.id_modulo,
        f.id_formador,
        f.nome,
        f.apelido
    FROM itc_v3.formador_modulo fm
    LEFT JOIN itc_v3.formador f 
        ON fm.id_formador = f.id_formador
	where id_modulo = ?;";
    // $resultado = mysqli_query($conn, $query);
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $termo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $options = "<option value=''>Selecione um Formador</option>";
    // while ($row = mysqli_fetch_assoc($resultado)) {
    while ($row = $resultado->fetch_assoc()) {
        $options .= "<option value='{$row['id_formador']}'>" . htmlspecialchars($row['nome'] .  ' ' . $row['apelido']) . "</option>";
    }
}
echo $options;