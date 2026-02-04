<?php
session_start();

// Récupérer le code d'erreur
$error_code = $_GET['code'] ?? 500;
$error_code = (int)$error_code;

// Messages d'erreur personnalisés
$error_messages = [
    400 => [
        'title' => 'Erreur 400 - Requête invalide',
        'icon' => 'fa-exclamation-triangle',
        'message' => 'La requête envoyée est invalide ou malformée.'
    ],
    403 => [
        'title' => 'Erreur 403 - Accès refusé',
        'icon' => 'fa-ban',
        'message' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'
    ],
    404 => [
        'title' => 'Erreur 404 - Page non trouvée',
        'icon' => 'fa-search',
        'message' => 'La page que vous recherchez n\'existe pas ou a été supprimée.'
    ],
    500 => [
        'title' => 'Erreur 500 - Erreur serveur',
        'icon' => 'fa-server',
        'message' => 'Une erreur interne du serveur s\'est produite. Veuillez réessayer plus tard.'
    ]
];

$error = $error_messages[$error_code] ?? $error_messages[500];
$custom_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

// Vérifier si l'utilisateur est connecté pour afficher le layout complet
$is_authenticated = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Erreur - Gestion Utilisateurs" />
    <title><?= htmlspecialchars($error['title']) ?> - Gestion Utilisateurs</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($is_authenticated): ?>
        <!-- Afficher avec le layout si connecté -->
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.php">Gestion Utilisateurs</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="row justify-content-center mt-5">
                            <div class="col-lg-6">
                                <div class="card border-0 shadow">
                                    <div class="card-body text-center py-5">
                                        <div class="mb-4">
                                            <i class="fas <?= htmlspecialchars($error['icon']) ?> fa-5x text-danger"></i>
                                        </div>
                                        <h1 class="display-4 fw-bold text-danger"><?= $error_code ?></h1>
                                        <h2 class="h3 mb-3"><?= htmlspecialchars($error['title']) ?></h2>
                                        <p class="text-muted fs-5">
                                            <?= htmlspecialchars($error['message']) ?>
                                        </p>
                                        <?php if (!empty($custom_message)): ?>
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle"></i> <?= htmlspecialchars($custom_message) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-4">
                                            <a href="index.php" class="btn btn-primary me-2">
                                                <i class="fas fa-home"></i> Retour au tableau de bord
                                            </a>
                                            <a href="javascript:history.back()" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Retour
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Gestion Utilisateurs 2026</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    <?php else: ?>
        <!-- Afficher sans layout si non connecté -->
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-body text-center py-5">
                                        <div class="mb-4">
                                            <i class="fas <?= htmlspecialchars($error['icon']) ?> fa-5x text-danger"></i>
                                        </div>
                                        <h1 class="display-4 fw-bold text-danger mb-3"><?= $error_code ?></h1>
                                        <h2 class="h3 mb-3"><?= htmlspecialchars($error['title']) ?></h2>
                                        <p class="text-muted">
                                            <?= htmlspecialchars($error['message']) ?>
                                        </p>
                                        <div class="mt-4">
                                            <a href="login.php" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt"></i> Aller à la connexion
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-center small">
                            <div class="text-muted">Copyright &copy; Gestion Utilisateurs 2026</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
