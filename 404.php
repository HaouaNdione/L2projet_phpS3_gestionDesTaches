<?php
// Redirection automatique vers page d'erreur 404
require_once 'config/db.php';
showError(404, 'La page demandée n\'existe pas.');
?>
