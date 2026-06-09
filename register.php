<?php
include "config.php";

if (isset($_POST["register"])) {
    $nom = mysqli_real_escape_string($conn, $_POST["nom"]);
    $prenom = mysqli_real_escape_string($conn, $_POST["prenom"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs (nom, prenom, email, password)
            VALUES ('$nom', '$prenom', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>Compte cree avec succes</p>";
    } else {
        echo "<p style='color: red;'>Erreur : " . mysqli_error($conn) . "</p>";
    }
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
        <h1>Inscription</h1>
        <form method="POST">
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prenom" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="register">S'inscrire</button>
        </form>
        <p>Deja un compte ? <a href="login.php">Connectez-vous</a></p>
    </div>
</body>
</html>