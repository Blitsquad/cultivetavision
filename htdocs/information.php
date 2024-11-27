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

// Vérifier si le formulaire de suppression d'article a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_article'])) {
    // Récupérer l'ID de l'article à supprimer
    $id_article = $_POST['id_article'];
    
    // Appeler la méthode Supprimer de la classe Articles
    $resultat = $articlesManager->Supprimer($id_article);
    
    if ($resultat) {
        // L'article a été supprimé avec succès
        echo "L'article a été supprimé avec succès.";
    } else {
        // Une erreur s'est produite lors de la suppression de l'article
        echo "Une erreur s'est produite lors de la suppression de l'article.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Mettez ici les balises meta, les liens vers les feuilles de style, etc. -->
        <link rel="stylesheet" href="./assets/css/styles.css">
        <link rel="stylesheet" href="./assets/index/styles.css">

        <title>CultiveTaVision | En savoir plus</title>
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

<!-- Inclure le fichier JavaScript -->
<script src="./assets/css/app.js"></script>

<!-- Page header with logo and tagline-->
<header class="py-5 mb-4" style="background-image: url('https://img.freepik.com/premium-photo/stoic-man-statue-strong-stoic-man_358089-361.jpg'); background-size: cover; background-position: center; background-blend-mode: overlay; color: white; background-color: rgba(0, 0, 0, 0.5);">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder"><strong>Découvrez notre Histoire.</strong></h1>
            <p class="lead mb-0">♟ Explorez votre plein potentiel</p>
        </div>
    </div>
</header>

<section class="py-5">
	<div class="container">
		<div class="row align-items-center gx-4">
			<div class="col-md-5">
				<div class="ms-md-2 ms-lg-5"><img class="img-fluid rounded-3" src="./assets/images/g1.png"></div>
			</div>
			<div class="col-md-6 offset-md-1">
				<div class="ms-md-2 ms-lg-5">
					<span class="text-muted">CultiveTaVision</span>
					<h2 class="display-5 fw-bold">♜ Bienvenue sur CultiveTaVision</h2>
					<p class="lead">CultiveTaVision est bien plus qu'un simple blog. C'est une plateforme en ligne conçue pour offrir un soutien et une inspiration précieux à ceux qui se sentent mentalement affaiblis et ont du mal à voir les choses en grand. Ce blog a été créé entièrement en ligne par un étudiant passionné de 21 ans dans le cadre d'un projet d'école en développement web. Grâce à sa détermination et à sa vision, il a donné naissance à un espace numérique dédié à l'amélioration de la santé mentale et au développement personnel.</p>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="py-5 bg-black text-white">
	<div class="container">
		<div class="row align-items-center gx-4">
			<div class="col-md-6 offset-md-1">
				<div class="ms-md-2 ms-lg-5">
					<span class="text-muted">CultiveTaVision</span>
					<h2 class="display-5 fw-bold">❦ Changer votre perspective</h2>
					<p class="lead">Le principal objectif de CultiveTaVision est d'aider les individus à surmonter leurs luttes mentales et à adopter une perspective plus positive et constructive sur la vie. Que ce soit en fournissant des conseils pratiques, en partageant des témoignages inspirants ou en créant une communauté de soutien, le blog vise à être un phare dans l'obscurité pour ceux qui cherchent à trouver leur chemin vers la clarté mentale et le bien-être émotionnel.</p>
				</div>
			</div>
			<div class="col-md-5">
				<div class="ms-md-2 ms-lg-5"><img class="img-fluid rounded-3" src="./assets/images/g2.png"></div>
			</div>
		</div>
	</div>
</section>
<section class="py-5">
	<div class="container">
		<div class="row align-items-center gx-4">
			<div class="col-md-5">
				<div class="ms-md-2 ms-lg-5"><img class="img-fluid rounded-3" src="./assets/images/g3.png"></div>
			</div>
			<div class="col-md-6 offset-md-1">
				<div class="ms-md-2 ms-lg-5">
					<span class="text-muted">CultiveTaVision</span>
					<h2 class="display-5 fw-bold">➹ Explorer le potentiel</h2>
					<p class="lead">CultiveTaVision propose une variété de contenu engageant et informatif, allant des articles sur le développement personnel et la santé mentale aux réflexions profondes sur le pouvoir de la pensée positive. Les lecteurs peuvent trouver des conseils pratiques pour gérer le stress, surmonter l'anxiété et cultiver une mentalité de croissance. De plus, le blog met en avant des histoires inspirantes de personnes ayant surmonté des défis mentaux et trouvé la force intérieure pour voir grand dans la vie.</p>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="py-5 bg-black text-white">
	<div class="container">
		<div class="row align-items-center gx-4">
			<div class="col-md-6 offset-md-1">
				<div class="ms-md-2 ms-lg-5">
					<span class="text-muted">CultiveTaVision</span>
					<h2 class="display-5 fw-bold">✪ Une communauté bienveillante</h2>
					<p class="lead">Ce blog ne se contente pas d'offrir du contenu ; il crée également une communauté accueillante et solidaire pour ceux qui cherchent à partager leurs expériences, leurs luttes et leurs succès. Les lecteurs peuvent interagir via les commentaires, les forums de discussion et les réseaux sociaux pour se soutenir mutuellement, échanger des conseils et trouver de l'inspiration dans les parcours des autres.</p>
				</div>
			</div>
			<div class="col-md-5">
				<div class="ms-md-2 ms-lg-5"><img class="img-fluid rounded-3" src="./assets/images/g4.png"></div>
			</div>
		</div>
	</div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row align-items-center gx-4">
            <div class="col-md-5">
                <div class="ms-md-2 ms-lg-5"><img class="img-fluid rounded-3" src="./assets/images/g5.png"></div>
            </div>
            <div class="col-md-6 offset-md-1">
                <div class="ms-md-2 ms-lg-5">
                    <span class="text-muted">CultiveTaVision</span>
                    <h2 class="display-5 fw-bold">✉ Contact & Support</h2>
                    <p class="lead">Pour toute question, suggestion ou besoin de soutien supplémentaire, les lecteurs sont invités à contacter l'équipe de CultiveTaVision via l'adresse e-mail dédiée : <strong>cultivetavision@hotmail.com</strong>. L'équipe est disponible pour répondre aux questions, offrir des conseils personnalisés et fournir un soutien émotionnel à ceux qui en ont besoin.</p>
                    <!-- Bouton "Découvrir les conseils mindset" -->
                    <a href="index.php" class="btn btn-dark btn-block text-white">➤ Découvrir les conseils mindset</a>
                </div>
            </div>
        </div>
    </div>
</section>




