<?php
$page_title = "Gestion des Utilisateurs";
require_once 'config/db.php';

// Vérifier l'authentification et les rôles
requireLogin();
requireRole('admin');

require_once 'includes/header.php';

// Récupérer tous les utilisateurs
try {
    $sql = "SELECT id, prenom, nom, login, role FROM utilisateurs ORDER BY id DESC";
    $stmt = executeQuery($sql);
    $Utilisateurs = $stmt->fetchAll();
} catch (Exception $e) {
    $Utilisateurs = [];
    $error_message = "Erreur lors de la récupération des utilisateurs.";
}

// Gestion du mode édition
$editMode = false;
$userToEdit = null;
if (isset($_GET['action']) && $_GET['action'] === 'modifier' && isset($_GET['id'])) {
    $editMode = true;
    $userId = (int)$_GET['id'];
    try {
        $sql = "SELECT id, prenom, nom, login, role FROM utilisateurs WHERE id = :id";
        $stmt = executeQuery($sql, ['id' => $userId]);
        $userToEdit = $stmt->fetch();
        if (!$userToEdit) {
            $editMode = false;
        }
    } catch (Exception $e) {
        $editMode = false;
    }
}

// Afficher les messages de session
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? $error_message ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<?php require_once 'includes/sidebar.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Utilisateurs</li>
    </ol>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-<?= $editMode ? 'edit' : 'user-plus' ?>"></i>
                    <?= $editMode ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?>
                </div>
                <div class="card-body">
                    <form method="POST" action="traitements/action.php">
                        <input type="hidden" name="action" value="<?= $editMode ? 'modifier' : 'ajouter' ?>">
                        <?php if ($editMode && $userToEdit): ?>
                            <input type="hidden" name="id" value="<?= $userToEdit['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="prenom" class="form-label">
                                <i class="fas fa-user"></i> Prénom <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="prenom" name="prenom" 
                                   value="<?= $editMode && $userToEdit ? htmlspecialchars($userToEdit['prenom']) : '' ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">
                                <i class="fas fa-user"></i> Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?= $editMode && $userToEdit ? htmlspecialchars($userToEdit['nom']) : '' ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="login" class="form-label">
                                <i class="fas fa-at"></i> Login <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="login" name="login" 
                                   value="<?= $editMode && $userToEdit ? htmlspecialchars($userToEdit['login']) : '' ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag"></i> Rôle <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Sélectionner un rôle --</option>
                                <option value="admin" <?= ($editMode && $userToEdit && $userToEdit['role'] === 'admin') ? 'selected' : '' ?>>
                                    Administrateur
                                </option>
                                <option value="simple_user" <?= ($editMode && $userToEdit && $userToEdit['role'] === 'simple_user') ? 'selected' : '' ?>>
                                    Utilisateur Simple
                                </option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mot de passe 
                                <?php if (!$editMode): ?>
                                    <span class="text-danger">*</span>
                                <?php else: ?>
                                    <span class="text-muted small">(laisser vide pour ne pas modifier)</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   <?= !$editMode ? 'required' : '' ?>>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-<?= $editMode ? 'warning' : 'primary' ?>">
                                <i class="fas fa-<?= $editMode ? 'save' : 'plus' ?>"></i>
                                <?= $editMode ? 'Mettre à jour' : 'Ajouter' ?>
                            </button>
                            <?php if ($editMode): ?>
                                <a href="indexUser.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table"></i>
                    Liste des utilisateurs (<?= count($Utilisateurs) ?>)
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>Login</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($Utilisateurs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-inbox"></i> Aucun utilisateur trouvé
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($Utilisateurs as $u): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= htmlspecialchars($u['prenom']) ?></td>
                                        <td><?= htmlspecialchars($u['nom']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-at"></i> <?= htmlspecialchars($u['login']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-user-shield"></i> Admin
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-user"></i> Simple User
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="indexUser.php?action=modifier&id=<?= $u['id'] ?>" 
                                               class="btn btn-sm btn-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="traitements/action.php?action=supprimer&id=<?= $u['id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               title="Supprimer"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
