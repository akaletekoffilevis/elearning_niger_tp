<?php
$host = "localhost";
$user = "elearning";
$password = "elearning123";
$dbname = "elearning_tp";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Erreur de connexion a la base de donnees");
}
?>