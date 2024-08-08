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
        @media print {
            .no-print {
                display: none;
            }
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
                <div class="no-print">
                
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
                            &nbsp;&nbsp;&nbsp; 
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
        </div>

        <div class="container mt-5">
            <h3>Fiche du ticket SAV</h3>
            <?php
            // Vérifier si le paramètre ticket_id est passé dans l'URL
            if (isset($_GET['ticket_id'])) {
                // Récupérer les données du ticket spécifique
                $stmt = $bdd->prepare('SELECT * FROM sav WHERE numero_ticket = ?');
                $stmt->execute([$_GET['ticket_id']]);
                $ticket = $stmt->fetch();

                if ($ticket) {
                    $formattedTicketNumber = str_pad($ticket['numero_ticket'], 5, '0', STR_PAD_LEFT);

                    echo "<div class='card mb-3'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>Numéro de ticket : " . $formattedTicketNumber . "</h5>";
                    echo "<p class='card-text'>Bicycode : " . $ticket['bicycode'] . "</p>";
                    echo "<p class='card-text'>Nom du client : " . $ticket['nom_client'] . "</p>";
                    echo "<p class='card-text'>Date de prise en charge : " . $ticket['date_prise_en_charge'] . "</p>";
                    echo "<p class='card-text'>Date d'intervention : " . $ticket['date_intervention'] . "</p>";
                    
                    // Vérifier si des tâches à réaliser sont disponibles
                    $stmt = $bdd->prepare('SELECT * FROM taches_a_realiser WHERE sav_id = ?');
                    $stmt->execute([$ticket['numero_ticket']]);
                    $taches_a_realiser = $stmt->fetchAll();
                    
                    if (!empty($taches_a_realiser)) {
                        echo "<p class='card-text'>Tâches à réaliser :</p>";
                        echo "<ul>";
                        foreach ($taches_a_realiser as $tache) {
                            echo "<li>" . $tache['tache'] . "</li>";
                        }
                        echo "</ul>";
                    }
                    
                    // Vérifier si des tâches réalisées sont disponibles
                    $stmt = $bdd->prepare('SELECT * FROM taches_realisees WHERE sav_id = ?');
                    $stmt->execute([$ticket['numero_ticket']]);
                    $taches_realisees = $stmt->fetchAll();
                    
                    if (!empty($taches_realisees)) {
                        echo "<p class='card-text'>Tâches réalisées :</p>";
                        echo "<ul>";
                        foreach ($taches_realisees as $tache) {
                            echo "<li>" . $tache['tache'] . "</li>";
                        }
                        echo "</ul>";
                    }
                    
                    echo "<p class='card-text'>Numéro de téléphone : " . (!empty($ticket['numero_telephone']) ? $ticket['numero_telephone'] : '') . "</p>";
                    echo "<p class='card-text'>E-mail : " . (!empty($ticket['email']) ? $ticket['email'] : '') . "</p>";

                    echo "<p class='card-text'>Intervenant : " . $ticket['intervenant'] . "</p>";
                    echo "<p class='card-text'>Commentaire : " . $ticket['commentaire'] . "</p>";
                    echo "<p class='card-text'>Date de clôture du ticket : " . $ticket['date_cloture_ticket'] . "</p>";
                    echo "<p class='card-text'>Garantie ou facturation : " . $ticket['garantie_ou_facturation'] . "</p>";
                    echo "<p class='card-text'>Status : " . $ticket['status'] . "</p>";

                    // Vérifier si le statut du ticket est "Terminé"
                    if ($ticket['status'] !== 'Terminé') {
                        // Afficher le bouton "Modifier" seulement si le statut n'est pas "Terminé"
                        echo "<div class='no-print'>";
                        echo "<a href='modifier_sav.php?numero_ticket=" . $ticket['numero_ticket'] . "' class='btn btn-primary mr-2'>Modifier</a>";
                    }

                    // Bouton d'impression (masqué lors de l'impression)
                    echo "<button class='btn btn-primary' onclick='window.print()'>Imprimer</button>";

                    // Bouton de retour (masqué lors de l'impression)
                    echo "<a href='gerer_ticket.php' class='btn btn-primary ml-2'>Retour</a>";

                    // Fermer la div si nécessaire
                    if ($ticket['status'] !== 'Terminé') {
                        echo "</div>";
                    }

                    echo "</div>";
                    echo "</div>";

                } else {
                    echo "<p>Aucun ticket trouvé.</p>";
                }
            }
            ?>
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
</body>

</html>
