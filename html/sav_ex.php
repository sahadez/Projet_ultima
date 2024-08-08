<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once 'config.php';

// Charger PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$error = ""; // Variable pour stocker les éventuelles erreurs

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bicycode = $_POST['bicycode'];
    $nom_client = $_POST['nom_client'];
    $email = $_POST['email'];
    $numero_telephone = $_POST['numero_telephone'];
    $commentaire = $_POST['commentaire'];

    // Insertion des données dans la table SAV
    $stmt = $bdd->prepare("INSERT INTO sav (bicycode, nom_client, email, numero_telephone, commentaire) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$bicycode, $nom_client, $email, $numero_telephone, $commentaire])) {
        // Récupérer le numéro de ticket généré
        $numero_ticket = $bdd->lastInsertId();

        // Création du corps de l'e-mail
        $subject = "Création du ticket - " . $numero_ticket;
        $message = "Bonjour " . $nom_client . ",\n\nVotre ticket SAV a été créé avec succès.\nNuméro de ticket : " . $numero_ticket . "\n\nObjet : Création du ticket - " . $numero_ticket . "\nCommentaire :\n" . $commentaire . "\n\nMerci pour votre confiance.\nCordialement,\nUltima Mobility";

        // Envoi de l'e-mail en utilisant PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Paramètres SMTP d'Infomaniak
    $mail->SMTPDebug = 2; // Mettez à 2 pour les informations de débogage
    $mail->isSMTP();
    $mail->Host = 'mail.infomaniak.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'workshop@ultima.dev'; // Remplacez par votre adresse e-mail Infomaniak
    $mail->Password = 'Ultima69'; // Remplacez par votre mot de passe e-mail Infomaniak
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Adresses d'expéditeur et de destinataire
    $mail->setFrom('workshop@ultima.dev', 'Ultima Mobility');
    $mail->addAddress($email);
    $mail->addCC('theo.colombies@ultima.dev');
   

    // Contenu du message
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body = $message;

    // Envoyer l'e-mail
    $mail->send();
            

            // Afficher le message de succès dans un pop-up
            echo '<script>alert("Le ticket a été créé avec succès, vous allez recevoir une confirmation par mail!\nNuméro de ticket : ' . $numero_ticket . '"); window.location.href = "https://ultima.dev/fr";</script>';
        } catch (Exception $e) {
            // Afficher l'erreur
            $error = "Une erreur est survenue lors de l'envoi de l'e-mail. Erreur : " . $mail->ErrorInfo;
            echo '<script>alert("' . $error . '");</script>';
        }
    } else {
        // Afficher l'erreur
        $error = "Une erreur est survenue lors de la création du ticket.";
        echo '<script>alert("' . $error . '");</script>';
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <title>Ultima Mobility</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="col-md-12">
        <div class="container mt-5">
            <h2>Créer un ticket SAV</h2>
            <form method="post">
                <div class="form-group">
                    <label for="bicycode">Bicycode :</label>
                    <input type="text" class="form-control" id="bicycode" name="bicycode" required>
                </div>

                <div class="form-group">
                    <label for="nom_client">Nom du client :</label>
                    <input type="text" class="form-control" id="nom_client" name="nom_client" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail :</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="numero_telephone">Numéro de téléphone :</label>
                    <input type="text" class="form-control" id="numero_telephone" name="numero_telephone" required>
                </div>

                <div class="form-group">
                    <label for="commentaire">Description :</label>
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Créer SAV</button>
            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
                integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
                crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
                integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
                crossorigin="anonymous"></script>
</body>
</html>
