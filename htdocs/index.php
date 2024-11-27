<?php 
require_once "./DATA/queries.php";
session_set_cookie_params([
  'lifetime' => 3600, // 1 heure
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
// Créer une instance de la classe Articles
$articleHandler = new Articles();
$UtilisateurHandler = new Utilisateurs();
$PrivilegesHandler = new Privileges();



// Vérifier si le privilégié est connecté
if (isset($_SESSION['privileges'])) {
    // L'utilisateur est connecté
    $connectButtonText = "Administration";
    $connectButtonLink = "administration.php"; // Lien vers le script de déconnexion
} else {
    // Le privilégié n'est pas connecté
    $connectButtonText = "";
    $connectButtonLink = ""; // Lien vers le formulaire de connexion
    $registerButtonText = ""; // Lien vers le formulaire de connexion 
    $registerButtonLink = ""; // Lien vers le formulaire d'inscription
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    // Rediriger l'utilisateur vers la page de connexion
    header("Location: login.php");
    exit; // Arrêter l'exécution du script
}



// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['pseudo'])) {
    // L'utilisateur est connecté
    $connectButtonText = "Se déconnecter";
    $connectButtonLink = "logout.php"; // Lien vers le script de déconnexion
} else {
    // L'utilisateur n'est pas connecté
    $connectButtonText = "Se connecter";
    $connectButtonLink = "login.php"; // Lien vers le formulaire de connexion
    $registerButtonText = "S'enregistrer"; // Lien vers le formulaire de connexion 
    $registerButtonLink = "register.php"; // Lien vers le formulaire d'inscription
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    // Rediriger l'utilisateur vers la page de connexion
    header("Location: login.php");
    exit; // Arrêter l'exécution du script
}

// Vérifier si le formulaire d'ajout d'article a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_article'])) {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_heure_articles = date('Y-m-d H:i:s'); // Utiliser la date actuelle
    $auteur = $_SESSION['utilisateur_id']; // L'auteur est l'utilisateur connecté
    $photo_articles = $_POST['photo_articles'];
    $tag = $_POST['tag'];
    $sous_titre = $_POST['sous_titre'];
    
    // Appeler la méthode Ajouter de la classe Articles
    $resultat = $articlesManager->Ajouter($titre, $description, $date_heure_articles, $auteur, $photo_articles, $tag, $sous_titre);

    // Récupérer l'auteur de l'article
    $resultat = $articleHandler->Profil_Utilisateurs_byid($auteur);
    
    if ($resultat) {
        // L'article a été ajouté avec succès
        echo "L'article a été ajouté avec succès.";
    } else {
        // Une erreur s'est produite lors de l'ajout de l'article
        echo "Une erreur s'est produite lors de l'ajout de l'article.";
    }
}

// Vérifier si le formulaire de modification d'article a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_article'])) {
    // Récupérer les données du formulaire
    $id_article = $_POST['id_article'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_heure_articles = date('Y-m-d H:i:s'); // Utiliser la date actuelle
    $auteur = $_SESSION['utilisateur_id']; // L'auteur est l'utilisateur connecté
    $photo_articles = $_POST['photo_articles'];
    $tag = $_POST['tag'];
    $sous_titre = $_POST['sous_titre'];
    
    // Appeler la méthode Modifier de la classe Articles
    $resultat = $articlesManager->Modifier($id_article, $titre, $description, $date_heure_articles, $auteur, $photo_articles, $tag, $sous_titre);
    
    if ($resultat) {
        // L'article a été modifié avec succès
        echo "L'article a été modifié avec succès.";
    } else {
        // Une erreur s'est produite lors de la modification de l'article
        echo "Une erreur s'est produite lors de la modification de l'article.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Mettez ici les balises meta, les liens vers les feuilles de style, etc. -->
        <link rel="stylesheet" href="./assets/css/styles.css">
        <link rel="stylesheet" href="./assets/index/styles.css">
        <link rel="stylesheet" href="./ASSETS/ADMINISTRATION/styles_administration.css">
        <link rel="stylesheet" href="./assets/css/styles.css">
        <link rel="stylesheet" href="./assets/index/styles.css">

        <title>CultiveTaVision | Blog</title>
        <!-- Favicon-->
        <link rel="icon" type="image/png" href="./assets/favicon.png"> <!-- Assurez-vous que le chemin vers votre favicon est correct -->
        <!-- Core theme CSS (includes Bootstrap)-->
        <!--<link href="css/styles.css" rel="stylesheet" />-->
    </head>
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
        <header class="py-5 mb-11" style="background-image: url('https://cdn.futura-sciences.com/buildsv6/images/wide1920/3/f/2/3f221b86fe_121830_cerveau-organe.jpg'); background-size: cover; background-position: center; background-blend-mode: overlay; color: white; background-color: rgba(0, 0, 0, 0.5);">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder"><strong>CultiveTaVision</strong></h1>
            <p class="lead mb-0">Grandir ensemble, réussir ensemble.</p>
        </div>
    </div>
</header>

<!-- Side widget-->
<div class="card mb-4" style="max-width: 850px; margin-left: 280px; margin-right: 300px; margin-top: 20px;">
    <div class="card-header text-white bg-black">
    ➤ Bienvenue sur le Blog CultiveTaVision ♦ | ♛ Conseils Mindset | ❦ Entraide | ♜ Développement Personnel
    </div>
    <div class="card-body">✧ Plongez dans l'univers de CultiveTaVision, où vous trouverez les meilleurs conseils pour réussir tant mentalement que financièrement. ✪ Rejoignez une communauté engagée où l'entraide est au cœur de notre démarche. Partagez vos propres conseils et découvrez ceux des autres pour avancer ensemble vers le succès ! 🌟</div>
</div>





        <!-- Page content-->
        <div class="container">
            <div class="row">
                <!-- Blog entries-->
                <div class="col-lg-8">
                    <div class="container mt-4">
                        <div class="text-align:center">
                        </div>
                    </div>

                    

                    <!-- Titre 'Top 4 des Meilleures Articles' -->
                    <header class="py-7 mb-4" style="background-image: url('https://th.bing.com/th/id/OIP.ZQ4dky6x4Mqpea1vmZN2gQAAAA?rs=1&pid=ImgDetMain'); background-size: cover; background-position: center; background-blend-mode: overlay; color: white; background-color: rgba(0, 0, 0, 0.3);">
    <div class="container">
        <div class="text-center my-11">
        <h1 class="fw-bolder">✪ Top 4 Des Meilleures Articles ✪</h1>
        </div>
    </div>
</header>

<?php 
// Vérifier si l'article a été supprimé avec succès
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_article'])) {
    // Récupérer l'ID de l'article à supprimer
    $id_article = $_POST['id_article'];
    
    // Appeler la méthode Supprimer de la classe Articles
    $resultat = $articleHandler->Supprimer($id_article);
    
    // Afficher le message de suppression avec un style spécifique
    if ($resultat) {
        // L'article a été supprimé avec succès
        echo '<div style="background-color: #4CAF50; color: black; text-align: center; padding: 10px;">Votre article a bien été supprimé.</div>';
    } else {
        // Une erreur s'est produite lors de la suppression de l'article
        echo '<div style="background-color: #f44336; color: white; text-align: center; padding: 10px;">Une erreur s\'est produite lors de la suppression de l\'article.</div>';
    }
}
?>

<!-- AFFICHAGE DES ARTICLES -->

<?php
// Récupérer les articles depuis la base de données
$articles = $articleHandler->GetAllArticles();

// Vérifier si des articles ont été récupérés
if ($articles) {
    foreach ($articles as $article) {
        // Afficher chaque article sous forme de carte
    }
}


?>

 <!-- Nested row for non-featured blog posts-->
 <div class="row">
                        <div class="col-lg-6">
                            <!-- Blog post-->
                            <div class="card mb-4">
                                
                                <a href="#!"><img class="card-img-top" src="https://img.freepik.com/photos-premium/statue-du-dieu-grec-stoicien_962194-133.jpg" alt="..." /></a>
                                <div class="card-body">
                                    <div class="small text-muted">Célébrer les petites victoires</div>
                                    <h2 class="card-title h4">Pratiquer une gratitude quotidienne ♣</h2>
                                    <p class="card-text">❦ Transformez votre vie avec la gratitude en intégrant la gratitude dans notre quotidien, nous devenons les architectes de notre propre bonheur. Chaque jour est une nouvelle opportunité de cultiver la gratitude, de célébrer la vie et d'atteindre de nouveaux sommets de bien-être. Rejoignez-nous dans cette .....✨</p>
                                    <a class="btn btn-dark" href="./article.php?value=25">Découvrir →</a>
                                </div>
                            </div>
                            <!-- Blog post-->
                            <div class="card mb-4">
                            <a href="#!"><img class="card-img-top" src="https://img.freepik.com/fotos-premium/estatua-dios-griego-estoico_962194-116.jpg" alt="..." /></a>
                                <div class="card-body">
                                <div class="small text-muted">Réaligner les pensées négatives</div>
                                    <h2 class="card-title h4">Cultiver la pensée positive ✪</h2>
                                    <p class="card-text">❦ Devenez le maître de votre destin
                                en cultivant la pensée positive, nous devenons les maîtres de notre destin, les capitaines de notre âme. Rejoignez-nous dans cette quête de croissance personnelle et découvrez le pouvoir transformateur de la pensée positive pour .....</p>
                                <a class="btn btn-dark" href="./article.php?value=26">Découvrir →</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <!-- Blog post-->
                            <div class="card mb-4">
                            <a href="#!"><img class="card-img-top" src="https://img.freepik.com/premium-photo/stoic-greek-god-statue_962194-162.jpg" alt="..." style="max-height: 415px;" /></a>
                                <div class="card-body">
                                    <div class="small text-muted"> Affronter les défis avec détermination </div>
                                    <h2 class="card-title h4">Cultiver la persévérance ♜</h2>
                                    <p class="card-text"> ❦ N'abandonnez jamais
                                en cultivant la persévérance, nous adoptons une mentalité d'invincibilité. Peu importe les obstacles sur notre chemin, nous sommes résolus à continuer, à persévérer, à réussir. Rejoignez-nous dans cette quête de persévérance et découvrez le pouvoir de réaliser l'impossible avec une détermination .....  !  </p>
                                <a class="btn btn-dark" href="./article.php?value=28">Découvrir →</a>
                                </div>
                            </div>
                            <!-- Blog post-->
                            <div class="card mb-4">
                                <a href="#!"><img class="card-img-top" src="https://img.freepik.com/premium-photo/stoic-greek-god-statue_962194-132.jpg" alt="..." /></a>
                                <div class="card-body">
                                    <div class="small text-muted">Créer un futur désirable</div>
                                    <h2 class="card-title h4">Pratiquer une visualisation créatrice♕</h2>
                                    <p class="card-text">❦ L'avenir est entre vos mains
                                en pratiquant la visualisation créatrice, nous ouvrons la porte à un univers d'opportunités infinies. Nous devenons les maîtres de notre destin, sculptant notre réalité avec les pinceaux de notre imagination et .....</p>
                                <a class="btn btn-dark" href="./article.php?value=27">Découvrir →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <header class="py-7 mb-4" style="background-image: url('https://blog.javda.com/wp-content/uploads/2021/04/overview-diamond-scintillation.jpg'); background-size: cover; background-position: center; background-blend-mode: overlay; color: white; background-color: rgba(0, 0, 0, 0.1);">
    <div class="container">
        <div class="text-center my-11">
            <h1 class="fw-bolder">⚜︎ Entre dans la puissance du mindset ⚜︎</h1>

        </div>
    </div>
</header>


<!-- Message de confirmation de suppression d'article de l'user -->

                    <!-- Featured blog post-->
                    <div class="card mb-4">
                        <div class="card-body">

                        <div class="col-md-6">
    <a href="ajouterarticle.php" class="btn btn-dark btn-block text-white">✚ Ajouter un article ♛</a>
</div>
<br>
<br>



<!-- AFFICHAGE DES ARTICLES -->

<?php
// Récupérer les articles depuis la base de données
$articles = $articleHandler->GetAllArticles();

// Vérifier si des articles ont été récupérés
if ($articles) {
    foreach ($articles as $article) {
        // Afficher chaque article sous forme de carte
?>
        <div class="card mb-4 bg-black" style="display: flex;">
    <a href="article.php?id=<?php echo $article['id']; ?>">
        <img class="card-img-left" src="<?php echo $article['photo_articles']; ?>" alt="Article Image" style="height: 200px; width: auto; margin-right: 10px; object-fit: cover;" />
    </a>
    <div class="card-body text-white">
        <div class="small text-muted"><?php echo date("F j, Y", strtotime($article['date_heure_articles'])); ?></div>
        <h2 class="card-title h4"><?php echo $article['titre']; ?></h2>
        <p class="card-text"><?php echo $article['description']; ?></p>
        <a class="btn btn-dark" href="article.php?value=<?php echo $article['id']; ?>">Voir plus →</a>

        <!-- Bouton Supprimer article avec pop up -->
        <?php if ($article['auteur'] == $_SESSION['utilisateur_id']): ?>
    <form action="index.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
        <input type="hidden" name="id_article" value="<?php echo $article['id']; ?>">
        <button type="submit" name="supprimer_article" class="btn btn-danger">Supprimer</button>
    </form>
<?php endif; ?>

<?php  
?>
    </div>
</div>
<?php


    }
} else {
    // Afficher un message si aucun article n'est disponible
    echo "<p>Aucun article n'est disponible pour le moment.</p>";
}
?>
                        </div>
                    </div>
                   
                    <!-- Pagination-->
                    <nav aria-label="Pagination">
                        <hr class="my-0" />
                    </nav>
                </div>
                <!-- Side widgets-->

                <div class="col-lg-4">
                <div class="card mb-4">
                <div class="card-header text-white bg-black">★ Épanouissement Intérieur </div>
                        <div class="card-body">❦ Découvrez des conseils pratiques pour cultiver la paix intérieure, la confiance en soi et le bien-être émotionnel au quotidien.</div>
                     </div>
                     <br>   
                <div class="card mb-4">
                        <div class="card-header text-white bg-black">★ Croissance Personnelle Continue </div>
                        <div class="card-body">❦ Explorez des stratégies efficaces pour surmonter les obstacles, développer vos compétences et atteindre vos objectifs personnels et professionnels.</div>
                     </div>
                <br>
                <div class="card mb-4">
                        <div class="card-header text-white bg-black">★ Droit vers le Succès </div>
                        <div class="card-body">❦ Trouvez l'inspiration auprès de figures emblématiques du développement personnel. Apprenez de leurs parcours, de leurs succès et de leurs stratégies pour vous motiver à atteindre vos propres objectifs.</div>
                     </div>
</div>
</div>
</div>

        
        
        <!-- Footer-->
        <footer class="py-5 bg-black">
            <div class="container"><p class="m-0 text-center text-white">Copyright &copy; CultiveTaVision 2024, by Mattéo</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="./assets/js/scripts.js"></script>
    </body>
</html>
