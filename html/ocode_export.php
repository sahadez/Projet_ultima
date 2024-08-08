<?php
// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

// Utilisez les informations d'authentification fournies
$username = "991038522917347";
$password = "BiEST23jXrWzZ2Zb/YUz5PvnEaiKXo86fULsmkwv+OQ=";
$xApiKey = "Yp59V96DZBw2xNRh4s7zHLoJVA5ZmGrNTx/1BFCoZEU=";

// Définissez les en-têtes (headers)
$headers = array(
    'Authorization: Basic ' . base64_encode($username . ':' . $password),
    'x-api-key: ' . $xApiKey,
    'accept-version: ~1',
    'Content-Type: application/json',
    'Accept-Encoding: gzip'
);

// Déclarer une variable pour stocker les informations des vélos
$bikeInfo = array();


            // Définir la fonction is_json()
            function is_json($string) {
                json_decode($string);
                return (json_last_error() == JSON_ERROR_NONE);
            }

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les bike_ids à partir de $_POST
    $bikeIds = isset($_POST['bike_id']) ? $_POST['bike_id'] : "";

    // Si des bike_ids ont été soumis, divisez-les en un tableau
    $bikeIdArray = array_filter(array_map('trim', explode(',', $bikeIds)));

    // Boucle à travers les bike_ids pour récupérer les informations de chaque vélo
    foreach ($bikeIdArray as $bikeId) {
        $apiUrl = 'https://dev.ocode.team/obikeapi/manufacturer/directselling/bike?bike_id=' . $bikeId;

        // Effectuer la requête API pour chaque bike_id
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactive la vérification du certificat SSL

        $response = curl_exec($ch);

        // Traiter la réponse
        if ($response) {
            $decodedResponse = gzdecode($response);


            // Vérifier si la réponse est au format JSON
            if (is_json($decodedResponse)) {
                $jsonData = json_decode($decodedResponse, true);

                // Vérifier s'il y a eu une erreur de décodage JSON
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Les données ont été récupérées avec succès
                    // Utilisez les données contenues dans $jsonData

                    $bikeInfo[] = $jsonData;
                } else {
                    // Erreur de décodage JSON
                    echo "Erreur de décodage JSON de la réponse de l'API : " . json_last_error_msg();
                }
            } else {
                // La réponse n'est pas au format JSON
                echo "Erreur : la réponse de l'API n'est pas au format JSON.";
            }
        } else {
            // Affichez les erreurs cURL pour le dépannage
            echo "Erreur lors de l'appel à l'API : " . curl_error($ch);
        }

        // Fermer la session cURL
        curl_close($ch);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Ultima Mobility</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">

    <!-- Style CSS personnalisé -->
    <style>
        body {
            background-color: #f4f4f4;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="col-md-12">
        <?php
        if (isset($_GET['err'])) {
            $err = htmlspecialchars($_GET['err']);
            switch ($err) {
                case 'current_password':
                    echo "<div class='alert alert-danger'>Le mot de passe actuel est incorrect</div>";
                    break;

                case 'success_password':
                    echo "<div class='alert alert-success'>Le mot de passe a bien été modifié ! </div>";
                    break;
            }
        }
        ?>

        <div class="text-center">
            <h1 class="p-5">Bonjour <?php echo isset($data['pseudo']) ? $data['pseudo'] : ''; ?> !</h1>
            <hr/>

            <!-- Affichage du formulaire de recherche -->
            <form action="" method="POST">
                <label for="bike_id">Entrez les bike_ids (séparés par des virgules) :</label>
                <input type="text" name="bike_id" id="bike_id" required>
                <button type="submit">Rechercher</button>
            </form>

            <!-- Affichage des informations des vélos -->
            <?php
            foreach ($bikeInfo as $bike) {
                echo '<h2>Informations du vélo (ID: ' . $bike['bike_id'] . ')</h2>';
                echo '<ul>';

                // Afficher les informations du vélo
                // Par exemple : echo '<li>Modèle : ' . $bike['model'] . '</li>';

                echo '</ul>';
            }
            ?>
        </div>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH4Y1f0B4yQf2JC0m"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQClgq6zC2tFetiK3xNO6Hbq9er6g0JZnMFkkRkkECwR3Wcwjgx"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>
