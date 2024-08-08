<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Générer le prochain numéro de ticket
$stmt = $bdd->query('SELECT MAX(numero_ticket) as max_ticket FROM sav');
$result = $stmt->fetch();
$numero_ticket = $result['max_ticket'] + 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $numero_ticket = $_POST['numero_ticket'];
    $bicycode = $_POST['bicycode'];
    $date_prise_en_charge = $_POST['date_prise_en_charge'];
    $date_intervention = $_POST['date_intervention'];
    $tache_a_realiser = $_POST['tache_a_realiser'];
    $intervenant = $_POST['intervenant'];
    $commentaire = $_POST['commentaire'];
    $date_cloture_ticket = $_POST['date_cloture_ticket'];
    $garantie_ou_facturation = $_POST['garantie_ou_facturation'];
    $nom_client = $_POST['nom_client'];

    // Insertion des données dans la table SAV
    $stmt = $bdd->prepare("INSERT INTO sav (bicycode, date_prise_en_charge, date_intervention, intervenant, commentaire, date_cloture_ticket, garantie_ou_facturation, nom_client, email, numero_telephone ) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)");
    $stmt->execute([$bicycode, $date_prise_en_charge, $date_intervention, $intervenant, $commentaire, $date_cloture_ticket, $garantie_ou_facturation, $nom_client, $email, $numero_telephone]);

    // Récupérer le numéro de ticket généré
    $numero_ticket = $bdd->lastInsertId();

    // Insérer les tâches à réaliser dans la table taches_a_realiser
    if (!empty($tache_a_realiser)) {
        foreach ($tache_a_realiser as $tache) {
            $stmt = $bdd->prepare("INSERT INTO taches_a_realiser (tache, sav_id) VALUES (?, ?)");
            $stmt->execute([$tache, $numero_ticket]);
        }
    }

    // Afficher le numéro de ticket
    echo "Le numéro de ticket généré est : " . $numero_ticket;

    header('Location: sav.php');
    exit;
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
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Créer un ticket SAV</h2>
                    <div>
                      <button type="button" class="btn btn-primary" onclick="window.location.href='gerer_ticket.php'">Gestion des tickets SAV</button>                           
                     </div>
                    </div>
        
        <form method="post">
            <div class="form-group">
                <label for="numero_ticket">Prochain ticket SAV :</label>
                <input type="text" class="form-control" id="numero_ticket" name="numero_ticket" value="<?php echo $numero_ticket; ?>" disabled>

            </div>

            <div class="form-group">
                <label for="bicycode">Bicycode :</label>
                <input type="text" class="form-control" id="bicycode" name="bicycode" required>
            </div>
            
            <div class="form-group">
                <label for="nom_client">Nom du client :</label>
                <input type="text" class="form-control" id="nom_client" name="nom_client" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="text" class="form-control" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="numero_telephone">Telephone :</label>
                <input type="text" class="form-control" id="numero_telephone" name="numero_telephone">
            </div>
            
            <div class="form-group">
                <label for="date_prise_en_charge">Date de prise en charge :</label>
                <input type="date" class="form-control" id="date_prise_en_charge" name="date_prise_en_charge"
                       required>
            </div>
            <!-- 
            <div class="form-group">
                <label for="date_intervention">Date d'intervention' :</label>
                <input type="date" class="form-control" id="date_intervention" name="date_intervention" required>
            </div> -->

            <!-- Commenter ou supprimer les champs des tâches à réaliser -->
             <div id="taches_a_realiser">
                <div class="form-group">
                    <label for="tache_a_realiser_1">Tâche à réaliser :</label>
                    <input type="text" class="form-control" id="tache_a_realiser_1" name="tache_a_realiser[]">
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addTacheARealiser()">+</button>

            <!-- Commenter ou supprimer les champs des tâches réalisées -->
            <!-- <div id="taches_realisees">
                <div class="form-group">
                    <label for="tache_realisee_1">Tâche réalisée :</label>
                    <input type="text" class="form-control" id="tache_realisee_1" name="tache_realisee[]" required>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addTacheRealisee()">+</button> -->

            
            <div class="form-group">
                <label for="commentaire">Commentaire :</label>
                <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
            </div>
           
            <div class="form-group">
                <label for="garantie_ou_facturation">Garantie ou facturation :</label>
                <select class="form-control" id="garantie_ou_facturation" name="garantie_ou_facturation" required>
                    <option value="Garantie">Garantie</option>
                    <option value="Facturation">Facturation</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Créer SAV</button>
        </form>
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

    <!-- Commenter ou supprimer les fonctions JavaScript d'ajout de tâches -->
    <script>
        var countTacheARealiser = 1;
        var countTacheRealisee = 1;

        function addTacheARealiser() {
            countTacheARealiser++;
            var tacheDiv = document.createElement('div');
            tacheDiv.className = 'form-group';
            tacheDiv.innerHTML = '<label for="tache_a_realiser_' + countTacheARealiser + '">Tâche à réaliser :</label>' +
                '<input type="text" class="form-control" id="tache_a_realiser_' + countTacheARealiser + '" name="tache_a_realiser[]" required>';

            document.getElementById('taches_a_realiser').appendChild(tacheDiv);
        }

        //    function addTacheRealisee() {
          //  countTacheRealisee++;
            //var tacheDiv = document.createElement('div');
            //tacheDiv.className = 'form-group';
            //tacheDiv.innerHTML = '<label for="tache_realisee_' + countTacheRealisee + '">Tâche réalisée :</label>' +
              //  '<input type="text" class="form-control" id="tache_realisee_' + countTacheRealisee + '" name="tache_realisee[]" required>';

           // document.getElementById('taches_realisees').appendChild(tacheDiv);
      //  }
    </script> 
</body>
</html>
