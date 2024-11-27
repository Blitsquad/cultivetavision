<?php
require_once "./DATA/queries.php";
session_set_cookie_params([
    'lifetime' => 3600, // 1 heure
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
  ]);
session_start();

if (! $_SESSION){
    $_SESSION['article_wo_session'] = TRUE;
    header('Location: login.php');
    exit();
}

$articleHandler = new articles();
$UtilisateurHandler = new Utilisateurs();
$TagHandler = new Tags();

$pseudo = $_SESSION['utilisateur_pseudo'];
$id = $_SESSION['utilisateur_id'];

$tags = $TagHandler-> Obtenir();
$tagNames = array_column($tags, 'libelle');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete_photo_profil']) && $_POST['delete_photo_profil']){
        unset($_SESSION['image_article_A']);

        if (empty($_POST['titre_article']) || empty($_POST['sous_titre_article']) || empty($_POST['description_article']) ){
            $_SESSION['PREVIOUS_POST_A'] = $_POST;
        }

        header('Location: ajouterarticle.php');
        exit();
    }

    if (isset($_FILES['photo_article']) && $_FILES['photo_article']['name'] != ""){
        $file = $_FILES['photo_article'];
        $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {

            // Lire le contenu du fichier
            $content = file_get_contents($file['tmp_name']);

            // Encoder le contenu en base64
            $base64 = base64_encode($content);
            $b64_img = 'data:image/'.$imageFileType.';base64,'.$base64;

            $_SESSION['image_article_A'] = $b64_img;
            $_SESSION['valid_photo_article'] = TRUE;
            $_SESSION['PREVIOUS_POST_A'] = $_POST;


        }else {
            $_SESSION['error_photo_article'] = TRUE;
        }
        if (empty($_POST['titre_article']) || empty($_POST['sous_titre_article']) || empty($_POST['description_article']) ){
            $_SESSION['PREVIOUS_POST_A'] = $_POST;
        }
        
        header('Location: ajouterarticle.php');
        exit();

    }

    if (isset($_POST['sauvegarder_article']) && $_POST['sauvegarder_article']){
        if (! empty($_POST['titre_article']) || ! empty($_POST['sous_titre_article']) || ! empty($_POST['description_article']) ){
            $_SESSION['PREVIOUS_POST_A'] = $_POST;
        }
    }

    if (isset($_POST['effacer_article']) && $_POST['effacer_article']){
        unset($_SESSION['image_article_A']);
        unset($_SESSION['PREVIOUS_POST']);
        header('Location: ajouterarticle.php');
        exit();
    }

    $titre = htmlspecialchars($_POST['titre_article']);
    $sous_titre = htmlspecialchars($_POST['sous_titre_article']);
    $description = htmlspecialchars($_POST['description_article']);

    $tag = isset($_POST['tag_article']) && strlen($_POST['tag_article']) > 1 ? htmlspecialchars($_POST['tag_article']) : ($_SESSION['no_tag_article'] = true);

    $photo_article = isset($_SESSION['image_article_A']) ? $_SESSION['image_article_A'] : ($_SESSION['no_photo_article'] = true);


    if (isset($tag) && ! isset($_SESSION['no_tag_article'])){
        if (! in_array($tag,$tagNames)){
            try {
                $resultat_AjouterTag = $TagHandler -> Ajouter($tag);
                if ( ! $resultat_AjouterTag){
                    $_SESSION['error_ajout_tag'] = TRUE;
                    $_SESSION['error_ajout_tag_msg'] = "Erreur lors de l'ajout de l'enregistrement : " . implode(", ", $resultat_AjouterTag->errorInfo());
                }else{
                    unset($_SESSION['error_ajout_tag']);
                    unset($_SESSION['error_ajout_tag_msg']);
                }
            } catch (PDOException $e) {
                $_SESSION['error_ajout_tag'] = TRUE;
                $_SESSION['error_ajout_tag_msg'] = "Erreur lors de l'ajout de l'enregistrement : " . $e->getMessage();
            }
        }
    }else{
        $_SESSION['no_tag_article'] = TRUE;
        $_SESSION['PREVIOUS_POST_A'] = $_POST;
    }

    if (isset($_POST['publier_article']) && $_POST['publier_article']){
        if (! empty($_POST['titre_article']) || ! empty($_POST['sous_titre_article']) || ! empty($_POST['description_article']) ){
            $_SESSION['PREVIOUS_POST_A'] = $_POST;
        }

        if (
            strlen(trim($titre)) > 5 && !empty($titre) &&
            strlen(trim($sous_titre)) > 5 && !empty($sous_titre) &&
            strlen(trim($description)) > 100 && !empty($description) &&
            !empty($tag) &&
            !empty($photo_article)
        ) {
            $resultat_ObtenirIdParTag = $TagHandler -> ObtenirId($tag);
            try {
                $resultat_AjouterArticle = $articleHandler-> Ajouter($titre, $description, date("Y-m-d H:i:s"), $id, $photo_article, $resultat_ObtenirIdParTag['id'], $sous_titre);
                if ( ! $resultat_AjouterArticle){
                    $_SESSION['error_ajout_article'] = TRUE;
                    $_SESSION['error_ajout_article_msg'] = "Erreur lors de l'ajout de l'enregistrement : " . implode(", ", $resultat_AjouterArticle->errorInfo());
                }else{
                    $_SESSION['valid_article_publication'] = TRUE;
                    unset($_SESSION['PREVIOUS_POST']);
                    unset($_SESSION['error_ajout_article']);
                    unset($_SESSION['error_ajout_article_msg']);
                    unset($_SESSION['image_article_A']);
                    header('Location: ajouterarticle.php');
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error_ajout_article'] = TRUE;
                $_SESSION['error_ajout_article_msg'] = "Erreur lors de l'ajout de l'enregistrement : " . $e->getMessage();
            }
        }else{
            if ( strlen(trim($titre)) < 5){
                $_SESSION['warning_titre_article'] = TRUE;
            }elseif ( strlen(trim($sous_titre)) < 5){
                $_SESSION['warning_sous_titre_article'] = TRUE;
            }elseif ( strlen(trim($description)) < 100){
                $_SESSION['warning_description_article'] = TRUE;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Gestion de l'article</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="./assets/ajouterarticle/styles_ajouterarticle.css">
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
<body>
<div class="mt-1 pt-3 mx-5 position-relative text-center">
<?php
if(isset($_SESSION['error_photo_article']) && $_SESSION['error_photo_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="error_photo_article"><div class="text-center alert alert-warning mx-auto " role="alert">
    Le fichier sélectionné n\'est pas une image valide !
    </div></div>';
    unset($_SESSION['error_photo_article']);
}elseif(isset($_SESSION['no_photo_article']) && $_SESSION['no_photo_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="no_photo_article"><div class="text-center alert alert-warning w-50" role="alert">
    La photo de l\'article n\'a pas été sélectionnée !
    </div></div>';
    unset($_SESSION['no_photo_article']);
}elseif(isset($_SESSION['valid_photo_article']) && $_SESSION['valid_photo_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="valid_photo_article"><div class="text-center alert alert-success mx-auto " role="alert">
    Photo de l\'article sélectionnée avec succès !
    </div></div>';
    unset($_SESSION['valid_photo_article']);
    unset($_SESSION['no_tag_article']);
}elseif(isset($_SESSION['error_ajout_tag']) && $_SESSION['error_ajout_tag']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="error_ajout_tag"><div class="text-center alert alert-warning w-50" role="alert">
    '.$_SESSION['error_ajout_tag_msg'].'
    </div></div>';
    unset($_SESSION['error_ajout_tag']);
    unset($_SESSION['error_ajout_tag_msg']);
}elseif(isset($_SESSION['error_ajout_article']) && $_SESSION['error_ajout_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="error_ajout_article"><div class="text-center alert alert-warning w-50" role="alert">
    '.$_SESSION['error_ajout_article_msg'].'
    </div></div>';
    unset($_SESSION['error_ajout_article']);
    unset($_SESSION['error_ajout_article_msg']);
}elseif(isset($_SESSION['warning_titre_article']) && $_SESSION['warning_titre_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="warning_titre_article"><div class="text-center alert alert-warning w-50" role="alert">
    Le titre comporte moins de 5 caractères.
    </div></div>';
    unset($_SESSION['warning_titre_article']);
}elseif(isset($_SESSION['warning_sous_titre_article']) && $_SESSION['warning_sous_titre_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="warning_sous_titre_article"><div class="text-center alert alert-warning w-50" role="alert">
    Le sous-titre comporte moins de 5 caractères.
    </div></div>';
    unset($_SESSION['warning_sous_titre_article']);
}elseif(isset($_SESSION['warning_description_article']) && $_SESSION['warning_description_article']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="warning_description_article"><div class="text-center alert alert-warning w-50" role="alert">
    La description comporte moins de 100 caractères.
    </div></div>';
    unset($_SESSION['warning_description_article']);
}elseif(isset($_SESSION['valid_article_publication']) && $_SESSION['valid_article_publication']){
    echo '<div class="w-100 position-absolute top-0 start-50 translate-middle-x d-flex justify-content-center" id="valid_article_publication"><div class="text-center alert alert-success w-50" role="alert">
    Article envoyé !
    </div></div>';
    unset($_SESSION['valid_article_publication']);

}

?>
</div>
<form action="ajouterarticle.php" method="POST" enctype="multipart/form-data">
    <div class="container rounded bg-white mt-5 mb-5 pt-5">
        <div class="row">
            <div class="col-md-3 border-right">
                <div class="position-relative d-flex flex-column align-items-center text-center p-3 py-5">
                    <?php
                    if (isset($_SESSION['image_article_A']) && $_SESSION['image_article_A'] != ""){
                        echo '<div class="container fill d-flex mr-2">
                        <img class="mt-5 ml-4 img-fluid border border-info rounded-top w-75" width="150px" src="'.$_SESSION['image_article_A'].'" >
                        <input class="position-absolute top-0 start-0 h-75 w-75 d-inline-block" style="opacity:0;" type="file" title="Choisir une nouvelle photo de profil" name="photo_article" accept="image/png, image/jpeg" id="photo_article_input">
                    </div>';

                        echo '<button style="z-index:5;" class="btn btn-danger mt-2 w-75" type="submit" name="delete_photo_profil" value="TRUE"><i class="fa-solid fa-trash"></i> SUPPRIMER</button>';
                    
                    }else{
                        echo '<div class="container fill d-flex ml-5">
                        <i class="fa-solid fa-upload rounded mt-5 ml-4 img-fluid" style="font-size:50px;"></i>

                        <input class="position-absolute top-0 start-0 h-100 w-75 d-inline-block" style="opacity:0;" type="file" title="Choisir une nouvelle photo de profil" name="photo_article" accept="image/png, image/jpeg" id="photo_article_input">
                    </div>';
                    echo'
                    <div class="row mt-4">
                        <div class="">
                            <div class="text-center alert alert-primary" role="alert">
                                Ajouter une photo à votre article en cliquant sur : <i class="fa-solid fa-upload"></i>
                            </div>
                        </div>    
                    </div>';
                    }
                    
                    ?>
                </div>
            </div>
            <div class="col-md-5 border-right" style="background-color: white; color: black;">
    <div class="p-3 py-5">
    <h3 class="text-center">♜ Entre dans la puissance du mindset</h3>
    <br>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-right">Ajouter un article</h4>
        </div>
        <div class="row mt-2">
            <div class="col-md-12"><label class="labels">Titre de l'article : </label><input type="text" class="form-control" placeholder="Titre de l'article" name="titre_article" id="titre_article"
                    <?php
                    if(isset($_SESSION['PREVIOUS_POST'])){
                        if(isset($_SESSION['PREVIOUS_POST']['titre_article'])){
                            echo "value=\"".$_SESSION['PREVIOUS_POST']['titre_article']."\"";
                        }
                    }
                    ?>></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12"><label class="labels">Sous-titre de l'article :</label><input type="text" class="form-control" placeholder="Sous-titre de l'article" name="sous_titre_article"
                    <?php
                    if(isset($_SESSION['PREVIOUS_POST'])){
                        if(isset($_SESSION['PREVIOUS_POST']['sous_titre_article'])){
                            echo "value=\"".$_SESSION['PREVIOUS_POST']['sous_titre_article']."\"";
                        }
                    }
                    ?>></div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12"><label class="labels">Description de votre article : </label><textarea class="form-control" id="exampleFormControlTextarea1" name="description_article" rows="5" placeholder="Description de l'article"><?php
                    if(isset($_SESSION['PREVIOUS_POST'])){
                        if(isset($_SESSION['PREVIOUS_POST']['description_article'])){
                            echo $_SESSION['PREVIOUS_POST']['description_article'];
                        }
                    }
                    ?></textarea></div>
        </div>

        <div class="mt-5 text-center">
            <button class="btn btn-danger profile-button" type="button" onclick="window.location.href = 'index.php'; return false;"><i class="fa-solid fa-chevron-left"></i> Revenir au blog</button>
        </div>
        <div class="mt-4 text-center">
            <button class="btn btn-danger profile-button mr-4" id="effacer_article" name="effacer_article" value="TRUE"><i class="fa-solid fa-trash"></i> Effacer l'article</button>
            <?php 
                    echo '<button class="btn btn-success profile-button ml-4" id="publier_article" name="publier_article" value="TRUE"><i class="fa-regular fa-paper-plane"></i> Publier l\'article</button>';
                ?>
        </div>
    </div>
</div>
</div>
</div>
</div>
</form>
</body>
<script src="./ASSETS/AJOUTERARTICLE/app_ajouterarticle.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function(){
    // Utiliser la fonction typeahead() pour activer la recherche
    var articles = <?php echo json_encode($tagNames); ?>;
    $('#articleSearch').typeahead({
      source: articles
    });
  });
</script>
<script>
document.getElementById('photo_article_input').addEventListener('change', function () {
    document.getElementById('sauvegarder_article').click();
});
</script>
</html>