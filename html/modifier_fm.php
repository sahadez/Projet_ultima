<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$data = $req->fetch();

// Récupération des options pour les listes déroulantes
// TODO: remplacer par les requêtes appropriées pour récupérer les options
$existingBicycodes = $bdd->query('SELECT bicycode FROM multipath')->fetchAll(PDO::FETCH_COLUMN);
$options_bicycode = $bdd->query('SELECT num_bicycode FROM bicycode')->fetchAll(PDO::FETCH_COLUMN);
$options_payment = ['Acompte', 'Total'];
$options_facture_envoyee = ['oui', 'non'];

// Récupération des informations du vélo à modifier
$bicycode = $_GET['bicycode'] ?? '';
$stmt = $bdd->prepare('SELECT * FROM multipath WHERE bicycode = ?');
$stmt->execute([$bicycode]);
$multipath_info = $stmt->fetch();

// Récupération de la date de fin de montage depuis la base de données
$date_bl = $multipath_info['date_bl'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fields = [
        'gravage',
        'montage',
        'calibrage_mdu',
        'calibrage_hmi',
        'calibrage_bms',
        'calibrage_iot',
        'puk',
        'generation_moteur',
        'commentaire',
        'nom_client',
        'prenom_client',
        'option9',
        'option10',
    ];
    $values = array_map(function ($field) {
        return $_POST[$field] ?? null;
    }, $fields);

    $placeholders = implode(' = ?, ', $fields) . ' = ?';
    $values[] = $bicycode;

    // Vérifier si le montage est déjà terminé
    if ($multipath_info['montage'] != 'Terminé') {
        $sql = "UPDATE multipath SET $placeholders WHERE bicycode = ?";
        $stmt = $bdd->prepare($sql);
        $stmt->execute($values);
    }

    // Vérifier si la date de fin de montage a été modifiée
    if ($date_bl !== ($multipath_info['date_bl'] ?? '')) {
        // Mettre à jour la base de données avec la nouvelle valeur
        $sql = "UPDATE multipath SET date_bl = ? WHERE bicycode = ?";
        $stmt = $bdd->prepare($sql);
        $stmt->execute([$date_bl, $bicycode]);
    }

    // Rediriger l'utilisateur vers la page de détails du vélo après la modification
    header("Location: fiche_velo.php?bicycode=$bicycode");
    exit;
}
?>

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
    <h3>Modifier Multipath</h3>
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
                    <label for="bicycode">Bicycode:</label>
                    <input type="text" id="bicycode" name="bicycode" class="form-control" value="<?php echo $multipath_info['bicycode']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="nom_client">Nom du Client:</label>
                    <input type="text" id="nom_client" name="nom_client" class="form-control" value="<?php echo $multipath_info['nom_client']; ?>">
                </div>

                <div class="form-group">
                    <label for="prenom_client">Prenom du Client:</label>
                    <input type="text" id="prenom_client" name="prenom_client" class="form-control" value="<?php echo $multipath_info['prenom_client']; ?>">
                </div>

                <div class="form-group">
                    <label for="gravage">Gravage:</label>
                    <select id="gravage" name="gravage" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="Oui" <?php echo ($multipath_info['gravage'] == 'Oui') ? 'selected' : ''; ?>>Oui</option>
                        <option value="Non" <?php echo ($multipath_info['gravage'] == 'Non') ? 'selected' : ''; ?>>Non</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_bl">Date Fin de Montage:</label>
                    <input type="date" id="date_bl" name="date_bl" class="form-control" value="<?php echo htmlspecialchars($date_bl); ?>" <?php echo isset($multipath_info['date_bl']) ? 'readonly' : ''; ?>>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="montage">Montage et Control qualité:</label>
                    <select id="montage" name="montage" class="form-control">
                        <option value="">Sélectionner une option</option>
                        <option value="EN COURS" <?php echo ($multipath_info['montage'] == 'EN COURS') ? 'selected' : ''; ?>>En cours</option>
                        <option value="Termine" <?php echo ($multipath_info['montage'] == 'Termine') ? 'selected' : ''; ?>>Termine</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="calibrage_mdu">Calibrage MDU:</label>
                    <input type="text" id="calibrage_mdu" name="calibrage_mdu" class="form-control" value="<?php echo $multipath_info['calibrage_mdu']; ?>">
                </div>
                <div class="form-group">
                    <label for="calibrage_hmi">Calibrage HMI:</label>
                    <input type="text" id="calibrage_hmi" name="calibrage_hmi" class="form-control" value="<?php echo $multipath_info['calibrage_hmi']; ?>">
                </div>
                <div class="form-group">
                <label for="option9">Option supplémentaire:</label>
                <select id="option9" name="option9" class="form-control">
                    <option value="">Sélectionner une option</option>
                    <optgroup label="1.Guidons">
                    <option value="Guidon Hollandais" <?php echo ($multipath_info['option9'] == 'Guidon Hollandais') ? 'selected' : ''; ?>>Guidon Hollandais</option>
                    <option value="Guidon Baramind" <?php echo ($multipath_info['option9'] == 'Guidon Baramind') ? 'selected' : ''; ?>>Guidon Baramind</option>
                    </optgroup>
                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +"<?php echo ($multipath_info['option9'] == 'Selle Italia Confort +') ? 'selected' : ''; ?>>Selle Italia Confort +</option>
                    <option value="Selle Italia Classique"<?php echo ($multipath_info['option9'] == 'Selle Italia Classique') ? 'selected' : ''; ?>>Selle Italia Classique</option>

                    </optgroup>
                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide"<?php echo ($multipath_info['option9'] == 'Tige de selle rigide') ? 'selected' : ''; ?>>Tige de selle rigide</option>
                    <option value="Tige de selle ajustable"<?php echo ($multipath_info['option9'] == 'Tige de selle ajustable') ? 'selected' : ''; ?>>Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg"<?php echo ($multipath_info['option9'] == 'Pneu Touareg') ? 'selected' : ''; ?>>Pneu Touareg</option>
                    <option value="Pneu Skeleton"<?php echo ($multipath_info['option9'] == 'Pneu Skeleton') ? 'selected' : ''; ?>>Pneu Skeleton</option>
                    <option value="Pneu Overide"<?php echo ($multipath_info['option9'] == 'Pneu Overide') ? 'selected' : ''; ?>>Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1"<?php echo ($multipath_info['option9'] == 'Potence Ultima V1') ? 'selected' : ''; ?>>Potence Ultima V1</option>
                    <option value="Potence OneBox"<?php echo ($multipath_info['option9'] == 'Potence OneBox') ? 'selected' : ''; ?>>Potence OneBox</option>
                    <option value="Potence Ergotec"<?php echo ($multipath_info['option9'] == 'Potence Ergotec') ? 'selected' : ''; ?>>Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima"<?php echo ($multipath_info['option9'] == 'Réhausse Ultima') ? 'selected' : ''; ?>>Réhausse Ultima</option>
                    <option value="Réhausse Ergotec"<?php echo ($multipath_info['option9'] == 'Réhausse Ergotec') ? 'selected' : ''; ?>>Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag"<?php echo ($multipath_info['option9'] == 'Cargo soft bag') ? 'selected' : ''; ?>>Cargo soft bag</option>
                    <option value="Cargo caisse bois"<?php echo ($multipath_info['option9'] == 'Cargo caisse bois') ? 'selected' : ''; ?>>Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon"<?php echo ($multipath_info['option9'] == 'Bidon et prote-bidon') ? 'selected' : ''; ?>>Bidon et porte-bidon</option>
                    <option value="Attelage"<?php echo ($multipath_info['option9'] == 'Attelage') ? 'selected' : ''; ?>>Attelage</option>
                    <option value="Antivol"<?php echo ($multipath_info['option9'] == 'Antivol') ? 'selected' : ''; ?>>Antivol</option>
                    <option value="Béquille"<?php echo ($multipath_info['option9'] == 'Béquille') ? 'selected' : ''; ?>>Béquille</option>
                    <option value="Sabot"<?php echo ($multipath_info['option9'] == 'Sabot') ? 'selected' : ''; ?>>Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal"<?php echo ($multipath_info['option9'] == 'Rétrovisseur Droit Zéfal') ? 'selected' : ''; ?>>Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal"<?php echo ($multipath_info['option9'] == 'Rétroviseur Gauche Zéfal') ? 'selected' : ''; ?>>Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal"<?php echo ($multipath_info['option9'] == 'Kit Rétroviseur Zefal') ? 'selected' : ''; ?>>Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants"<?php echo ($multipath_info['option9'] == 'Kit Rétroviseur clignotants') ? 'selected' : ''; ?>>Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking"<?php echo ($multipath_info['option9'] == 'Porte sacoches Trekking') ? 'selected' : ''; ?>>Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue"<?php echo ($multipath_info['option9'] == 'Porte bagage 27kg avec garde-boue') ? 'selected' : ''; ?>>Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue"<?php echo ($multipath_info['option9'] == 'Porte bagage 27kg sans garde-boue') ? 'selected' : ''; ?>>Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit"<?php echo ($multipath_info['option9'] == 'Garde-boue Kit') ? 'selected' : ''; ?>>Garde-boue Kit</option>
                    <option value="Garde-boue Arrière"<?php echo ($multipath_info['option9'] == 'Garde-boue Arrière') ? 'selected' : ''; ?>>Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle"<?php echo ($multipath_info['option9'] == 'Sacoche individuelle') ? 'selected' : ''; ?>>Sacoche individuelle</option>
                    <option value="Paire de sacoches"<?php echo ($multipath_info['option9'] == 'Paire de sacoches') ? 'selected' : ''; ?>>Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City"<?php echo ($multipath_info['option9'] == 'Fourche City') ? 'selected' : ''; ?>>Fourche City</option>
                    <option value="Fourche Trekking"<?php echo ($multipath_info['option9'] == 'Fourche Trekking') ? 'selected' : ''; ?>>Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag"<?php echo ($multipath_info['option9'] == 'Fourche Cargo soft bag') ? 'selected' : ''; ?>>Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois"<?php echo ($multipath_info['option9'] == 'Fourche Cargo caisse bois') ? 'selected' : ''; ?>>Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire"<?php echo ($multipath_info['option9'] == 'Siège Family supplémentaire') ? 'selected' : ''; ?>>Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique"<?php echo ($multipath_info['option9'] == 'Gravure spécifique') ? 'selected' : ''; ?>>Gravure spécifique</option>
                    <option value="Covering"<?php echo ($multipath_info['option9'] == 'Covering') ? 'selected' : ''; ?>>Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS"<?php echo ($multipath_info['option9'] == 'Tracker GPS') ? 'selected' : ''; ?>>Tracker GPS</option>

                    </optgroup>

    </select>
</div>
<div class="form-group">
                <label for="option10">Option supplémentaire:</label>
                <select id="option10" name="option10" class="form-control">
                    <option value="">Sélectionner une option</option>
                    <optgroup label="1.Guidons">
                    <option value="Guidon Hollandais" <?php echo ($multipath_info['option10'] == 'Guidon Hollandais') ? 'selected' : ''; ?>>Guidon Hollandais</option>
                    <option value="Guidon Baramind" <?php echo ($multipath_info['option10'] == 'Guidon Baramind') ? 'selected' : ''; ?>>Guidon Baramind</option>
                    </optgroup>
                    <optgroup label="2.Selles">

                    <option value="Selle Italia Confort +"<?php echo ($multipath_info['option10'] == 'Selle Italia Confort +') ? 'selected' : ''; ?>>Selle Italia Confort +</option>
                    <option value="Selle Italia Classique"<?php echo ($multipath_info['option10'] == 'Selle Italia Classique') ? 'selected' : ''; ?>>Selle Italia Classique</option>

                    </optgroup>
                    <optgroup label="3.Tige de selle">

                    <option value="Tige de selle rigide"<?php echo ($multipath_info['option10'] == 'Tige de selle rigide') ? 'selected' : ''; ?>>Tige de selle rigide</option>
                    <option value="Tige de selle ajustable"<?php echo ($multipath_info['option10'] == 'Tige de selle ajustable') ? 'selected' : ''; ?>>Tige de selle ajustable</option>
				
                    </optgroup>

                    <optgroup label="4.Pneu">
                    <option value="Pneu Touareg"<?php echo ($multipath_info['option10'] == 'Pneu Touareg') ? 'selected' : ''; ?>>Pneu Touareg</option>
                    <option value="Pneu Skeleton"<?php echo ($multipath_info['option10'] == 'Pneu Skeleton') ? 'selected' : ''; ?>>Pneu Skeleton</option>
                    <option value="Pneu Overide"<?php echo ($multipath_info['option10'] == 'Pneu Overide') ? 'selected' : ''; ?>>Pneu Overide</option>

                    </optgroup>

                    <optgroup label="5.Potences">

                    <option value="Potence Ultima V1"<?php echo ($multipath_info['option10'] == 'Potence Ultima V1') ? 'selected' : ''; ?>>Potence Ultima V1</option>
                    <option value="Potence OneBox"<?php echo ($multipath_info['option10'] == 'Potence OneBox') ? 'selected' : ''; ?>>Potence OneBox</option>
                    <option value="Potence Ergotec"<?php echo ($multipath_info['option10'] == 'Potence Ergotec') ? 'selected' : ''; ?>>Potence Ergotec</option>

                    </optgroup>
 
                    <optgroup label="6.Réhausses">

                    <option value="Réhausse Ultima"<?php echo ($multipath_info['option10'] == 'Réhausse Ultima') ? 'selected' : ''; ?>>Réhausse Ultima</option>
                    <option value="Réhausse Ergotec"<?php echo ($multipath_info['option10'] == 'Réhausse Ergotec') ? 'selected' : ''; ?>>Réhausse Ergotec</option>

                    </optgroup>

                    <optgroup label="7.Cargo">

                    <option value="Cargo soft bag"<?php echo ($multipath_info['option10'] == 'Cargo soft bag') ? 'selected' : ''; ?>>Cargo soft bag</option>
                    <option value="Cargo caisse bois"<?php echo ($multipath_info['option10'] == 'Cargo caisse bois') ? 'selected' : ''; ?>>Cargo caisse bois</option>

                    </optgroup>

                    <optgroup label="8.Accessoires">

                    <option value="Bidon et porte-bidon"<?php echo ($multipath_info['option10'] == 'Bidon et prote-bidon') ? 'selected' : ''; ?>>Bidon et porte-bidon</option>
                    <option value="Attelage"<?php echo ($multipath_info['option10'] == 'Attelage') ? 'selected' : ''; ?>>Attelage</option>
                    <option value="Antivol"<?php echo ($multipath_info['option10'] == 'Antivol') ? 'selected' : ''; ?>>Antivol</option>
                    <option value="Béquille"<?php echo ($multipath_info['option10'] == 'Béquille') ? 'selected' : ''; ?>>Béquille</option>
                    <option value="Sabot"<?php echo ($multipath_info['option10'] == 'Sabot') ? 'selected' : ''; ?>>Sabot</option>

                    </optgroup>

                    <optgroup label="9.Rétroviseurs">

                    <option value="Rétroviseur Droit Zéfal"<?php echo ($multipath_info['option10'] == 'Rétrovisseur Droit Zéfal') ? 'selected' : ''; ?>>Rétroviseur Droit Zéfal</option>
                    <option value="Rétroviseur Gauche Zéfal"<?php echo ($multipath_info['option10'] == 'Rétroviseur Gauche Zéfal') ? 'selected' : ''; ?>>Rétroviseur Gauche Zéfal</option>
                    <option value="Kit Rétroviseur Zefal"<?php echo ($multipath_info['option10'] == 'Kit Rétroviseur Zefal') ? 'selected' : ''; ?>>Kit Rétroviseur Zefal</option>
                    <option value="Kit Rétroviseur clignotants"<?php echo ($multipath_info['option10'] == 'Kit Rétroviseur clignotants') ? 'selected' : ''; ?>>Kit Rétroviseur clignotants</option>

                    </optgroup>

                    <optgroup label="10.Garde-boues">

                    <option value="Porte sacoches Trekking"<?php echo ($multipath_info['option10'] == 'Porte sacoches Trekking') ? 'selected' : ''; ?>>Porte sacoches Trekking</option>
                    <option value="Porte bagage 27kg avec garde-boue"<?php echo ($multipath_info['option10'] == 'Porte bagage 27kg avec garde-boue') ? 'selected' : ''; ?>>Porte bagage 27kg avec garde-boue</option>
                    <option value="Porte bagage 27kg sans garde-boue"<?php echo ($multipath_info['option10'] == 'Porte bagage 27kg sans garde-boue') ? 'selected' : ''; ?>>Porte bagage 27kg sans garde-boue</option>
                    <option value="Garde-boue Kit"<?php echo ($multipath_info['option10'] == 'Garde-boue Kit') ? 'selected' : ''; ?>>Garde-boue Kit</option>
                    <option value="Garde-boue Arrière"<?php echo ($multipath_info['option10'] == 'Garde-boue Arrière') ? 'selected' : ''; ?>>Garde-boue Arrière</option>

                    </optgroup>

                    <optgroup label="11.Sacoches">

                    <option value="Sacoche individuelle"<?php echo ($multipath_info['option10'] == 'Sacoche individuelle') ? 'selected' : ''; ?>>Sacoche individuelle</option>
                    <option value="Paire de sacoches"<?php echo ($multipath_info['option10'] == 'Paire de sacoches') ? 'selected' : ''; ?>>Paire de sacoches</option>

                    </optgroup>

                    <optgroup label="12.Pièces supplémentaires">

                    <option value="Fourche City"<?php echo ($multipath_info['option10'] == 'Fourche City') ? 'selected' : ''; ?>>Fourche City</option>
                    <option value="Fourche Trekking"<?php echo ($multipath_info['option10'] == 'Fourche Trekking') ? 'selected' : ''; ?>>Fourche Trekking</option>
                    <option value="Fourche Cargo soft bag"<?php echo ($multipath_info['option10'] == 'Fourche Cargo soft bag') ? 'selected' : ''; ?>>Fourche Cargo soft bag</option>
                    <option value="Fourche Cargo caisse bois"<?php echo ($multipath_info['option10'] == 'Fourche Cargo caisse bois') ? 'selected' : ''; ?>>Fourche Cargo caisse bois</option>
                    <option value="Siège Family supplémentaire"<?php echo ($multipath_info['option10'] == 'Siège Family supplémentaire') ? 'selected' : ''; ?>>Siège Family supplémentaire</option>

                    </optgroup>

                    <optgroup label="13.Personnalisation">

                    <option value="Gravure spécifique"<?php echo ($multipath_info['option10'] == 'Gravure spécifique') ? 'selected' : ''; ?>>Gravure spécifique</option>
                    <option value="Covering"<?php echo ($multipath_info['option10'] == 'Covering') ? 'selected' : ''; ?>>Covering</option>

                    </optgroup>

                    <optgroup label="14.Gps">

                    <option value="Tracker GPS"<?php echo ($multipath_info['option10'] == 'Tracker GPS') ? 'selected' : ''; ?>>Tracker GPS</option>

                    </optgroup>

    </select>
</div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="calibrage_bms">Calibrage BMS:</label>
                    <input type="text" id="calibrage_bms" name="calibrage_bms" class="form-control" value="<?php echo $multipath_info['calibrage_bms']; ?>">
                </div>
                <div class="form-group">
                    <label for="calibrage_iot">Calibrage IoT:</label>
                    <input type="text" id="calibrage_iot" name="calibrage_iot" class="form-control" value="<?php echo $multipath_info['calibrage_iot']; ?>">
                </div>
                <div class="form-group">
                    <label for="puk">PUK:</label>
                    <input type="text" id="puk" name="puk" class="form-control" value="<?php echo $multipath_info['puk']; ?>">
                </div>
                <div class="form-group">
                    <label for="generation_moteur">Génération moteur:</label>
                    <select id="generation_moteur" name="generation_moteur" class="form-control">
                        <option value="">Sélectionner une génération de moteur</option>
                        <option value="Gen 1" <?php echo ($multipath_info['generation_moteur'] == 'Gen 1') ? 'selected' : ''; ?>>Gen 1</option>
                        <option value="Gen 2" <?php echo ($multipath_info['generation_moteur'] == 'Gen 2') ? 'selected' : ''; ?>>Gen 2</option>
                        <option value="Gen 3" <?php echo ($multipath_info['generation_moteur'] == 'Gen 3') ? 'selected' : ''; ?>>Gen 3</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="calibrage_hmi">Commentaire:</label>
                    <input type="text" id="commentaire" name="commentaire" class="form-control" value="<?php echo $multipath_info['commentaire']; ?>">
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
                <div class="col-md-0 text-right">
                    <a href="fiche_velo.php?bicycode=<?php echo $bicycode; ?>" class="btn btn-primary">Annuler</a>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>
