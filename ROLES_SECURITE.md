# 🔒 Gestion des Rôles et Permissions - Documentation Complète

## Résumé des Modifications

Un système complet de gestion des rôles et des pages d'erreur personnalisées a été implémenté pour assurer la sécurité et l'expérience utilisateur.

---

## 🔐 Fonctions de Contrôle d'Accès

### Nouvelles fonctions dans `config/db.php`:

#### 1. `requireLogin()`
```php
requireLogin();  // Redirige vers login.php si non connecté
```
**Usage:** Pages accessibles uniquement aux utilisateurs authentifiés

#### 2. `hasRole($roles)`
```php
if (hasRole('admin')) { ... }
if (hasRole(['admin', 'simple_user'])) { ... }
```
**Usage:** Vérifier si l'utilisateur a un rôle spécifique

#### 3. `requireRole($roles)`
```php
requireRole('admin');  // Redirige vers error.php?code=403 si non autorisé
```
**Usage:** Garantir un rôle spécifique, sinon afficher erreur 403

#### 4. `showError($code, $message)`
```php
showError(404, 'Page non trouvée');
showError(403, 'Vous n\'avez pas les permissions');
```
**Usage:** Afficher une page d'erreur personnalisée

---

## 📄 Pages Créées/Modifiées

### Pages Protégées par Authentification:

| Page | Authentification | Rôles acceptés | Fonction |
|------|------------------|-----------------|----------|
| `index.php` | ✅ Requise | Admin + Simple User | Tableau de bord (lecture) |
| `indexUser.php` | ✅ Requise | Admin uniquement | Gestion CRUD des utilisateurs |
| `test.php` | ✅ Requise | Admin + Simple User | Test des permissions |

### Pages Publiques:

| Page | Authentification | Fonction |
|------|------------------|----------|
| `login.php` | ❌ Non requise | Formulaire de connexion |
| `logout.php` | ✅ Requise | Déconnexion sécurisée |
| `404.php` | ❌ Non requise | Redirection erreur 404 |
| `error.php` | N/A | Page d'erreur personnalisée |

---

## 🎯 Gestion des Rôles

### Administrateur (admin)
```php
$_SESSION['user_role'] === 'admin'
```

**Permissions:**
- ✅ Accès au tableau de bord
- ✅ Gestion complète des utilisateurs (Ajouter, Modifier, Supprimer)
- ✅ Voir le menu "Administration" dans la sidebar
- ✅ Accès à toutes les statistiques

**Comptes de test:**
- `admin` / `admin123`
- `mmartin` / `admin123`

### Utilisateur Simple (simple_user)
```php
$_SESSION['user_role'] === 'simple_user'
```

**Permissions:**
- ✅ Accès au tableau de bord (lecture seule)
- ❌ Pas d'accès à la gestion des utilisateurs
- ❌ Pas d'accès au menu "Administration"
- ❌ Pas de modification d'utilisateurs

**Comptes de test:**
- `jdupont` / `admin123`
- `pdurand` / `admin123`

---

## 🛡️ Contrôles d'Accès Implémentés

### 1. Dans `header.php`
- Vérification de session au démarrage
- Redirection automatique vers login si session invalide
- Affichage du rôle dans le profil utilisateur

### 2. Dans `sidebar.php`
- Menu "Administration" visible uniquement pour les admins
- Affichage du rôle en bas de la sidebar
- Menu dynamique selon le rôle

### 3. Dans `indexUser.php`
```php
requireLogin();      // Vérifier l'authentification
requireRole('admin'); // Vérifier le rôle admin
```
- Redirection vers error.php?code=403 si non autorisé

### 4. Dans `index.php`
```php
requireLogin();  // Accessible à tous les utilisateurs connectés
```
- Tous les rôles peuvent accéder
- Tableau de bord adapté au rôle

### 5. Dans `traitements/action.php`
- Double vérification: authentification + rôle admin
- Validation stricte de toutes les données
- Messages d'erreur personnalisés
- Redirection sécurisée vers error.php

---

## 📊 Pages d'Erreur Personnalisées

### Structure: `error.php?code=[CODE]`

#### Erreur 400 - Requête invalide
- **Icône:** Exclamation triangle
- **Cas d'usage:** Paramètres manquants ou invalides

#### Erreur 403 - Accès refusé
- **Icône:** Interdiction
- **Cas d'usage:** Permissions insuffisantes
- **Trigger:** `requireRole()` échoue

#### Erreur 404 - Page non trouvée
- **Icône:** Recherche
- **Cas d'usage:** Page inexistante
- **Trigger:** `404.php` ou URL inexistante

#### Erreur 500 - Erreur serveur
- **Icône:** Serveur
- **Cas d'usage:** Erreur interne
- **Trigger:** Exception non gérée

### Fonctionnalités des pages d'erreur:
- ✅ Design responsive avec Bootstrap
- ✅ Icônes Font Awesome
- ✅ Boutons de navigation (retour, accueil)
- ✅ Message personnalisé optionnel
- ✅ Layout différent si connecté ou non

---

## 🧪 Page de Test

**URL:** `http://localhost/mini-projet-php-haoua-l2glS3030226/test.php`

Permet de vérifier:
- ✅ Informations utilisateur actuel
- ✅ Vérification des permissions
- ✅ Accès aux pages protégées
- ✅ Tests des pages d'erreur
- ✅ Résumé de la gestion des rôles

---

## 🔐 Sécurité Implémentée

### Authentification:
- ✅ Hashage des mots de passe (bcrypt - PASSWORD_DEFAULT)
- ✅ Requêtes préparées (PDO) contre SQL injection
- ✅ Régénération d'ID de session après login
- ✅ Destruction sécurisée de session au logout

### Autorisation:
- ✅ Vérification de rôle sur toutes les pages protégées
- ✅ Redirection automatique sans afficher d'infos
- ✅ Messages d'erreur génériques (pas de détails sensibles)
- ✅ Logs d'erreur en backend

### Protection XSS:
- ✅ `htmlspecialchars()` sur toutes les sorties
- ✅ Échappement des variables de session
- ✅ Validation côté serveur

### CSRF (optionnel - non implémenté):
- Peut être ajouté avec tokens uniques par session

---

## 📋 Cas d'Usage et Scénarios

### Scénario 1: Utilisateur simple essaie d'accéder à la gestion des utilisateurs
```
1. Accès à /indexUser.php
2. requireRole('admin') échoue
3. Redirection vers /error.php?code=403
4. Page d'erreur personnalisée affichée
```

### Scénario 2: Non-connecté essaie d'accéder au tableau de bord
```
1. Accès à /index.php
2. requireLogin() échoue
3. Redirection vers /login.php
4. Message d'authentification
```

### Scénario 3: Admin modifie un utilisateur
```
1. Accès à /indexUser.php?action=modifier&id=5
2. requireLogin() ✅
3. requireRole('admin') ✅
4. Formulaire de modification affiché
5. POST vers /traitements/action.php
6. Vérifications + mise à jour BD
7. Message de succès + redirection
```

### Scénario 4: Admin essaie de supprimer son propre compte
```
1. Clic sur supprimer (son ID)
2. action.php vérifie if ($id == $_SESSION['user_id'])
3. Exception levée: "Vous ne pouvez pas supprimer votre propre compte"
4. Message d'erreur affiché
```

---

## ✅ Checklist de Sécurité

- [x] Authentification par login/password hashé
- [x] Gestion des sessions sécurisée
- [x] Contrôle d'accès basé sur les rôles (RBAC)
- [x] Redirection automatique non authentifiés
- [x] Rôles vérifiés avant chaque action sensible
- [x] Pages d'erreur personnalisées
- [x] Protection contre SQL injection (PDO)
- [x] Protection XSS (htmlspecialchars)
- [x] Messages d'erreur non-informatifs
- [x] Logs d'erreur en backend
- [x] Validation des entrées
- [x] Prévention de suppression du compte personnel

---

## 🚀 Utilisation des Fonctions

### Dans une nouvelle page protégée:

```php
<?php
$page_title = "Ma Page";
require_once 'config/db.php';

// Vérifier l'authentification
requireLogin();

// Si la page est réservée aux admins
requireRole('admin');

require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// ... Contenu de la page ...

require_once 'includes/footer.php';
?>
```

### Vérification conditionnelle dans le contenu:

```php
<?php if (hasRole('admin')): ?>
    <!-- Contenu visible uniquement pour les admins -->
<?php endif; ?>
```

---

## 📞 Support et Dépannage

**Page de test:** [test.php](test.php) - Vérifiez vos permissions actuelles

**Erreur 403?** Vous n'avez pas les permissions (rôle insuffisant)

**Erreur 404?** Page inexistante

**Erreur 500?** Erreur de base de données ou exception non gérée

---

**Auteur:** L2GLS3030226 | **Date:** 2026-02-03
