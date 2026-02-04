# 🔐 INFORMATIONS DE CONNEXION - MINI PROJET PHP

## 📊 Base de données: `gestion_utilisateurs`

### 🔧 Configuration
- **Host:** localhost
- **Database:** gestion_utilisateurs
- **User:** root
- **Password:** (vide)

---

## 👥 COMPTES UTILISATEURS

### 🛡️ ADMINISTRATEURS (3 comptes)

#### 1. Admin Principal
- **Login:** `admin`
- **Password:** `admin123`
- **Rôle:** Admin
- **Nom:** Admin Système

#### 2. Marie Martin
- **Login:** `mmartin`
- **Password:** `admin123`
- **Rôle:** Admin
- **Nom:** Marie Martin

#### 3. Luc Moreau
- **Login:** `lmoreau`
- **Password:** `admin123`
- **Rôle:** Admin
- **Nom:** Luc Moreau

---

### 👤 UTILISATEURS SIMPLES (6 comptes)

#### 1. Jean Dupont
- **Login:** `jdupont`
- **Password:** `admin123`
- **Rôle:** Simple User

#### 2. Pierre Durand
- **Login:** `pdurand`
- **Password:** `admin123`
- **Rôle:** Simple User

#### 3. Sophie Bernard
- **Login:** `sbernard`
- **Password:** `admin123`
- **Rôle:** Simple User

#### 4. Alice Petit
- **Login:** `apetit`
- **Password:** `admin123`
- **Rôle:** Simple User

#### 5. Thomas Roux
- **Login:** `troux`
- **Password:** `admin123`
- **Rôle:** Simple User

#### 6. Emma Girard
- **Login:** `egirard`
- **Password:** `admin123`
- **Rôle:** Simple User

---

## 📋 TÂCHES CRÉÉES (24 tâches)

### Distribution par utilisateur:
- **Admin (1):** 4 tâches
- **Jean Dupont (2):** 3 tâches
- **Marie Martin (3):** 3 tâches
- **Pierre Durand (4):** 3 tâches
- **Sophie Bernard (5):** 2 tâches
- **Luc Moreau (6):** 2 tâches
- **Alice Petit (7):** 2 tâches
- **Thomas Roux (8):** 2 tâches
- **Emma Girard (9):** 0 tâches (nouveau compte)

### Statuts:
- ✅ **Terminées:** 3 tâches
- 🔄 **En cours:** 7 tâches
- ⏳ **En attente:** 14 tâches

### Priorités:
- 🔴 **Urgente:** 2 tâches
- 🟠 **Haute:** 7 tâches
- 🟡 **Moyenne:** 9 tâches
- 🟢 **Basse:** 6 tâches

---

## 🎯 PERMISSIONS PAR RÔLE

### 🛡️ Administrateur
- ✅ Accès au tableau de bord complet
- ✅ Gestion complète des utilisateurs (CRUD)
- ✅ Voir toutes les tâches du système
- ✅ Créer/Modifier/Supprimer toutes les tâches
- ✅ Assigner des tâches à n'importe quel utilisateur
- ✅ Accès aux statistiques globales

### 👤 Utilisateur Simple
- ✅ Accès au tableau de bord (ses statistiques)
- ❌ Pas d'accès à la gestion des utilisateurs
- ✅ Voir uniquement ses propres tâches
- ✅ Créer des tâches (assignées à soi-même)
- ✅ Modifier/Supprimer uniquement ses propres tâches
- ✅ Voir ses statistiques personnelles

---

## 🚀 INSTALLATION

### 1. Importer la base de données
```bash
# Option 1: Via phpMyAdmin
1. Ouvrir http://localhost/phpmyadmin
2. Créer une nouvelle base: "gestion_utilisateurs"
3. Importer le fichier: database.sql

# Option 2: Via ligne de commande
mysql -u root < database.sql
```

### 2. Accéder à l'application
```
http://localhost/mini-projet-php-haoua-l2glS3030226/
```

### 3. Se connecter
- Utiliser un des comptes ci-dessus
- Tous les mots de passe sont: `admin123`

---

## 🎨 DESIGN

### Template utilisé
- **SB Admin** - Bootstrap 5
- Source: https://startbootstrap.com/template/sb-admin
- Framework: Bootstrap 5.2.3
- Icons: Font Awesome 6.1.1

### Fonctionnalités du design
- ✅ Responsive (Mobile, Tablet, Desktop)
- ✅ Dark sidebar avec navigation
- ✅ Cartes colorées pour les statistiques
- ✅ Tableaux interactifs avec DataTables
- ✅ Graphiques avec Chart.js
- ✅ Messages d'alerte (succès/erreur)
- ✅ Badges colorés pour statuts et priorités
- ✅ Modals pour les confirmations
- ✅ Design moderne et professionnel

---

## 📁 STRUCTURE DU PROJET

```
mini-projet-php-haoua-l2glS3030226/
├── assets/              # Template SB Admin
│   ├── css/
│   ├── js/
│   └── demo/
├── config/
│   └── db.php          # Configuration BDD + fonctions utilitaires
├── includes/
│   ├── header.php      # En-tête + navigation
│   ├── sidebar.php     # Menu latéral
│   └── footer.php      # Pied de page + scripts
├── traitements/
│   └── action.php      # Traitement CRUD (users + tâches)
├── index.php           # Tableau de bord
├── indexUser.php       # Gestion utilisateurs (admin)
├── indexTache.php      # Gestion tâches
├── login.php           # Page de connexion
├── logout.php          # Déconnexion
├── error.php           # Page d'erreur personnalisée
├── database.sql        # Script SQL complet
└── ACCES.md           # Ce fichier
```

---

## 🔒 SÉCURITÉ

- ✅ Mots de passe hashés (bcrypt)
- ✅ Requêtes préparées (PDO)
- ✅ Protection XSS (htmlspecialchars)
- ✅ Sessions sécurisées
- ✅ Contrôle d'accès par rôle
- ✅ Validation des données
- ✅ Protection CSRF (à améliorer)

---

## 📞 SUPPORT

Pour toute question:
- Étudiant: L2GLS3030226
- Date: 04 Février 2026
- Template: SB Admin (Bootstrap 5)

---

**Note:** Pour des raisons de sécurité, en production:
- Changer tous les mots de passe
- Utiliser des mots de passe forts
- Configurer HTTPS
- Activer les logs d'erreur
- Mettre à jour les configurations
