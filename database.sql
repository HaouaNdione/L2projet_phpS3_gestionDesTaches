-- Base de données pour le Mini Projet PHP - Gestion des Utilisateurs et des Tâches
-- Créé le: 2026-02-03
-- Modifié le: 2026-02-04
-- Auteur: L2GLS3030226

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_utilisateurs DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gestion_utilisateurs;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'simple_user') NOT NULL DEFAULT 'simple_user',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion d'un utilisateur administrateur par défaut
-- Login: admin / Password: admin123
INSERT INTO utilisateurs (prenom, nom, login, password_hash, role) 
VALUES ('Admin', 'Système', 'admin', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'admin');

-- Insertion d'utilisateurs de test
-- Tous les mots de passe sont: admin123
INSERT INTO utilisateurs (prenom, nom, login, password_hash, role) 
VALUES 
('Jean', 'Dupont', 'jdupont', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'simple_user'),
('Marie', 'Martin', 'mmartin', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'admin'),
('Pierre', 'Durand', 'pdurand', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'simple_user'),
('Sophie', 'Bernard', 'sbernard', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'simple_user'),
('Luc', 'Moreau', 'lmoreau', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'admin'),
('Alice', 'Petit', 'apetit', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'simple_user'),
('Thomas', 'Roux', 'troux', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'simple_user'),
('Emma', 'Girard', 'egirard', '$2y$10$8s9ULMUOZoNZT9pINkl5KO4.cwZxwXLSDTc./6KF1V.JHt0DMByLq', 'simple_user');

-- Création d'index pour améliorer les performances
CREATE INDEX idx_login ON utilisateurs(login);
CREATE INDEX idx_role ON utilisateurs(role);

-- =====================================================
-- TABLE DES TÂCHES
-- =====================================================
CREATE TABLE IF NOT EXISTS taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    statut ENUM('en_attente', 'en_cours', 'terminee') NOT NULL DEFAULT 'en_attente',
    priorite ENUM('basse', 'moyenne', 'haute', 'urgente') NOT NULL DEFAULT 'moyenne',
    date_debut DATE,
    date_fin DATE,
    id_utilisateur INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création d'index pour les tâches
CREATE INDEX idx_statut ON taches(statut);
CREATE INDEX idx_priorite ON taches(priorite);
CREATE INDEX idx_utilisateur ON taches(id_utilisateur);
CREATE INDEX idx_date_fin ON taches(date_fin);

-- Insertion de tâches de test
INSERT INTO taches (titre, description, statut, priorite, date_debut, date_fin, id_utilisateur) 
VALUES 
-- Tâches pour Admin (id=1)
('Développer le module utilisateur', 'Créer le système CRUD pour la gestion des utilisateurs avec interface moderne', 'terminee', 'haute', '2026-02-01', '2026-02-03', 1),
('Implémenter l\'authentification', 'Mettre en place le système de login/logout sécurisé avec gestion des sessions', 'terminee', 'urgente', '2026-02-01', '2026-02-02', 1),
('Créer le dashboard', 'Développer l\'interface du tableau de bord avec statistiques et graphiques', 'en_cours', 'haute', '2026-02-03', '2026-02-05', 1),
('Audit de sécurité', 'Vérifier les failles XSS, SQL injection et CSRF', 'en_attente', 'urgente', '2026-02-05', '2026-02-06', 1),

-- Tâches pour Jean Dupont (id=2)
('Tester les fonctionnalités', 'Tester toutes les fonctionnalités du système', 'en_cours', 'moyenne', '2026-02-04', '2026-02-07', 2),
('Rédiger le manuel utilisateur', 'Créer la documentation pour les utilisateurs finaux', 'en_attente', 'basse', '2026-02-06', '2026-02-10', 2),
('Optimiser les requêtes SQL', 'Améliorer les performances des requêtes de base de données', 'en_attente', 'moyenne', '2026-02-08', '2026-02-12', 2),

-- Tâches pour Marie Martin (id=3)
('Valider le design', 'Vérifier la conformité du design avec les maquettes', 'terminee', 'moyenne', '2026-02-01', '2026-02-03', 3),
('Configurer le serveur', 'Installer et configurer l\'environnement de production', 'en_cours', 'haute', '2026-02-04', '2026-02-06', 3),
('Backup automatique', 'Mettre en place un système de sauvegarde automatique', 'en_attente', 'haute', '2026-02-07', '2026-02-09', 3),

-- Tâches pour Pierre Durand (id=4)
('Formation utilisateurs', 'Former les utilisateurs finaux à l\'utilisation du système', 'en_attente', 'moyenne', '2026-02-10', '2026-02-12', 4),
('Créer les tutoriels vidéo', 'Enregistrer des vidéos explicatives pour chaque fonctionnalité', 'en_attente', 'basse', '2026-02-12', '2026-02-15', 4),
('Support utilisateurs', 'Répondre aux questions et problèmes des utilisateurs', 'en_cours', 'moyenne', '2026-02-04', '2026-02-20', 4),

-- Tâches pour Sophie Bernard (id=5)
('Intégration API externe', 'Connecter le système avec l\'API de gestion de projet', 'en_attente', 'haute', '2026-02-08', '2026-02-14', 5),
('Tests d\'intégration', 'Effectuer les tests d\'intégration du système complet', 'en_attente', 'haute', '2026-02-10', '2026-02-12', 5),

-- Tâches pour Luc Moreau (id=6)
('Monitoring du système', 'Mettre en place les outils de monitoring et alertes', 'en_cours', 'urgente', '2026-02-04', '2026-02-07', 6),
('Documentation technique', 'Rédiger la documentation technique complète du projet', 'en_cours', 'moyenne', '2026-02-05', '2026-02-10', 6),

-- Tâches pour Alice Petit (id=7)
('Améliorer l\'UX', 'Optimiser l\'expérience utilisateur sur mobile', 'en_attente', 'moyenne', '2026-02-08', '2026-02-11', 7),
('Accessibilité WCAG', 'Rendre le site conforme aux normes d\'accessibilité', 'en_attente', 'basse', '2026-02-12', '2026-02-18', 7),

-- Tâches pour Thomas Roux (id=8)
('Corriger les bugs', 'Résoudre les bugs identifiés lors des tests', 'en_cours', 'haute', '2026-02-04', '2026-02-08', 8),
('Refactoring du code', 'Améliorer la qualité et la maintenabilité du code', 'en_attente', 'basse', '2026-02-10', '2026-02-15', 8);
