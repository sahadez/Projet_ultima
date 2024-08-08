<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST['search'];

    if (!empty($search)) {  // Vérifie que la chaîne de recherche n'est pas vide
        //connexion à la base de données
        require 'config.php';
        $req = $bdd->prepare("SELECT * FROM multipath WHERE bicycode LIKE :search OR nom_client LIKE :search OR prenom_client LIKE :search OR numero_commande LIKE :search OR calibrage_mdu LIKE :calibrage_mdu");
        $req->execute(array(":search"=>"%$search%", ":calibrage_mdu"=>"%$search%"));

        if ($req->rowCount() > 0) { 
            $data = $req->fetch();
            $bicycode = $data['bicycode'];
            header("Location: fiche_velo.php?bicycode=$bicycode");
            exit;
        } else {
            echo "<div class='text-center'>";
            echo "<h4>Aucune correspondance trouvée pour : " . htmlspecialchars($search) . "</h4>";
            echo "<p style='font-size: 50px;'>🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<p style='font-size: 50px;'>&ensp;🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<p style='font-size: 50px;'>&ensp;&ensp;🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<p style='font-size: 50px;'>&ensp;&ensp;&ensp;🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<p style='font-size: 50px;'>&ensp;&ensp;&ensp;&ensp;🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<p style='font-size: 50px;'>&ensp;&ensp;&ensp;&ensp;&ensp;🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<p style='font-size: 50px;'>&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;🚴‍♂️ &ensp;🚴‍♀️</p>"; // Emoji représentant une personne qui pédale
            echo "<h4>Je vous rattraperai grâce à mon Multipath 😎</h4>";
            echo "<a href='home.php' class='btn btn-primary'>Retour</a>"; // Bouton de retour
            echo "</div>";
        }
    }
}
?>
