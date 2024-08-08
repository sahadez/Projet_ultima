<?php
 
session_start();
require_once 'config.php'; // Inclure la connexion à la base de données

if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_retype'])) {
    // Échapper les données contre les attaques XSS
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $password_retype = htmlspecialchars($_POST['password_retype']);

    $email = strtolower($email); // Convertir l'email en minuscules pour éviter les doublons

    // Vérifier si l'utilisateur existe
    $check = $bdd->prepare('SELECT email, password FROM clients WHERE email = ?');
    $check->execute(array($email));
    $data = $check->fetch();
    $row = $check->rowCount();

    if($row == 0) { // Si l'utilisateur n'existe pas
        if(strlen($nom) <= 50 && strlen($prenom) <= 50) { // Vérifier la longueur des noms
            if(strlen($email) <= 100) { // Vérifier la longueur de l'email
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) { // Valider le format de l'email
                    if($password === $password_retype) { // Vérifier si les mots de passe correspondent
                        // Hacher le mot de passe avec Bcrypt, coût de 12
                        $cost = ['cost' => 12];
                        $password = password_hash($password, PASSWORD_BCRYPT, $cost);

                        // Obtenir l'adresse IP
                        $ip = $_SERVER['REMOTE_ADDR'];

                        // Générer un token
                        $token = bin2hex(openssl_random_pseudo_bytes(64));

                        // Insérer dans la base de données
                        $insert = $bdd->prepare('INSERT INTO clients(nom, prenom, email, password, ip, token, date_inscription) VALUES(:nom, :prenom, :email, :password, :ip, :token, NOW())');
                        $insert->execute(array(
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'email' => $email,
                            'password' => $password,
                            'ip' => $ip,
                            'token' => $token
                        ));

                        // Rediriger avec un message de succès
                        
                        $_SESSION['user'] = $token;

                        header('Location: bicycodesR.php');
                        die();
                    } else {
                        header('Location: clients_inscription.php?reg_err=password');
                        die();
                    }
                } else {
                    header('Location: clients_inscription.php?reg_err=email');
                    die();
                }
            } else {
                header('Location: clients_inscription.php?reg_err=email_length');
                die();
            }
        } else {
            header('Location: clients_inscription.php?reg_err=nom_prenom_length');
            die();
        }
    } else {
        header('Location: clients_inscription.php?reg_err=already');
        die();
    }
}
