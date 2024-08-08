<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire
    $bikeId = $_POST["bikeId"];
    $brand = $_POST["brand"];
    $color = $_POST["color"];
    $model = $_POST["model"];
    $gearType = $_POST["gearType"];
    $isElectric = isset($_POST["isElectric"]) ? true : false;
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : 2;
    $socialReasonOwner = $_POST["socialReasonOwner"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $siret = $_POST["siret"];
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
            "social_reason" => $socialReasonOwner,
            "phone" => $phone,
            "mail" => $email
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
} else {
    // Il y a des erreurs, enregistrez-les dans les logs
    foreach ($errors as $error) {
        error_log("Erreur de validation : $error");
    }
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ultima Mobility - Enregistrer un vélo pour une entreprise</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <hr>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="home.php" class="btn btn-info">Multipath</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="expedition.php" class="btn btn-info">Expedition</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="sav.php" class="btn btn-info">SAV</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="export.php" class="btn btn-info">Velco - O'code</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="statistique.php" class="btn btn-info">State</a>
                            </li>
                        </ul>
                        <form class="form-inline my-2 my-lg-0" action="search.php" method="post">
                            <input class="form-control mr-sm-2" type="search" placeholder="Search" name="search"
                                   aria-label="Search">
                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                        </form>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
        <div class="form-group">
        <h2>Enregistrer un vélo</h2>
        </div>
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
        <label for="bikeId">ID du vélo:</label>
        <input type="text"class="form-control" name="bikeId" id="bikeId" required><br>
        </div>

        <div class="form-group">
        <label for="brand">Marque:</label>
        <input type="text" class="form-control" name="brand" id="brand" value ="ULTIMA Mobility" required><br>
        </div>

        <div class="form-group">
        <label for="color">Couleur:</label>
        <input type="text" class="form-control" name="color" id="color" required><br>
        </div>

        <div class="form-group">
        <label for="model">Modèle:</label>
        <input type="text" class="form-control" name="model" id="model" required><br>
        </div>

        <div class="form-group">
        <label for="gearType">Type de velo:</label>
        <input type="text" class="form-control" name="gearType" id="gearType" required><br>
        </div>

        <div class="form-group">
        <label for="isElectric">Électrique:</label>
        <input type="checkbox"  name="isElectric" id="isElectric" value ="yes"><br>
        </div>
        
        <div class="form-group">
        <label for="socialReasonOwner">Raison sociale du propriétaire:</label>
        <input type="text" class="form-control" name="socialReasonOwner" id="socialReasonOwner" required><br>
        </div>

        <div class="form-group">
        <label for="phone">Téléphone:</label>
        <input type="text" class="form-control" name="phone" id="phone" required><br>
        </div>

        <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" name="email" id="email" required><br>
        </div>

        <div class="form-group">
        <label for="siret">Siret:</label>
        <input type="text" class="form-control" name="siret" id="siret"  value="90760519000020" required><br>
        </div>


        <div class="form-group">
        <label for="storeId">ID du magasin:</label>
        <input type="text" class="form-control" name="storeId" id="storeId" value ="ST-3811882472484" required><br>
        </div>

        <div class="form-group">
        <label for="socialReasonRegisteredBy">Raison sociale du magasin:</label>
        <input type="text" class="form-control" name="socialReasonRegisteredBy" id="socialReasonRegisteredBy" value ="SAS Ultima Mobility" required><br>
        </div>
        
        <input type="hidden" name="sendmail" value="1"> <!-- Valeur par défaut pour sendmail -->
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="ocode_api.php" class="btn btn-primary mr-2">Retour</a>
    </form>
</body>
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
</script>
</html>
