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

// Informations d'authentification pour l'API de production
$username = "56160227594830113";
$password = "OmDS3dTn7nGH9tfn8InHMhvgna/9kw7vjj4tIldXNn8=";
$xApiKey = "k+NNheG8r2yRYq3kz0EbYNcrYpforQOVjmgMr+BzLt0=";

// Liste des bike_ids que vous souhaitez récupérer
$bikeIds = array(
    "BY4F4HAH57", "BYH9CNT8B2", "BYZE6ZJ7BF", "BY4E3DS6Z5", "BY34ZHV6H5",
    "BY577VJ472", "BYD9ZAVZCD", "BY6DDHS3D9", "BY5Z3ESDF4", "BYH54HDD6B",
    "BY957FX3FB", "BYB2CKPEA7", "BY6ECVCHA4", "BY65BSNEAF", "BYBAAASFHD",
    "BYB6ZHCED2", "BYCHBAHF3C", "BYDEBSJ898", "BYH5CDDA86", "BYBCBCB5FB",
    "BY5EAHVFFA", "BYDB2PA54E", "BY9HHFV35F", "BYB7ADZH9C", "BYDFHJB853",
    "BYCFZCXDAH", "BY59HAV9A3", "BYZAEDT98B", "BYD7EXS284", "BYD82XAZAE",
    "BY87ECNCFE", "BYB8AXVD92", "BYDEEDHED4", "BYCZDZBC7H", "BY3F9ZD5H5",
    "BY2ECTAEF6", "BYZ2CVVH6H", "BY835NSAC5", "BY2DCFP549", "BYBD9BNCC9",
    "BYAZBHJ4EA", "BYH4HED955", "BY9CZXDA68", "BY894XS39Z", "BY8CBZHC5D",
    "BYDFABDH7Z", "BY89AKEBZE", "BY9D4FD93C", "BYH9HSK7H2", "BY58ETJZ36",
    "BYE9CZSHCE", "BY9C7DK4HZ", "BYH9CNT8B2", "BYZ8DAV93E", "BYH2EDD569",
    "BYH67XEADA", "BYZ49DXHDD", "BYH54HDD6B", "BY6DDHS3D9", "BYD9ZAVZCD",
    "BY577VJ472", "BY34ZHV6H5", "BY4E3DS6Z5", "BY957FX3FB", "BYZE6ZJ7BF",
    "BY3ZFXD867", "BYA94HPZ8C", "BYHB3DAF5A", "BYFBAAB2D8", "BY825VSAHC",
    "BYD5FSS4CZ", "BYZZBTX76Z", "BYZFHHXED3", "BYZF8KA8B2", "BYFFCDHH58",
    "BYH3DED33D", "BYFHEFH6F9", "BYFDZKD76H", "BYFCEFJF8F", "BYFBDPXD62",
    "BYA3FHHH34", "BY9ZFETD6D", "BY8Z4FHF34", "BY72CFH74F", "BY59DAPZ92",
    "BYH54SV428", "BYZ49KE4E6", "BYHACNVD8F", "BY638NJE57", "BY4F5BC35A",
    "BY3FCVTZFH", "BY36BPF7F5", "BY2E2NP3FZ", "BY2C4HDZZ7", "BY67APAZ27",
    "BY4D8ZB652", "BY6EADZB92", "BY699VN229", "BY62ZVF5A4", "BY524NX832",
    "BY5DCCX935", "BY5A9KH352", "BY4CFAD854", "BY569FE38D", "BY47AHC67D",
    "BY45CSV9C6", "BY398XP467", "BY394HA66C", "BY98CXEFF6", "BY4HHSN77A",
    "BY3E2EXBC7", "BY35FSBF77", "BY345HD2CC", "BY2F2XPZ3F", "BY278AXDHC",
    "BY2B6SX73H", "BY263DADZ8", "BYC48NE3D5", "BYZ5HNT5H3", "BY2C4SN335",
    "BYAC4TC48F", "BY6HEHN5D5", "BY23EPZ836", "BYD48BC76F", "BY4ABVZDB4",
    "BYZEHEJDZD", "BY3BZHV624", "BY6A7FSH6C", "BY5BFJF2B2", "BY99HJXZFZ"
);

// Tableau pour stocker toutes les données des vélos
$allBikeData = array();

// Tableau pour stocker les en-têtes cURL
$headers = array(
    'Authorization: Basic ' . base64_encode($username . ':' . $password),
    'x-api-key: ' . $xApiKey,
    'accept-version: ~1',
    'Content-Type: application/json',
    'Accept-Encoding: gzip'
);

foreach ($bikeIds as $bikeId) {
    $apiUrl = 'https://api.o-code.co/obikeapi/manufacturer/directselling/bike?bike_id=' . $bikeId;

    // Effectuer la requête API pour chaque vélo
    $ch = curl_init($apiUrl);

    // Configurer les en-têtes cURL avec les en-têtes définis dans $headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactive la vérification du certificat SSL

    $response = curl_exec($ch);

    // Vérifier si la réponse est au format JSON
    if ($response) {
        $decodedResponse = gzdecode($response);

        // Utilisez json_decode pour vérifier si la réponse est au format JSON
        $jsonData = json_decode($decodedResponse, true);

        if ($jsonData !== null) {
            // La réponse est au format JSON et a été décodée avec succès
            $allBikeData[$bikeId] = $jsonData;
        } else {
            // La réponse n'est pas au format JSON
            echo "Erreur : la réponse de l'API pour le vélo $bikeId n'est pas au format JSON.";
        }
    } else {
        // Affichez les erreurs cURL pour le dépannage
        echo "Erreur lors de l'appel à l'API pour le vélo $bikeId : " . curl_error($ch);
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ultima Mobility - Liste des vélos enregistrés</title>
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
                        <!-- Ajoutez ici votre menu de navigation -->
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
                                <a href="sav.php" class ="btn btn-info">SAV</a>
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
            <h2>Liste des vélos enregistrés</h2>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID de vélo</th>
                    <th>Marque</th>
                    <th>Couleur</th>
                    <th>Modèle</th>
                    <th>Type de vélo</th>
                    <th>Électrique</th>
                    <th>Status</th>
                    <th>Email</th>
                    <th>Enregistré par</th>
                    <th>Date de création</th>
                    <th>Date de modification</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Insérez ici votre code PHP pour récupérer et afficher les données des vélos enregistrés
                foreach ($allBikeData as $bikeId => $bikeInfo) {
                    echo "<tr>";
                    echo "<td>$bikeId</td>";
                    echo "<td>" . (isset($bikeInfo['bike_description']['brand']) ? $bikeInfo['bike_description']['brand'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_description']['color']) ? $bikeInfo['bike_description']['color'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_description']['model']) ? $bikeInfo['bike_description']['model'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_description']['gear_type']) ? $bikeInfo['bike_description']['gear_type'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_description']['is_electric']) ? ($bikeInfo['bike_description']['is_electric'] ? 'Oui' : 'Non') : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_status']) ? $bikeInfo['bike_status'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_owner']['mail']) ? $bikeInfo['bike_owner']['mail'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['bike_registered_by']['social_reason']) ? $bikeInfo['bike_registered_by']['social_reason'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['create_date']) ? $bikeInfo['create_date'] : '') . "</td>";
                    echo "<td>" . (isset($bikeInfo['modify_date']) ? $bikeInfo['modify_date'] : '') . "</td>";
                    echo "</tr>";
                }
                
                ?>
            </tbody>
        </table>
        <a href="ocode_api.php" class="btn btn-primary mr-2">Retour</a>
    </div>
</body>
</html>
