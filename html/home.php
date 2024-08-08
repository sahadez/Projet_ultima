<?php
// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php'; // ajout connexion bdd

// si la session n'existe pas, soit si l'on n'est pas connecté, on redirige
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

// On récupère les données de l'utilisateur
$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$data = $req->fetch();

// Gestion du filtre de statut de montage avec des sessions
if (isset($_GET['montageStatus'])) {
    $montageStatusFilter = $_GET['montageStatus'];
    $_SESSION['montageStatusFilter'] = $montageStatusFilter;
} elseif (isset($_SESSION['montageStatusFilter'])) {
    $montageStatusFilter = $_SESSION['montageStatusFilter'];
} else {
    $montageStatusFilter = ''; // Valeur par défaut
}

// Gestion du filtre par nom_client
if (isset($_GET['nomClient'])) {
    $nomClientFilter = $_GET['nomClient'];
    $_SESSION['nomClientFilter'] = $nomClientFilter;
} elseif (isset($_SESSION['nomClientFilter'])) {
    $nomClientFilter = $_SESSION['nomClientFilter'];
} else {
    $nomClientFilter = ''; // Valeur par défaut
}

// Requête pour compter le nombre total de bicycodes dans la table "multipath"
$req_count_records = $bdd->prepare('SELECT COUNT(*) FROM multipath' .
    ($montageStatusFilter || $nomClientFilter ? ' WHERE' : '') .
    ($montageStatusFilter ? ' montage = ?' : '') .
    ($montageStatusFilter && $nomClientFilter ? ' AND' : '') .
    ($nomClientFilter ? ' nom_client = ?' : ''));
$req_count_records_params = [];
if ($montageStatusFilter) {
    $req_count_records_params[] = $montageStatusFilter;
}
if ($nomClientFilter) {
    $req_count_records_params[] = $nomClientFilter;
}
$req_count_records->execute($req_count_records_params);
$totalRecords = $req_count_records->fetchColumn();
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
            left: 80px; /* Ajustez la position selon vos préférences */
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

                <hr />
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="collapse navbar-collapse" id="navbarNav">

                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                    <a href="#" class="btn btn-info">Bike</a>
                        <ul class="sous">
                    <a href="home.php" class="btn btn-info">Multipath/Larrum</a>
                    <li><a href="boheme.php" class="btn btn-info">Boheme</a></li>
                    <li><a href="gravel.php" class="btn btn-info">Gravel</a></li>
                    <li><a href="dev.php" class="btn btn-info">Dev</a></li>
                    </ul>
                    </li>
                    </ul>
                        <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                    <a href="#" class="btn btn-info">+</a>
                        <ul class="sous">
                    <a href="register.php" class="btn btn-info">Multipath</a>
                    <li><a href="larrum+.php" class="btn btn-info">Larrum</a></li>
                    <li><a href="boheme+.php" class="btn btn-info">Boheme</a></li>
                    <li><a href="gravel+.php" class="btn btn-info">Gravel</a></li>
                    <li><a href="dev+.php" class="btn btn-info">Dev</a></li>
                    </ul>
                    </li>
                    </ul>
<style>
    .sous {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

/* Affichage de la liste déroulante lorsqu'on survole le bouton '+' */
.nav-item:hover .sous {
    display: block;
}

/* Style des éléments de la liste déroulante */
.sous li {
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

/* Changement de couleur au survol des éléments de la liste déroulante */
.sous li:hover {
    background-color: #ddd;
}
</style>

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
            <!-- Formulaire de filtrage avec valeur pré-remplie depuis la session -->
            <form method="GET" action="home.php">
                <label for="montageStatus">Statut de montage:</label>
                <select name="montageStatus" id="montageStatus">
                    <option value="">Tous</option>
                    <option value="Termine" <?php if ($montageStatusFilter === 'Termine') echo 'selected'; ?>>Terminé
                    </option>
                    <option value="EN COURS" <?php if ($montageStatusFilter === 'EN COURS') echo 'selected'; ?>>En
                        cours</option>
                </select>

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="nomClient">Nom du client:</label>
                <input type="text" name="nomClient" value="<?= htmlspecialchars($nomClientFilter) ?>">

                <button type="submit" class="btn btn-primary">Filtrer</button>
            </form>
            <div class="float-right">
                Nombre total de bicycodes : <span id="totalRecordsCounter">0</span>
            </div>
            <?php
            // Récupère les données de tous les enregistrements dans la table multipath
            // Récupérez la valeur du filtre (si soumis)
            $montageStatusFilter = isset($_GET['montageStatus']) ? $_GET['montageStatus'] : '';

            // Modifiez votre requête SQL en conséquence
            $req_all_records = $bdd->prepare('SELECT bicycode, nom_client, prenom_client, numero_commande, gravage, montage, commentaire, Totem_PLV, Podium_PLV, Catalogues FROM multipath' .
           ($montageStatusFilter || $nomClientFilter ? ' WHERE' : '') .
           ($montageStatusFilter ? ' montage = ?' : '') .
           ($montageStatusFilter && $nomClientFilter ? ' AND' : '') .
           ($nomClientFilter ? ' nom_client = ?' : ''));
            $req_all_records_params = [];
            if ($montageStatusFilter) {
                $req_all_records_params[] = $montageStatusFilter;
                                        }
            if ($nomClientFilter) {
                $req_all_records_params[] = $nomClientFilter;
                                    }
                $req_all_records->execute($req_all_records_params);

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
                <button id="scrollToBottom" onclick="scrollToBottom()" title="Scroll To Bottom">Bas</button>
                <button id="scrollToTop" onclick="scrollToTop()" title="Scroll To Top">Haut</button>
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
            document.getElementById("scrollToBottom").style.display = "block";
        } else {
            document.getElementById("scrollToTop").style.display = "none";
            document.getElementById("scrollToBottom").style.display = "none";
        }
    }

    function scrollToTop() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    function scrollToBottom() {
        // Ajustez la position souhaitée vers le bas de la page
        window.scrollTo(0, document.documentElement.scrollHeight);
    }
</script>

    <script>
        // Fonction pour animer le compteur progressif
        function animateCounter(targetValue, duration) {
            const totalRecordsCounter = document.getElementById('totalRecordsCounter');
            const initialValue = parseInt(totalRecordsCounter.textContent);
            const increment = Math.ceil(targetValue / (duration / 50));

            function updateCounter() {
                const currentValue = parseInt(totalRecordsCounter.textContent);
                if (currentValue < targetValue) {
                    totalRecordsCounter.textContent = Math.min(currentValue + increment, targetValue);
                    setTimeout(updateCounter, 50);
                }
            }

            updateCounter();
        }

        // Appel de la fonction pour commencer l'animation
        animateCounter(<?php echo $totalRecords; ?>, 1000); // Vous pouvez ajuster la durée en millisecondes (1000 = 1 seconde)
    </script>

<footer class="bg-dark text-light py-4">
                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

</body>

</html>
