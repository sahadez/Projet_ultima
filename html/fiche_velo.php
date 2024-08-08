<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location:index.php');
    die();
}

if (isset($_GET['bicycode'])) {
    $bicycode = $_GET['bicycode'];

    $req_multipath = $bdd->prepare('SELECT * FROM multipath WHERE bicycode = ?');
    $req_multipath->execute([$bicycode]);
    $multipath_info = $req_multipath->fetch();

    $req_sav = $bdd->prepare('SELECT * FROM sav WHERE bicycode = ?');
    $req_sav->execute([$bicycode]);
    $sav_info = $req_sav->fetchAll();
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
        #barcode {
            margin: 20px auto;
            display: block;
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
    <div class="container">
       
        <h3 class="text-center mt-4">Fiche Multipath</h3>
        <div class="text-center">
            <svg id="barcode"></svg>
        </div>

        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
                <?php if (isset($multipath_info)): ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Informations Multipath
                        </div>
                        <div class="card-body">
                            <p><strong>Bicycode:</strong> <?php echo $multipath_info['bicycode']; ?></p>
                            <p><strong>Nom du client:</strong> <?php echo $multipath_info['nom_client']; ?></p>
                            <p><strong>Numéro de commande:</strong> <?php echo $multipath_info['numero_commande']; ?></p>
                            <p><strong>Configuration:</strong> <?php echo $multipath_info['configuration']; ?></p>
                            <p><strong>Batterie:</strong> <?php echo $multipath_info['batterie']; ?></p>

                            <?php if (!empty($multipath_info['option1'])): ?>
                                <p><strong>Option 1:</strong> <?php echo $multipath_info['option1']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option2'])): ?>
                                <p><strong>Option 2:</strong> <?php echo $multipath_info['option2']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option3'])): ?>
                                <p><strong>Option 3:</strong> <?php echo $multipath_info['option3']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option4'])): ?>
                                <p><strong>Option 4:</strong> <?php echo $multipath_info['option4']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option5'])): ?>
                                <p><strong>Option 5:</strong> <?php echo $multipath_info['option5']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option6'])): ?>
                                <p><strong>Option 6:</strong> <?php echo $multipath_info['option6']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option7'])): ?>
                                <p><strong>Option 7:</strong> <?php echo $multipath_info['option7']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option8'])): ?>
                                <p><strong>Option 8:</strong> <?php echo $multipath_info['option8']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option9'])): ?>
                                <p><strong>Option 9:</strong> <?php echo $multipath_info['option9']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option10'])): ?>
                                <p><strong>Option 10:</strong> <?php echo $multipath_info['option10']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option11'])): ?>
                                <p><strong>Option 11:</strong> <?php echo $multipath_info['option11']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option12'])): ?>
                                <p><strong>Option 12:</strong> <?php echo $multipath_info['option12']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option13'])): ?>
                                <p><strong>Option 13:</strong> <?php echo $multipath_info['option13']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option14'])): ?>
                                <p><strong>Option 14:</strong> <?php echo $multipath_info['option14']; ?></p>
                            <?php endif; ?>

                            <?php if (!empty($multipath_info['option15'])): ?>
                                <p><strong>Option 15:</strong> <?php echo $multipath_info['option15']; ?></p>
                            <?php endif; ?>


                            <p><strong>Modèle du vélo:</strong> <?php echo $multipath_info['modele']; ?></p>
                            <p><strong>Couleur du vélo:</strong> <?php echo $multipath_info['couleur']; ?></p>
                            <p><strong>Gravage Bicycode & Spécifique:</strong> <?php echo $multipath_info['gravage']; ?></p>
                            <p><strong>Montage:</strong> <?php echo $multipath_info['montage']; ?></p>
                            <p><strong>Calibrage MDU:</strong> <?php echo $multipath_info['calibrage_mdu']; ?></p>
                            <p><strong>Calibrage BMS:</strong> <?php echo $multipath_info['calibrage_bms']; ?></p>
                            <p><strong>Calibrage HMI:</strong> <?php echo $multipath_info['calibrage_hmi']; ?></p>
                            <p><strong>Calibrage IoT:</strong> <?php echo $multipath_info['calibrage_iot']; ?></p>
                            <p><strong>PUK:</strong> <?php echo $multipath_info['puk']; ?></p>
                            <p><strong>Génération moteur:</strong> <?php echo $multipath_info['generation_moteur']; ?></p>
                            <p><strong>Commentaire:</strong> <?php echo $multipath_info['commentaire']; ?></p>
                            <p><strong>Totem PLV:</strong> <?php echo $multipath_info['Totem_PLV']; ?></p>
                            <p><strong>Podium PLV:</strong> <?php echo $multipath_info['Podium_PLV']; ?></p>
                            <p><strong>Catalogues:</strong> <?php echo $multipath_info['Catalogues']; ?></p>
                            <p><strong>Date de creation:</strong> <?php echo $multipath_info['date_creation']; ?></p>
                            <p><strong>Date fin de montage:</strong> <?php echo $multipath_info['date_bl']; ?></p>
                            
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">Aucune information trouvée pour le bicycode spécifié dans la table multipath.</div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <?php if (!empty($sav_info)): ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Informations SAV
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($sav_info as $sav): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <span>SAV <?php echo $sav['numero_ticket']; ?></span>
                                                <button class="btn btn-sm btn-primary sav-toggle collapsed" data-toggle="collapse" data-target="#sav-details-<?php echo $sav['numero_ticket']; ?>"></button>
                                            </div>
                                            <div class="card-body collapse" id="sav-details-<?php echo $sav['numero_ticket']; ?>">
                                                <p><strong>Bicycode:</strong> <?php echo $sav['bicycode']; ?></p>
                                                <p><strong>Nom du client:</strong> <?php echo $sav['nom_client']; ?></p>
                                                <p><strong>Date de prise en charge:</strong> <?php echo $sav['date_prise_en_charge']; ?></p>
                                                <p><strong>Date d'intervention:</strong> <?php echo $sav['date_intervention']; ?></p>
                                                <p><strong>Tâches à réaliser:</strong></p>
                                                <?php
                                                $stmt_a_realiser = $bdd->prepare('SELECT tache FROM taches_a_realiser WHERE sav_id = ?');
                                                $stmt_a_realiser->execute([$sav['numero_ticket']]);
                                                $taches_a_realiser = $stmt_a_realiser->fetchAll();
                                                foreach ($taches_a_realiser as $tache) {
                                                    echo "<p>" . $tache['tache'] . "</p>";
                                                }
                                                ?>
                                                <p><strong>Tâches réalisées:</strong></p>
                                                <?php
                                                $stmt_realisees = $bdd->prepare('SELECT tache FROM taches_realisees WHERE sav_id = ?');
                                                $stmt_realisees->execute([$sav['numero_ticket']]);
                                                $taches_realisees = $stmt_realisees->fetchAll();
                                                foreach ($taches_realisees as $tache) {
                                                    echo "<p>" . $tache['tache'] . "</p>";
                                                }
                                                ?>
                                                <p><strong>Intervenant:</strong> <?php echo $sav['intervenant']; ?></p>
                                                <p><strong>Commentaire:</strong> <?php echo $sav['commentaire']; ?></p>
                                                <p><strong>Date de clôture du ticket:</strong> <?php echo $sav['date_cloture_ticket']; ?></p>
                                                <p><strong>Garantie ou facturation:</strong> <?php echo $sav['garantie_ou_facturation']; ?></p>
                                                <p><strong>Statut:</strong> <?php echo $sav['status']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-center mt-4">
            <div class="d-flex justify-content-center">
                <a href="home.php" class="btn btn-primary mr-2">Retour</a>
                <a href="modifier_fm.php?bicycode=<?php echo $bicycode; ?>" class="btn btn-secondary">Modifier</a>
                <button class="btn btn-info ml-2" onclick="imprimerInformations()">Imprimer</button>
            </div>
        </div>
    </div>

     <!-- Div pour la partie SAV -->
     <div id="sav" style="display: none;">
        <!-- Code HTML pour la partie SAV -->
        <!-- Par exemple, vous pouvez y inclure votre boucle pour afficher les informations SAV -->
    </div>

    <!-- JavaScript pour l'impression -->
    <script>
    function imprimerInformations() {
        // Générez le code-barres avec JsBarcode
        JsBarcode("#barcode", "<?php echo $bicycode; ?>", {
            format: "CODE128",
            displayValue: true,
            fontSize: 18,
            textMargin: 0
        });
        
        // Attendez que le code-barres soit généré avant d'imprimer
        setTimeout(function() {
            var printContents = document.querySelector('.col-md-6').innerHTML;
            var originalContents = document.body.innerHTML;

            // Cacher la partie SAV avant d'imprimer
            document.getElementById('sav').style.display = 'none';

            // Clonez le code-barres et ajoutez-le à la partie imprimée
            var barcodeClone = document.getElementById('barcode').cloneNode(true);
            var divBarcode = document.createElement('div');
            divBarcode.appendChild(barcodeClone);
            divBarcode.style.position = 'fixed';
            divBarcode.style.bottom = '50px';
            divBarcode.style.width = '100%';
            divBarcode.style.textAlign = 'center';
            printContents += divBarcode.outerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            // Réafficher la partie SAV après l'impression
            document.getElementById('sav').style.display = 'block';
        }, 500); // Attendre 500 millisecondes (0.5 seconde) pour s'assurer que le code-barres est généré
    }
</script>



    <script>
    // Attendez que le document soit chargé
    document.addEventListener("DOMContentLoaded", function(event) {
        // Générez le code-barres avec JsBarcode
        JsBarcode("#barcode", "<?php echo $bicycode; ?>", {
            format: "CODE128",
            displayValue: true,
            fontSize: 18,
            textMargin: 0
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/jsbarcode/3.6.0/JsBarcode.all.min.js"></script>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

</body>

</html>
