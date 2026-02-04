# Mini Projet PHP - Système de Gestion des Utilisateurs

**Étudiant:** HAOUA MAMADOU NDIONE
**Date:** 04 Février 2026  
**Template:** SB Admin Bootstrap 5

## 📋 Description

Système complet de gestion des utilisateurs avec interface d'administration moderne utilisant le template SB Admin. Le projet comprend l'authentification, la gestion CRUD des utilisateurs, et un contrôle d'accès basé sur les rôles.

## ✨ Fonctionnalités

- ✅ **Authentification sécurisée** avec sessions PHP
- ✅ **Gestion CRUD complète** des utilisateurs (Create, Read, Update, Delete)
- ✅ **Gestion des rôles** (Administrateur / Utilisateur Simple)
- ✅ **Interface responsive** avec Bootstrap 5 et SB Admin
- ✅ **Sécurité renforcée** : mots de passe hashés, requêtes préparées, protection CSRF
- ✅ **Tableau de bord** avec statistiques et graphiques
- ✅ **Messages de feedback** (succès/erreur)
- ✅ **Design moderne** avec Font Awesome icons

## 🚀 Installation

### Prérequis
- XAMPP (Apache + MySQL + PHP 7.4+)
- Navigateur web moderne

### Étapes d'installation

1. **Copier le projet** dans le dossier XAMPP
   ```
   C:\xampp\htdocs\mini-projet-php-haoua-l2glS3030226\
   ```

2. **Démarrer XAMPP**
   - Lancer Apache
   - Lancer MySQL

3. **Créer la base de données**
   - Ouvrir phpMyAdmin : http://localhost/phpmyadmin
   - Cliquer sur "Importer"
   - Sélectionner le fichier `database.sql`
   - Cliquer sur "Exécuter"

4. **Accéder à l'application**
   ```
   http://localhost/mini-projet-php-haoua-l2glS3030226/
   ```

## 👤 Comptes de test

### Administrateur
- **Login:** admin
- **Password:** admin123

### Utilisateur simple
- **Login:** jdupont
- **Password:** admin123

## 📁 Structure du projet

```
mini-projet-php-haoua-l2glS3030226/
├── assets/                      # Template SB Admin (CSS, JS, images)
│   ├── css/
│   ├── js/
│   └── assets/
├── config/
│   └── db.php                   # Configuration base de données
├── includes/
│   ├── header.php               # En-tête avec navigation
│   ├── sidebar.php              # Barre latérale
│   └── footer.php               # Pied de page
├── traitements/
│   └── action.php               # Traitement des actions CRUD
├── database.sql                 # Script de création de la BDD
├── index.php                    # Tableau de bord
├── indexUser.php                # Gestion des utilisateurs
├── login.php                    # Page de connexion
├── logout.php                   # Déconnexion
└── README.md                    # Ce fichier
```

## 🔧 Configuration

### Base de données
Modifier les paramètres dans `config/db.php` si nécessaire :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_utilisateurs');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 🔒 Sécurité

- ✅ **Mots de passe** : Hashés avec `password_hash()` (bcrypt)
- ✅ **SQL Injection** : Requêtes préparées PDO
- ✅ **XSS** : `htmlspecialchars()` sur toutes les sorties
- ✅ **Sessions** : Régénération d'ID après connexion
- ✅ **Contrôle d'accès** : Vérification des rôles sur chaque page

## 📊 Base de données

### Table: utilisateurs
| Champ | Type | Description |
|-------|------|-------------|
| id | INT | Clé primaire auto-incrémentée |
| prenom | VARCHAR(100) | Prénom de l'utilisateur |
| nom | VARCHAR(100) | Nom de l'utilisateur |
| login | VARCHAR(50) | Identifiant unique |
| password_hash | VARCHAR(255) | Mot de passe hashé |
| role | ENUM | 'admin' ou 'simple_user' |
| date_creation | TIMESTAMP | Date de création |
| date_modification | TIMESTAMP | Date de modification |

## 🎨 Technologies utilisées

- **Backend:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Template:** SB Admin
- **Icons:** Font Awesome 6
- **Charts:** Chart.js

## 📝 Fonctionnalités détaillées

### Tableau de bord (index.php)
- Statistiques en temps réel
- Graphique de répartition des rôles
- Liste des utilisateurs récents
- Informations système

### Gestion des utilisateurs (indexUser.php)
- Ajouter un nouvel utilisateur
- Modifier un utilisateur existant
- Supprimer un utilisateur
- Liste complète avec filtres
- Validation des formulaires

### Authentification
- Page de connexion sécurisée
- Gestion des sessions
- Déconnexion propre
- Messages de feedback

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifier que MySQL est démarré dans XAMPP
- Vérifier les identifiants dans `config/db.php`
- S'assurer que la base de données `gestion_utilisateurs` existe

### Problèmes d'assets (CSS/JS)
- Vérifier que le dossier `assets/` contient les fichiers SB Admin
- Vérifier les chemins dans les fichiers include

### Erreurs de session
- Vérifier que `session_start()` est appelé
- Vérifier les permissions du dossier de sessions PHP

## 📄 Licence

Projet académique - L2 GL S3 - 2026

## 👨‍💻 Auteur

**HAOUA MAMADOU NDIONE** - Mini Projet PHP
