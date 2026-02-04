<?php
$page_title = "Gestion des Tâches";
require_once 'config/db.php';

// Vérifier l'authentification (tous les utilisateurs connectés peuvent accéder)
requireLogin();

require_once 'includes/header.php';

// Récupérer les tâches selon le rôle
try {
    if (isAdmin()) {
        // Admin voit toutes les tâches
        $sql = "SELECT t.*, u.prenom, u.nom, u.login 
                FROM taches t 
                INNER JOIN utilisateurs u ON t.id_utilisateur = u.id 
                ORDER BY t.id DESC";
        $stmt = executeQuery($sql);
    } else {
        // Simple user voit uniquement ses tâches
        $sql = "SELECT t.*, u.prenom, u.nom, u.login 
                FROM taches t 
                INNER JOIN utilisateurs u ON t.id_utilisateur = u.id 
                WHERE t.id_utilisateur = :user_id 
                ORDER BY t.id DESC";
        $stmt = executeQuery($sql, ['user_id' => $_SESSION['user_id']]);
    }
    $taches = $stmt->fetchAll();
} catch (Exception $e) {
    $taches = [];
    $error_message = "Erreur lors de la récupération des tâches.";
}

// Récupérer tous les utilisateurs pour le formulaire (admin seulement)
$utilisateurs = [];
if (isAdmin()) {
    try {
        $sql = "SELECT id, prenom, nom FROM utilisateurs ORDER BY prenom, nom";
        $stmt = executeQuery($sql);
        $utilisateurs = $stmt->fetchAll();
    } catch (Exception $e) {
        $utilisateurs = [];
    }
}

// Gestion du mode édition
$editMode = false;
$tacheToEdit = null;
if (isset($_GET['action']) && $_GET['action'] === 'modifier' && isset($_GET['id'])) {
    $editMode = true;
    $tacheId = (int)$_GET['id'];
    try {
        if (isAdmin()) {
            $sql = "SELECT * FROM taches WHERE id = :id";
            $stmt = executeQuery($sql, ['id' => $tacheId]);
        } else {
            // Simple user ne peut modifier que ses tâches
            $sql = "SELECT * FROM taches WHERE id = :id AND id_utilisateur = :user_id";
            $stmt = executeQuery($sql, ['id' => $tacheId, 'user_id' => $_SESSION['user_id']]);
        }
        $tacheToEdit = $stmt->fetch();
        if (!$tacheToEdit) {
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
    <h1 class="mt-4"><i class="fas fa-tasks"></i> Gestion des Tâches</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Tâches</li>
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
                    <i class="fas fa-<?= $editMode ? 'edit' : 'plus-circle' ?>"></i>
                    <?= $editMode ? 'Modifier une tâche' : 'Ajouter une tâche' ?>
                </div>
                <div class="card-body">
                    <form method="POST" action="traitements/action.php">
                        <input type="hidden" name="action" value="<?= $editMode ? 'modifier_tache' : 'ajouter_tache' ?>">
                        <?php if ($editMode && $tacheToEdit): ?>
                            <input type="hidden" name="id" value="<?= $tacheToEdit['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titre" name="titre" required 
                                   value="<?= $editMode ? htmlspecialchars($tacheToEdit['titre']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= $editMode ? htmlspecialchars($tacheToEdit['description']) : '' ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en_attente" <?= ($editMode && $tacheToEdit['statut'] === 'en_attente') ? 'selected' : '' ?>>En attente</option>
                                    <option value="en_cours" <?= ($editMode && $tacheToEdit['statut'] === 'en_cours') ? 'selected' : '' ?>>En cours</option>
                                    <option value="terminee" <?= ($editMode && $tacheToEdit['statut'] === 'terminee') ? 'selected' : '' ?>>Terminée</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="priorite" class="form-label">Priorité <span class="text-danger">*</span></label>
                                <select class="form-select" id="priorite" name="priorite" required>
                                    <option value="basse" <?= ($editMode && $tacheToEdit['priorite'] === 'basse') ? 'selected' : '' ?>>Basse</option>
                                    <option value="moyenne" <?= ($editMode && $tacheToEdit['priorite'] === 'moyenne') ? 'selected' : '' ?>>Moyenne</option>
                                    <option value="haute" <?= ($editMode && $tacheToEdit['priorite'] === 'haute') ? 'selected' : '' ?>>Haute</option>
                                    <option value="urgente" <?= ($editMode && $tacheToEdit['priorite'] === 'urgente') ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_debut" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                       value="<?= $editMode ? htmlspecialchars($tacheToEdit['date_debut']) : '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="date_fin" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                       value="<?= $editMode ? htmlspecialchars($tacheToEdit['date_fin']) : '' ?>">
                            </div>
                        </div>
                        
                        <?php if (isAdmin()): ?>
                            <div class="mb-3">
                                <label for="id_utilisateur" class="form-label">Assigné à <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_utilisateur" name="id_utilisateur" required>
                                    <option value="">Sélectionner un utilisateur</option>
                                    <?php foreach ($utilisateurs as $user): ?>
                                        <option value="<?= $user['id'] ?>" 
                                                <?= ($editMode && $tacheToEdit['id_utilisateur'] == $user['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <!-- Pour les simple users, la tâche est automatiquement assignée à eux -->
                            <input type="hidden" name="id_utilisateur" value="<?= $_SESSION['user_id'] ?>">
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-<?= $editMode ? 'save' : 'plus' ?>"></i>
                                <?= $editMode ? 'Enregistrer les modifications' : 'Ajouter la tâche' ?>
                            </button>
                            <?php if ($editMode): ?>
                                <a href="indexTache.php" class="btn btn-secondary">
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
                    <i class="fas fa-list"></i>
                    Liste des tâches <?= !isAdmin() ? '(Mes tâches)' : '(Toutes les tâches)' ?>
                </div>
                <div class="card-body">
                    <?php if (empty($taches)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucune tâche trouvée.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="datatablesSimple">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <?php if (isAdmin()): ?>
                                            <th>Assigné à</th>
                                        <?php endif; ?>
                                        <th>Statut</th>
                                        <th>Priorité</th>
                                        <th>Date fin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($taches as $tache): ?>
                                        <tr>
                                            <td><?= $tache['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($tache['titre']) ?></strong>
                                                <?php if (!empty($tache['description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(substr($tache['description'], 0, 50)) ?><?= strlen($tache['description']) > 50 ? '...' : '' ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (isAdmin()): ?>
                                                <td><?= htmlspecialchars($tache['prenom'] . ' ' . $tache['nom']) ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <?php
                                                $statutClass = [
                                                    'en_attente' => 'warning',
                                                    'en_cours' => 'info',
                                                    'terminee' => 'success'
                                                ];
                                                $statutLabel = [
                                                    'en_attente' => 'En attente',
                                                    'en_cours' => 'En cours',
                                                    'terminee' => 'Terminée'
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $statutClass[$tache['statut']] ?>">
                                                    <?= $statutLabel[$tache['statut']] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $prioriteClass = [
                                                    'basse' => 'secondary',
                                                    'moyenne' => 'primary',
                                                    'haute' => 'warning',
                                                    'urgente' => 'danger'
                                                ];
                                                $prioriteLabel = [
                                                    'basse' => 'Basse',
                                                    'moyenne' => 'Moyenne',
                                                    'haute' => 'Haute',
                                                    'urgente' => 'Urgente'
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $prioriteClass[$tache['priorite']] ?>">
                                                    <?= $prioriteLabel[$tache['priorite']] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($tache['date_fin']): ?>
                                                    <?= date('d/m/Y', strtotime($tache['date_fin'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non définie</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="indexTache.php?action=modifier&id=<?= $tache['id'] ?>" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="traitements/action.php?action=supprimer_tache&id=<?= $tache['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       title="Supprimer"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
