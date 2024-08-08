<?php
// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location:index.php');
    die();
}

// Récupération des Bicycodes déjà utilisés dans les tables boheme et multipath
$usedBicycodes = $bdd->query('SELECT bicycode FROM multipath UNION SELECT bicycode FROM boheme')->fetchAll(PDO::FETCH_COLUMN);

// Récupération des options pour les listes déroulantes
$options_bicycode = $bdd->query('SELECT num_bicycode FROM bicycode')->fetchAll(PDO::FETCH_COLUMN);

// Filtrage des Bicycodes disponibles
$availableBicycodes = array_diff($options_bicycode, $usedBicycodes);


$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$data = $req->fetch();

// Récupération des options pour les listes déroulantes
// TODO: remplacer par les requêtes appropriées pour récupérer les options
$existingBicycodes = $bdd->query('SELECT bicycode FROM multipath')->fetchAll(PDO::FETCH_COLUMN);
$options_bicycode = $bdd->query('SELECT num_bicycode FROM bicycode')->fetchAll(PDO::FETCH_COLUMN);
$options_payment = ['Acompte', 'Total'];
$options_facture_envoyee = ['oui', 'non'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier si le numéro de commande existe déjà dans la base de données
    $existingCommande = $bdd->prepare('SELECT COUNT(*) AS count FROM multipath WHERE numero_commande = ?');
    $existingCommande->execute([$_POST['numero_commande']]);
    $count = $existingCommande->fetchColumn();

    if ($count > 0) {
        // Numéro de commande déjà existant, afficher un message d'erreur ou effectuer une action appropriée
        $_SESSION['error'] = 'Ce numéro de commande existe déjà.';
        header("Location: ".$_SERVER['REQUEST_URI']);
        exit;
    }

    $fields = [
        'numero_commande',
        'nom_client',
        'numero_telephone',
        'bicycode',
        'configuration',
        'modele',
        'couleur',
        'option1',
        'option2',
        'option3',
        'option4',
        'option5',
        'option6',
        'option7',
        'option8',
        'option9',
        'option10',
        'option11',
        'option12',
        'option13',
        'option14',
        'option15',
        'date_bl',
        'payment',
        'facture_envoyee',
        'commentaire',
        'of_disponible',
        'gravage',
        'montage',
        'calibrage_mdu',
        'calibrage_hmi',
        'calibrage_bms',
        'calibrage_iot',
        'puk',
        'generation_moteur',
        'prenom_client',
        'shop',
        'type_client',
        'id_fnuci',
        'type_velo',
        'bike_maker',
        'vae',
        'display_BLE_present',
        'bike_unique_id_by_customer',
        'frame_serial_number',
        'reference',
        'unique_marking_number',
        'brand',
        'type',
        'date_prod',
        'batterie',

    ];
    $values = array_map(function ($field) {
        if ($field === 'unique_marking_number') {
            return $_POST['bicycode'] ?? null;
        } 
        elseif ($field === 'date_prod') {
            return date('Y-m-d H:i:s'); // Obtenir la date et l'heure actuelles au format AAAA-MM-JJ HH:MN:SS
        }
        elseif ($field === 'brand') {
            return 'Ultima_Mobility';
        } elseif ($field === 'id_fnuci' || $field === 'frame_serial_number') {
            return $_POST['bicycode'] ?? null;
        } elseif ($field === 'bike_maker') {
            return 'UltimaMobility';
        } elseif ($field === 'vae') {
            return 'oui';
        } elseif ($field === 'display_BLE_present') {
            return 0;
        } elseif ($field === 'bike_unique_id_by_customer') {
            return '';
        } elseif ($field === 'reference') {
            return $_POST['configuration'] ?? null;
        } elseif ($field === 'type') {
            return 'Classic';
        } elseif ($field === 'type_velo') {
            $modele = $_POST['modele'] ?? null;
            switch ($modele) {
                case 'City':
                    return 'Ville';
                case 'Trekking':
                    return 'VTC';
                case 'Cargo Famille':
                    return 'Cargo 2 ou 3 roues';
                case 'Cargo Urbain':
                    return 'Cargo 2 ou 3 roues';
                default:
                    return null;
            }
        }
        return $_POST[$field] ?? null;
    }, $fields);
    
    
    $gravage = isset($_POST['gravage']) ? 1 : 0;

    $placeholders = implode(',', array_fill(0, count($fields), '?'));

    $sql = "INSERT INTO multipath (".implode(',', $fields).") VALUES ($placeholders)";
    $stmt = $bdd->prepare($sql);
    $stmt->execute($values);
    
    // Enregistrement réussi, afficher le popup et rediriger l'utilisateur
    echo "<script>
        var confirmNewEntry = confirm('Enregistrement réussi ! Voulez-vous enregistrer un nouveau vélo ?');
        if (confirmNewEntry) {
            window.location.href = 'register.php'; // Rediriger vers la page d'enregistrement
        } else {
            window.location.href = 'home.php'; // Rediriger vers la page d'accueil
        }
    </script>";
    exit;
}
?>

<!-- Reste du code HTML inchangé -->


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ultima Mobility</title>
    <!-- CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
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
    <input class="form-control mr-sm-2" type="search" placeholder="Search" name="search" aria-label="Search">
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
                     
                    &nbsp;&nbsp;&nbsp;
<div class="container">
    <h3>Formulaire Multipath</h3>
    <?php if(isset($_SESSION['flash'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['flash']; ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <form method="post" id="myForm">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="numero_commande">Numéro de commande:</label>
                    <input type="text" id="numero_commande" name="numero_commande" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="nom_client">Nom du client:</label>
                    <input type="text" id="nom_client" name="nom_client" class="form-control">
                </div>
                <div class="form-group">
                    <label for="prenom_client">Prenom du client:</label>
                    <input type="text" id="prenom_client" name="prenom_client" class="form-control">
                </div>
                <div class="form-group">
                    <label for="numero_telephone">Numéro de téléphone:</label>
                    <input type="text" id="numero_telephone" name="numero_telephone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="bicycode">Bicycode :</label>
                    <select id="bicycode" name="bicycode" class="form-control" required>
                        <option value="">Sélectionner un Bicycode</option>
                        <?php foreach ($availableBicycodes as $option): ?>
                            <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="configuration">Configuration:</label>
                    <select id="configuration" name="configuration" class="form-control">
                        <option value="">Sélectionner une Configuration</option>
                        <optgroup label="Carbone">
                        <option value="V001">V001 City</option>
                        <option value="V002">V002 Cargo Utility</option>
                        <option value="V003">V003 Cargo Family</option>
                        <option value="V004">V004 Trekking</option>
                        </optgroup>
                        
                        <optgroup label="Sand">
                        <option value="V005">V005 City</option>
                        <option value="V006">V006 Cargo Utility</option>
                        <option value="V007">V007 Cargo Family</option>
                        <option value="V008">V008 Trekking</option>
                        </optgroup>

                        <optgroup label="Indigo">
                        <option value="V009">V009 City</option>
                        <option value="V010">V010 Cargo Utility</option>
                        <option value="V011">V011 Cargo Family</option>
                        <option value="V012">V012 Trekking</option>
                        </optgroup>

                        <optgroup label="Very Peri">
                        <option value="V013">V013 City</option>
                        <option value="V014">V014 Cargo Utility</option>
                        <option value="V015">V015 Family</option>
                        <option value="V016">V016 Trekking</option>
                        </optgroup>

                        <optgroup label="Ral Specifique">
                        <option value="V017">V017 City</option>
                        <option value="V018">V018 Cargo Utility</option>
                        <option value="V019">V019 Cargo Family</option>   
                        <option value="V020">V020 Trekking</option>
                        </optgroup>

                        <optgroup label="White">
                        <option value="V021">V021 City</option>
                        <option value="V022">V022 Cargo Utility</option>
                        <option value="V023">V023 Cargo Family</option>
                        <option value="V024">V024 Trekking</option>
                        </optgroup>

                        <optgroup label="Red Hot">
                        <option value="V025">V025 City</option>
                        <option value="V026">V026 Cargo Utility</option>
                        <option value="V027">V027 Cargo Family</option>
                        <option value="V028">V028 Trekking</option>
                        </optgroup>

                        <optgroup label="Vernis">
                        <option value="V029">V029 City</option>
                        <option value="V030">V030 Cargo Utility</option>
                        <option value="V031">V031 Cargo Family</option>
                        <option value="V032">V032 Trekking</option>
                        </optgroup>
                        
                    </select>
                </div>

                <div class="form-group">
                    <label for="batterie">Batterie:</label>
                    <select id="batterie" name="batterie" class="form-control">
                        <option value="">Sélectionner une batterie</option>
                        <option value="i500wh">i500wh</option>
                        <option value="i630wh">i630wh</option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="modele">Modèle:</label>
                    <select id="modele" name="modele" class="form-control" disabled>
                        <option value="">Sélectionner un modèle</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="couleur">Couleur:</label>
                    <select id="couleur" name="couleur" class="form-control" disabled>
                        <option value="">Sélectionner une couleur</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="option1">Option 1:</label>
                    <select id="option1" name="option1" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>

                    </select>
                </div>
                <div class="form-group">
                    <label for="option2">Option 2:</label>
                    <select id="option2" name="option2" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>

                    </select>
                </div>
                <div class="form-group">
                    <label for="option3">Option 3:</label>
                    <select id="option3" name="option3" class="form-control">
                    <option value="">Sélectionner une option</option>
                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="option4">Option 4:</label>
                    <select id="option4" name="option4" class="form-control">
                    <option value="">Sélectionner une option</option>

                     <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>

                    </select>
                </div>

                <div class="form-group">
                    <label for="option5">Option 5:</label>
                    <select id="option5" name="option5" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>

                    </select>
                </div>

                <div class="form-group">
                    <label for="option6">Option 6:</label>
                    <select id="option6" name="option6" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option7">Option 7:</label>
                    <select id="option7" name="option7" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>

                    </select>
                </div>

                <div class="form-group">
                    <label for="option8">Option 8:</label>
                    <select id="option8" name="option8" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option9">Option 9:</label>
                    <select id="option9" name="option9" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option10">Option 10:</label>
                    <select id="option10" name="option10" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option11">Option 11:</label>
                    <select id="option11" name="option11" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option12">Option 12:</label>
                    <select id="option12" name="option12" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option13">Option 13:</label>
                    <select id="option13" name="option13" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option14">Option 14:</label>
                    <select id="option14" name="option14" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option15">Option 15:</label>
                    <select id="option15" name="option15" class="form-control">
                    <option value="">Sélectionner une option</option>

                    <optgroup label="1.Guidons">

                    <option value="Guidon Hollandais">Guidon Hollandais</option>
                    <option value="Guidon Baramind">Guidon Baramind</option>

                    </optgroup>

                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +">Selle Italia Confort +</option>
                    <option value="Selle Italia Classique">Selle Italia Classique</option>

                    </optgroup>

                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide">Tige de selle rigide</option>
                    <option value="Tige de selle ajustable">Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg">Pneu Touareg</option>
                    <option value="Pneu Skeleton">Pneu Skeleton</option>
                    <option value="Pneu Overide">Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1">Potence Ultima V1</option>
                    <option value="Potence OneBox">Potence OneBox</option>
                    <option value="Potence Ergotec">Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima">Réhausse Ultima</option>
                    <option value="Réhausse Ergotec">Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag">Cargo soft bag</option>
                    <option value="Cargo caisse bois">Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon">Bidon et porte-bidon</option>
                    <option value="Attelage">Attelage</option>
                    <option value="Antivol">Antivol</option>
                    <option value="Béquille">Béquille</option>
                    <option value="Sabot">Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal">Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal">Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal">Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants">Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking">Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue">Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue">Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit">Garde-boue Kit</option>
                    <option value="Garde-boue Arrière">Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle">Sacoche individuelle</option>
                    <option value="Paire de sacoches">Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City">Fourche City</option>
                    <option value="Fourche Trekking">Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag">Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois">Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire">Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique">Gravure spécifique</option>
                    <option value="Covering">Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS">Tracker GPS</option>

                    </optgroup>
                    </select>
                </div>


                <!--div class="form-group">
                    <label for="date_bl">Date BL:</label>
                    <input type="date" id="date_bl" name="date_bl" class="form-control">
                </div>
                <div class="form-group">
                    <label for="payment">Paiement:</label>
                    <select id="payment" name="payment" class="form-control">
                        <option value="">Sélectionner un mode de paiement</option>
                        <?php foreach ($options_payment as $option): ?>
                            <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="facture_envoyee">Facture envoyée:</label>
                    <select id="facture_envoyee" name="facture_envoyee" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <?php foreach ($options_facture_envoyee as $option): ?>
                            <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div!-->
                
                
            </div>
            <div class="col-md-4">
            <div class="form-group">                    
                    <label  for="Totem_PLV">Totem PLV:</label>
                    <select id="Totem_PLV" name="Totem_PLV" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="✔">👍</option>
                        <option value="✘">👎</option>
                    </select>
                    </div>

                    <div class="form-group">                     
                    <label  for="Podium_PLV">Podium PLV:</label>
                    <select id="Podium_PLV" name="Podium_PLV" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="✔">👍</option>
                        <option value="✘">👎</option>
                    </select>
                    </div>
                    
                 
                    <div class="form-group">                     
                    <label  for="Catalogues">Catalogues:</label>
                    <select id="Catalogues" name="Catalogues" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="✔">👍</option>
                        <option value="✘">👎</option>
                    </select>
                    </div>
                <div class="form-group">
                    <label for="of_disponible">OF Disponible:</label>
                    <select id="of_disponible" name="of_disponible" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="Oui">Oui</option>
                        <option value="Non">Non</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-group">                     
                    <label  for="gravage">Gravage:</label>
                    <select id="gravage" name="gravage" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="Oui">Oui</option>
                        <option value="Non">Non</option>
                    </select>
                    </div>
                </div>
                <div class="form-group">
                <label for="montage">Montage:</label>
    <select id="montage" name="montage" class="form-control">
        <option value="">Sélectionner une option</option>
        <option value="EN COURS">En cours</option>
        <option value="Termine">Termine</option>
        
    </select>
                </div>
                <div class="form-group">
                    <label for="calibrage_mdu">Calibrage MDU:</label>
                    <input type="text" id="calibrage_mdu" name="calibrage_mdu" class="form-control">
                </div>
                <div class="form-group">
                    <label for="calibrage_hmi">Calibrage HMI:</label>
                    <input type="text" id="calibrage_hmi" name="calibrage_hmi" class="form-control">
                </div>
                <div class="form-group">
                    <label for="calibrage_bms">Calibrage BMS:</label>
                    <input type="text" id="calibrage_bms" name="calibrage_bms" class="form-control">
                </div>
                <div class="form-group">
                    <label for="calibrage_iot">Calibrage IoT:</label>
                    <input type="text" id="calibrage_iot" name="calibrage_iot" class="form-control">
                </div>
                <div class="form-group">
                    <label for="puk">PUK:</label>
                    <input type="text" id="puk" name="puk" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="generation_moteur">Génération moteur:</label>
                    <select id="generation_moteur" name="generation_moteur" class="form-control">
                        <option value="">Sélectionner une génération de moteur</option>
                        <option value="Gen 1">Gen 1</option>
                        <option value="Gen 2">Gen 2</option>
                        <option value="Gen 3">Gen 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="commentaire">Commentaire:</label>
                    <textarea id="commentaire" name="commentaire" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <div class="container">
    <div class="row">
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
        <div class="col-md-0 text-right">
            <a href="home.php" class="btn btn-primary">Retour</a>
        </div>
    </div>
</div>
    </form>
</div>

<script>
    // Écouteur d'événement pour la sélection de la configuration
    document.getElementById("configuration").addEventListener("change", function() {
        // Récupérer la valeur sélectionnée de la configuration
        var selectedConfiguration = this.value;
        
        // Mettre à jour les options du champ "Modèle" en fonction de la configuration sélectionnée
        var modeleSelect = document.getElementById("modele");
        modeleSelect.innerHTML = ""; // Effacer les options existantes
          // Mettre à jour les options du champ "Couleur" en fonction de la configuration sélectionnée
        var couleurSelect = document.getElementById("couleur");
        couleurSelect.innerHTML = ""; // Effacer les options existantes
        
        switch (selectedConfiguration) {
            case "V001":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Carbone'>Carbone</option>";
                break;
            case "V002":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Carbone'>Carbone</option>";
                break;
            
            case "V003":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Carbone'>Carbone</option>";
                break;

            case "V004":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Carbone'>Carbone</option>";
                break;

            case "V005":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Sand'>Sand</option>";
                break;
            case "V006":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Sand'>Sand</option>";
                break;
        
            case "V007":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Sand'>Sand</option>";
                break;

            case "V008":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Sand'>Sand</option>";
                break;

            case "V009":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Indigo'>Indigo</option>";
                break;
            case "V010":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Indigo'>Indigo</option>";
                break;
        
            case "V011":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Indigo'>Indigo</option>";
                break;

            case "V012":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Indigo'>Indigo</option>";
                break;

            case "V013":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Very Peri'>Very Peri</option>";
                break;
            case "V014":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Very Peri'>Very Peri</option>";
                break;
        
            case "V015":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Very Peri'>Very Peri</option>";
                break;

            case "V016":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Very Peri'>Very Peri</option>";
                break;

            case "V017":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Special'>Special</option>";
                break;
            case "V018":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Special'>Special</option>";
                break;
        
            case "V019":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Special'>Special</option>";
                break;

            case "V020":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Special'>Special</option>";
                break;


            case "V021":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='White'>White</option>";
                break;
            case "V022":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='White'>White</option>";
                break;
        
            case "V023":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='White'>White</option>";
                break;

            case "V024":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='White'>White</option>";
                break;
                
            case "V025":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Red Hot'>Red Hot</option>";
                break;
            case "V026":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Red Hot'>Red Hot</option>";
                break;
        
            case "V027":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Red Hot'>Red Hot</option>";
                break;

            case "V028":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Red Hot'>Red Hot</option>";
                break; 

            case "V029":
                modeleSelect.innerHTML = "<option value='City'>City</option>";
                couleurSelect.innerHTML = "<option value='Vernis'>Vernis</option>";
                break;
            case "V030":
                modeleSelect.innerHTML = "<option value='Cargo Urbain'>Cargo Urbain</option>";
                couleurSelect.innerHTML = "<option value='Vernis'>Vernis</option>";
                break;
        
            case "V031":
                modeleSelect.innerHTML = "<option value='Cargo Famille'>Cargo Famille</option>";
                couleurSelect.innerHTML = "<option value='Vernis'>Vernis</option>";
                break;

            case "V032":
                modeleSelect.innerHTML = "<option value='Trekking'>Trekking</option>";
                couleurSelect.innerHTML = "<option value='Vernis'>Vernis</option>";
                break;     
                
            // Ajoutez d'autres cas pour les autres configurations
        }
        
        // Activer ou désactiver le champ "Modèle" en fonction de la configuration sélectionnée
        modeleSelect.disabled = (modeleSelect.innerHTML === "");
         // Activer ou désactiver le champ "Couleur" en fonction de la configuration sélectionnée
        couleurSelect.disabled = (couleurSelect.innerHTML === "");
    });
</script>

<script>
    // Script pour afficher la notification d'enregistrement réussi
    <?php if(isset($_SESSION['success'])): ?>
    $(document).ready(function() {
        $('#successModal').modal('show');
    });
    <?php endif; ?>

    // Script pour afficher la notification d'erreur de saisie
    <?php if(isset($_SESSION['error'])): ?>
    $(document).ready(function() {
        $('#errorModal').modal('show');
    });
    <?php endif; ?>
</script>

<!-- Modals pour les notifications -->
<!-- Modal de succès -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Succès</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Enregistrement réussi !
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'erreur -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Erreur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Veuillez remplir tous les champs obligatoires.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
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

</body>

</html>