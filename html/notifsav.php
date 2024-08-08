<?php
require_once 'config.php'; // Ajout de la connexion à la base de données

// Récupérez les tickets SAV en cours avec les tâches associées depuis la base de données
$req_sav_en_cours = $bdd->prepare('SELECT bicycode, nom_client, numero_ticket FROM sav WHERE status = "EN COURS"');
$req_sav_en_cours->execute();
$sav_en_cours_data = $req_sav_en_cours->fetchAll(PDO::FETCH_ASSOC);

// Construisez le contenu du message e-mail
$message = "Liste des tickets SAV en cours :\n\n";
foreach ($sav_en_cours_data as $ticket) {
    $message .= "Numéro du ticket : " . $ticket['numero_ticket'] . "\n";
    $message .= "Bicycode : " . $ticket['bicycode'] . "\n";
    $message .= "Nom du client : " . $ticket['nom_client'] . "\n";

    // Vérifier si des tâches à réaliser sont disponibles pour ce ticket
    $stmt = $bdd->prepare('SELECT tache FROM taches_a_realiser WHERE sav_id = ?');
    $stmt->execute([$ticket['numero_ticket']]);
    $taches_a_realiser = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($taches_a_realiser)) {
        $message .= "Tâches à réaliser :\n";
        foreach ($taches_a_realiser as $tache) {
            $message .= "- " . $tache . "\n";
        }
    }

    // Ajoutez deux lignes vides pour créer un espace de 2 lignes entre chaque ticket
    $message .= "\n\n";
}

// Adresse e-mail de destination
$to = 'support@ultima.dev';

// Sujet de l'e-mail
$subject = 'Notification des tickets SAV en cours';

// En-têtes de l'e-mail
$headers = 'From: theo.colombies@ultima.dev' . "\r\n" .
    'Reply-To: theo.colombies@ultima.dev' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

// Envoyez l'e-mail
$mail_sent = mail($to, $subject, $message, $headers);

if ($mail_sent) {
    echo "Notification envoyée avec succès.";
} else {
    echo "Échec de l'envoi de la notification.";
}
?>
