<?php
    session_start();
    require_once 'config.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        die();
    }

    // Récupérer les données de l'utilisateur
$req = $bdd->prepare('SELECT email FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$userData = $req->fetch();

    // adresse e-mail autorisées
$emailsAutorises = array('sahade.zongo@ultima.dev');

// Vérifier si l'e-mail de l'utilisateur est autorisé
if (!in_array($userData['email'], $emailsAutorises)) {
    echo "<div class='alert alert-danger'>Accès restreint : Cette page est réservée à certaines adresses e-mail autorisées</div>";
    // Vous pouvez également rediriger l'utilisateur vers une autre page après avoir affiché le message
     header('Location: home.php');
    die();
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
            
            <hr/>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="collapse navbar-collapse" id="navbarNav">

                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a href="home.php" class="btn btn-info">Multipath</a>
                        </li>
                    </ul>
                    <a href="register.php" class="btn btn-info">+</a>&emsp;&emsp;
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

        <div class="container mt-5">
            <!-- Votre contenu ici -->
            <div class="text-center">
                <h1>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</h1>

        <div class="container text-center mt-5">
            <a href="ocode_get.php" class="btn btn-primary">Vélos déclarés</a>
            
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
            <a href="ocode_postentreprise.php" class="btn btn-primary">Déclaration Entreprise</a>
            &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
            <a href="ocode_postparticulier.php" class="btn btn-primary">Déclaration Particulier</a>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>


</body>
</html>
