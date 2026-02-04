<?php
$page_title = "Tableau de bord";
require_once 'config/db.php';

// Vérifier l'authentification (accessible à tous les rôles)
requireLogin();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Récupérer les statistiques
try {
    // Statistiques utilisateurs
    $sql = "SELECT COUNT(*) as total FROM utilisateurs";
    $stmt = executeQuery($sql);
    $totalUsers = $stmt->fetchColumn();
    
    $sql = "SELECT COUNT(*) as total FROM utilisateurs WHERE role = 'admin'";
    $stmt = executeQuery($sql);
    $totalAdmins = $stmt->fetchColumn();
    
    $sql = "SELECT COUNT(*) as total FROM utilisateurs WHERE role = 'simple_user'";
    $stmt = executeQuery($sql);
    $totalSimpleUsers = $stmt->fetchColumn();
    
    // Statistiques tâches
    if (isAdmin()) {
        // Admin voit toutes les tâches
        $sql = "SELECT COUNT(*) as total FROM taches";
        $stmt = executeQuery($sql);
        $totalTaches = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as total FROM taches WHERE statut = 'en_attente'";
        $stmt = executeQuery($sql);
        $tachesEnAttente = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as total FROM taches WHERE statut = 'en_cours'";
        $stmt = executeQuery($sql);
        $tachesEnCours = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as total FROM taches WHERE statut = 'terminee'";
        $stmt = executeQuery($sql);
        $tachesTerminees = $stmt->fetchColumn();
    } else {
        // Simple user voit uniquement ses tâches
        $sql = "SELECT COUNT(*) as total FROM taches WHERE id_utilisateur = :user_id";
        $stmt = executeQuery($sql, ['user_id' => $_SESSION['user_id']]);
        $totalTaches = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as total FROM taches WHERE statut = 'en_attente' AND id_utilisateur = :user_id";
        $stmt = executeQuery($sql, ['user_id' => $_SESSION['user_id']]);
        $tachesEnAttente = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as total FROM taches WHERE statut = 'en_cours' AND id_utilisateur = :user_id";
        $stmt = executeQuery($sql, ['user_id' => $_SESSION['user_id']]);
        $tachesEnCours = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as total FROM taches WHERE statut = 'terminee' AND id_utilisateur = :user_id";
        $stmt = executeQuery($sql, ['user_id' => $_SESSION['user_id']]);
        $tachesTerminees = $stmt->fetchColumn();
    }
    
    // Derniers utilisateurs ajoutés (admin seulement)
    if (isAdmin()) {
        $sql = "SELECT prenom, nom, login, role, date_creation FROM utilisateurs ORDER BY date_creation DESC LIMIT 5";
        $stmt = executeQuery($sql);
        $recentUsers = $stmt->fetchAll();
    } else {
        $recentUsers = [];
    }
    
    // Tâches récentes ou urgentes
    if (isAdmin()) {
        $sql = "SELECT t.*, u.prenom, u.nom FROM taches t 
                INNER JOIN utilisateurs u ON t.id_utilisateur = u.id 
                WHERE t.statut != 'terminee' 
                ORDER BY t.priorite DESC, t.date_fin ASC LIMIT 5";
        $stmt = executeQuery($sql);
    } else {
        $sql = "SELECT t.*, u.prenom, u.nom FROM taches t 
                INNER JOIN utilisateurs u ON t.id_utilisateur = u.id 
                WHERE t.id_utilisateur = :user_id AND t.statut != 'terminee' 
                ORDER BY t.priorite DESC, t.date_fin ASC LIMIT 5";
        $stmt = executeQuery($sql, ['user_id' => $_SESSION['user_id']]);
    }
    $recentTaches = $stmt->fetchAll();
    
} catch (Exception $e) {
    $totalUsers = 0;
    $totalAdmins = 0;
    $totalSimpleUsers = 0;
    $totalTaches = 0;
    $tachesEnAttente = 0;
    $tachesEnCours = 0;
    $tachesTerminees = 0;
    $recentUsers = [];
    $recentTaches = [];
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tableau de bord</li>
    </ol>
    
    <!-- Cartes de statistiques -->
    <div class="row">
        <!-- Statistiques Utilisateurs (Admin uniquement) -->
        <?php if (isAdmin()): ?>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Utilisateurs</div>
                            <div class="h2"><?= $totalUsers ?></div>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="indexUser.php">Voir les détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Statistiques Tâches -->
        <div class="col-xl-<?= isAdmin() ? '3' : '4' ?> col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small"><?= isAdmin() ? 'Total Tâches' : 'Mes Tâches' ?></div>
                            <div class="h2"><?= $totalTaches ?></div>
                        </div>
                        <div>
                            <i class="fas fa-tasks fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="indexTache.php">Gérer les tâches</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-<?= isAdmin() ? '3' : '4' ?> col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">En attente</div>
                            <div class="h2"><?= $tachesEnAttente ?></div>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="indexTache.php">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-<?= isAdmin() ? '3' : '4' ?> col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">En cours</div>
                            <div class="h2"><?= $tachesEnCours ?></div>
                        </div>
                        <div>
                            <i class="fas fa-spinner fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="indexTache.php">Travailler dessus</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphique et activités récentes -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Répartition des tâches par statut
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Tâches prioritaires <?= !isAdmin() ? '(Mes tâches)' : '' ?>
                </div>
                <div class="card-body">
                    <?php if (empty($recentTaches)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p>Aucune tâche en cours ou urgente!</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentTaches as $tache): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <i class="fas fa-tasks"></i>
                                            <?= htmlspecialchars($tache['titre']) ?>
                                        </h6>
                                        <?php if ($tache['date_fin']): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i>
                                                <?= date('d/m/Y', strtotime($tache['date_fin'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-1">
                                        <?php
                                        $prioriteColors = [
                                            'basse' => 'secondary',
                                            'moyenne' => 'primary',
                                            'haute' => 'warning',
                                            'urgente' => 'danger'
                                        ];
                                        $prioriteLabels = [
                                            'basse' => 'Basse',
                                            'moyenne' => 'Moyenne',
                                            'haute' => 'Haute',
                                            'urgente' => 'Urgente'
                                        ];
                                        $statutColors = [
                                            'en_attente' => 'warning',
                                            'en_cours' => 'info',
                                            'terminee' => 'success'
                                        ];
                                        $statutLabels = [
                                            'en_attente' => 'En attente',
                                            'en_cours' => 'En cours',
                                            'terminee' => 'Terminée'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $prioriteColors[$tache['priorite']] ?>">
                                            <?= $prioriteLabels[$tache['priorite']] ?>
                                        </span>
                                        <span class="badge bg-<?= $statutColors[$tache['statut']] ?>">
                                            <?= $statutLabels[$tache['statut']] ?>
                                        </span>
                                        <?php if (isAdmin()): ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-user"></i> <?= htmlspecialchars($tache['prenom'] . ' ' . $tache['nom']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (isAdmin() && !empty($recentUsers)): ?>
    <!-- Section utilisateurs récents (Admin uniquement) -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-clock me-1"></i>
                    Utilisateurs récemment ajoutés
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentUsers as $user): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <i class="fas fa-user-circle"></i>
                                        <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($user['date_creation'])) ?>
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-at"></i> <?= htmlspecialchars($user['login']) ?>
                                    </span>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-user-shield"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-user"></i> Simple User
                                        </span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informations système -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Informations système
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong><i class="fas fa-server"></i> Serveur:</strong><br>
                    <?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?>
                </div>
                <div class="col-md-3">
                    <strong><i class="fab fa-php"></i> Version PHP:</strong><br>
                    <?= phpversion() ?>
                </div>
                <div class="col-md-3">
                    <strong><i class="fas fa-database"></i> Base de données:</strong><br>
                    MySQL <?= $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?>
                </div>
                <div class="col-md-3">
                    <strong><i class="fas fa-calendar"></i> Date:</strong><br>
                    <?= date('d/m/Y H:i:s') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.js"></script>
<script>
// Graphique circulaire de répartition des tâches par statut
const ctx = document.getElementById('taskStatusChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['En attente', 'En cours', 'Terminées'],
        datasets: [{
            label: 'Nombre de tâches',
            data: [<?= $tachesEnAttente ?>, <?= $tachesEnCours ?>, <?= $tachesTerminees ?>],
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',   // Warning - En attente
                'rgba(13, 202, 240, 0.8)',  // Info - En cours
                'rgba(25, 135, 84, 0.8)'    // Success - Terminées
            ],
            borderColor: [
                'rgba(255, 193, 7, 1)',
                'rgba(13, 202, 240, 1)',
                'rgba(25, 135, 84, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: true,
                text: <?= isAdmin() ? "'Répartition de toutes les tâches'" : "'Répartition de mes tâches'" ?>
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
