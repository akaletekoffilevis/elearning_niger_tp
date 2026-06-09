<?php
session_start();
include "config.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user"];
$sql = "SELECT c.titre, c.description, i.date_inscription
        FROM inscriptions i
        JOIN cours c ON i.cours_id = c.id
        WHERE i.utilisateur_id = $user_id";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - E-Learning</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue <?= $_SESSION["prenom"] ?> <?= $_SESSION["nom"] ?></h1>
        <nav>
            <a href="cours.php">Voir les cours</a>
            <a href="logout.php">Deconnexion</a>
        </nav>
        <h2>Mes cours inscrits</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="cours">
                    <h3><?= $row["titre"] ?></h3>
                    <p><?= $row["description"] ?></p>
                    <small>Inscrit le : <?= $row["date_inscription"] ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Vous n'etes inscrit a aucun cours.</p>
        <?php endif; ?>
    </div>
</body>
</html>