<?php
session_start();
require_once 'config.php';

// Vérifier si le numéro de vélo est passé en paramètre
if (!isset($_GET['numero_de_velo'])) {
    header('Location: mes_inventaires.php');
    die();
}

$numero_de_velo = htmlspecialchars($_GET['numero_de_velo']);

// Récupérer l'enregistrement correspondant
$req = $bdd->prepare('SELECT * FROM larrun_diag WHERE numero_de_velo = ?');
$req->execute(array($numero_de_velo));
$inventaire = $req->fetch(PDO::FETCH_ASSOC);

if (!$inventaire) {
    header('Location: mes_inventaires.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'inventaire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .fiche {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
        th {
            background: #f4f4f9;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .action-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .action-buttons button:hover {
            background: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="fiche">
        <h1>Détails de l'inventaire</h1>
        <table>
            <tr>
                <th>Numéro de vélo</th>
                <td><?= htmlspecialchars($inventaire['numero_de_velo']) ?></td>
            </tr>
            <tr>
                <th>Nom du modèle</th>
                <td><?= htmlspecialchars($inventaire['nom_du_modele']) ?></td>
            </tr>
            <tr>
                <th>Couleur</th>
                <td><?= htmlspecialchars($inventaire['couleur']) ?></td>
            </tr>
            <tr>
                <th>Vérifié par</th>
                <td><?= htmlspecialchars($inventaire['verifie_par']) ?></td>
            </tr>
            <tr>
                <th>Batterie état</th>
                <td><?= htmlspecialchars($inventaire['batterie_etat']) ?></td>
            </tr>
            <tr>
                <th>Passage de vitesse</th>
                <td><?= htmlspecialchars($inventaire['passage_de_vitesse']) ?></td>
            </tr>
            <tr>
                <th>Sonnette</th>
                <td><?= htmlspecialchars($inventaire['sonnette']) ?></td>
            </tr>
            <tr>
                <th>Alignement Garde-boue et Garde-chaîne</th>
                <td><?= htmlspecialchars($inventaire['alignement_garde_boue_et_garde_chaine']) ?></td>
            </tr>
            <tr>
                <th>État disque de frein</th>
                <td><?= htmlspecialchars($inventaire['etat_disque_de_frein']) ?></td>
            </tr>
            <tr>
                <th>État chaîne/cassette</th>
                <td><?= htmlspecialchars($inventaire['etat_chaine_cassette']) ?></td>
            </tr>
            <tr>
                <th>Pièces rouillées</th>
                <td><?= htmlspecialchars($inventaire['pieces_rouillees']) ?></td>
            </tr>
            <tr>
                <th>État câble et gaine dérailleur</th>
                <td><?= htmlspecialchars($inventaire['etat_cable_et_gaine_derailleur']) ?></td>
            </tr>
            <tr>
                <th>Diagnostique</th>
                <td><?= htmlspecialchars($inventaire['diagnostique']) ?></td>
            </tr>
        </table>
        <div class="action-buttons">
            <button onclick="window.print()">Imprimer</button>
            <button onclick="window.location.href = 'inventaire.php'">Retour</button>
        </div>
    </div>
</body>
</html>
