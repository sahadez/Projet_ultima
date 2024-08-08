<?php
session_start();
require_once 'config.php';

// Récupérer tous les enregistrements
$req = $bdd->prepare('SELECT numero_de_velo, nom_du_modele, diagnostique FROM larrun_diag');
$req->execute();
$inventaires = $req->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Inventaires</title>
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
        .inventaire {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .inventaire h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }
        .inventaire p {
            margin: 5px 0;
        }
        .action {
            text-align: right;
            margin-top: 10px;
        }
        .action a {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            text-decoration: none;
            background: #007bff;
            display: inline-block;
        }
        .action a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Mes Inventaires</h1>
    <?php foreach ($inventaires as $inventaire): ?>
    <div class="inventaire">
        <h2><?= htmlspecialchars($inventaire['nom_du_modele']) ?></h2>
        <p><strong>Numéro de vélo:</strong> <?= htmlspecialchars($inventaire['numero_de_velo']) ?></p>
        <p><strong>Diagnostique:</strong> <?= htmlspecialchars($inventaire['diagnostique']) ?></p>
        <div class="action">
            <a href="fiche.php?numero_de_velo=<?= htmlspecialchars($inventaire['numero_de_velo']) ?>">Voir la fiche</a>
        </div>
    </div>
    <?php endforeach; ?>
    <div class="action">
        <a href="diag.php">Retour</a>
    </div>
</body>
</html>
