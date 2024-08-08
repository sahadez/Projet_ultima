<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas

    session_start();
    require_once 'config.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        die();
    }

    // Récupérer les données de l'utilisateur
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
                    <a href="#" class="btn btn-info">+</a>
                        <ul class="sous">
                    <a href="register.php" class="btn btn-info">Multipath</a>
                    <li><a href="larrum.php" class="btn btn-info">Larrum</a></li>
                    <li><a href="larrum.php" class="btn btn-info">Boheme</a></li>
                    <li><a href="larrum.php" class="btn btn-info">Gravel</a></li>
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
        <?php
    // Fonction pour nettoyer les données avant de les exporter en CSV
    function cleanData($str) {
        // Si la valeur contient une virgule, des guillemets ou des retours à la ligne,
        // il faut l'entourer de guillemets et doubler les guillemets existants
        if (strpos($str, ',') !== false || strpos($str, '"') !== false || strpos($str, "\n") !== false) {
            $str = '"' . str_replace('"', '""', $str) . '"';
        }
        return $str;
    }

    // Récupération des données de la base de données
    $req = $bdd->prepare('SELECT * FROM multipath');
    $req->execute();
    $rows = $req->fetchAll(PDO::FETCH_ASSOC);
?>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>Liste des vélos</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>bike_maker</th>
                                <th>assembly_date</th>
                                <th>frame_serial_number</th>
                                <th>reference</th>
                                <th>unique_marking_number</th>
                                <th>brand</th>
                                <th>modele</th>
                                <th>type</th>
                                <th>color</th>
                                <th>display_BLE_present</th>
                                <th>bike_unique_id_by_customer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $req = $bdd->prepare('SELECT * FROM multipath');
                                $req->execute();
                                while ($row = $req->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['bike_maker'] . '</td>';
                                    echo '<td>' . $row['date_prod'] . '</td>';
                                    echo '<td>' . $row['frame_serial_number'] . '</td>';
                                    echo '<td>' . $row['reference'] . '</td>';
                                    echo '<td>' . $row['unique_marking_number'] . '</td>';
                                    echo '<td>' . $row['brand'] . '</td>';
                                    echo '<td>' . $row['modele'] . '</td>';
                                    echo '<td>' . $row['type'] . '</td>';
                                    echo '<td>' . $row['couleur'] . '</td>';
                                    echo '<td>' . $row['display_BLE_present'] . '</td>';
                                    echo '<td>' . $row['bike_unique_id_by_customer'] . '</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                    <div class="mt-4">
                <a href="exportvelco.php" class="btn btn-primary" target="_blank" id="export-btn">Exporter en CSV</a>
            </div>
                </div>
            </div>
        </div>
    </div>

    </div>

<div class="footer text-center">
<button id="scrollToBottom" onclick="scrollToBottom()" title="Scroll To Bottom">Bas</button>
<button id="scrollToTop" onclick="scrollToTop()" title="Scroll To Top">Haut</button>
</div>

</div>

<script>
    // Script pour déclencher l'export en CSV
    document.getElementById("export-btn").addEventListener("click", function() {
        window.location.href = "exportvelco.php";
    });
   
</script>



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
</body>
</html>
