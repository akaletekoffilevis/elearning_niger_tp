<?php
session_start();
include "config.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user"];
$cours_id = (int)$_GET["id"];

// verifier si deja inscrit
$check = "SELECT * FROM inscriptions WHERE utilisateur_id = $user_id AND cours_id = $cours_id";
$check_result = mysqli_query($conn, $check);

if (mysqli_num_rows($check_result) == 0) {
    $sql = "INSERT INTO inscriptions (utilisateur_id, cours_id, date_inscription)
            VALUES ($user_id, $cours_id, NOW())";
    mysqli_query($conn, $sql);
    $msg = "Inscription reussie au cours";
} else {
    $msg = "Vous etes deja inscrit a ce cours";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - E-Learning</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1><?= $msg ?></h1>
        <a href="cours.php">Retour aux cours</a>
        <a href="dashboard.php">Tableau de bord</a>
    </div>
</body>
</html>