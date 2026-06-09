<?php
session_start();
include "config.php";

if (isset($_POST["login"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM utilisateurs WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user["id"];
        $_SESSION["nom"] = $user["nom"];
        $_SESSION["prenom"] = $user["prenom"];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - E-Learning</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" name="login">Connexion</button>
        </form>
        <p>Pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </div>
</body>
</html>