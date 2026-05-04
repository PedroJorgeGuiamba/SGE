<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../Controller/Admin/Home.php';
include_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);
?>

<?php require_once __DIR__ . '/../../Includes/header-estagio-situacao-admin.php' ?>

<section class="dashboard-header text-center bg-white border-bottom shadow-sm py-5 mb-4" style="margin-top: 13px;">
    <div class="container">
        <h1 class="display-5 fw-bold"><i class="fas fa-chart-pie me-3"></i>Situação de Estágios</h1>
        <p class="lead text-muted">Acompanhe as métricas globais e o status das candidaturas a estágio</p>
    </div>
</section>

<!-- Fundo cinza suave faz os cartões brancos saltarem à vista -->
<main class="container-fluid px-4 bg-light pb-5 pt-3">
    <!-- Charts Section -->
    <section class="row g-4 align-items-stretch">

        <!-- Bar Chart: Pedidos per Month -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-chart-line text-primary me-2"></i>Cartas Geradas Mensalmente (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="pedidosBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-layer-group text-success me-2"></i>Cartas por Qualificação (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="pedidosPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Width for Companies -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-building text-info me-2"></i>Empresas Solicitadas por Qualificação (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative" style="height: 40vh;">
                        <canvas id="credenciaisEmpresaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart: Status Resposta -->
        <div class="col-lg-4 col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-envelope-open-text text-warning me-2"></i>Estado das Respostas
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="statusRespostaPie"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart: Status Estagio -->
        <div class="col-lg-4 col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-briefcase text-secondary me-2"></i>Estado dos Estágios (Geral)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="statusEstagioPie"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12 mt-4">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-graduation-cap text-danger me-2"></i>Estágios por Qualificação
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="statusEstagioQualificacaoPie"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/situacaoDeEstagio.data.php"></script>
<script src="/estagio/Assets/JS/situacaoDeEstagio.js"></script>
</body>

</html>