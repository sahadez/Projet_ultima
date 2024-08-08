<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location:index.php');
    die();
}

if (!isset($_GET['numero_ticket'])) {
    header('Location:sav.php');
    die();
}

$ticket_id = $_GET['numero_ticket'];

$req = $bdd->prepare('SELECT * FROM sav WHERE numero_ticket = ?');
$req->execute([$ticket_id]);
$ticket = $req->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bicycode = $_POST['bicycode'];
    $nom_client = $_POST['nom_client'];
    $date_prise_en_charge = $_POST['date_prise_en_charge'];
    $date_intervention = $_POST['date_intervention'];
    $intervenant = $_POST['intervenant'];
    $commentaire = $_POST['commentaire'];
    $date_cloture_ticket = $_POST['date_cloture_ticket'];
    $garantie_ou_facturation = $_POST['garantie_ou_facturation'];
    $status = $_POST['status'];
    $taches_realisees = $_POST['tache_realisee'];

    // Insérer les tâches à réaliser dans la table taches_a_realiser
//$taches_a_realiser = $_POST['tache_a_realiser'];
//foreach ($taches_a_realiser as $tache) {
  //  $stmt = $bdd->prepare("INSERT INTO taches_a_realiser (sav_id, tache) VALUES (?, ?)");
    //$stmt->execute([$ticket_id, $tache]);
//}

// Insérer les tâches réalisées dans la table taches_realisees
$taches_realisees = $_POST['tache_realisee'];
foreach ($taches_realisees as $tache) {
    $stmt = $bdd->prepare("INSERT INTO taches_realisees (sav_id, tache) VALUES (?, ?)");
    $stmt->execute([$ticket_id, $tache]);
}


    // Mise à jour des données dans la table SAV
    $stmt = $bdd->prepare("UPDATE sav SET bicycode = ?, nom_client = ?, date_prise_en_charge = ?, date_intervention = ?, intervenant = ?, commentaire = ?, date_cloture_ticket = ?, garantie_ou_facturation = ?, status = ? WHERE numero_ticket = ?");
    $stmt->execute([$bicycode, $nom_client, $date_prise_en_charge, $date_intervention, $intervenant, $commentaire, $date_cloture_ticket, $garantie_ou_facturation, $status, $ticket_id]);

    header('Location: fiche_sav.php?ticket_id=' . $ticket_id);
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Modifier un ticket SAV</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
          crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="col-md-12">
        <h2>Modifier un ticket SAV</h2>
        <form method="post">
            <div class="form-group">
                <label for="bicycode">Bicycode :</label>
                <input type="text" class="form-control" id="bicycode" name="bicycode" required
                       value="<?php echo $ticket['bicycode']; ?>">
            </div>

            <div class="form-group">
                <label for="nom_client">Nom du client :</label>
                <input type="text" class="form-control" id="nom_client" name="nom_client" required
                       value="<?php echo $ticket['nom_client']; ?>">
            </div>

            <div class="form-group">
                <label for="date_prise_en_charge">Date de prise en charge :</label>
                <input type="date" class="form-control" id="date_prise_en_charge" name="date_prise_en_charge" required
                       value="<?php echo $ticket['date_prise_en_charge']; ?>">
            </div>

            <div class="form-group">
                <label for="date_intervention">Date d'intervention :</label>
                <input type="date" class="form-control" id="date_intervention" name="date_intervention" required
                       value="<?php echo $ticket['date_intervention']; ?>">
            </div>

            <!-- Les champs des tâches à réaliser 
             <div id="taches_a_realiser">
                <div class="form-group">
                    <label for="tache_a_realiser_1">Tâche à réaliser :</label>
                    <input type="text" class="form-control" id="tache_a_realiser_1" name="tache_a_realiser[]" required>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addTacheARealiser()">+</button> -->



            <!-- Les champs des tâches réalisées -->
          <!-- Récupération de la tâche à réaliser depuis la base de données -->
<?php
$stmt = $bdd->prepare('SELECT * FROM taches_a_realiser WHERE sav_id = ?');
$stmt->execute([$ticket['numero_ticket']]);
$taches_a_realiser = $stmt->fetchAll();

// Affichage des champs de saisie pour chaque tâche à réaliser
foreach ($taches_a_realiser as $tache) {
    echo '<div class="form-group">';
    echo '<label for="tache_a_realiser">Tâche à réaliser :</label>';
    echo '<input type="text" class="form-control" name="tache_a_realiser" value="' . htmlspecialchars($tache['tache']) . '" required>';
    echo '</div>';
}
?>


            <div id="taches_realisees">
                <div class="form-group">
                    <label for="tache_realisee_1">Tâche réalisée :</label>
                    <input type="text" class="form-control" id="tache_realisee_1" name="tache_realisee[]">
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addTacheRealisee()">Ajouter une tâche</button>
        
            <div class="form-group">
                <label for="intervenant">Intervenant :</label>
                <select class="form-control" id="intervenant" name="intervenant" required>
                    <option value="">Sélectionner un intervenant</option>
                    <option value="Romann" <?php if ($ticket['intervenant'] == 'Romann') echo 'selected'; ?>>Romann
                    </option>
                    <option value="Jules" <?php if ($ticket['intervenant'] == 'Jules') echo 'selected'; ?>>Jules
                    </option>
                    <option value="Baptiste" <?php if ($ticket['intervenant'] == 'Baptiste') echo 'selected'; ?>>
                        Baptiste
                    </option>
                    <option value="Mathis" <?php if ($ticket['intervenant'] == 'Mathis') echo 'selected'; ?>>Mathis
                    </option>
                    <option value="Gregorie" <?php if ($ticket['intervenant'] == 'Gregorie') echo 'selected'; ?>>
                        Gregory
                    </option>
                    <option value="Bruno" <?php if ($ticket['intervenant'] == 'Bruno') echo 'selected'; ?>>Bruno
                    </option>
                    <option value="Pascal" <?php if ($ticket['intervenant'] == 'Pascal') echo 'selected'; ?>>Pascal
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="commentaire">Commentaire :</label>
                <textarea class="form-control" id="commentaire" name="commentaire"
                          rows="3"><?php echo $ticket['commentaire']; ?></textarea>
            </div>

           

            <div class="form-group">
                <label for="date_cloture_ticket">Date de clôture du ticket :</label>
                <input type="date" class="form-control" id="date_cloture_ticket" name="date_cloture_ticket" required
                       value="<?php echo $ticket['date_cloture_ticket']; ?>">
            </div>

            <div class="form-group">
                <label for="garantie_ou_facturation">Garantie ou facturation :</label>
                <select class="form-control" id="garantie_ou_facturation" name="garantie_ou_facturation" required>
                    <option value="Garantie" <?php if ($ticket['garantie_ou_facturation'] == 'Garantie') echo 'selected'; ?>>
                        Garantie
                    </option>
                    <option value="Facturation" <?php if ($ticket['garantie_ou_facturation'] == 'Facturation') echo 'selected'; ?>>
                        Facturation
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Statut :</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="En cours" <?php if ($ticket['status'] == 'En cours') echo 'selected'; ?>>En cours</option>
                    <option value="Terminé" <?php if ($ticket['status'] == 'Terminé') echo 'selected'; ?>>Terminé</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="fiche_sav.php?ticket_id=<?php echo $ticket_id; ?>" class="btn btn-secondary ml-2">Annuler</a>
            </div>
        </form>
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

 <!--  fonctions JavaScript d'ajout de tâches -->
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

        function addTacheRealisee() {
            countTacheRealisee++;
            var tacheDiv = document.createElement('div');
            tacheDiv.className = 'form-group';
            tacheDiv.innerHTML = '<label for="tache_realisee_' + countTacheRealisee + '">Tâche réalisée :</label>' +
                '<input type="text" class="form-control" id="tache_realisee_' + countTacheRealisee + '" name="tache_realisee[]" required>';

            document.getElementById('taches_realisees').appendChild(tacheDiv);
        }
    </script>
</body>
</html>
