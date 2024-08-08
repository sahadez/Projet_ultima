<?php
session_start();
require_once 'config.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numDev = htmlspecialchars($_POST['numDev']);
    $typevelo = htmlspecialchars($_POST['typevelo']);
    $numMDU = htmlspecialchars($_POST['numMDU']);
    $numHMI = htmlspecialchars($_POST['numHMI']);
    $numBatterie = htmlspecialchars($_POST['numBatterie']);
    $puk = htmlspecialchars($_POST['puk']);
    $gps = htmlspecialchars($_POST['gps']);
    $commentaire = htmlspecialchars($_POST['commentaire']);

    // Prépare et exécute l'insertion
    $req = $bdd->prepare('INSERT INTO dev (numDev, typevelo, numMDU, numHMI, numBatterie, puk, gps, Commentaire) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $req->execute(array($numDev, $typevelo, $numMDU, $numHMI, $numBatterie, $puk, $gps, $commentaire));

    // Redirige après l'insertion
    header('Location: home.php');
    exit();
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
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
        }

        #scrollToTop,
        #scrollToBottom {
            display: none;
            position: fixed;
            bottom: 20px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 50%;
            transition: background 0.3s;
        }

        #scrollToTop:hover,
        #scrollToBottom:hover {
            background: #0069d9;
        }

        /* Positionnement du bouton "Bottom" à gauche */
        #scrollToBottom {
            left: 80px;
        }

        .sous {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .nav-item:hover .sous {
            display: block;
        }

        .sous li {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .sous li:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="col-md-12">

            <div class="text-center">
                <hr />
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="#" class="btn btn-info">Bike</a>
                                <ul class="sous">
                                    <li><a href="home.php" class="btn btn-info">Multipath/Larrum</a></li>
                                    
                                    <li><a href="boheme.php" class="btn btn-info">Boheme</a></li>
                                    <li><a href="gravel.php" class="btn btn-info">Gravel</a></li>
                                    <li><a href="dev.php" class="btn btn-info">DEV</a></li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="#" class="btn btn-info">+</a>
                                <ul class="sous">
                                    <li><a href="register.php" class="btn btn-info">Multipath</a></li>
                                    <li><a href="larrum+.php" class="btn btn-info">Larrum</a></li>
                                    <li><a href="boheme+.php" class="btn btn-info">Boheme</a></li>
                                    <li><a href="gravel+.php" class="btn btn-info">Gravel</a></li>
                                    <li><a href="dev+.php" class="btn btn-info">DEV</a></li>
                                </ul>
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
<body>
    <div class="container mt-5">
        <h1>Environnement DEV</h1>
        <form method="post" id="myForm">
            <div class="form-group">
                <label for="numDev">Numero</label>
                <input type="text" class="form-control" id="numDev" name="numDev" required>
            </div>
            <div class="form-group">
                <label for="typevelo">Type</label>
                <input type="text" class="form-control" id="typevelo" name="typevelo" required>
            </div>
            <div class="form-group">
                <label for="numMDU">N°MDU</label>
                <input type="text" class="form-control" id="numMDU" name="numMDU">
            </div>
            <div class="form-group">
                <label for="numHMI">N°HMI</label>
                <input type="text" class="form-control" id="numHMI" name="numHMI">
            </div>
            <div class="form-group">
                <label for="numBatterie">N°Batterie</label>
                <input type="text" class="form-control" id="numBatterie" name="numBatterie">
            </div>
            <div class="form-group">
                <label for="puk">PUK</label>
                <input type="text" class="form-control" id="puk" name="puk">
            </div>
            <div class="form-group">
                <label for="gps">GPS</label>
                <input type="text" class="form-control" id="gps" name="gps">
            </div>
            <div class="form-group">
                <label for="commentaire">Commentaire</label>
                <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
</body>

</html>
