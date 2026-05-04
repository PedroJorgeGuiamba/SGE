<?php
session_start();
header('Content-Type: application/javascript');

require_once __DIR__ . '/../../Conexao/conector.php';
$conector = new Conector();
$conn = $conector->getConexao();

// User roles pie chart
$user_roles_query = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM usuarios GROUP BY role");
$user_roles_labels = [];
$user_roles_data = [];
if ($user_roles_query) {
    while ($row = mysqli_fetch_assoc($user_roles_query)) {
        $user_roles_labels[] = $row['role'];
        $user_roles_data[] = $row['count'];
    }
}

// Monthly sessions bar chart
$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$monthly_sessions_query = mysqli_query($conn, "SELECT MONTH(data) as month, COUNT(*) as count FROM sessao WHERE YEAR(data) = YEAR(CURDATE()) GROUP BY MONTH(data)");
$monthly_sessions = array_fill(0, 12, 0);
if ($monthly_sessions_query) {
    while ($row = mysqli_fetch_assoc($monthly_sessions_query)) {
        $monthly_sessions[$row['month'] - 1] = $row['count'];
    }
}

// Formandos per curso bar chart
$formandos_per_curso_query = mysqli_query($conn, "SELECT c.nome, COUNT(f.id_formando) as count FROM formando f JOIN turma_formando tf ON f.codigo = tf.codigo_formando JOIN turma t ON tf.codigo_turma = t.codigo JOIN curso c ON t.codigo_curso = c.codigo GROUP BY c.nome");
$formandos_curso_labels = [];
$formandos_curso_data = [];
if ($formandos_per_curso_query) {
    while ($row = mysqli_fetch_assoc($formandos_per_curso_query)) {
        $formandos_curso_labels[] = $row['nome'];
        $formandos_curso_data[] = $row['count'];
    }
}

echo "// Dados gerados pelo PHP para os gráficos\n";
echo "window.adminData = " . json_encode([
    'months' => $months,
    'user_roles_labels' => $user_roles_labels,
    'user_roles_data' => $user_roles_data,
    'formandos_curso_labels' => $formandos_curso_labels,
    'formandos_curso_data' => $formandos_curso_data,
    'monthly_sessions' => $monthly_sessions,
]) . ";\n";

echo "console.log('Dados carregados');\n";