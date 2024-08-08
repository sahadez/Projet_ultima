<?php

session_start();
require_once 'config.php'; // Assurez-vous d'inclure votre fichier de configuration de la base de données ici

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: bicycodes.php');
    die();
}
// Code de traitement du formulaire ici
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire
    $bikeId = $_POST["bikeId"];
    $brand = $_POST["brand"];
    $color = $_POST["color"];
    $model = $_POST["model"];
    $gearType = $_POST["gearType"];
    $isElectric = isset($_POST["isElectric"]) ? true : false;
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : 1;
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $siret = $_POST["siret"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $storeId = $_POST["storeId"];
    $socialReasonRegisteredBy = $_POST["socialReasonRegisteredBy"];
    $sendmail = intval($_POST["sendmail"]); // Convertissez en entier

    // Initialisez un tableau pour stocker les erreurs
    $errors = [];

    // Vérification des champs obligatoires
    if (empty($bikeId)) {
        $errors[] = "Le champ 'ID du vélo' est obligatoire.";
    }

    if (empty($brand)) {
        $errors[] = "Le champ 'Marque' est obligatoire.";
    }

    if (empty($color)) {
        $errors[] = "Le champ 'Couleur' est obligatoire.";
    }

    if (empty($model)) {
        $errors[] = "Le champ 'Modèle' est obligatoire.";
    }

    if (empty($gearType)) {
        $errors[] = "Le champ 'Type de vélo' est obligatoire.";
    }

    if (empty($phone)) {
        $errors[] = "Le champ 'Téléphone' est obligatoire.";
    }

    if (empty($email)) {
        $errors[] = "Le champ 'Email' est obligatoire.";
    }

    if (empty($siret)) {
        $errors[] = "Le champ 'Siret' est obligatoire.";
    }

    if (empty($storeId)) {
        $errors[] = "Le champ 'ID du magasin' est obligatoire.";
    }

    if (empty($socialReasonRegisteredBy)) {
        $errors[] = "Le champ 'Raison sociale du magasin' est obligatoire.";
    }

    // Validation du champ 'email'
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }

    // Validation du champ 'phone' (exemple de validation)
    if (!preg_match("/^\+\d{1,14}$/", $phone)) {
        $errors[] = "Le numéro de téléphone n'est pas valide. Il doit commencer par un signe plus (+) suivi de chiffres.";
    }

    // Vérification des erreurs
    if (empty($errors)) {
        // Pas d'erreurs, continuez avec la requête vers l'API

        // Construisez le tableau de données à envoyer au serveur
        $data = array(
            "bike_id" => $bikeId,
            "bike_status" => 1,
            "bike_description" => array(
                "brand_id" => 334,
                "brand" => $brand,
                "color" => $color,
                "model" => $model,
                "gear_type_id" => 56,
                "gear_type" => $gearType,
                "is_electric" => $isElectric
            ),
            "bike_owner" => array(
                "gender" => $gender,
                "phone" => $phone,
                "mail" => $email,
                "first_name" => $firstName,
                "last_name" => $lastName
            ),
            "bike_registered_by" => array(
                "siret" => $siret,
                "store_id" => $storeId,
                "social_reason" => $socialReasonRegisteredBy
            ),
            "sendmail" => $sendmail // Maintenant, c'est un entier
        );

        // Convertissez le tableau de données en JSON
        $jsonData = json_encode($data);

        // Informations d'authentification pour l'API de production
        $username = "56160227594830113";
        $password = "OmDS3dTn7nGH9tfn8InHMhvgna/9kw7vjj4tIldXNn8=";
        $xApiKey = "k+NNheG8r2yRYq3kz0EbYNcrYpforQOVjmgMr+BzLt0=";

        // Définissez l'URL de l'API pour l'enregistrement du vélo
        $apiUrl = 'https://api.o-code.co/obikeapi/manufacturer/directselling/bike';

        // Définissez les en-têtes de la requête
        $headers = array(
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
            'x-api-key: ' . $xApiKey,
            'accept-version: ~1',
            'Content-Type: application/json'
        );

        // Initialisez cURL pour envoyer la requête POST
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactivez la vérification du certificat SSL
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Exécutez la requête cURL
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log("Erreur cURL : " . curl_error($ch));
        }

        // Fermez la connexion cURL
        curl_close($ch);

        // Affichez la réponse du serveur (peut être commenté lors de la production)
        echo "Réponse du serveur : " . $response;

        // Afficher un message de succès et rediriger après 5 secondes
        echo '<div class="header-banner animated-banner" style="background-color: green;">';
        echo '<h1 class="text-center">Enregistrement réussi !</h1>';
        echo '</div>';

        echo '<script>
            setTimeout(function() {
                window.location.href = "https://www.ultima.dev";
            }, 5000); // Rediriger après 5 secondes
        </script>';
        
    } else {
        // Il y a des erreurs, enregistrez-les dans les logs
        foreach ($errors as $error) {
            error_log("Erreur de validation : $error");
        }
    }

}


?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ultima Mobility</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

    <style>
        /* Styles pour la bannière d'en-tête */
        .header-banner {
            background-color: #333; /* Couleur de fond de la bannière */
            color: #fff; /* Couleur du texte */
            text-align: center;
            padding: 20px 0;
        }

        /* Styles pour l'animation */
        @keyframes slide-in {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(0);
            }
        }

        .animated-banner {
            animation: slide-in 1s ease;
        }

        /* Styles pour le formulaire */
        .container {
            max-width: 900px; /* Largeur maximale du formulaire */
            margin: 0 auto; /* Centrer le formulaire horizontalement */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 20px; /* Espacement entre les groupes de formulaire */
        }

        label {
            display: block; /* Mettre les libellés sur une ligne distincte */
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="checkbox"] {
            margin-right: 5px; /* Espacement entre la case à cocher et le texte */
        }

        button {
            background-color: #007bff; /* Couleur de fond du bouton */
            color: #fff; /* Couleur du texte du bouton */
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3; /* Couleur de fond au survol du bouton */
        }

    </style>
</head>

<body>
<div class="header-banner animated-banner">
        <h1 class="text-center">Enregistrez votre BicyCode®</h1>
    </div><br><br><br>
    <div class="container">
                <div class="text-center mb-4"> 
                    <img src="/mob.png" alt="ultimamob" class="img-fluid">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="/ocode1.PNG" alt="ocode" class="img-fluid">
                </div>
        <div class="row justify-content-center">

            <div class="mb-3 text-center">
                <p style="font-size: 18px; color: blue;">
                    SERVICE ULTIMA
                </p>
            </div>
            <div class="col-lg-6">

            <form action="" method="post">
            <?php
            if (!empty($errors)) {
                echo '<div class="alert alert-danger">';
                echo '<ul>';
                foreach ($errors as $error) {
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            ?>
                    <div class="form-group">
                        <label for="bikeId">N° Bicycode:</label>
                        <input type="text" class="form-control" name="bikeId" id="bikeId" placeholder="BYXXXXXX" maxlength="10" required>
                    </div>

                    <div class="form-group">
                        <label for="brand">Marque:</label>
                        <input type="text" class="form-control" name="brand" id="brand" value="ULTIMA Mobility" readonly required>
                    </div>

                    <div class="form-group">
                            <label for="color">Couleur:</label>
                        <select class="form-control" name="color" id="color" required>
                            <option value="">Veuillez cliquer pour selectionner votre couleur</option>
                            <option value="Carbone">Carbone</option>
                            <option value="Sand">Sand</option>
                            <option value="Indigo">Indigo</option>
                            <option value="Very Peri">Very Peri</option>
                            <option value="White">White</option>
                            <option value="Red Hot">Red Hot</option>
                            <option value="Vernis">Vernis</option>
                            <option value="Special">Special</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="model">Modèle:</label>
                        <select class="form-control" name="model" id="model" required>
                            <option value="">Veuillez cliquer pour selectionner votre modéle</option>
                            <option value="City">City</option>
                            <option value="Trekking">Trekking</option>
                            <option value="Cargo-U">Cargo-U</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gearType"></label>
                        <input type="hidden" class="form-control" name="gearType" id="gearType" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="isElectric"></label>
                        <input type="hidden" name="isElectric" value="1">
                    </div>
                    <div class="form-group">
                        <label for="storeId"></label>
                        <input type="hidden" class="form-control" name="storeId" id="storeId" value="ST-3811882472484" readonly required>
                    </div>
                
            </div>

            <div class="col-lg-6">
                <form action="bicycodesR.php" method="post">
                    <div class="form-group">
                        <label for="firstName">Nom du client:</label>
                        <input type="text" class="form-control" name="firstName" id="firstName" placeholder="eg: DUPONT" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Prénom du client:</label>
                        <input type="text" class="form-control" name="lastName" id="lastName" placeholder="eg: Jean" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone du client:</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="+3306XXXXXXXX" required pattern="^\+\d{1,3}\d{8,}$" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email du client:</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="example@exemple.com" required>
                    </div>

                    <div class="form-group">
                        <label for="siret"></label>
                        <input type="hidden" class="form-control" name="siret" id="siret" required value="90760519000020" readonly>
                    </div>

                    

                    <div class="form-group">
                        <label for="socialReasonRegisteredBy"></label>
                        <input type="hidden" class="form-control" name="socialReasonRegisteredBy" id="socialReasonRegisteredBy" value="SAS Ultima Mobility" readonly required>
                    </div>

                    <input type="hidden" name="sendmail" value="1"> <!-- Valeur par défaut pour sendmail -->
                    </div>

                    <div class="mb-3 text-center">
                        <p style="font-size: 12px; color: grey;">
                        Le traitement des données est utilisé uniquement à seule fin de création du Bicycode. En validant ce formulaire, je reconnais avoir pris connaissance de notre politique de confidentialité.
                        </p><br>
                    <button type="submit" class="btn btn-primary btn-lg">Envoyer ma demande</button>

        </div>
        
    </div>
                </form>
            </div>
        </div>

        
</body>
<script>
// Obtenez une référence aux éléments d'entrée
var modelInput = document.getElementById("model");
var gearTypeInput = document.getElementById("gearType");

// Ajoutez un gestionnaire d'événements pour détecter les modifications dans le champ "Modèle"
modelInput.addEventListener("input", function() {
    // Obtenez la valeur du champ "Modèle" en minuscules
    var modelValue = modelInput.value.toLowerCase();

    // Déterminez le type de vélo en fonction du modèle (en minuscules)
    switch (modelValue) {
        case "trekking":
            gearTypeInput.value = "VTC";
            break;
        case "city":
            gearTypeInput.value = "Ville";
            break;
        case "cargo-u":
            gearTypeInput.value = "Vélo cargo 2 ou 3";
            break;
        default:
            gearTypeInput.value = ""; // Effacez le champ si le modèle n'est pas reconnu
    }
});

</script>&nbsp;&nbsp;&nbsp;


<!-- Pied de page -->
<footer class="bg-dark text-light py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Logo du pied de page -->
            <div class="col-lg-2">
            <a href="https://ultima.dev/fr">
                <img src="logo_white.png" alt="logo_white" class="img-fluid">
            </a>    
            </div>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
            <!-- Liens de navigation -->
            <div class="col-lg-2 mb-3">
                <h5>Découvrez</h5>
                <ul class="list-unstyled">
                    <li>Multipath</li>
                    <li>Accessoires</li>
                    <li>Pièces détachées</li>
                </ul>
            </div>&emsp;
            <div class="col-lg-2 mb-3">
                <h5>Ultima</h5>
                <ul class="list-unstyled">
                    <li>Ultima Mobility</li>
                    <li>Guide de démarrage</li>
                    <li>Liste des revendeurs</li>
                    <li>Contact</li>
                    <li>Revue de presse</li>
                    <li>Blog</li>
                </ul>
            </div>&emsp;
            <!-- Liens de bas de page -->
            <div class="col-lg-2 mb-3">
                <h5>Besoin d'aide ?</h5>
                <ul class="list-unstyled">
                    <li>Mon compte</li>
                    <li>Enregistrez votre BicyCode</li>
                    <li>Support</li>
                    <li>Paiements</li>
                    <li>Retours</li>
                </ul>
            </div>&emsp;
            <div class="col-lg-3">
                <h5>Suivez-nous</h5>
                <ul class="list-inline social-icons">
                    <li class="list-inline-item">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

</html>
