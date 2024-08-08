<?php

// Ignorer le timeout de session
session_set_cookie_params(0); // 0 signifie que la session ne timeout pas
    require_once 'config.php';

    // Récupération des données de la base de données
    $req = $bdd->prepare('SELECT * FROM multipath');
    $req->execute();
    $rows = $req->fetchAll(PDO::FETCH_ASSOC);

    // Entête du fichier CSV
    $filename = 'export.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);

    // Ouverture du fichier de sortie
    $output = fopen('php://output', 'w');

    // En-têtes de colonne
    $columns = [
        'bike_maker',
        'assembly_date',
        'frame_serial_number',
        'reference',
        'unique_marking_number',
        'brand',
        'modele',
        'type',
        'color',
        'display_BLE_present',
        'bike_unique_id_by_customer'
    ];
    fputcsv($output, $columns);

    // Écriture des données dans le fichier CSV
    foreach ($rows as $row) {
        $data = [
            $row['bike_maker'],
            $row['date_creation'],
            $row['frame_serial_number'],
            $row['reference'],
            $row['unique_marking_number'],
            $row['brand'],
            $row['modele'],
            $row['type'],
            $row['couleur'],
            $row['display_BLE_present'],
            $row['bike_unique_id_by_customer']
        ];
        fputcsv($output, $data);
    }

    // Fermeture du fichier de sortie
    fclose($output);
    exit;
?>
