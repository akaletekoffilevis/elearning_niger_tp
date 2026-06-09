========================================
PLATEFORME E-LEARNING - PROJET PHP
========================================

Auteur : AKALETE KOFFI LEVIS
Technologies : PHP + MySQL + HTML + CSS

----------------------------------------
STRUCTURE DU PROJET
----------------------------------------

projet_php/
├── index.php          # Redirection vers login ou dashboard
├── config.php         # Connexion a la base de donnees
├── register.php       # Inscription utilisateur
├── login.php          # Connexion utilisateur
├── dashboard.php      # Tableau de bord avec cours inscrits
├── cours.php          # Liste des cours disponibles
├── inscription.php    # Inscription a un cours
├── logout.php         # Deconnexion
├── base.sql           # Export de la base de donnees
├── README.txt         # Ce fichier
└── css/
    └── style.css      # Styles CSS

----------------------------------------
BASE DE DONNEES
----------------------------------------

Nom : elearning_tp
Tables :
  - utilisateurs (id, nom, prenom, email, password)
  - cours (id, titre, description)
  - inscriptions (id, utilisateur_id, cours_id, date_inscription)

Utilisateur MySQL : elearning / elearning123

----------------------------------------
FONCTIONNALITES
----------------------------------------

1. Gestion des utilisateurs
   - Inscription avec mot de passe hache (password_hash)
   - Connexion avec verification (password_verify)
   - Deconnexion (session_destroy)

2. Gestion des cours
   - Affichage de tous les cours disponibles

3. Inscription aux cours
   - Inscription a un cours
   - Visualisation des cours inscrits dans le tableau de bord

4. Securite
   - Mots de passe haches avec password_hash()
   - Verification avec password_verify()
   - Sessions PHP pour la connexion
   - Protection contre les injections SQL

----------------------------------------
INSTALLATION : ( sous linux ubuntu sur windows utiliser mysql phpmyadmin)
----------------------------------------

1. Importer base.sql dans MySQL :
   mysql -u root -p elearning_tp < base.sql

2. Placer le dossier dans le serveur web

3. Acceder via http://localhost/projet_php/

----------------------------------------
COMPTE ADMIN PAR DEFAUT
----------------------------------------

Email : admin@elearning.com
Mot de passe : 123456
