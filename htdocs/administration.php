<?php
require_once "./DATA/queries.php";
session_set_cookie_params([
    'lifetime' => 3600, // 1 heure
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
  ]);
session_start();
$articleHandler = new Articles();
$UtilisateurHandler = new Utilisateurs();
$CommentaireHandler = new Commentaire();
$PrivilegesHandler = new Privileges();
$moyenne_commentaires = $PrivilegesHandler->Moyenne_Commentaires_Par_Article();


if (! $_SESSION['utilisateur_id']){
    $_SESSION['admin_wo_session'] = TRUE;
    header('Location: login.php');
    exit();
}

$UserRight = ""; // Initialisez $UserRight à une valeur par défaut

if(isset($_SESSION['privileges']['root']) && $_SESSION['privileges']['root']==1){
    $UserRight="root";
}elseif(isset($_SESSION['privileges']['administrateur']) && $_SESSION['privileges']['administrateur']==1){
    $UserRight="administrateur";
}elseif(isset($_SESSION['privileges']['moderateur']) && $_SESSION['privileges']['moderateur']==1){
    $UserRight="moderateur";
}


//}else{
    //$_SESSION['not_admin'] = TRUE;
    //header('Location: index.php');
    //exit();
//}


$pseudo = $_SESSION['utilisateur_pseudo'];
$id = $_SESSION['utilisateur_id'];

?>
<!DOCTYPE html>
<html>
<head>
  <title>Page d'administration</title>
  <meta charset="UTF-8">
    <link rel="stylesheet" href="./assets/ADMINISTRATION/styles_administration.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/index/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://kit.fontawesome.com/97d5131342.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
</head>
<body style="background-color: black">
<body>

<!-- Début de la barre de Naviguation -->
<nav class="navbar navbar-expand-lg white-black bg-black">
<a class="navbar-brand" href="profil.php">

        <!-- Si l'utilisateur est connecté, afficher le bouton 'Mon Profil' -->
        <?php if (isset($_SESSION['utilisateur_id'])): ?>
        <a class="nav-link btn btn-dark text-white" href="profil.php" >♔ Mon profil</a>
        <?php endif; ?>

        <!-- Si l'utilisateur n'est pas connecté, ne rien afficher -->
        <?php if (isset($_SESSION['utilisateur_id'])): ?>
        <a class="nav-link btn btn-white text-black" href=""></a>
        <?php endif; ?>

        <div class="container">
        <a class="navbar-brand" href="index.php">
        <img src="./assets/images/logooff2.png" alt="Logo" width="180" height="80">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">Conseils Mindset</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="information.php">En savoir plus</a></li>
        <?php if ($_SESSION['utilisateur_id'] == 1): ?>
    <li class="nav-item"><a class="nav-link text-white btn btn-dark" href="administration.php">Administration 🔒</a></li>
<?php endif; ?>
<li class="nav-item"><a class="nav-link active text-white" aria-current="page" href="#"></a></li>



        <?php if (isset($_SESSION['utilisateur_id'])) { ?>
        <!-- Si l'utilisateur est connecté -->
        <li class="nav-item">
        <a class="nav-link btn btn-danger text-white" href="logout.php">Se déconnecter</a>
        </li>
        <?php } else { ?>
        <!-- Si l'utilisateur n'est pas connecté -->
        <li class="nav-item">
        <a class="nav-link btn btn-connect" href="login.php">Se connecter</a>
        </li>
        <li class="nav-item">
        <a class="nav-link btn btn-register" href="register.php">S'enregistrer</a>
        </li>
        <?php } ?>
        </ul>
        </div>
        </div>
        </nav>
<!-- Fin de la barre de Naviguation -->


        <!-- Page header with logo and tagline-->
        <header class="py-5 mb-11" style="background-image: url('https://as2.ftcdn.net/v2/jpg/05/67/32/99/1000_F_567329969_LxpWb0FwrBps5iJxcDorN9cSFZUBSNQc.jpg'); background-size: cover; background-position: center; background-blend-mode: overlay; color: white; background-color: rgba(0, 0, 0, 0.5);">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder"><strong>Page d'administration</strong></h1>
            <p class="lead mb-0">Veillons sur notre communauté</p>
        </div>
    </div>
</header>

<div class="mt-1 pt-3 mx-5 position-relative text-center">
<?php
if(isset($_SESSION['valid_article_publication']) && $_SESSION['valid_article_publication']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="valid_article_publication"><div class="text-center alert alert-success w-50" role="alert">
    Article envoyé !
    </div></div>';
    unset($_SESSION['valid_article_publication']);
}
?>

<?php
if(isset($_SESSION['utilisateur_id'])){
    //requete dans la base par id de l'user connecté pour vérifier privilèges
    $resultat_privileges = $PrivilegesHandler->Verifier_Privileges($_SESSION['utilisateur_id']);
    //si la variable $resultat_privileges contient la clé administrateur et est égal à 1=> $administrateur == true;
    if($resultat_privileges['administrateur'] == 1){
      $administrateur = true;
    }
  }else{
    header('Location: login.php');
    exit();
  }
// Suppression de l'utilisateur si l'utilisateur connecté est administrateur
if(isset($_POST['delete_user']) && isset($_POST['user_id']) && isset($_POST['admin_id'])) {
    // Vérification des privilèges de l'utilisateur connecté
    $admin_id = $_POST['admin_id'];
    if($admin_id == 1) { // Vérifie si l'utilisateur connecté est administrateur
        $user_id = $_POST['user_id']; // Récupération de l'ID de l'utilisateur à supprimer
        $deleted_rows = $PrivilegesHandler->Supprimer_Utilisateur($user_id);
        
        // Vérification si la suppression a réussi
        if($deleted_rows > 0) {
            echo '<div class="alert alert-success" role="alert">L\'utilisateur a été supprimé avec succès.</div>';
        } else {
            echo '<div class="alert alert-success" role="alert">L\'utilisateur a été supprimé avec succès.</div>';
        }
    } else {
        echo "Vous n'avez pas les privilèges pour effectuer cette action.";
    }
}
?>

<div class="container mt-5">
    <form method="post">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="user_id" class="text-white">Sélectionnez l'utilisateur à supprimer:</label>
                    <select class="form-control" id="user_id" name="user_id">
                        <?php
                        // Récupération de la liste des utilisateurs avec leurs ID et pseudo
                        $result_user = $UtilisateurHandler->Obtenir__Id_Pseudo();
                        foreach ($result_user as $user) {
                            echo '<option value="' . $user['id'] . '">' . $user['id'] . ' - ' . $user['pseudo'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="admin_id" class="text-white">Entrez votre ID d'administrateur:</label>
                    <input type="text" class="form-control" id="admin_id" name="admin_id" value="1" readonly>
                </div>
            </div>
            <div class="col-md-4">
            <button type="submit" name="delete_user" class="btn btn-danger mt-4" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">❌ Supprimer un utilisateur</button>
            </div>
        </div>
    </form>
</div>
```


</div>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    // Vérifiez si l'utilisateur a le privilège root
    if ($UserRight === "root") {
        // Supprimez l'utilisateur ayant l'ID 2
        $userIdToDelete = 7; // ID de l'utilisateur à supprimer
        $deleted = $UtilisateurHandler->SupprimerIdUtilisateur($userIdToDelete); // Méthode hypothétique pour supprimer un utilisateur

        if ($deleted) {
            // Utilisateur supprimé avec succès
            echo '<div class="alert alert-success" role="alert">L\'utilisateur avec l\'ID 2 a été supprimé avec succès.</div>';
        } else {
            // Erreur lors de la suppression de l'utilisateur
            echo '<div class="alert alert-danger" role="alert">Erreur lors de la suppression de l\'utilisateur.</div>';
}
    }
}
?>


<!-- Ajoutez le formulaire de suppression -->
<br>

<br>
<div class="py-5 border border-secondary ">
    <div class="container">
    <h1 class="display-5 font-weight-bold mb-4 text-center mt-5 mb-5 text-white">♟ Statistiques</h1>

        <div class="row hidden-md-up">
            <div class="col-md-3">
                <div class="card text-center border border-primary">
                    <div class="card-body">
                        <h4 class="card-title">Statistique n°1</h4>
                        <p class="card-text">Nombre d'articles : <?= ($articleHandler->Obtenir_Tous_Pour_Stat())['COUNT(*)'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-primary">
                    <div class="card-body">
                        <h4 class="card-title">Statistique n°2</h4>
                        <p class="card-text">Nombre d'Utilisateurs : <?= ($UtilisateurHandler->Obtenir_Tous_Pour_Stat())['COUNT(*)'] ?></p>
                    </div>

                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-primary">
                    <div class="card-body">
                        <h4 class="card-title">Statistique n°3</h4>
                        <p class="card-text">Nombre de commentaires : <?= ($CommentaireHandler->Obtenir_Tous_Pour_Stat())['COUNT(*)'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-primary">
                    <div class="card-body">
                        <h4 class="card-title">Statistique n°4</h4>
                        <p class="card-text">Nombre d'utilisateurs privilégiés : <?= ($PrivilegesHandler->Obtenir_Tous_Pour_Stat())['COUNT(*)'] ?></p>
                    </div>
                </div>
            </div>
        <div class="row">
    <div class="col-md-4">
        <div class="card text-center border border-primary">
            <div class="card-body">
                <h4 class="card-title">Statistique n°5</h4>
                <p class="card-text">Moyenne de commentaire(s) par article : <?php echo $moyenne_commentaires['moyenne_commentaires_par_article']; ?></p>
            </div>
        </div>
    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h1 class="display-5 font-weight-bold mb-4 text-center mt-5 mb-5 text-white">❦ Liste des utilisateurs</h1>

<div class="list-group mx-5 mt-5">
    <?php
    $result_user = $UtilisateurHandler->Obtenir__Id_Pseudo();

    foreach($result_user as $user){
        echo '<a href="profil.php?pseudo='.$user['pseudo'].'" class="list-group-item list-group-item-action">'.$user['pseudo'].'   '.($UserRight == "root" ? ' | ID = N°'.$user['id'] : '').'</a>';
    }
    ?>  
</div>


<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top mt-5 text-white">
    <div class="col-md-4 d-flex align-items-center">
      <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
        <svg class="bi" width="30" height="24"><use xlink:href="#bootstrap"></use></svg>
      </a>
      <span class="mb-3 mb-md-0 text-body-secondary">© CultiveTaVision | 2024 Company, Inc</span>
    </div>
  </footer>

</body>
<script src="./ASSETS/ADMINISTRATION/app_administration.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

</html>