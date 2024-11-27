<?php
require_once "./DATA/queries.php";
session_set_cookie_params([
    'lifetime' => 3600, // 1 heure
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
  ]);
session_start();

$UtilisateurHandler = new Utilisateur();
$LikeHandler = new Like();

if (! $_SESSION){
    header('Location: login.php');
    exit();
}

function tronquerTexte($texte, $nombreDeMots) {
    $mots = explode(' ', $texte);
    $texteTronque = implode(' ', array_slice($mots, 0, $nombreDeMots));
    return $texteTronque;
}

function generateGravatarBase64($email, $size = 80, $default = 'identicon', $rating = 'g') {
    $gravatarUrl = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size . '&d=' . $default . '&r=' . $rating;

    $imageContent = file_get_contents($gravatarUrl);

    if ($imageContent !== false) {
        $base64Image = base64_encode($imageContent);
        return "data:image/png;base64,".$base64Image;
    } else {
        return false;
    }
}


$pseudo = $_SESSION['utilisateur_pseudo'];
$id = $_SESSION['utilisateur_id'];

$resultat_ObtenirMailUtilisateurParId = $UtilisateurHandler -> Obtenir_Mail_ParId($id);
$mail = $resultat_ObtenirMailUtilisateurParId['mail'];

$resultat_ObtenirPhotoUtilisateurParId = $UtilisateurHandler -> Obtenir_Photo_ParId($id);
$photo_profil = $resultat_ObtenirPhotoUtilisateurParId['photo_profil'];

$resultat_ObtenirDescriptionUtilisateurParId = $UtilisateurHandler -> Obtenir_Description_ParId($id);
$description = $resultat_ObtenirDescriptionUtilisateurParId['description'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['change_password']) || (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['new_password_bis']))) {
        $new_password = htmlspecialchars($_POST['new_password']);
        $new_password_bis = htmlspecialchars($_POST['new_password_bis']);
        if ($new_password === $new_password_bis &&
        (strpos($new_password, $pseudo) === FALSE) &&
        (strpos($new_password, $mail) === FALSE)){
            $resultat_ObtenirUtilisateurParPseudo = $UtilisateurHandler -> Obtenir_Pseudo_Password_ParPseudo($pseudo);
            if (password_verify($_POST['old_password'], $resultat_ObtenirUtilisateurParPseudo['mot_de_passe'])) {
                try{
                    $hash_pwd = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $UtilisateurHandler -> Mise_A_Jour_MotDePasse($hash_pwd, $id);
                    $_SESSION['success_modification_mot_de_passe'] = TRUE;
                    header('Location: settings.php');
                    exit();
                } catch (PDOException $e) {
                    $_SESSION['error_modification_mot_de_passe'] = TRUE;
                    echo "Erreur lors de l'ajout de l'enregistrement : " . $e->getMessage();
                }
            }else{
                $_SESSION['warning_bis_modification_mot_de_passe'] = TRUE;
            }
        }else{
            $_SESSION['warning_modification_mot_de_passe'] = TRUE;
        } 
    }
    
    if (isset($_POST['delete_photo_profil']) && $_POST['delete_photo_profil'] && ! isset($_POST['old_password'])){
        try{
            $UtilisateurHandler -> Mise_A_Jour_PhotoProfil(generateGravatarBase64($mail),$id);
            $_SESSION['success_photo'] = TRUE;
        }catch (PDOException $e) {
            $_SESSION['error_modification'] = TRUE;
            echo "Erreur lors de l'ajout de l'enregistrement : " . $e->getMessage();
        }
        header('Location: settings.php');
        exit();
    }

    if (isset($_FILES['photo_profil_new']) && $_FILES['photo_profil_new']['name'] != "" ) {
        $file = $_FILES['photo_profil_new'];

        // Vérifier si le fichier est une image
        $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {

            // Lire le contenu du fichier
            $content = file_get_contents($file['tmp_name']);

            // Encoder le contenu en base64
            $base64 = base64_encode($content);

            $b64_img = 'data:image/'.$imageFileType.';base64,'.$base64;

            $_SESSION['success_photo'] = TRUE;

            $UtilisateurHandler -> Mise_A_Jour_PhotoProfil($b64_img,$id);

        } else {
            $_SESSION['error_photo'] = TRUE;
        }
        header('Location: settings.php');
        exit();
    }

    if (isset($_POST['modification']) && $_POST['modification'] ) {
        $pseudo_new = htmlspecialchars($_POST['pseudo_new']);
        $mail_new = htmlspecialchars($_POST['mail_new']);
        $description_new = htmlspecialchars($_POST['description_new']);

        $resultat_VerifUtilisateurPseudo = $UtilisateurHandler -> Verifier_Pseudo_ParPseudo($pseudo_new);

        if($resultat_VerifUtilisateurPseudo && $pseudo_new != $pseudo){
            $_SESSION['error_pseudo_new'] = TRUE;
            //echo "Pseudo déjà utilisé";
        }else{

            $resultat_VerifUtilisateurMail = $UtilisateurHandler -> Verifier_ParMail($mail_new);

            if($resultat_VerifUtilisateurMail && $mail_new != $mail){
                $_SESSION['error_mail_new'] = TRUE;
                //echo "Mail déjà utilisé";
            }else{
            
                try {
                    $resultat_MiseAJourUtilisateur = $UtilisateurHandler -> Mise_A_Jour($pseudo_new,$mail_new,$description_new,$id);
                    if ($resultat_MiseAJourUtilisateur){
                        $_SESSION['valid_modification'] = TRUE;
                        $_SESSION['utilisateur_pseudo'] = $pseudo_new;
                        header('Location: settings.php');
                        exit();
                    }else{
                        $_SESSION['error_modification'] = TRUE;
                        echo "Erreur lors de l'ajout de l'enregistrement : " . implode(", ", $requete_AjouterUtilisateur->errorInfo());
                    }
                } catch (PDOException $e) {
                    $_SESSION['error_modification'] = TRUE;
                    echo "Erreur lors de l'ajout de l'enregistrement : " . $e->getMessage();
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Paramètres du profil utilisateur</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="./ASSETS/SETTINGS/styles_settings.css">
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
</head>
<body>
<?php

echo'<div class="position-relative d-flex justify-content-center mt-3 mb-4">';

if (isset($_SESSION['error_pseudo_new']) && $_SESSION['error_pseudo_new']) {
    echo '<div class="position-absolute top-0 start-0" id="error_pseudo_new"><div class="alert alert-danger" role="alert">
    Le pseudo que vous avez choisi est déjà utilisé par une autre personne !
    </div></div>';
    unset($_SESSION['error_pseudo_new']);
} elseif (isset($_SESSION['error_mail_new']) && $_SESSION['error_mail_new']) {
    echo '<div class="position-absolute top-0 start-0" id="error_mail_new"><div class="alert alert-danger" role="alert">
    Le mail que vous avez choisi est déjà utilisé par une autre personne !
    </div></div>';
    unset($_SESSION['error_mail_new']);
} elseif (isset($_SESSION['error_modification']) && $_SESSION['error_modification']) {
    echo '<div class="position-absolute top-0 start-0" id="error_modification"><div class="alert alert-danger" role="alert">
    Erreur lors de la modification ! Merci de contacter l\'administrateur du site.
    </div></div>';
    unset($_SESSION['error_modification']);
} elseif (isset($_SESSION['valid_modification']) && $_SESSION['valid_modification']) {
    echo '<div class="position-absolute top-0 start-0" id="valid_modification"><div class="alert alert-success" role="alert">
    Informations modifiées avec succès !
    </div></div>';
    unset($_SESSION['valid_modification']);
}

if (isset($_SESSION['error_photo']) && $_SESSION['error_photo']) {
    echo '<div class="position-absolute top-0 start-0" id="error_photo"><div class="alert alert-warning" role="alert">
    Le fichier sélectionné n\'est pas une image valide !
    </div></div>';
    unset($_SESSION['error_photo']);
} elseif (isset($_SESSION['success_photo']) && $_SESSION['success_photo']) {
    echo '<div class="position-absolute top-0 start-0" id="success_photo"><div class="alert alert-success" role="alert">
    Photo du profil modifiée avec succès !
    </div></div>';
    unset($_SESSION['success_photo']);
}

if (isset($_SESSION['error_modification_mot_de_passe']) && $_SESSION['error_modification_mot_de_passe']) {
    echo '<div class="position-absolute top-0 start-0" id="error_modification_mot_de_passe"><div class="alert alert-danger" role="alert">
    Erreur lors de la modification du mot de passe !
    </div></div>';
    unset($_SESSION['error_modification_mot_de_passe']);
} elseif (isset($_SESSION['warning_modification_mot_de_passe']) && $_SESSION['warning_modification_mot_de_passe']) {
    echo '<div class="position-absolute top-0 start-0" id="warning_modification_mot_de_passe"><div class="alert alert-warning" role="alert">
    Les nouveaux mots de passe saisis ne sont pas identiques ou possèdent votre mail/pseudo !
    </div></div>';
    unset($_SESSION['warning_modification_mot_de_passe']);
} elseif (isset($_SESSION['warning_bis_modification_mot_de_passe']) && $_SESSION['warning_bis_modification_mot_de_passe']) {
    echo '<div class="position-absolute top-0 start-0" id="warning_bis_modification_mot_de_passe"><div class="alert alert-warning" role="alert">
    L\'ancien mot de passe saisi n\'est pas valide !
    </div></div>';
    unset($_SESSION['warning_bis_modification_mot_de_passe']);
} elseif (isset($_SESSION['success_modification_mot_de_passe']) && $_SESSION['success_modification_mot_de_passe']) {
    echo '<div class="position-absolute top-0 start-0" id="success_modification_mot_de_passe"><div class="alert alert-success" role="alert">
    Mot de passe modifié avec succès !
    </div></div>';
    unset($_SESSION['success_modification_mot_de_passe']);
}


echo '</div>';
?>
<form action="settings.php" method="POST" enctype="multipart/form-data">
    <div class="rounded bg-white mt-5 mb-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-3 border-right">
                <div class="position-relative d-flex flex-column align-items-center text-center p-3 py-5">
                    <?php
                    if ($photo_profil != ""){
                        echo '<div class="container fill d-flex mr-2 ">
                        <img class="mt-5 img-fluid border border-info rounded-top w-75 mx-auto" width="150px" src="'.$photo_profil.'" >
                        <input class="position-absolute top-0 start-0 h-75 w-75 d-inline-block" style="opacity:0;" type="file" title="Choisir une nouvelle photo de profil" name="photo_profil_new" accept="image/png, image/jpeg" id="photo_profil_input">
                        
                    </div>';
                    if ($photo_profil != generateGravatarBase64($mail)){
                        echo '<button style="z-index:5;" class="btn btn-danger mt-2 w-25 " type="submit" name="delete_photo_profil" value="TRUE"><i class="fa-solid fa-trash" onkeydown="return event.key != 13;"></i> SUPPRIMER</button>';
                    }
                    
                    }else{
                        echo '<div class="container fill d-flex">
                        <img class="rounded-circle mt-5 img-fluid mx-auto" width="150px" src="https://icons-for-free.com/iconfiles/png/512/user+icon-1320190636314922883.png">
                        <input class="position-absolute top-0 start-0 h-100 w-75 d-inline-block" style="opacity:0;" type="file" title="Choisir une nouvelle photo de profil" name="photo_profil_new" accept="image/png, image/jpeg" id="photo_profil_input">
                    </div>';
                    }
                    
                    ?>
                </div>
            </div>
            <div class="col-md-5 border-right">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-right">Paramètre du profil</h4>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12"><label class="labels">Pseudo de votre profil : </label><input type="text" class="form-control" placeholder="Pseudo" name="pseudo_new" id="pseudo_new" value="<?php echo $pseudo;?>"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12"><label class="labels">Mail lié à votre profil :</label><input type="text" class="form-control" placeholder="Mail" name="mail_new" value="<?php echo $mail ?>"></div>
                        <div class="col-md-12"><label class="labels">Description de votre profil : </label><textarea class="form-control" id="exampleFormControlTextarea1" name="description_new" rows="3" placeholder="Description" value=""><?php echo $description; ?></textarea></div>
                    </div>
                    <div class="row mt-4">
                        <div class="">
                            <div class="text-center alert alert-primary" role="alert">
                                Vous pouvez modifier votre photo de profil en cliquant dessus !
                            </div>
                        </div>    
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="text-center">Modification du mot de passe : 
                                <button type="button" class="ml-2 btn btn-warning" data-toggle="modal" data-target="#modal_change_password">
                                    <i class="fa-solid fa-key"></i>
                                </button> 
                            </h5>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <button class="btn btn-danger profile-button mr-1" type="button" onclick="window.location.href = 'index.php'; return false;"><i class="fa-solid fa-chevron-left"></i> Revenir au blog</button>
                        <button class="btn btn-primary profile-button ml-1" id="modification_donnees" name="modification" value="TRUE"><i class="fa-regular fa-floppy-disk"></i> Enregistrer les modifications</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 py-5">
                    <?php
                    
                    $resultat_Afficher5like = $LikeHandler -> Obtenir_5_Derniers_Article_Par_Utilisateur($id);

                    echo '<h4 class="text-center">Liste des '.count($resultat_Afficher5like).' derniers articles aimées</h4>';

                    echo '<table class="table text-right">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Titre</th>
                        <th scope="col">Description</th>
                        <th scope="col">Auteur</th>
                        <th scope="col">Date</th>
                      </tr>
                    </thead>
                    <tbody>';

                    foreach($resultat_Afficher5like as $article){
                        setlocale(LC_ALL, 'fr_FR.UTF8', 'fr_FR','fr','fr','fra','fr_FR@euro');
                        $date_origine = $article['date_heure_article'];
                        $date_format = strftime("%e %B %Y \xC3\xA0 %H:%M", strtotime($date_origine));

                        //$resultat_Obtenir_Pseudo_Photo_ParId = $UtilisateurHandler -> Obtenir_Pseudo_Photo_ParId($article['auteur']);

                    //<th scope="row">'.(intval(array_search($article, $resultat_Afficher5like))+1).'</th>
                    echo'<tr>
                    <th scope="row">'.(-intval(array_search($article, $resultat_Afficher5like))+count($resultat_Afficher5like)).'</th>
                    <td>'.$article['titre'].'</td>
                    <td>'.tronquerTexte($article['description'], 8).'...</td>
                    <td>'.$UtilisateurHandler -> Obtenir_Pseudo_Photo_ParId($article['auteur'])['pseudo'].'</td>
                    <td>'.$date_format.'</td>
                  </tr>';

                    //print_r($resultat_Obtenir_Pseudo_Photo_ParId);
                    }

                echo'
                    </tbody>
                  </table>';
                    //print_r($resultat_Afficher5like);


                    ?>

                    
                    <!--<div class="d-flex justify-content-between align-items-center experience"><span>Edit Experience</span><span class="border px-3 p-1 add-experience"><i class="fa fa-plus"></i>&nbsp;Experience</span></div><br>
                    <div class="col-md-12"><label class="labels">Experience in Designing</label><input type="text" class="form-control" placeholder="experience" value=""></div> <br>
                    <div class="col-md-12"><label class="labels">Additional Details</label><input type="text" class="form-control" placeholder="additional details" value=""></div>-->
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</form>


<div class="modal fade" id="modal_change_password" tabindex="-1" role="dialog" aria-labelledby="modal_change_password">
    <form action="settings.php" method="POST">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title" id="modal_change_password">Modification de mot de passe</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                <div class="form-group">
                    <label class="labels">Ancien mot de passe</label>
                    <input type="password" class="form-control" placeholder="Ancien mot de passe" name="old_password">
                </div>
                <div class="form-group">
                    <label class="labels">Nouveau mot de passe</label>
                    <input type="password" class="form-control" placeholder="Nouveau mot de passe" name="new_password">
                </div>
                <div class="form-group">
                    <label class="labels">Nouveau mot de passe x2</label>
                    <input type="password" class="form-control" placeholder="Nouveau mot de passe x2" name="new_password_bis">
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <button type="submit" name="change_password" value="TRUE" class="btn btn-primary" >Enregistrer les modifications</button>
                </div>
            </div>
        </div>
    </form>
</div>

</body>
<script src="./ASSETS/SETTINGS/app_settings.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
    document.getElementById('photo_profil_input').addEventListener('change', function () {
        document.getElementById('modification_donnees').click();
    });
</script>
</html>