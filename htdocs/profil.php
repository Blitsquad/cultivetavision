<?php
require_once "./DATA/queries.php";
session_set_cookie_params([
    'lifetime' => 3600, // 1 heure
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
  ]);
  
session_start();
$UtilisateursHandler = new Utilisateurs();
$FollowHandler = new Follow(); //instance follow 


// Vérifie si l'utilisateur est connecté
// Récupère les informations de l'utilisateur connecté

// Vérifie si l'ID de l'utilisateur à afficher est spécifié dans l'URL
if (isset($_GET['pseudo'])) {
  $pseudo = $_GET['pseudo'];
  $profil_ =  $UtilisateursHandler ->Profil_Utilisateurs_bypseudo($_GET['pseudo']);
  // Vérifie si le profil existe
  if (!$profil_) {
      header('Location: index.php');
      exit();
  }
}elseif(isset($_SESSION['utilisateur_id'])){
  $id_utilisateur = $_SESSION['utilisateur_id'];
  $profil_ = $UtilisateursHandler->Profil_Utilisateurs_byid($id_utilisateur);

} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<head>
        <!-- Mettez ici les balises meta, les liens vers les feuilles de style, etc. -->
        <link rel="stylesheet" href="./assets/css/styles.css">
        <link rel="stylesheet" href="./assets/index/styles.css">

        <title>CultiveTaVision | Profil de <?php echo $profil_['pseudo']; ?></title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <!--<link href="css/styles.css" rel="stylesheet" />-->
    </head>
    <body>
       <!-- Responsive navbar-->
<nav class="navbar navbar-expand-lg white-black bg-black">
<a class="navbar-brand" href="profil.php">

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
        <!-- Page header with logo and tagline-->
<header class="py-5 mb-4" style="background-image: url('https://i.pinimg.com/originals/a0/c4/67/a0c467d00ba0ae36d22e7ca9a3bd73e5.jpg'); background-size: cover; background-position: center; background-blend-mode: overlay; color: white; background-color: rgba(0, 0, 0, 0.7);">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder">★ Explorer le profil de <?php echo $profil_['pseudo']; ?></h1>
            <p class="lead mb-0">Inspire toi des meilleurs.</p>
        </div>
    </div>
</header>
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inclure vos fichiers CSS et JavaScript -->
    <link rel="stylesheet" href="./assets/profil/profil.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/index/styles.css">
</head>
<body>
   <!-- Main -->
<main id="main" class="flexbox-col-start-center">
<!-- Profile Page -->
<div class="view-width">

  <!-- Profile Header -->
  <section class="profile-header">
    <div class="profile-header-inner flexbox">
      <div class="phi-info-wrapper flexbox">
        <div class="phi-info-left flexbox">
          <!-- Profile Picture -->
          <div class="phi-profile-picture-wrapper">
            <div class="phi-profile-picture-inner flexbox">
              <img class="phi-profile-picture" src="<?= ($profil_['photo_profil'] != "") ? $profil_['photo_profil'] :  "images/default_profile_picture.jpg"  ?>" alt="">
            </div>
            <img class="phi-profile-picture-blur" src="<?= ($profil_['photo_profil'] != "") ? $profil_['photo_profil'] :  "images/default_profile_picture.jpg"  ?>" alt="">
          </div>
          <!-- Profile Username -->
          <div class="phi-profile-username-wrapper flexbox-col-left">
            <h3 class="phi-profile-username flexbox">♛ Profil de <?php echo $profil_['pseudo'];?><span class="material-icons-round"></span></h3>
            <p class="phi-profile-tagline text-white">Bio de l'utilisateur <?php echo $profil_['description'];?></p>
          </div>
        </div>
        <div class="phi-info-right flexbox-right">
          <div>
          </div>
        </div>
      </div>
      <!-- Profile Header Image -->
      <div class="profile-header-overlay"></div>
      <img class="profile-header-image" src="<?= ($profil_['photo_profil'] != "") ? $profil_['photo_profil'] :  "images/default_profile_picture.jpg"  ?>" alt="" style="filter: blur(8px);">
            </div>
            <br>
            <a href="index.php" class="btn-primary-gray button btn-primary flexbox" style="text-decoration: none;">
    <ion-icon name="heart-outline"></ion-icon><strong>← Revenir au blog </strong><div class="btn-secondary"></div>
</a>
          </div>    
        </div>
     
  </section>
</div>
</main>
</body>
</html>
