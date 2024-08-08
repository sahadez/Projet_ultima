<?php 
session_start();
require_once 'config.php'; 

if(!isset($_SESSION['user'])){
    header('Location:index.php');
    die();
}

$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE token = ?');
$req->execute(array($_SESSION['user']));
$data = $req->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['bicycode'])) {
        $num_bicycode = $_POST['bicycode'];
        
        $check = $bdd->prepare('SELECT num_bicycode FROM bicycode WHERE num_bicycode = ?');
        $check->execute(array($num_bicycode));
        if ($check->rowCount() > 0) {
            $_SESSION['flash'] = 'Le numéro Bicycode existe déjà';
        } else {
            try {
                $req = $bdd->prepare('INSERT INTO bicycode (num_bicycode) VALUES (?)');
                $req->execute(array($num_bicycode));
                $_SESSION['flash'] = 'Le numéro Bicycode a été enregistré avec succès';
                header('Location: ' . $_SERVER['PHP_SELF']);
                die();
            } catch (PDOException $e) {
                $_SESSION['flash'] = 'Erreur : ' . $e->getMessage();
            }
            
        }
    }
}

?>


<!doctype html>
<html lang="en">
  <head>
    <title>Ultima Mobility</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>

        <?php if(isset($_SESSION['flash'])): ?>
            <div class="alert alert-info">
                <?= $_SESSION['flash']; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

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
                    <h1 class="p-5">Bonjour <?php echo $data['pseudo']; ?> !</h1>
                    <hr />

                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <div class="collapse navbar-collapse" id="navbarNav">

                        <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a href="home.php" class="btn btn-info">Accueil</a>
                    </li>
                    </ul>
                    <ul class="navbar-nav mr-auto">
                    <li class="nav-item">

                    <a href="register.php" class="btn btn-info">+</a>
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

                    <div class="container mt-5">
                        <!-- Ajout du formulaire pour recueillir le numéro Bicycode -->
                        <?php
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        die();
    }

    // Vérifier l'inactivité de l'utilisateur
    $timeout = 300; // Temps d'inactivité en secondes (1440 secondes = 24 minutes)300mn
    $last_activity = $_SESSION['last_activity'];

    if (time() - $last_activity > $timeout) {
        session_destroy();
        header('Location: index.php?timeout=true');
        die();
    }

    // Mettre à jour l'heure de dernière activité
    $_SESSION['last_activity'] = time();
    ?>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="bicycode">Veuillez saisir le numéro du Bicycode:</label>
                                <input type="text" class="form-control" id="bicycode" name="bicycode" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
