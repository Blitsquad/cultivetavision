<?php
session_start();


include "./DATA/config.php";
include "./DATA/queries.php";

$UtilisateursHandler = new Utilisateurs();

$alertClass = ''; // Variable pour stocker la classe de style de l'alerte
$alertMessage = ''; // Variable pour stocker le message de l'alerte

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['connect'])) {
        if (!empty($_POST['pseudo']) && !empty($_POST['mdp'])) {
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $mdp = md5($_POST['mdp']);

            $resultat_Recuperer_Utilisateurs = $UtilisateursHandler->Recuperer_Utilisateurs($pseudo, $mdp);

            if (! empty($resultat_Recuperer_Utilisateurs)) {
				$_SESSION['utilisateur_id']=$resultat_Recuperer_Utilisateurs['id'];
				$_SESSION['utilisateur_pseudo']=$resultat_Recuperer_Utilisateurs['pseudo'];
                header('location:index.php');
                exit();
            } else {
                $alertClass = 'alert-danger'; // Ajoutez la classe pour un message d'erreur
                $alertMessage = "Votre compte n'a pas été trouvé";
            }
        } else {
            $alertClass = 'alert-danger'; // Ajoutez la classe pour un message d'erreur
            $alertMessage = "Erreur !";
        }
    }

    if (isset($_POST['register'])) {
        $mdp1 = $_POST['password'];
        $mdp2 = $_POST['password_bis'];
        if ($mdp1 == $mdp2) {
            if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $pseudo = htmlspecialchars($_POST['pseudo']);
                $email = htmlspecialchars($_POST['email']);
                $mdp = md5($_POST['password']);

                $resultat_AjouterUtilisateurs = $UtilisateursHandler->Ajout_Utilisateurs($pseudo, $email, $mdp);
                if ($resultat_AjouterUtilisateurs) {
                    $alertClass = 'alert-success'; // Ajoutez la classe pour un message de succès
                    $alertMessage = "Votre compte a bien été créé, veuillez vous connecter";
                } else {
                    $alertClass = 'alert-danger'; // Ajoutez la classe pour un message d'erreur
                    $alertMessage = "Une erreur s'est produite, veuillez correctement remplir les champs";
                }
            } else {
                $alertClass = 'alert-danger'; // Ajoutez la classe pour un message d'erreur
                $alertMessage = "Erreur pendant la création de votre compte..";
            }
        } else {
            $alertClass = 'alert-danger'; // Ajoutez la classe pour un message d'erreur
            $alertMessage = "Les mots de passe ne correspondent pas";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    <link rel="stylesheet" href="./assets/login/styles.css">
</head>

<body>
	<div style="text-align:center">
		<img src="./assets/images/logooff.png" alt="CultiveTaVision">
	</div>
	<div class="section">
		<div class="container">
			<div class="row full-height justify-content-center">
				<div class="col-12 text-center align-self-center py-5">
					<div class="section pb-5 pt-5 pt-sm-2 text-center">
						<h6 class="mb-0 pb-3"><span>Se connecter </span><span>S'enregistrer</span></h6>
			          	<input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
			          	<label for="reg-log"></label>
						<div class="card-3d-wrap mx-auto">
							<div class="card-3d-wrapper">
								<div class="card-front">
								<form action="login.php" method="post">
									<div class="center-wrap">
										<div class="section text-center">
											<h4 class="mb-4 pb-3">Se connecter</h4>
											<div class="form-group">
												<input type="text" name="pseudo" class="form-style" placeholder="Pseudo" id="pseudo" autocomplete="off">
												<i class="input-icon uil uil-at"></i>
											</div>	
											<div class="form-group mt-2">
												<input type="password" name="mdp" class="form-style" placeholder="Votre mot de passe" id="mdp" autocomplete="off">
												<i class="input-icon uil uil-lock-alt"></i>
											</div>
											<button type="submit" name="connect" value="1" class="btn mt-4 ">Se connecter</button>
				      					</div>

			      					</div>
								</form>
			      				</div>
                                  <?php if (!empty($alertMessage)) { ?>
        <div class="alert <?php echo $alertClass; ?>" role="alert" style="background-color: white; color: black;">
            <?php echo $alertMessage; ?>
        </div>
    <?php } ?>
								<div class="card-back">
								<form action="login.php" method="post">
									<div class="center-wrap">
											<div class="section text-center">
											<h4 class="mb-4 pb-3">S'enregistrer</h4>
											<div class="form-group">
												<input type="text" name="pseudo" class="form-style" placeholder="Pseudo" id="pseudo" autocomplete="off">
												<i class="input-icon uil uil-user"></i>
											</div>	
											<div class="form-group mt-2">
												<input type="email" name="email" class="form-style" placeholder="Votre email" id="email" autocomplete="off">
												<i class="input-icon uil uil-at"></i>
											</div>	
											<div class="form-group mt-2">
												<input type="password" name="password" class="form-style" placeholder="Votre mot de passe" id="mdp" autocomplete="off">
												<i class="input-icon uil uil-lock-alt"></i>
											</div>
											<div class="form-group mt-2">
												<input type="password" name="password_bis" class="form-style" placeholder="Confirmer le mot de passe" id="confirm" autocomplete="off">
												<i class="input-icon uil uil-lock-alt"></i>
											</div>
											<button type="submit" name="register" value="1" class="btn mt-4 ">S'enregistrer</button>
				      					</div>
			      					</div>
								</form>
			      				</div>
			      			</div>
			      		</div>
			      	</div>
		      	</div>
	      	</div>
	    </div>
	</div>
</body>
</html>
