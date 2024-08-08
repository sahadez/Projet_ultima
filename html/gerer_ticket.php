<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location:index.php');
    die();
}

// Variables pour stocker les valeurs actuelles des filtres
if (isset($_GET['status'])) {
    $_SESSION['currentStatus'] = $_GET['status'];
} elseif (!isset($_SESSION['currentStatus'])) {
    $_SESSION['currentStatus'] = '';
}

if (isset($_GET['date'])) {
    $_SESSION['currentDate'] = $_GET['date'];
} elseif (!isset($_SESSION['currentDate'])) {
    $_SESSION['currentDate'] = '';
}

if (isset($_GET['tri'])) {
    $_SESSION['currentTri'] = $_GET['tri'];
} elseif (!isset($_SESSION['currentTri'])) {
    $_SESSION['currentTri'] = '';
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
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
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
        </div>
    </div>

    <div class="container mt-5">
        <h3>Liste des tickets SAV</h3>
        <form class="mb-3 form-inline" method="get" action="">
            <div class="form-group" style="max-width: 200px; margin-right: 10px;">
                <label for="status">Filtrer par statut :</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Tous</option>
                    <option value="En cours" <?php if ($_SESSION['currentStatus'] === 'En cours') echo 'selected'; ?>>En cours
                    </option>
                    <option value="Terminé" <?php if ($_SESSION['currentStatus'] === 'Terminé') echo 'selected'; ?>>Terminé
                    </option>
                </select>
            </div>

            <div class="form-group" style="max-width: 200px; margin-right: 10px;">
                <label for="date">Filtrer par date :</label>
                <input type="date" class="form-control" id="date" name="date" style="max-width: 150px;"
                       value="<?php echo $_SESSION['currentDate']; ?>">
            </div>

            <div class="form-group" style="max-width: 200px; margin-right: 10px;">
                <label for="tri">Trie par N° de ticket :</label>
                <select class="form-control form-control-sm" id="tri" name="tri">
                    <option value="">Aucun</option>
                    <option value="croissant" <?php if ($_SESSION['currentTri'] === 'croissant') echo 'selected'; ?>>Croissant
                    </option>
                    <option value="decroissant" <?php if ($_SESSION['currentTri'] === 'decroissant') echo 'selected'; ?>>Décroissant
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>

        <?php

// Fonction pour compter le nombre total de tickets en fonction du statut et des filtres
function countTickets($bdd, $currentStatus, $currentDate, $currentTri) {
    // Construction de la requête SQL en fonction des filtres sélectionnés
    $sql = 'SELECT COUNT(*) AS total_tickets
        FROM sav AS s
        INNER JOIN multipath AS m ON s.bicycode = m.bicycode
        LEFT JOIN taches_a_realiser AS t ON s.numero_ticket = t.sav_id';

    if (!empty($currentStatus)) {
        $sql .= " WHERE s.status = ?";
    }

    if (!empty($currentDate)) {
        if (!empty($currentStatus)) {
            $sql .= " AND";
        } else {
            $sql .= " WHERE";
        }
        $sql .= " s.date_prise_en_charge = ?";
    }

    // Préparation de la requête SQL
    $stmt = $bdd->prepare($sql);

    // Bind des valeurs des filtres
    if (!empty($currentStatus)) {
        $stmt->bindParam(1, $currentStatus, PDO::PARAM_STR);
    }
    if (!empty($currentDate)) {
        $stmt->bindParam(2, $currentDate, PDO::PARAM_STR);
    }

    // Exécution de la requête
    $stmt->execute();

    // Récupération du résultat
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retourne le nombre total de tickets
    return $result['total_tickets'];
}

// Utilisation de la fonction pour compter les tickets
$totalTickets = countTickets($bdd, $_SESSION['currentStatus'], $_SESSION['currentDate'], $_SESSION['currentTri']);

// Affichage du nombre total de tickets
echo "<p>Total de tickets : " . $totalTickets . "</p>";

        // Construction de la requête SQL en fonction des filtres sélectionnés
        $sql = 'SELECT s.numero_ticket, s.status, m.bicycode, m.nom_client, s.commentaire, t.tache
        FROM sav AS s
        INNER JOIN multipath AS m ON s.bicycode = m.bicycode
        LEFT JOIN taches_a_realiser AS t ON s.numero_ticket = t.sav_id';

        if (!empty($_SESSION['currentStatus'])) {
            $sql .= " WHERE s.status = '{$_SESSION['currentStatus']}'";
        }

        if (!empty($_SESSION['currentDate'])) {
            $sql .= " AND s.date_prise_en_charge = '{$_SESSION['currentDate']}'";
        }

        if (!empty($_SESSION['currentTri'])) {
            if ($_SESSION['currentTri'] === 'croissant') {
                $sql .= " ORDER BY s.numero_ticket ASC";
            } elseif ($_SESSION['currentTri'] === 'decroissant') {
                $sql .= " ORDER BY s.numero_ticket DESC";
            }
        }

        // Récupérer les données des tickets correspondants à la requête
        $stmt = $bdd->query($sql);
        $tickets = $stmt->fetchAll();

        // Parcourir les résultats et afficher les informations de chaque enregistrement
        foreach ($tickets as $ticket) {
            $formattedTicketNumber = str_pad($ticket['numero_ticket'], 5, '0', STR_PAD_LEFT); // Formatage du numéro de ticket

            echo "<div class='card mb-3'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>Numéro de ticket : " . $formattedTicketNumber . "</h5>";
            echo "<p class='card-text'>Numéro de bicycode : " . $ticket['bicycode'] . "</p>";
            echo "<p class='card-text'>Nom du client : " . $ticket['nom_client'] . "</p>";
            echo "<p class='card-text'>Tâche à réaliser : " . $ticket['tache'] . "</p>"; // Afficher la tâche à réaliser
            echo "<p class='card-text'>Commentaire : " . $ticket['commentaire'] . "</p>";

            // Espacement entre les boutons
            echo "&nbsp;&nbsp;&nbsp;";

            // Ajouter le bouton "Voir la fiche" avec le lien vers fiche_sav.php
            echo "<a href='fiche_sav.php?ticket_id=" . $ticket['numero_ticket'] . "' class='btn btn-primary'>Voir la fiche</a>";

            echo "</div>";
            echo "</div>";
        }
        ?>
           <div class="footer text-center">
                <button id="scrollToBottom" onclick="scrollToBottom()" title="Scroll To Bottom">Bas</button>
                <button id="scrollToTop" onclick="scrollToTop()" title="Scroll To Top">Haut</button>
            </div>

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
