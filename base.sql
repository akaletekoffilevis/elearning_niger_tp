CREATE DATABASE IF NOT EXISTS elearning_tp;
USE elearning_tp;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    cours_id INT NOT NULL,
    date_inscription DATE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (cours_id) REFERENCES cours(id)
);

INSERT IGNORE INTO cours (id, titre, description) VALUES
(1, 'PHP pour debutants', 'Apprenez les bases du langage PHP : variables, conditions, boucles, formulaires et connexion a une base de donnees.'),
(2, 'PHP et MySQL', 'Connectez vos applications PHP a une base de donnees MySQL avec requetes preparees et securisees.'),
(3, 'Java POO', 'Decouvrez la programmation orientee objet en Java : classes, objets, heritage, polymorphisme et encapsulation.'),
(4, 'Java avance', 'Approfondissez Java avec les exceptions, les fichiers, JDBC, les collections et les threads.'),
(5, 'HTML & CSS', 'Maitrisez la creation de sites web responsives avec HTML5 et CSS3, flexbox et grid layout.');

-- Mot de passe: 123456
INSERT IGNORE INTO utilisateurs (id, nom, prenom, email, password) VALUES
(1, 'Admin', 'System', 'admin@elearning.com', '$2y$12$lI6PyikAtAZU5BdwuEHALOUgoJMBd8ntABSVjxde28/Y8vdXkbsWm');
