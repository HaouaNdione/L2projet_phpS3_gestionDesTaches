<?php
/**
 * Configuration de la base de données
 * Mini Projet PHP - Gestion des Utilisateurs
 */

// Démarrer la session au début
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_utilisateurs');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Options PDO pour améliorer la sécurité et la gestion des erreurs
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Création de la connexion PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // En production, ne jamais afficher les détails de l'erreur
    die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
    // Pour le développement, décommenter la ligne suivante:
    // die("Erreur de connexion: " . $e->getMessage());
}

/**
 * Fonction utilitaire pour exécuter une requête préparée
 * 
 * @param string $sql Requête SQL avec placeholders
 * @param array $params Paramètres pour la requête
 * @return PDOStatement
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // Logger l'erreur en production
        error_log("Erreur SQL: " . $e->getMessage());
        throw new Exception("Une erreur est survenue lors de l'exécution de la requête.");
    }
}

/**
 * Vérifier si l'utilisateur est authentifié
 * Rediriger vers login si nécessaire
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Vérifier si l'utilisateur a un rôle spécifique
 * 
 * @param string|array $roles Rôle(s) autorisé(s)
 * @return bool
 */
function hasRole($roles) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    if (is_array($roles)) {
        return in_array($_SESSION['user_role'], $roles);
    }
    
    return $_SESSION['user_role'] === $roles;
}

/**
 * Exiger un rôle spécifique, rediriger sinon
 * 
 * @param string|array $roles Rôle(s) autorisé(s)
 */
function requireRole($roles) {
    if (!hasRole($roles)) {
        http_response_code(403);
        header('Location: error.php?code=403');
        exit();
    }
}

/**
 * Vérifier si l'utilisateur connecté est admin
 * 
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Vérifier si l'utilisateur connecté est un simple user
 * 
 * @return bool
 */
function isSimpleUser() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'simple_user';
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 * 
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir le nom complet de l'utilisateur connecté
 * 
 * @return string
 */
function getCurrentUserFullName() {
    $prenom = $_SESSION['user_prenom'] ?? '';
    $nom = $_SESSION['user_nom'] ?? '';
    return trim($prenom . ' ' . $nom);
}

/**
 * Afficher une page d'erreur personnalisée
 * 
 * @param int $code Code d'erreur HTTP
 * @param string $message Message d'erreur personnalisé
 */
function showError($code, $message = '') {
    http_response_code($code);
    $_SESSION['error_code'] = $code;
    $_SESSION['error_message'] = $message;
    
    if (isset($_SESSION['user_id'])) {
        header('Location: error.php?code=' . $code);
    } else {
        header('Location: login.php?error=' . $code);
    }
    exit();
}

?>

