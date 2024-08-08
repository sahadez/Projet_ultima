<?php
session_start();
require_once 'config.php';

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $email = strtolower($email);

    $check = $bdd->prepare('SELECT nom, prenom, email, password, token FROM clients WHERE email = ?');
    $check->execute(array($email));
    $data = $check->fetch();
    $row = $check->rowCount();
 // Si > à 0 alors l'utilisateur existe
 if($row > 0)
 {
     // Si le mail est bon niveau format
     if(filter_var($email, FILTER_VALIDATE_EMAIL))
     {
         // Si le mot de passe est le bon
         if(password_verify($password, $data['password']))
         {
             // On créer la session et on redirige sur home.php
             $_SESSION['user'] = $data['token'];
             //$_SESSION['last_activity'] = time();
             header('Location: bicycodesR.php');
             die();
         }else{ header('Location: bicycodes.php?login_err=password'); die(); }
     }else{ header('Location: bicycodes.php?login_err=email'); die(); }
 }else{ header('Location: bicycodes.php?login_err=already'); die(); }
}else{ header('Location: bicycodes.php'); die();} // si le formulaire est envoyé sans aucune données
?>
