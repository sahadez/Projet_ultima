<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location:index.php');
    die();
}

$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$data = $req->fetch();

// Requête pour récupérer les bicycodes avec un statut de "Montage terminé"
$req_bicycodes = $bdd->query('SELECT * FROM multipath WHERE montage = "Termine" AND (action_expedition IS NULL OR action_expedition != "oui")');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bicycode']) && isset($_POST['action_expedition']) && isset($_POST['date_expedition'])) {
        $bicycode = $_POST['bicycode'];
        $action_expedition = $_POST['action_expedition'];
        $date_expedition = $_POST['date_expedition'];

        // Mettre à jour les valeurs dans la table "multipath"
        $update_query = $bdd->prepare('UPDATE multipath SET action_expedition = ?, date_expedition = ? WHERE bicycode = ?');
        $update_query->execute([$action_expedition, $date_expedition, $bicycode]);

        // Rediriger vers la page "expedition.php" après la mise à jour
        header('Location: expedition.php');
        exit();
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

    <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['flash']; ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

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
                        </li>
                        </ul>
                        &ensp;&ensp;&ensp;&ensp;
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

                <div class="container mt-5">
    <h3>Liste des vélos à expédier</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Bicycode</th>
                <th>Nom du client</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $req_bicycodes->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['bicycode']; ?></td>
                    <td>
                        <?php
                            // Récupérer le nom du client à partir du bicycode
                            $req_client = $bdd->prepare('SELECT nom_client FROM multipath WHERE bicycode = ?');
                            $req_client->execute([$row['bicycode']]);
                            $client_info = $req_client->fetch(PDO::FETCH_ASSOC);
                            echo $client_info['nom_client'];
                        ?>
                    </td>
                    <td>
                        <?php if ($row['action_expedition'] !== 'oui'): ?>
                            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modifierModal-<?php echo $row['bicycode']; ?>">Modifier</a>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modal de modification -->
                <div class="modal fade" id="modifierModal-<?php echo $row['bicycode']; ?>" tabindex="-1" role="dialog" aria-labelledby="modifierModalLabel-<?php echo $row['bicycode']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modifierModalLabel-<?php echo $row['bicycode']; ?>">Modifier l'expédition</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="bicycode" value="<?php echo $row['bicycode']; ?>">
                                    <div class="form-group">
                                        <label for="action_expedition">Action d'expédition</label>
                                        <select class="form-control" name="action_expedition">
                                            <option value="">Sélectionner une action</option>
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_expedition">Date d'expédition</label>
                                        <input type="date" class="form-control" name="date_expedition" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="container mt-5">
    <h3>Liste des vélos expédiés</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Bicycode</th>
                <th>Nomdu client</th>
                <th>Modèle</th>
                <th>Date d'expédition</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Requête pour récupérer les vélos avec l'action d'expédition "oui" dans la table "multipath"
            $req_expedies = $bdd->query('SELECT bicycode, nom_client, modele, date_expedition FROM multipath WHERE action_expedition = "oui"');

            while ($row = $req_expedies->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['bicycode']; ?></td>
                    <td><?php echo $row['nom_client']; ?></td>
                    <td><?php echo $row['modele']; ?></td>
                    <td><?php echo $row['date_expedition']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

                <div class="footer text-center">
                <button id="scrollToBottom" onclick="scrollToBottom()" title="Scroll To Bottom">Bas</button>
                <button id="scrollToTop" onclick="scrollToTop()" title="Scroll To Top">Haut</button>
                 </div>

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

            </body>

            </html>



            