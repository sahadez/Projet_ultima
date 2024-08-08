<?php
session_start();
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des valeurs du formulaire
    $numero_de_velo = htmlspecialchars($_POST['numero_de_velo']);
    $nom_du_modele = htmlspecialchars($_POST['nom_du_modele']);
    $couleur = htmlspecialchars($_POST['couleur']);
    $verifie_par = htmlspecialchars($_POST['verifie_par']);
    $batterie_etat = htmlspecialchars($_POST['batterie_etat']);
    $passage_de_vitesse = htmlspecialchars($_POST['passage_de_vitesse']);
    $sonnette = htmlspecialchars($_POST['sonnette']);
    $alignement_garde_boue_et_garde_chaine = htmlspecialchars($_POST['alignement_garde_boue_et_garde_chaine']);
    $etat_disque_de_frein = htmlspecialchars($_POST['etat_disque_de_frein']);
    $etat_chaine_cassette = htmlspecialchars($_POST['etat_chaine_cassette']);
    $pieces_rouillees = htmlspecialchars($_POST['pieces_rouillees']);
    $etat_cable_et_gaine_derailleur = htmlspecialchars($_POST['etat_cable_et_gaine_derailleur']);
    $diagnostique = htmlspecialchars($_POST['diagnostique']);
    
    // Exécution de la requête
    $req = $bdd->prepare('INSERT INTO larrun_diag (numero_de_velo, nom_du_modele, couleur, verifie_par, batterie_etat, passage_de_vitesse, sonnette, alignement_garde_boue_et_garde_chaine, etat_disque_de_frein, etat_chaine_cassette, pieces_rouillees, etat_cable_et_gaine_derailleur, diagnostique) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $req->execute(array($numero_de_velo, $nom_du_modele, $couleur, $verifie_par, $batterie_etat, $passage_de_vitesse, $sonnette, $alignement_garde_boue_et_garde_chaine, $etat_disque_de_frein, $etat_chaine_cassette, $pieces_rouillees, $etat_cable_et_gaine_derailleur, $diagnostique));
    
    // Message de confirmation
    $message = "Inventaire enregistré avec succès.";
}

// Fonction pour récupérer le prochain numéro de vélo
function getNextNumeroDeVelo($bdd) {
    $stmt = $bdd->query('SELECT MAX(numero_de_velo) as max_numero FROM larrun_diag');
    $result = $stmt->fetch();
    return $result ? $result['max_numero'] + 1 : 1;
}

$nextNumeroDeVelo = getNextNumeroDeVelo($bdd);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaire | Larrun</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"], button, .button-group a {
            padding: 10px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: inline-block;
            width: 32%;
            text-align: center;
        }
        input[type="submit"] {
            background: #007bff;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        button {
            background: #dc3545;
        }
        button:hover {
            background: #c82333;
        }
        .confirmation-message {
            text-align: center;
            color: green;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        .button-group a {
            background: #28a745;
        }
        .button-group a:hover {
            background: #218838;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modelsColors = {
                "Citadelle": ["Non defini"],
                "Préville": ["Blanc", "Turquoise", "Gris"],
                "Bastide": ["Non defini"],
                "Beaumont": ["Non defini"],
                "Vélo Dame violet": ["Violet"],
                "Belscastel": ["Non defini"],
                "Kaléa": ["Orange", "Vert"],
                "Verdier ( Pliant )": ["Gris"],
                "Verdier pliant": ["Orange"],
                "pliant Autre": ["Non defini"]
            };

            const modeleSelect = document.getElementById('nom_du_modele');
            const couleurSelect = document.getElementById('couleur');
            const numeroDeVeloInput = document.getElementById('numero_de_velo');

            // Remplir la couleur automatiquement selon le modèle sélectionné
            modeleSelect.addEventListener('change', function() {
                const selectedModel = this.value;
                const colors = modelsColors[selectedModel] || [];
                couleurSelect.innerHTML = '';
                colors.forEach(color => {
                    const option = document.createElement('option');
                    option.value = color;
                    option.textContent = color;
                    couleurSelect.appendChild(option);
                });
                couleurSelect.disabled = colors.length === 0;
            });

            // Désactiver le champ numéro de vélo et définir la prochaine valeur
            numeroDeVeloInput.value = <?= $nextNumeroDeVelo ?>;
            numeroDeVeloInput.readOnly = true;

            // Effacer le formulaire
            document.getElementById('resetForm').addEventListener('click', function() {
                document.getElementById('veloForm').reset();
                numeroDeVeloInput.value = <?= $nextNumeroDeVelo ?>;
            });
        });
    </script>
</head>
<body>
    <h1>Inventaire Vélo complet Larrun</h1>
    <?php if ($message): ?>
        <div class="confirmation-message"><?= $message ?></div>
    <?php endif; ?>
    <form id="veloForm" method="POST">

        <label for="numero_de_velo">Numéro de vélo:</label><br>
        <input type="number" id="numero_de_velo" name="numero_de_velo" required><br><br>

        <label for="nom_du_modele">Nom du modèle:</label><br>
        <select id="nom_du_modele" name="nom_du_modele" required>
            <option value="">Sélectionnez un modèle</option>
            <option value="Citadelle">Citadelle</option>
            <option value="Préville">Préville</option>
            <option value="Bastide">Bastide</option>
            <option value="Beaumont">Beaumont</option>
            <option value="Vélo Dame violet">Vélo Dame violet</option>
            <option value="Belscastel">Belscastel</option>
            <option value="Kaléa">Kaléa</option>
            <option value="Verdier ( Pliant )">Verdier ( Pliant )</option>
            <option value="Verdier pliant">Verdier pliant</option>
            <option value="pliant Autre">pliant Autre</option>
        </select><br><br>

        <label for="couleur">Couleur:</label><br>
        <select id="couleur" name="couleur" required>
            <option value="">Sélectionnez une couleur</option>
        </select><br><br>

        <label for="verifie_par">Vérifié par:</label><br>
        <input type="text" id="verifie_par" name="verifie_par" required><br><br>

        <label for="batterie_etat">Batterie état:</label><br>
        <input type="text" id="batterie_etat" name="batterie_etat" required><br><br>

        <label for="passage_de_vitesse">Passage de vitesse:</label><br>
        <input type="text" id="passage_de_vitesse" name="passage_de_vitesse" required><br><br>

        <label for="sonnette">Sonnette:</label><br>
        <input type="text" id="sonnette" name="sonnette" required><br><br>

        <label for="alignement_garde_boue_et_garde_chaine">Alignement Garde-boue et Garde-chaîne:</label><br>
        <input type="text" id="alignement_garde_boue_et_garde_chaine" name="alignement_garde_boue_et_garde_chaine" required><br><br>

        <label for="etat_disque_de_frein">État disque de frein:</label><br>
        <input type="text" id="etat_disque_de_frein" name="etat_disque_de_frein" required><br><br>

        <label for="etat_chaine_cassette">État chaîne/cassette:</label><br>
        <input type="text" id="etat_chaine_cassette" name="etat_chaine_cassette" required><br><br>

        <label for="pieces_rouillees">Pièces rouillées:</label><br>
        <input type="text" id="pieces_rouillees" name="pieces_rouillees" required><br><br>

        <label for="etat_cable_et_gaine_derailleur">État câble et gaine dérailleur:</label><br>
        <input type="text" id="etat_cable_et_gaine_derailleur" name="etat_cable_et_gaine_derailleur" required><br><br>

        <label for="diagnostique">Diagnostique:</label><br>
        <textarea id="diagnostique" name="diagnostique" required></textarea><br><br>

        <div class="button-group">
            <input type="submit" value="Soumettre">
            <button type="button" id="resetForm">Annuler</button>
            <a href="inventaire.php">Mes inventaires</a>
        </div>
    </form>
</body>
</html>
