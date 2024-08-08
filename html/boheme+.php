<?php
session_start();
require_once 'config.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

// Récupération des Bicycodes déjà utilisés dans les tables boheme et multipath
$usedBicycodes = $bdd->query('SELECT bicycode FROM boheme UNION SELECT bicycode FROM multipath')->fetchAll(PDO::FETCH_COLUMN);

// Récupération des options pour les listes déroulantes
$options_bicycode = $bdd->query('SELECT num_bicycode FROM bicycode')->fetchAll(PDO::FETCH_COLUMN);

// Filtrage des Bicycodes disponibles
$availableBicycodes = array_diff($options_bicycode, $usedBicycodes);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commande = htmlspecialchars($_POST['commande']);
    $bicycode = htmlspecialchars($_POST['bicycode']);
    $configuration = htmlspecialchars($_POST['configuration']);
    $batterie = htmlspecialchars($_POST['batterie']);
    $jantes = htmlspecialchars($_POST['jantes']);
    $taille = htmlspecialchars($_POST['taille']);

    // Vérifie si le Bicycode est déjà utilisé dans la table boheme (redondant mais par précaution)
    $stmt = $bdd->prepare('SELECT COUNT(*) FROM boheme WHERE bicycode = ?');
    $stmt->execute([$bicycode]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Erreur: Ce Bicycode est déjà utilisé.";
    } else {
        // Prépare et exécute l'insertion
        $req = $bdd->prepare('INSERT INTO boheme (commande, bicycode, configuration, batterie, jantes, taille) VALUES (?, ?, ?, ?, ?, ?)');
        $req->execute(array($commande, $bicycode, $configuration, $batterie, $jantes, $taille));
        $_SESSION['message'] = "Vélo enregistré avec succès!";
    }

    // Redirige après l'insertion
    header('Location: boheme+.php');
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Ultima Mobility</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
        }
        #scrollToTop, #scrollToBottom {
            display: none;
            position: fixed;
            bottom: 20px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 50%;
            transition: background 0.3s;
        }
        #scrollToTop:hover, #scrollToBottom:hover {
            background: #0069d9;
        }
        #scrollToBottom {
            left: 80px;
        }
        .sous {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .nav-item:hover .sous {
            display: block;
        }
        .sous li {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .sous li:hover {
            background-color: #ddd;
        }
        form {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin: 20px auto;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="col-md-12">
            <div class="text-center">
                <hr />
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="#" class="btn btn-info">Bike</a>
                                <ul class="sous">
                                    <a href="home.php" class="btn btn-info">Multipath/Larrum</a>
                                    <li><a href="boheme.php" class="btn btn-info">Boheme</a></li>
                                    <li><a href="gravel.php" class="btn btn-info">Gravel</a></li>
                                    <li><a href="dev.php" class="btn btn-info">DEV</a></li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="#" class="btn btn-info">+</a>
                                <ul class="sous">
                                    <li><a href="register.php" class="btn btn-info">Multipath</a></li>
                                    <li><a href="larrum+.php" class="btn btn-info">Larrum</a></li>
                                    <li><a href="boheme+.php" class="btn btn-info">Boheme</a></li>
                                    <li><a href="gravel+.php" class="btn btn-info">Gravel</a></li>
                                    <li><a href="dev+.php" class="btn btn-info">DEV</a></li>
                                </ul>
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
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a href="statistique.php" class="btn btn-info">State</a>
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
            </div>
        </div>
        <div class="container">
            <h3 class="text-center mb-4">Enregistrer un Vélo Boheme</h3>
            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="commande">Commande :</label>
                    <input type="text" id="commande" name="commande" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="bicycode">Bicycode :</label>
                    <select id="bicycode" name="bicycode" class="form-control" required>
                        <option value="">Sélectionner un Bicycode</option>
                        <?php foreach ($availableBicycodes as $option): ?>
                            <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="configuration">Configuration :</label>
                    <select id="configuration" name="configuration" class="form-control" onchange="updateOptions()" required>
                        <option value="">Sélectionner</option>
                        <option value="Boheme Cross">Boheme Cross</option>
                        <option value="Boheme Cross S">Boheme Cross S</option>
                        <option value="Boheme Enduro">Boheme Enduro</option>
                        <option value="Boheme Rando">Boheme Rando</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="batterie">Batterie :</label>
                    <select id="batterie" name="batterie" class="form-control" required>
                        <!-- Options dynamiques -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="jantes">Jantes :</label>
                    <select id="jantes" name="jantes" class="form-control" required>
                        <!-- Options dynamiques -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="taille">Taille :</label>
                    <select id="taille" name="taille" class="form-control" required>
                        <!-- Options dynamiques -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
        <script>
            const options = {
                "Boheme Cross": {
                    "batterie": [630],
                    "jantes": ["29 pouces"],
                    "taille": ["L", "M"]
                },
                "Boheme Cross S": {
                    "batterie": [630],
                    "jantes": ["27,5 pouces"],
                    "taille": ["S"]
                },
                "Boheme Enduro": {
                    "batterie": [800],
                    "jantes": ["29"],
                    "taille": ["L", "M", "S"]
                },
                "Boheme Rando": {
                    "batterie": [700],
                    "jantes": ["Mullet"],
                    "taille": ["L", "M", "S"]
                }
            };

            function updateOptions() {
                const config = document.getElementById('configuration').value;
                const batterieSelect = document.getElementById('batterie');
                const jantesSelect = document.getElementById('jantes');
                const tailleSelect = document.getElementById('taille');

                batterieSelect.innerHTML = "<option value=''>Sélectionner</option>";
                jantesSelect.innerHTML = "<option value=''>Sélectionner</option>";
                tailleSelect.innerHTML = "<option value=''>Sélectionner</option>";

                if (config) {
                    // Remplir les options de batterie
                    if (options[config]["batterie"]) {
                        options[config]["batterie"].forEach(b => {
                            const option = document.createElement('option');
                            option.value = b;
                            option.textContent = b + " Wh"; // Exemple: Ajouter une unité si nécessaire
                            batterieSelect.appendChild(option);
                        });
                    }

                    // Remplir les options de jantes
                    if (options[config]["jantes"]) {
                        options[config]["jantes"].forEach(j => {
                            const option = document.createElement('option');
                            option.value = j;
                            option.textContent = j;
                            jantesSelect.appendChild(option);
                        });
                    }

                    // Remplir les options de taille en fonction de la configuration
                    if (options[config]["taille"]) {
                        options[config]["taille"].forEach(t => {
                            // Vérifier si la taille est compatible avec la configuration choisie
                            if (config === "Boheme Cross S" && t === "S") {
                                const option = document.createElement('option');
                                option.value = t;
                                option.textContent = t;
                                tailleSelect.appendChild(option);
                            } else if (config !== "Boheme Cross S") {
                                const option = document.createElement('option');
                                option.value = t;
                                option.textContent = t;
                                tailleSelect.appendChild(option);
                            }
                        });
                    }
                }
            }
        </script>
    </div>
</body>

</html>
