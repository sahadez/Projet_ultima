<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auteur" content="sahade"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>Inscription</title>
</head>
<body>
<div class="login-form">
    <?php 
    if(isset($_GET['reg_err']))
    {
        $err = htmlspecialchars($_GET['reg_err']);

        switch($err)
        {
            case 'success':
            ?>
                <div class="alert alert-success">
                    <strong>Succès</strong> Inscription réussie !
                </div>
            <?php
            break;

            case 'password':
            ?>
                <div class="alert alert-danger">
                    <strong>Erreur</strong> Les mots de passe ne correspondent pas.
                </div>
            <?php
            break;

            case 'email':
            ?>
                <div class="alert alert-danger">
                    <strong>Erreur</strong> Adresse e-mail non valide.
                </div>
            <?php
            break;

            case 'email_length':
            ?>
                <div class="alert alert-danger">
                    <strong>Erreur</strong> L'adresse e-mail est trop longue.
                </div>
            <?php 
            break;

            case 'nom_prenom_length':
            ?>
                <div class="alert alert-danger">
                    <strong>Erreur</strong> Le nom ou le prénom est trop long.
                </div>
            <?php 
            break;

            case 'already':
            ?>
                <div class="alert alert-danger">
                    <strong>Erreur</strong> Compte déjà existant.
                </div>
            <?php 
            break;

        }
    }
    ?>
    
    <form action="clients_traitement.php" method="post">
        <h2 class="text-center">Créer un compte</h2>       

        <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="nom" class="form-control" placeholder="Nom" required="required" autocomplete="off">
                </div>
        </div>

        <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="prenom" class="form-control" placeholder="Prénom" required="required" autocomplete="off">
                </div>
        </div>

        <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="email" name="email" class="form-control" placeholder="Email" required="required" autocomplete="off">
                </div>
        </div>
        
        <div class="form-group">
                    <div class="input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="Mot de passe" required="required" autocomplete="off">
                    </div>
        </div>

        <div class="form-group">
                    <div class="input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    </div>
                    <input type="password" name="password_retype" class="form-control" placeholder="Re-tapez le mot de passe" required="required" autocomplete="off">
                    </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Inscription</button>
        </div>  
        <div class="form-group">
            <a href="clients_connexion.php" class="btn btn-secondary btn-block">Connexion</a>
        </div>
        <div class="form-group">
            <a href="ultima.dev" class="btn btn-secondary btn-block">Retour à la boutique</a>
        </div>
    </form>
</div>
<style>

    body {
            background-image: url("/julio.jpg"); /* Remplacez 'images/background.jpg' par le chemin réel de votre image */
            background-size:120%; /* Ajuste la taille de l'image pour couvrir tout le corps */
            background-position: center; /* Centre l'image */
            background-repeat: no-repeat; /* Empêche la répétition de l'image */
        }
    .login-form {
        width: 340px;
        margin: 50px auto;
    }
    .login-form form {
        margin-bottom: 15px;
        background: rgba(247, 247, 247, 0.5);
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        padding: 30px;
    }
    .login-form h2 {
        margin: 0 0 15px;
    }
    .form-control, .btn {
        min-height: 38px;
        border-radius: 2px;
    }
    .btn {        
        font-size: 15px;
        font-weight: bold;
    }
</style>
</body>
</html>
