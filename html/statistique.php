<?php 
// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas

session_start();
require_once 'config.php';

// Vérifier si la session existe, sinon rediriger vers la page de connexion
if(!isset($_SESSION['user'])){
    header('Location:index.php');
    die();
}

// Récupérer les données de l'utilisateur
$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$data = $req->fetch();

// Récupérer les données de tous les enregistrements dans la table multipath
$req_all_records = $bdd->prepare('SELECT bicycode, nom_client, prenom_client, numero_commande, gravage, montage, commentaire, modele, couleur, configuration FROM multipath');
$req_all_records->execute();
$all_records = $req_all_records->fetchAll(PDO::FETCH_ASSOC);

// Statistiques sur les modèles de vélos
$req_statistics = $bdd->prepare('SELECT modele, COUNT(*) AS quantite FROM multipath GROUP BY modele');
$req_statistics->execute();
$statistics = $req_statistics->fetchAll(PDO::FETCH_ASSOC);

// Statistiques sur les couleurs des vélos
$req_color_statistics = $bdd->prepare('SELECT couleur, COUNT(*) AS quantite FROM multipath GROUP BY couleur');
$req_color_statistics->execute();
$color_statistics = $req_color_statistics->fetchAll(PDO::FETCH_ASSOC);

// Statistiques sur les vélos créés par mois et année à partir de 2024
$req_monthly_statistics = $bdd->prepare('SELECT YEAR(date_creation) AS annee, MONTH(date_creation) AS mois, COUNT(*) AS quantite FROM multipath WHERE YEAR(date_creation) >= 2024 GROUP BY YEAR(date_creation), MONTH(date_creation)');
$req_monthly_statistics->execute();
$monthly_statistics = $req_monthly_statistics->fetchAll(PDO::FETCH_ASSOC);

// Créer les tableaux de données pour le graphique des modèles
$modelLabels = [];
$modelData = [];
$modelColors = [];

foreach ($statistics as $statistic) {
    $modele = $statistic['modele'];
    $quantite = $statistic['quantite'];

    $modelLabels[] = $modele;
    $modelData[] = $quantite;

    // Générer une couleur aléatoire pour chaque modèle
    $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    $modelColors[] = $color;
}

// Créer les tableaux de données pour le graphique des couleurs
$colorLabels = [];
$colorData = [];
$colorColors = [];

foreach ($color_statistics as $statistic) {
    $couleur = $statistic['couleur'];
    $quantite = $statistic['quantite'];

    $colorLabels[] = $couleur;
    $colorData[] = $quantite;

    // Générer une couleur aléatoire pour chaque couleur
    $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    $colorColors[] = $color;
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="col-md-12">
            <?php 
            if(isset($_GET['err'])){
                $err = htmlspecialchars($_GET['err']);
                switch($err){
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
                            </li>&nbsp&nbsp&nbsp&nbsp
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
                            </li>&nbsp&nbsp&nbsp&nbsp
                            <li class="nav-item">
                                <a href="expedition.php" class="btn btn-info">Expedition</a>
                            </li>&nbsp&nbsp&nbsp&nbsp
                            <li class="nav-item">
                                <a href="sav.php" class="btn btn-info">SAV</a>
                            </li>&nbsp&nbsp&nbsp&nbsp
                            <li class="nav-item">
                                <a href="export.php" class="btn btn-info">Velco - O'code</a>
                            </li>&nbsp&nbsp&nbsp&nbsp
                            <li class="nav-item">
                                <a href="statistique.php" class="btn btn-info">State</a>
                            </li>&nbsp&nbsp&nbsp&nbsp
                        </ul>
                        <form class="form-inline my-2 my-lg-0" action="search.php" method="post">
                            <input class="form-control mr-sm-2" type="search" placeholder="Search" name="search" aria-label="Search">
                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                        </form>&nbsp&nbsp
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>   
                      
            <div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center">Statistiques sur les modèles de vélos</h2>
            <div class="d-flex justify-content-center">
                <div style="max-width: 500px; margin: auto;">
                    <canvas id="modelChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h2 class="text-center">Statistiques sur les couleurs des vélos</h2>
            <div class="d-flex justify-content-center">
                <div style="max-width: 500px; margin: auto;">
                    <canvas id="colorChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h2 class="text-center">Tableau des statistiques des modèles</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Modèle</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statistics as $statistic) : ?>
                            <tr>
                                <td><?php echo $statistic['modele']; ?></td>
                                <td><?php echo $statistic['quantite']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h2 class="text-center">Tableau des statistiques des couleurs</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Couleur</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($color_statistics as $colorStat) : ?>
                            <tr>
                                <td><?php echo $colorStat['couleur']; ?></td>
                                <td><?php echo $colorStat['quantite']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">Bilan sur la production des Multipaths</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Année</th>
                            <th>Mois</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php setlocale(LC_TIME, 'fr_FR.UTF-8');?>
                        <?php foreach ($monthly_statistics as $statistic) : ?>
                            <tr>
                                <td><?php echo $statistic['annee']; ?></td>
                                <td><?php echo strftime('%B', mktime(0, 0, 0, $statistic['mois'], 1)); ?></td>
                                <td><?php echo $statistic['quantite']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Récupérer les données depuis PHP
    var modelLabels = <?php echo json_encode($modelLabels); ?>;
    var modelData = <?php echo json_encode($modelData); ?>;
    var modelColors = <?php echo json_encode($modelColors); ?>;

    // Créer le graphique pour les modèles de vélos
    var modelCtx = document.getElementById('modelChart').getContext('2d');
    var modelChart = new Chart(modelCtx, {
        type: 'doughnut',
        data: {
            labels: modelLabels,
            datasets: [{
                data: modelData,
                backgroundColor: modelColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true,
                position: 'right'
            },
            plugins: {
                labels: {
                    render: 'label',
                    fontColor: '#000',
                    fontStyle: 'bold'
                }
            }
        }
    });

    // Récupérer les données depuis PHP
    var colorLabels = <?php echo json_encode($colorLabels); ?>;
    var colorData = <?php echo json_encode($colorData); ?>;
    var colorColors = <?php echo json_encode($colorColors); ?>;

    // Créer le graphique pour les couleurs des vélos
    var colorCtx = document.getElementById('colorChart').getContext('2d');
    var colorChart = new Chart(colorCtx, {
        type: 'doughnut',
        data: {
            labels: colorLabels,
            datasets: [{
                data: colorData,
                backgroundColor: colorColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true,
                position: 'right'
            },
            plugins: {
                labels: {
                    render: 'label',
                    fontColor: '#000',
                    fontStyle: 'bold'
                }
            }
        }
    });
</script>

</body>
</html>
