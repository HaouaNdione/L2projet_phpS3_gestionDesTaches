<?php
/**
 * Page de test et vérification des rôles
 * À utiliser à titre de test uniquement
 */
session_start();
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-check-circle"></i> Test des Permissions et Rôles</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Test des permissions</li>
    </ol>

    <!-- Informations utilisateur actuel -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user"></i> Informations utilisateur actuel
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <table class="table table-borderless">
                            <tr>
                                <th>ID:</th>
                                <td><?= htmlspecialchars($_SESSION['user_id']) ?></td>
                            </tr>
                            <tr>
                                <th>Prénom:</th>
                                <td><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Nom:</th>
                                <td><?= htmlspecialchars($_SESSION['user_nom'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Login:</th>
                                <td><span class="badge bg-info"><?= htmlspecialchars($_SESSION['user_login'] ?? 'N/A') ?></span></td>
                            </tr>
                            <tr>
                                <th>Rôle:</th>
                                <td>
                                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                        <span class="badge bg-danger"><i class="fas fa-user-shield"></i> Administrateur</span>
                                    <?php else: ?>
                                        <span class="badge bg-info"><i class="fas fa-user"></i> Utilisateur simple</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Vous n'êtes pas connecté.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Vérification des permissions -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-shield-alt"></i> Vérification des permissions
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Authentifié:</th>
                            <td>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Oui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Non</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Administrateur:</th>
                            <td>
                                <?php if (hasRole('admin')): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Oui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Non</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Accès au tableau de bord:</th>
                            <td>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Autorisé</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Refusé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Gestion des utilisateurs:</th>
                            <td>
                                <?php if (hasRole('admin')): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Autorisé</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Refusé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pages publiques et protégées -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-sitemap"></i> Navigation et pages disponibles
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-lock-open text-success"></i> Pages publiques (non connecté)</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <a href="login.php">Page de connexion</a>
                                    <span class="badge bg-success float-end">Accessible</span>
                                </li>
                                <li class="list-group-item">
                                    <a href="404.php">Page d'erreur 404</a>
                                    <span class="badge bg-warning float-end">Redirection auto</span>
                                </li>
                                <li class="list-group-item">
                                    <a href="error.php?code=403">Page d'erreur 403</a>
                                    <span class="badge bg-warning float-end">Non connecté</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-lock text-danger"></i> Pages protégées (authentification)</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <a href="index.php">Tableau de bord</a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <span class="badge bg-success float-end"><i class="fas fa-check"></i> Autorisé</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger float-end"><i class="fas fa-times"></i> Refusé</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item">
                                    <a href="indexUser.php">Gestion des utilisateurs</a>
                                    <?php if (hasRole('admin')): ?>
                                        <span class="badge bg-success float-end"><i class="fas fa-check"></i> Autorisé</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger float-end"><i class="fas fa-times"></i> Admin requis</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item">
                                    <a href="test.php">Page de test (cette page)</a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <span class="badge bg-success float-end"><i class="fas fa-check"></i> Autorisé</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger float-end"><i class="fas fa-times"></i> Refusé</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item">
                                    <a href="logout.php">Déconnexion</a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <span class="badge bg-success float-end"><i class="fas fa-check"></i> Disponible</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary float-end">Non connecté</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-exclamation-circle"></i> Pages d'erreur
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Cliquez sur les liens ci-dessous pour tester les pages d'erreur:</p>
                    <div class="btn-group" role="group">
                        <a href="error.php?code=400" class="btn btn-outline-warning">Erreur 400</a>
                        <a href="error.php?code=403" class="btn btn-outline-danger">Erreur 403</a>
                        <a href="error.php?code=404" class="btn btn-outline-danger">Erreur 404</a>
                        <a href="error.php?code=500" class="btn btn-outline-danger">Erreur 500</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé de la gestion des rôles -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Résumé de la gestion des rôles
                </div>
                <div class="card-body">
                    <h5>✅ Contrôles d'accès implémentés:</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Authentification requise pour le tableau de bord</li>
                        <li><i class="fas fa-check text-success"></i> Rôle admin requis pour la gestion des utilisateurs</li>
                        <li><i class="fas fa-check text-success"></i> Sessions sécurisées avec régénération d'ID</li>
                        <li><i class="fas fa-check text-success"></i> Mots de passe hashés avec bcrypt</li>
                        <li><i class="fas fa-check text-success"></i> Requêtes préparées (PDO) contre SQL injection</li>
                        <li><i class="fas fa-check text-success"></i> Pages d'erreur personnalisées (403, 404, 500)</li>
                        <li><i class="fas fa-check text-success"></i> Messages d'erreur sécurisés (htmlspecialchars)</li>
                        <li><i class="fas fa-check text-success"></i> Redirection automatique selon les permissions</li>
                    </ul>

                    <h5 class="mt-4">📋 Rôles disponibles:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-user-shield text-danger"></i> Administrateur (admin)</h6>
                                    <ul class="small list-unstyled">
                                        <li>✅ Tableau de bord</li>
                                        <li>✅ Gestion des utilisateurs (CRUD)</li>
                                        <li>✅ Paramètres système</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-user text-info"></i> Utilisateur simple (simple_user)</h6>
                                    <ul class="small list-unstyled">
                                        <li>✅ Tableau de bord (lecture seule)</li>
                                        <li>❌ Gestion des utilisateurs</li>
                                        <li>❌ Paramètres système</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
