<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
SecurityHeaders::setFull();
$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

<section class="dashboard-header text-center bg-white border-bottom shadow-sm py-5 mb-4" style="margin-top: 20px;">
    <div class="container">
        <h1 class="display-5 fw-bold"><i class="fas fa-chart-line me-3"></i>Resumo dos Dados Geral</h1>
        <p class="lead text-muted">Acompanhe e analise as métricas vitais do sistema</p>
    </div>
</section>

<!-- Fundo cinza suave faz os cartões brancos saltarem à vista -->
<main class="container-fluid px-4 bg-light pb-5 pt-3">
    <!-- Charts Section -->
    <section class="row g-4 align-items-stretch">
        <!-- Pie Chart: User Roles -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Distribuição de Perfil
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Formandos per Curso -->
        <div class="col-lg-8 col-md-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-chart-bar text-success me-2"></i>Formandos por Curso
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="formandosChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Monthly Sessions -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-users text-warning me-2"></i>Tráfego de Sessões Mensais (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/admin.data.php"></script>
<script src="/estagio/Assets/JS/admin.js"></script>
</body>

</html>