<?php
session_start();
include "config.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM cours";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cours - E-Learning</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Cours disponibles</h1>
        <nav>
            <a href="dashboard.php">Tableau de bord</a>
            <a href="logout.php">Deconnexion</a>
        </nav>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="cours">
                <h3><?= $row["titre"] ?></h3>
                <p><?= $row["description"] ?></p>
                <a href="inscription.php?id=<?= $row["id"] ?>" class="btn">S'inscrire</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>