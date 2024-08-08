<?php 

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
    session_start();
    require_once 'config.php'; // ajout connexion bdd 
   // si la session existe pas soit si l'on est pas connecté on redirige
    if(!isset($_SESSION['user'])){
        header('Location:index.php');
        die();
    }

    // On récupere les données de l'utilisateurs
    $req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
    $req->execute(array($_SESSION['user']));
    $data = $req->fetch();
   
?>
<!doctype html>
<html lang="en">
<head>
    <title>Ultima Mobility</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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

        #scrollToTop {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99;
            font-size: 18px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 50%;
        }

        #scrollToTop:hover {
            background: #0069d9;
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

            <hr/>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="collapse navbar-collapse" id="navbarNav">

                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a href="home.php" class="btn btn-info">Multipath</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">

                            <a href="register.php" class="btn btn-info">+</a>
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

    <div class="container mt-5 content">
        <!-- Your content here -->

        <?php
        // Récupère les données de tous les enregistrements dans la table multipath
        $req_all_records = $bdd->prepare('SELECT bicycode, nom_client,prenom_client, numero_commande,gravage,montage, commentaire, Totem_PLV, Podium_PLV,Catalogues FROM multipath');
        $req_all_records->execute();

        echo "<table class='table'>";
        echo "<thead><tr><th>Bicycode</th><th>Nom</th><th>Commande</th><th>Gravage</th><th>Montage</th><th>SAV</th><th>T PLV</th><th>P PLV</th><th>Catalogues</th><th>Commentaire</th></tr></thead>";
        echo "<tbody>";

        // Parcourt les résultats et affiche les informations de chaque enregistrement
        while ($data_all_records = $req_all_records->fetch()) {
            $bicycode = $data_all_records['bicycode'];
            $req_sav = $bdd->prepare('SELECT status FROM sav WHERE bicycode = ?');
            $req_sav->execute([$bicycode]);
            $sav_status = $req_sav->fetchColumn();

            echo "<tr>";
            echo "<td><a href='fiche_velo.php?bicycode=" . $bicycode . "'>" . $bicycode . "</a></td>";
            echo "<td>" . $data_all_records['nom_client'] . "</td>";

            echo "<td>" . $data_all_records['numero_commande'] . "</td>";
            echo "<td>" . $data_all_records['gravage'] . "</td>";
            echo "<td>" . $data_all_records['montage'] . "</td>";
            
            echo "<td>" . ($sav_status ? $sav_status : "No SAV") . "</td>";
            echo "<td>" . $data_all_records['Totem_PLV'] . "</td>";  
            echo "<td>" . $data_all_records['Podium_PLV'] . "</td>"; 
            echo "<td>" . $data_all_records['Catalogues'] . "</td>";  
            echo "<td>" . $data_all_records['commentaire'] . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        ?>

    </div>

    <div class="footer text-center">
        <button id="scrollToTop" onclick="scrollToTop()" title="Go to top">Top</button>
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

<script>
    window.onscroll = function () {
        scrollFunction();
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("scrollToTop").style.display = "block";
        } else {
            document.getElementById("scrollToTop").style.display = "none";
        }
    }

    function scrollToTop() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>
</body>
</html>
