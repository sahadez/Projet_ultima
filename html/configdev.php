<?php
$servername = 'localhost';
$username = 'root';
$password = 'Mobility-2023';

try {
    $bdd = new PDO('mysql:host=localhost;dbname=ultimadev', 'root', 'Mobility-2023');
       // définir le mode d'erreur PDO sur exception
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo ""; 
} catch(PDOException $e) {
    echo 'Échec de la connexion: ' . $e->getMessage();
}
?>

