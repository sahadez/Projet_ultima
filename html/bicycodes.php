<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auteur" content="@sahade@"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">


    <title>Bicycode</title>
</head>
<body>

<div class="container h-100">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-6">

            <div class="login-form ">

                <?php 
                if(isset($_GET['login_err']))
                {
                    $err = htmlspecialchars($_GET['login_err']);

                    switch($err)
                    {
                        case 'password':
                        ?>
                            <div class="alert alert-danger">
                                <strong>Erreur</strong> Mot de passe incorrect
                            </div>
                        <?php
                        break;

                        case 'email':
                        ?>
                            <div class="alert alert-danger">
                                <strong>Erreur</strong> Email incorrect
                            </div>
                        <?php
                        break;

                        case 'already':
                        ?>
                            <div class="alert alert-danger">
                                <strong>Erreur</strong> Compte inexistant
                                <p style="font-size: 12px; color: red;">
                                 Ce site est indépendant du site Ultima.dev. S'il s'agit de votre première visite pour renseigner un BICYCODE, merci de bien vouloir vous créer un compte <a href="http://141.94.247.221/clients_inscription.php">ici</a>.
                                </p>
                            </div>
                        <?php
                        break;
                    }
                }
                ?> 
                
                <form action="clients_connexion.php" method="post">
                    
                
                <h2 class="text-center">Se Connecter</h2>
                    <h2 class="text-center"><img src="/logo.png" alt="Logo" class="img-responsive float-center" style="width: 150px; height: 150px;"></h2>       
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
                        <button id="login-btn" type="submit" class="btn btn-primary btn-block">Connexion</button>
                    </div>  
                    
                    <div class="form-group">
                    <p class="text-center"><a href="clients_inscription.php">Créer un compte</a></p> 
                    </div>  
                </form>
                <div class="mb-3 text-center">
                        <p style="font-size: 12px; color: white;">
                        Ce site est indépendant du site Ultima.dev. S'il s'agit de votre première visite pour renseigner un BICYCODE, merci de bien vouloir vous créer un compte.
                        </p>
                    
                </div>
            </div>
            </div>
        </div>
    </div>
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
        transition: transform 0.3s ease-in-out;
    }
    .btn.invalid {
        animation: shake 0.5s;
    }
    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        50% { transform: translateX(10px); }
        75% { transform: translateX(-10px); }
        100% { transform: translateX(0); }
    }
</style>
<script>
    document.getElementById("login-btn").addEventListener("click", function(event) {
        var passwordInput = document.getElementsByName("password")[0];
        if (!isPasswordValid(passwordInput.value)) {
            event.preventDefault();
            var loginButton = event.target;
            loginButton.classList.add("invalid");
            setTimeout(function() {
                loginButton.classList.remove("invalid");
            }, 500);
        }
    });

    function isPasswordValid(password) {
        // Ajoutez ici votre logique pour vérifier la validité du mot de passe
        // Par exemple, vérifiez si le mot de passe respecte certaines règles ou s'il correspond à une valeur attendue
        // Retournez true si le mot de passe est valide, sinon false
        return true; // Exemple de retour de validation du mot de passe
    }
</script>
</body>
</html>
