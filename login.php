<?php
session_start();

// Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'config/db.php';

$error = '';
$info_message = '';

// Afficher un message d'erreur si fourni dans l'URL
if (isset($_GET['error'])) {
    $error_code = (int)$_GET['error'];
    $error_messages = [
        403 => 'Vous n\'avez pas les permissions pour accéder à cette page.',
        404 => 'La page que vous recherchiez n\'existe pas.'
    ];
    $info_message = $error_messages[$error_code] ?? '';
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Récupérer l'utilisateur depuis la base de données
            $sql = "SELECT id, prenom, nom, login, password_hash, role FROM utilisateurs WHERE login = :login";
            $stmt = executeQuery($sql, ['login' => $login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Authentification réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['user_role'] = $user['role'];
                
                // Régénérer l'ID de session pour la sécurité
                session_regenerate_id(true);
                
                header('Location: index.php');
                exit();
            } else {
                $error = "Identifiants incorrects.";
            }
        } catch (Exception $e) {
            $error = "Une erreur est survenue. Veuillez réessayer.";
            error_log("Erreur de connexion: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Connexion au système de gestion des utilisateurs" />
    <meta name="author" content="L2GLS3030226" />
    <title>Connexion - Gestion Utilisateurs</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">
                                        <i class="fas fa-user-lock"></i> Connexion
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($info_message)): ?>
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($info_message) ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle"></i> Vous avez été déconnecté avec succès.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputLogin" type="text" name="login" 
                                                   placeholder="Login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" 
                                                   required autofocus />
                                            <label for="inputLogin">
                                                <i class="fas fa-user"></i> Login
                                            </label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" type="password" 
                                                   name="password" placeholder="Mot de passe" required />
                                            <label for="inputPassword">
                                                <i class="fas fa-lock"></i> Mot de passe
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" />
                                            <label class="form-check-label" for="inputRememberPassword">
                                                Se souvenir de moi
                                            </label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="#!">Mot de passe oublié ?</a>
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-sign-in-alt"></i> Se connecter
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small">
                                        <p class="mb-2"><strong>📋 Comptes de test disponibles:</strong></p>
                                        <div class="row text-start">
                                            <div class="col-md-6 mb-2">
                                                <div class="badge bg-danger w-100 p-2">
                                                    <i class="fas fa-user-shield"></i> Admin<br>
                                                    <small>admin / admin123</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="badge bg-info w-100 p-2">
                                                    <i class="fas fa-user"></i> User<br>
                                                    <small>jdupont / admin123</small>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0 mt-2">
                                            <i class="fas fa-info-circle"></i> Tous les mots de passe: admin123
                                        </p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
