<?php
require_once "./DATA/queries.php";
session_set_cookie_params([
  'lifetime' => 3600, // 1 heure
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
session_regenerate_id(true);

$articleHandler = new Articles();
$UtilisateurHandler = new Utilisateurs();
$TagHandler = new Tags();
$CommentaireHandler = new Commentaire();
$LikeHandler = new Like();
$PrivilegesHandler = new Privileges();


if(isset($_SESSION['utilisateur_id'])){
  // Requête dans la base par id de l'utilisateur connecté pour vérifier les privilèges
  $resultat_privileges = $PrivilegesHandler->Verifier_Privileges($_SESSION['utilisateur_id']);
  
  // Vérifier si $resultat_privileges est un tableau et s'il contient la clé 'administrateur' égale à 1
  if(is_array($resultat_privileges) && isset($resultat_privileges['administrateur']) && $resultat_privileges['administrateur'] == 1){
    $administrateur = true;
  }
}else{
  // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
  header('Location: login.php');
  exit();
}



$resultat = $articleHandler->Obtenir();

foreach ($resultat as $_articles) {
  if ($_articles['id'] == $_GET['value']){
      $article = $_articles;
      break;
  }
}

if (!$article) { // Vérifier si l'article existe
  header('Location: index.php');
  exit();
}

if (isset($_POST['like_status'])){
  $resultat_VerifierLikeParUtilisateurParPost = $LikeHandler -> Verifier_ParUtilisateur_ParArticle($_SESSION['utilisateur_id'],$article['id']);
  if ($article['id'].'vrai'.$_SESSION['utilisateur_id'] == $_POST['like_status'] && $resultat_VerifierLikeParUtilisateurParPost){
    $resultat_SupprimerLikeParUtilisateurParArticle = $LikeHandler -> Supprimer_ParUtilisateur_ParArticle($_SESSION['utilisateur_id'],$article['id']);
    $_SESSION['valid_']= TRUE;
    $_SESSION['valid_msg']= "Vous avez retiré votre 'j'aime' de la publication.";
    header('Location: article.php?value='.$article['id']);

    exit();
  }elseif (($article['id'].'faux'.$_SESSION['utilisateur_id']) == $_POST['like_status'] && ! $resultat_VerifierLikeParUtilisateurParPost){
    $resultat_AjouterLikeParUtilisateurParArticle = $LikeHandler -> Ajouter_ParUtilisateur_ParArticle($_SESSION['utilisateur_id'],$article['id'],date("Y-m-d H:i:s"));
    $_SESSION['valid_']= TRUE;
    $_SESSION['valid_msg']= "Vous avez ajouté un 'j'aime' à la publication.";
    header('Location: article.php?value='.$article['id']);
    exit();
  }else{
    $_SESSION['erreur_']= TRUE;
    $_SESSION['erreur_msg']= "Une erreur est survenue, merci de réessayer.";
    echo "erreur like";
  }
}

if (isset($_POST['post_commentaire']) && $_POST['post_commentaire']){
  if (strlen($_POST['commentaire']) >= 3){
    $texte_commentaire = htmlspecialchars($_POST['commentaire']);
    try {
      $resultat_AjouterCommentaire = $CommentaireHandler -> Ajouter($texte_commentaire, date("Y-m-d H:i:s"), $_SESSION['utilisateur_id'], $article['id']);
      if ( ! $resultat_AjouterCommentaire){
        $_SESSION['erreur_']= TRUE;
        $_SESSION['erreur_msg']= "Erreur lors de l'ajout du commentaire : ";
      }else{
        // Commentaire posté avec succès ! 
        $_SESSION['valid_'] = TRUE;
        $_SESSION['valid_msg'] = "Votre commentaire a bien été posté.";
        unset($_SESSION['erreur_']);
        unset($_SESSION['erreur_msg']);
        header('Location: article.php?value='.$article['id']);
        exit();
      }
    // Erreur lors de l'ajout du com
    } catch (PDOException $e) {
      $_SESSION['erreur_']= TRUE;
      $_SESSION['erreur_msg']= "Erreur lors de l'ajout du commentaire : " . $e->getMessage();
    }
 // Erreur si commentaire moins de 3 caractères
  }else{
    $_SESSION['erreur_']= TRUE;
    $_SESSION['erreur_msg']= "Votre commentaire doit faire plus de 3 caractères !";
  }
}

if (isset($_POST['edit_commentaire']) && $_POST['edit_commentaire']){
  if (strlen($_POST['new_commentaire']) >= 3){
    $texte_commentaire = htmlspecialchars($_POST['new_commentaire']);
    try {
      $resultat_ModifierCommentaire = $CommentaireHandler -> Modifier($texte_commentaire, date("Y-m-d H:i:s"), $_SESSION['utilisateur_id'], $article['id']);
      if ( ! $resultat_ModifierCommentaire){
        $_SESSION['erreur_']= TRUE;
        $_SESSION['erreur_msg']= "Erreur lors de la modification du commentaire : " . implode(", ", $resultat_ModifierCommentaire->errorInfo());
      }else{
        $_SESSION['valid_'] = TRUE;
        $_SESSION['valid_msg'] = "Votre commentaire a bien été modifié.";
        unset($_SESSION['erreur_']);
        unset($_SESSION['erreur_msg']);
        header('Location: article.php?value='.$article['id']);
        exit();
      }
    } catch (PDOException $e) {
      $_SESSION['erreur_']= TRUE;
      $_SESSION['erreur_msg']= "Erreur lors de la modification du commentaire : " . $e->getMessage();
    }
  }else{
    $_SESSION['commentaire_short'] = TRUE;
  }
}

// Vérifier les privilèges de modération
//if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $commentaire['auteur']) {
  // Affichage du bouton "Effacer" pour l'auteur du commentaire
//} elseif (isset($_SESSION['utilisateur_id'])) {
  // Vérifier les privilèges de modération
  //$resultat_privileges = $CommentaireHandler->VerifierPrivilegesModerateur($_SESSION['utilisateur_id']);

  // if ($resultat_privileges && $resultat_privileges['role'] == 'moderateur') {
      // Affichage du bouton "Effacer" pour les modérateurs
  //}
//}

if (isset($_POST['delete_commentaire']) && $_POST['delete_commentaire']){
  // Vérifier si l'utilisateur est administrateur
  
  if ($administrateur) {
      // Obtenir tous les commentaires de l'article
      $resultat_Commentaires_Article = $CommentaireHandler->Obtenir_ParIdArticle($article['id']);

      // Parcourir tous les commentaires de l'article
      foreach ($resultat_Commentaires_Article as $commentaire){
          // Vérifier si le commentaire correspond à celui qui doit être supprimé
          if ($_POST['commentaire_value'] == md5($commentaire['id'].' '.$commentaire['texte'].' '.$_SESSION['utilisateur_id'])){
              try {
                  // Supprimer le commentaire
                  $resultat_SupprimerCommentaire = $CommentaireHandler->Supprimer_Wadmin($commentaire['texte'], $commentaire['auteur'], $article['id'], $_SESSION['utilisateur_id']);
                  if (!$resultat_SupprimerCommentaire){
                      $_SESSION['erreur_'] = TRUE;
                      $_SESSION['erreur_msg'] = "Erreur lors de la suppression du commentaire.";
                  } else {
                      $_SESSION['valid_'] = TRUE;
                      $_SESSION['valid_msg'] = "Le commentaire a été supprimé avec succès.";
                      unset($_SESSION['erreur_']);
                      unset($_SESSION['erreur_msg']);
                      header('Location: article.php?value='.$article['id']);
                      exit();
                  }
              } catch (PDOException $e) {
                  $_SESSION['erreur_'] = TRUE;
                  $_SESSION['erreur_msg'] = "Erreur lors de la suppression du commentaire : " . $e->getMessage();
              }
              break;
          } else {
              $_SESSION['erreur_'] = TRUE;
              $_SESSION['erreur_msg'] = "Il ne s'agit pas de votre commentaire !";
          }
      }
  }elseif(isset($_SESSION['utilisateur_id'])){
    // Obtenir tous les commentaires de l'article
    $resultat_Commentaires_Article = $CommentaireHandler->Obtenir_ParAuteur($_SESSION['utilisateur_id']);

    // Parcourir tous les commentaires de l'article
    foreach ($resultat_Commentaires_Article as $commentaire){
        // Vérifier si le commentaire correspond à celui qui doit être supprimé
        if ($_POST['commentaire_value'] == md5($commentaire['id'].' '.$commentaire['texte'].' '.$_SESSION['utilisateur_id'])){
            try {
                // Supprimer le commentaire
                $resultat_SupprimerCommentaire = $CommentaireHandler->Supprimer($commentaire['texte'], $commentaire['auteur'], $article['id']);
                if (!$resultat_SupprimerCommentaire){
                    $_SESSION['erreur_'] = TRUE;
                    $_SESSION['erreur_msg'] = "Erreur lors de la suppression du commentaire.";
                } else {
                    $_SESSION['valid_'] = TRUE;
                    $_SESSION['valid_msg'] = "Le commentaire a été supprimé avec succès.";
                    unset($_SESSION['erreur_']);
                    unset($_SESSION['erreur_msg']);
                    header('Location: article.php?value='.$article['id']);
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['erreur_'] = TRUE;
                $_SESSION['erreur_msg'] = "Erreur lors de la suppression du commentaire : " . $e->getMessage();
            }
            break;
        } else {
            $_SESSION['erreur_'] = TRUE;
            $_SESSION['erreur_msg'] = "Il ne s'agit pas de votre commentaire !";
        }
    }

  } else {
      $_SESSION['erreur_'] = TRUE;
      $_SESSION['erreur_msg'] = "Vous n'avez pas les autorisations nécessaires pour supprimer ce commentaire.";
  }
}



?>

<html>
<head>
  <title>CultiveTaVision | Article</title>

  <!-- Métadonnées -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Liens vers les fichiers CSS de Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Liens vers les fichiers JavaScript de Bootstrap et dépendances -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- KIT MODAL -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <!-- KIT fontawesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://kit.fontawesome.com/97d5131342.js" crossorigin="anonymous"></script>  <script src="https://kit.fontawesome.com/97d5131342.js" crossorigin="anonymous"></script>
  
  <!-- CSS PERSO -->
  <link rel="stylesheet" href="./ASSETS/ARTICLE/styles_article.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="./assets/index/styles.css">

  <!-- FONT GOOGLE -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open Sans">
  <style>
    h1,h2,h3,h4,h5,h6 {font-family: "Oswald"}
    body {font-family: "Open Sans"}
  </style>
</head>
<?php

if(isset($_SESSION['valid_'])){
  echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="success_dialog"><div class="text-center alert alert-success mx-auto " role="alert">
  <i class="fa-regular fa-circle-check"></i>
  '.$_SESSION['valid_msg'].'
  </div></div>';
  unset($_SESSION['valid_']);
  unset($_SESSION['valid_msg']);
}elseif(isset($_SESSION['erreur_'])){
  echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="error_dialog"><div class="text-center alert alert-danger mx-auto " role="alert">
  <i class="fa-regular fa-circle-xmark"></i>
  '.$_SESSION['erreur_msg'].'
  </div></div>';
  unset($_SESSION['erreur_']);
  unset($_SESSION['erreur_msg']);
}  
?>

<!-- Barre de Naviguation -->
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
                        <a class="nav-link btn btn-disconnect" href="logout.php">Se déconnecter</a>
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
<!-- /Fin de la Barre de Naviguation/ -->


<body class="bg-light">
    <div class="container mt-12">
    <div class="row pt-4">

<!-- Articles -->
<div class="col-lg-12 col-md-12 mt-5">
  <?php

  
$resultat_ObtenirUtilisateurActuel = $UtilisateurHandler-> Obtenir_Pseudo_Photo_ParId($article['auteur']);

  // Obtenir les commentaires de l'article
  $resultat_ObtenirCommentaireArticle = $CommentaireHandler -> Obtenir_ParIdArticle($article['id']);

  // Formater la date de l'article
  setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR', 'fr', 'fr', 'fra', 'fr_FR@euro', 'fr_FR.ISO8859-1', 'fr_FR.ISO-8859-15');

  // Récupérer la date d'origine depuis la base de données
  $date_origine = $article['date_heure_articles'];
  
  // Convertir la date au format timestamp
  $timestamp = strtotime($date_origine);
  
  // Utiliser la fonction utf8_encode pour afficher correctement les accents
  $date_format = utf8_encode(strftime("%e %B %Y à %H:%M", $timestamp));
  
  // Ton code pour récupérer les détails de l'auteur et des commentaires...

  echo '<div class="card mb-4">';
  echo '<div class="card-body text-center">';
  echo "<h3 class='card-title'>" . $article['titre'] . "</h3>";
  echo '<h5 class="card-subtitle mb-2 text-muted">' . $article['sous_titre'] . ', <span class="w3-opacity">' . $date_format . '</span></h5>';
  echo '<div class="text-justify">
  <img src="' . $article['photo_articles'] . '" alt="" class="img-fluid mx-auto d-block mb-4" style="max-width:60%;">
  <p class="card-text">';
  // Structure du texte de la description avec saut de ligne entre chaque partie
  echo nl2br($article['description']);
  echo '</p>
  <div class="container row pt-3">
  <div class="col-md-4 text-md-start">
  <button class="btn btn-danger mr-2" type="button" onclick="window.location.href = \'index.php\'; return false;"><i class="fa-solid fa-chevron-left"></i> Revenir au blog</button>
  </div>
  ';
  
  if (isset($_SESSION['utilisateur_id'])) {
    $article_liked = $LikeHandler -> Verifier_ParUtilisateur_ParArticle($_SESSION['utilisateur_id'], $article['id']);

    echo '<div class="col-md-8 text-md-end"><form action="article.php?value='.($article['id']).'" method="POST">
    <input type="hidden" name="like_status" value="'.($article_liked ? ($article['id'].'vrai'.$_SESSION['utilisateur_id']) :($article['id'].'faux'.$_SESSION['utilisateur_id'])).'">
    <button type="submit" class="btn btn-light" onclick="likeFunction(this)">
    <b>'.($article_liked ? '✓ Liked' : '<i class="fa fa-thumbs-up"></i> Like').' '.count($LikeHandler->Obtenir_Par_IdArticle($article['id'])) . '</b>
    </button>
    </form>
    </div>';
  } else {
    $_SESSION['like_comment_wo_session'] = TRUE;
    echo '<div class="col-md-8 text-md-end"><button class="btn btn-light" onclick="location.href = \'login.php\';"><b><i class="fa fa-thumbs-up"></i> Like ' . count($LikeHandler->Obtenir_Par_IdArticle($article['id'])) . '</b></button>
    </div>';
  }

  echo '<div class="row">
    <div class="col-md-12 d-flex justify-content-center">
      <button class="btn btn-secondary" onclick="Hide_N_Show()">
        <b>Commentaires  </b> <span class="badge badge-white">' . count($resultat_ObtenirCommentaireArticle) . '</span>
      </button>
    </div>
  </div>';

  echo '<p class="clear"></p>';

  echo '<div class="container" id="commentaires" '.(isset($_SESSION['valid_ajout_commentaire']) || isset($_POST['delete_commentaire']) || isset($_SESSION['valid_modif_commentaire']) ? '' : 'hidden').'>
        <div class="pb-1" > <hr>';
  

  if (isset($_SESSION['utilisateur_id'])) {
    $_SESSION['like_comment_wo_session'] = FALSE;
    echo '<div class="text-center mt-1 mb-4">';
    echo '<button class="btn btn-info profile-button mr-2" type="button" data-toggle="modal" data-target="#modal_commentaire">Ajouter un commentaire <i class="fa-regular fa-comment"></i></button>';
    echo '</div>';
  }else{
    $_SESSION['like_comment_wo_session'] = TRUE;
    echo '<div class="text-center mt-1 mb-4">';
    echo '<button class="btn btn-info profile-button mr-2" type="button" onclick="window.location.href = \'login.php\'; return false;">Ajouter un commentaire <i class="fa-regular fa-comment"></i></button>';
    echo '</div>';
  }

  $index_commentaire = 0;
  if ($resultat_ObtenirCommentaireArticle){
    foreach($resultat_ObtenirCommentaireArticle as $commentaire){
  
      $date_origine = $commentaire['date_heure_commentaire'];
      $date_format = strftime("Le %e %B %Y à %H:%M", strtotime($date_origine));
    
      if ($connexion){
        $resultat_ObtenirUtilisateurActuel = $UtilisateurHandler-> Obtenir_Pseudo_Photo_ParId($commentaire['auteur']);
      }


      echo '
        <div class="card mt-3 '.($index_commentaire === count($resultat_ObtenirCommentaireArticle) - 1 ? "" : "mb-3" ).'">
          <div class="card-body d-flex">
            <img src="'.$resultat_ObtenirUtilisateurActuel['photo_profil'].'" class="img-fluid rounded" style="max-width: 70px; border: 5px ridge #89d5e3;">
            <div class="ms-3">
              <h4 class="card-subtitle mb-2 text-muted text-start">'.$resultat_ObtenirUtilisateurActuel['pseudo'].' </h4>
              <p class="card-text text-start">'.$commentaire['texte'].'</p>
            </div>
          </div>
          <div class="d-flex position-relative w-100 mt-5">
            <div class="position-absolute bottom-0 start-0">
              <div class="mr-auto p-2">
                <small>'.$date_format.'</small>
              </div>
            </div>
          </div>';
            
     if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $commentaire['auteur']){
        echo '<div class="position-absolute bottom-0 start-50 translate-middle-x">
                <div class="p-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modal_commentaire_edit_'.md5($_SESSION['utilisateur_id'].' '.$commentaire['auteur'].' '.$commentaire['texte']).'">Modifier <i class="fa-solid fa-pen"></i></button>
                </div>
              </div>';
      }

      if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == 1){
        echo '
        <form action="article.php?value='.$article['id'].'" method="POST">
          <div class="position-absolute bottom-0 end-0">
            <div class="p-2">
            <input type="hidden" name="commentaire_value" value="'.md5($commentaire['id'].' '.$commentaire['texte'].' '.$_SESSION['utilisateur_id']).'">
                <button type="submit" class="btn btn-sm btn-outline-danger" name="delete_commentaire" value="TRUE">Effacer <i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </form>';
    } elseif (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $commentaire['auteur']){
        echo '
        <form action="article.php?value='.$article['id'].'" method="POST">
          <div class="position-absolute bottom-0 end-0">
            <div class="p-2">
              <input type="hidden" name="commentaire_value" value="'.md5($commentaire['id'].' '.$commentaire['texte'].' '.$_SESSION['utilisateur_id']).'">
                <button type="submit" class="btn btn-sm btn-outline-danger" name="delete_commentaire" value="TRUE">Effacer <i class="fa-solid fa-trash"></i></button>
            </div>
          </div>
        </form>';
    }
    

  echo '</div>';
  }
}
   

  echo '
  </div>
  </div>
  </div>
        </div>';
  ?>
</div>

<div class="modal fade" id="modal_commentaire" tabindex="-1" role="dialog" aria-labelledby="modal_commentaire">
    <form action="article.php?value=<?php echo $article['id']?>" method="POST">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" id="modal_commentaire">Ajouter un commentaire</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                <div class="form-group">
                  <label class="labels">Commentaire : </label>
                  <textarea class="form-control" id="exampleFormControlTextarea1" name="commentaire" rows="3" placeholder="Commentaire" value=""></textarea>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <button type="submit" name="post_commentaire" value="TRUE" class="btn btn-primary" >Valider</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
foreach($resultat_ObtenirCommentaireArticle as $commentaire){
  if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $commentaire['auteur']){
    echo '<div class="modal fade" id="modal_commentaire_edit_'.md5($_SESSION['utilisateur_id'].' '.$commentaire['auteur'].' '.$commentaire['texte']).'" tabindex="-1" role="dialog" aria-labelledby="modal_commentaire">
    <form action="article.php?value='.$article['id'].'" method="POST">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" id="modal_commentaire">Modifier un commentaire</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                <div class="form-group">
                  <label class="labels">Commentaire : </label>
                  <textarea class="form-control" id="exampleFormControlTextarea1" name="new_commentaire" rows="3" placeholder="Commentaire" value="">'.$commentaire['texte'].'</textarea>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <button type="submit" name="edit_commentaire" value="TRUE" class="btn btn-primary" >Valider</button>
                </div>
            </div>
        </div>
    </form>
</div>
';
  }
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var x = document.getElementById("commentaires");
});
function Hide_N_Show() {
    var x = document.getElementById("commentaires");
    x.hidden = !x.hidden;
}
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="./ASSETS/ARTICLE/app_article.js"></script>
</body>
</html>
<?php

if (isset($_SESSION['valid_ajout_commentaire'])){
  unset($_SESSION['valid_ajout_commentaire']);
}