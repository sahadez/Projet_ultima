<?php
// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php'; // ajout connexion bdd

// Vérifier si le formulaire de modification a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $commande = $_POST['editcommande'];
    $configuration = $_POST['editconfiguration'];
    $batterie = $_POST['editbatterie'];
    $jantes = $_POST['editjantes'];
    $taille = $_POST['edittaille'];

    // Préparer et exécuter la requête SQL pour mettre à jour l'enregistrement dans la base de données
    $stmt = $bdd->prepare('UPDATE boheme SET configuration = ?, batterie = ?, jantes = ?, taille = ? WHERE commande = ?');
    $stmt->execute([$configuration, $batterie, $jantes, $taille, $commande]);

    // Redirection vers la page d'accueil ou toute autre page après la mise à jour
    header('Location: boheme.php');
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

        <div class="container mt-5 content">
            <?php
            // Récupère les données de tous les enregistrements dans la table dev
            $req_all_records = $bdd->prepare('SELECT commande, configuration, batterie, jantes, bicycode, taille FROM boheme');
            $req_all_records->execute();

            echo "<table class='table'>";
            echo "<thead><tr><th>Bicycode</th><th>Numero</th><th>Configuration</th><th>Batterie</th><th>Jante</th><th>Taille</th><th>Action</th></tr></thead>";
            echo "<tbody>";

            // Affiche les données de chaque enregistrement
            while ($data_all_records = $req_all_records->fetch()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($data_all_records['bicycode']) . "</td>";
                echo "<td data-id='" . htmlspecialchars($data_all_records['commande']) . "' class='editable'>" . htmlspecialchars($data_all_records['commande']) . "</td>";
                echo "<td>" . htmlspecialchars($data_all_records['configuration']) . "</td>";
                echo "<td>" . htmlspecialchars($data_all_records['batterie']) . "</td>";
                echo "<td>" . htmlspecialchars($data_all_records['jantes']) . "</td>";
                echo "<td>" . htmlspecialchars($data_all_records['taille']) . "</td>";
                echo "<td><button class='btn btn-info edit-button'>Modifier</button></td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier l'enregistrement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire pour modifier l'enregistrement -->
                    <form id="editForm" method="post">
                        <div class="form-group">
                            <label for="editconfiguration">Configuration</label>
                            <input type="text" class="form-control" id="editconfiguration" name="editconfiguration">
                        </div>
                        <div class="form-group">
                            <label for="editbatterie">Batterie</label>
                            <input type="text" class="form-control" id="editbatterie" name="editbatterie">
                        </div>
                        <div class="form-group">
                            <label for="editjantes">Jantes</label>
                            <input type="text" class="form-control" id="editjantes" name="editjantes">
                        </div>
                        <div class="form-group">
                            <label for="edittaille">Taille</label>
                            <input type="text" class="form-control" id="edittaille" name="edittaille">
                        </div>
                        <input type="hidden" id="editcommande" name="editcommande">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
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

    <script>
        $(document).ready(function () {
            // Gestion du clic sur le bouton "Modifier"
            $('.edit-button').click(function () {
                var row = $(this).closest('tr');
                var commande = row.find('.editable').data('id');
                var configuration = row.find('td:eq(1)').text();
                var batterie = row.find('td:eq(2)').text();
                var jantes = row.find('td:eq(3)').text();
                var taille = row.find('td:eq(4)').text();
                
                $('#editcommande').val(commande);
                $('#editconfiguration').val(configuration);
                $('#editbatterie').val(batterie);
                $('#editjantes').val(jantes);
                $('#edittaille').val(taille);

                $('#editModal').modal('show');
            });
        });
    </script>
</body>

</html>
 