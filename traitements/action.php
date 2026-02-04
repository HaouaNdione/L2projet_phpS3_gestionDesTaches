<?php
session_start();
require_once '../config/db.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Récupérer l'action
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Vérifier les permissions selon l'action
$actions_admin_only = ['ajouter', 'modifier', 'supprimer'];
if (in_array($action, $actions_admin_only)) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['error_message'] = "Accès refusé. Seuls les administrateurs peuvent effectuer cette action.";
        http_response_code(403);
        header('Location: ../error.php?code=403');
        exit();
    }
}

try {
    switch ($action) {
        case 'ajouter':
            // Validation des données
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            
            if (empty($prenom) || empty($nom) || empty($login) || empty($password) || empty($role)) {
                throw new Exception("Tous les champs sont obligatoires.");
            }
            
            // Validation du rôle
            if (!in_array($role, ['admin', 'simple_user'])) {
                throw new Exception("Rôle invalide.");
            }
            
            // Validation du login (doit être unique)
            $sql = "SELECT COUNT(*) FROM utilisateurs WHERE login = :login";
            $stmt = executeQuery($sql, ['login' => $login]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Ce login existe déjà. Veuillez en choisir un autre.");
            }
            
            // Validation du mot de passe (minimum 6 caractères)
            if (strlen($password) < 6) {
                throw new Exception("Le mot de passe doit contenir au moins 6 caractères.");
            }
            
            // Hachage du mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertion dans la base de données
            $sql = "INSERT INTO utilisateurs (prenom, nom, login, password_hash, role) 
                    VALUES (:prenom, :nom, :login, :password_hash, :role)";
            executeQuery($sql, [
                'prenom' => $prenom,
                'nom' => $nom,
                'login' => $login,
                'password_hash' => $password_hash,
                'role' => $role
            ]);
            
            $_SESSION['success_message'] = "L'utilisateur a été ajouté avec succès.";
            break;
            
        case 'modifier':
            // Validation des données
            $id = (int)($_POST['id'] ?? 0);
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            
            if ($id <= 0 || empty($prenom) || empty($nom) || empty($login) || empty($role)) {
                throw new Exception("Tous les champs obligatoires doivent être remplis.");
            }
            
            // Validation du rôle
            if (!in_array($role, ['admin', 'simple_user'])) {
                throw new Exception("Rôle invalide.");
            }
            
            // Vérifier que l'utilisateur existe
            $sql = "SELECT id FROM utilisateurs WHERE id = :id";
            $stmt = executeQuery($sql, ['id' => $id]);
            if (!$stmt->fetch()) {
                throw new Exception("Utilisateur introuvable.");
            }
            
            // Validation du login (doit être unique sauf pour cet utilisateur)
            $sql = "SELECT COUNT(*) FROM utilisateurs WHERE login = :login AND id != :id";
            $stmt = executeQuery($sql, ['login' => $login, 'id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Ce login existe déjà. Veuillez en choisir un autre.");
            }
            
            // Mise à jour de l'utilisateur
            if (!empty($password)) {
                // Si un nouveau mot de passe est fourni
                if (strlen($password) < 6) {
                    throw new Exception("Le mot de passe doit contenir au moins 6 caractères.");
                }
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE utilisateurs 
                        SET prenom = :prenom, nom = :nom, login = :login, 
                            password_hash = :password_hash, role = :role 
                        WHERE id = :id";
                executeQuery($sql, [
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'login' => $login,
                    'password_hash' => $password_hash,
                    'role' => $role,
                    'id' => $id
                ]);
            } else {
                // Sans changement de mot de passe
                $sql = "UPDATE utilisateurs 
                        SET prenom = :prenom, nom = :nom, login = :login, role = :role 
                        WHERE id = :id";
                executeQuery($sql, [
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'login' => $login,
                    'role' => $role,
                    'id' => $id
                ]);
            }
            
            // Mettre à jour la session si l'utilisateur modifie son propre compte
            if ($id == $_SESSION['user_id']) {
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_login'] = $login;
                $_SESSION['user_role'] = $role;
            }
            
            $_SESSION['success_message'] = "L'utilisateur a été modifié avec succès.";
            break;
            
        case 'supprimer':
            $id = (int)($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception("ID utilisateur invalide.");
            }
            
            // Empêcher la suppression de son propre compte
            if ($id == $_SESSION['user_id']) {
                throw new Exception("Vous ne pouvez pas supprimer votre propre compte.");
            }
            
            // Vérifier que l'utilisateur existe
            $sql = "SELECT id FROM utilisateurs WHERE id = :id";
            $stmt = executeQuery($sql, ['id' => $id]);
            if (!$stmt->fetch()) {
                throw new Exception("Utilisateur introuvable.");
            }
            
            // Supprimer l'utilisateur
            $sql = "DELETE FROM utilisateurs WHERE id = :id";
            executeQuery($sql, ['id' => $id]);
            
            $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès.";
            break;
            
        // =====================================================
        // GESTION DES TÂCHES
        // =====================================================
        case 'ajouter_tache':
            // Validation des données
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statut = $_POST['statut'] ?? 'en_attente';
            $priorite = $_POST['priorite'] ?? 'moyenne';
            $date_debut = $_POST['date_debut'] ?? null;
            $date_fin = $_POST['date_fin'] ?? null;
            $id_utilisateur = (int)($_POST['id_utilisateur'] ?? 0);
            
            // Validation
            if (empty($titre)) {
                throw new Exception("Le titre est obligatoire.");
            }
            
            if (!in_array($statut, ['en_attente', 'en_cours', 'terminee'])) {
                throw new Exception("Statut invalide.");
            }
            
            if (!in_array($priorite, ['basse', 'moyenne', 'haute', 'urgente'])) {
                throw new Exception("Priorité invalide.");
            }
            
            // Si simple user, forcer l'ID utilisateur à être le sien
            if ($_SESSION['user_role'] !== 'admin') {
                $id_utilisateur = $_SESSION['user_id'];
            }
            
            if ($id_utilisateur <= 0) {
                throw new Exception("Utilisateur invalide.");
            }
            
            // Validation des dates
            if (!empty($date_debut) && !empty($date_fin)) {
                if (strtotime($date_fin) < strtotime($date_debut)) {
                    throw new Exception("La date de fin doit être après la date de début.");
                }
            }
            
            // Insertion dans la base de données
            $sql = "INSERT INTO taches (titre, description, statut, priorite, date_debut, date_fin, id_utilisateur) 
                    VALUES (:titre, :description, :statut, :priorite, :date_debut, :date_fin, :id_utilisateur)";
            executeQuery($sql, [
                'titre' => $titre,
                'description' => $description,
                'statut' => $statut,
                'priorite' => $priorite,
                'date_debut' => $date_debut ?: null,
                'date_fin' => $date_fin ?: null,
                'id_utilisateur' => $id_utilisateur
            ]);
            
            $_SESSION['success_message'] = "La tâche a été ajoutée avec succès.";
            header('Location: ../indexTache.php');
            exit();
            
        case 'modifier_tache':
            $id = (int)($_POST['id'] ?? 0);
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statut = $_POST['statut'] ?? 'en_attente';
            $priorite = $_POST['priorite'] ?? 'moyenne';
            $date_debut = $_POST['date_debut'] ?? null;
            $date_fin = $_POST['date_fin'] ?? null;
            $id_utilisateur = (int)($_POST['id_utilisateur'] ?? 0);
            
            if ($id <= 0 || empty($titre)) {
                throw new Exception("Données invalides.");
            }
            
            if (!in_array($statut, ['en_attente', 'en_cours', 'terminee'])) {
                throw new Exception("Statut invalide.");
            }
            
            if (!in_array($priorite, ['basse', 'moyenne', 'haute', 'urgente'])) {
                throw new Exception("Priorité invalide.");
            }
            
            // Vérifier les permissions
            if ($_SESSION['user_role'] !== 'admin') {
                // Simple user ne peut modifier que ses tâches
                $sql = "SELECT id FROM taches WHERE id = :id AND id_utilisateur = :user_id";
                $stmt = executeQuery($sql, ['id' => $id, 'user_id' => $_SESSION['user_id']]);
                if (!$stmt->fetch()) {
                    throw new Exception("Vous ne pouvez modifier que vos propres tâches.");
                }
                $id_utilisateur = $_SESSION['user_id'];
            }
            
            if ($id_utilisateur <= 0) {
                throw new Exception("Utilisateur invalide.");
            }
            
            // Validation des dates
            if (!empty($date_debut) && !empty($date_fin)) {
                if (strtotime($date_fin) < strtotime($date_debut)) {
                    throw new Exception("La date de fin doit être après la date de début.");
                }
            }
            
            // Mise à jour de la tâche
            $sql = "UPDATE taches 
                    SET titre = :titre, description = :description, statut = :statut, 
                        priorite = :priorite, date_debut = :date_debut, date_fin = :date_fin, 
                        id_utilisateur = :id_utilisateur 
                    WHERE id = :id";
            executeQuery($sql, [
                'titre' => $titre,
                'description' => $description,
                'statut' => $statut,
                'priorite' => $priorite,
                'date_debut' => $date_debut ?: null,
                'date_fin' => $date_fin ?: null,
                'id_utilisateur' => $id_utilisateur,
                'id' => $id
            ]);
            
            $_SESSION['success_message'] = "La tâche a été modifiée avec succès.";
            header('Location: ../indexTache.php');
            exit();
            
        case 'supprimer_tache':
            $id = (int)($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception("ID tâche invalide.");
            }
            
            // Vérifier les permissions
            if ($_SESSION['user_role'] !== 'admin') {
                // Simple user ne peut supprimer que ses tâches
                $sql = "SELECT id FROM taches WHERE id = :id AND id_utilisateur = :user_id";
                $stmt = executeQuery($sql, ['id' => $id, 'user_id' => $_SESSION['user_id']]);
                if (!$stmt->fetch()) {
                    throw new Exception("Vous ne pouvez supprimer que vos propres tâches.");
                }
            }
            
            // Supprimer la tâche
            $sql = "DELETE FROM taches WHERE id = :id";
            executeQuery($sql, ['id' => $id]);
            
            $_SESSION['success_message'] = "La tâche a été supprimée avec succès.";
            header('Location: ../indexTache.php');
            exit();
            
        default:
            throw new Exception("Action invalide.");
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

// Redirection vers la page de gestion des utilisateurs
header('Location: ../indexUser.php');
exit();
?>
