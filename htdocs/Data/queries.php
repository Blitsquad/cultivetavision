<?php 

require "config.php";

class Utilisateurs {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }


    public function Ajout_Utilisateurs ($pseudo, $email, $mdp) {
        $requete = $this->connexion->prepare("INSERT INTO Utilisateurs(pseudo, email, mot_de_passe) VALUES (:pseudo, :email, :mot_de_passe)");

        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->bindParam(':email', $email, PDO::PARAM_STR);
        $requete->bindParam(':mot_de_passe', $mdp, PDO::PARAM_STR);
        $resultat = $requete->execute();

        return $resultat;
    }

    public function Obtenir__Id_Pseudo() {
        $requete = $this->connexion->prepare("SELECT id, pseudo FROM utilisateurs");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }


    public function Recuperer_Utilisateurs ($pseudo, $mdp) {
        $requete = $this->connexion->prepare("SELECT * FROM Utilisateurs WHERE pseudo = :pseudo AND mot_de_passe = :mot_de_passe");
        
        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->bindParam(':mot_de_passe', $mdp, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Profil_Utilisateurs_bypseudo ($pseudo) {
        $requete = $this->connexion->prepare("SELECT * FROM Utilisateurs WHERE pseudo = :pseudo");
        
        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Profil_Utilisateurs_byid ($id) {
        $requete = $this->connexion->prepare("SELECT * FROM Utilisateurs WHERE id = :id");
        
        $requete->bindParam(':id', $id, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Obtenir_Pseudo_Photo_ParId($id_user) {
        $requete = $this->connexion->prepare("SELECT pseudo, photo_profil FROM utilisateurs WHERE id = :id");
        $requete->bindParam(':id', $id_user, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Obtenir_Tous_Pour_Stat(){
        $requete = $this->connexion->prepare('SELECT COUNT(*) FROM utilisateurs');
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }


    public function Obtenir_Pseudo_Password_ParPseudo($pseudo) {
        $requete = $this->connexion->prepare("SELECT id, pseudo, mot_de_passe FROM utilisateur WHERE pseudo = :pseudo");
        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);  
    }   

    public function Obtenir_Id_ParPseudo($pseudo) {
        $requete = $this->connexion->prepare("SELECT id FROM utilisateur WHERE pseudo = :pseudo");
        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }    
    
    public function Obtenir_Mail_ParId($id_user) {
        $requete = $this->connexion->prepare("SELECT id,mail FROM utilisateur WHERE id = :id");
        $requete->bindParam(':id', $id_user, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    
    public function Obtenir_Photo_ParId($id_user) {
        $requete = $this->connexion->prepare("SELECT id,photo_profil FROM utilisateur WHERE id = :id");
        $requete->bindParam(':id', $id_user, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    
    public function Obtenir_Description_ParId($id_user) {
        $requete = $this->connexion->prepare("SELECT id,description FROM utilisateur WHERE id = :id");
        $requete->bindParam(':id', $id_user, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    
    public function Verifier_Pseudo_ParPseudo($pseudo) {
        $requete = $this->connexion->prepare("SELECT pseudo FROM utilisateur WHERE pseudo = :pseudo");
        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    
    public function Verifier_ParMail($mail) {
        $requete = $this->connexion->prepare("SELECT mail FROM utilisateur WHERE mail = :mail");
        $requete->bindParam(':mail', $mail, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Ajouter($pseudo, $mail, $mot_de_passe, $photo_profil) {
        $requete = $this->connexion->prepare("INSERT INTO utilisateur(pseudo, mail, mot_de_passe, photo_profil) VALUES (:pseudo, :mail, :mot_de_passe, :photo_profil)");
        $requete->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->bindParam(':mail', $mail, PDO::PARAM_STR);
        $requete->bindParam(':mot_de_passe', $mot_de_passe, PDO::PARAM_STR);
        $requete->bindParam(':photo_profil', $photo_profil, PDO::PARAM_STR);
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Mise_A_Jour($pseudo_new, $mail_new, $description_new, $id) {
        $requete = $this->connexion->prepare("UPDATE utilisateur SET pseudo = :pseudo_new, mail = :mail_new, description = :description_new WHERE id = :id");
        $requete->bindParam(':pseudo_new', $pseudo_new, PDO::PARAM_STR);
        $requete->bindParam(':mail_new', $mail_new, PDO::PARAM_STR);
        $requete->bindParam(':description_new', $description_new, PDO::PARAM_STR);
        $requete->bindParam(':id', $id, PDO::PARAM_INT);
        
        $resultat = $requete->execute();
        return $resultat;
    }
    public function Mise_A_Jour_PhotoProfil($photo_profil_new, $id) {
        $requete = $this->connexion->prepare("UPDATE utilisateur SET photo_profil = :photo_profil_new WHERE id = :id");
        $requete->bindParam(':photo_profil_new', $photo_profil_new, PDO::PARAM_STR);
        $requete->bindParam(':id', $id, PDO::PARAM_INT);
        
        $resultat = $requete->execute();
        return $resultat;
    }
    public function Mise_A_Jour_MotDePasse($mot_de_passe_new, $id_user){
        $requete = $this->connexion->prepare("UPDATE utilisateur SET mot_de_passe = :mot_de_passe_new WHERE id = :id_user");
        $requete->bindParam(':mot_de_passe_new', $mot_de_passe_new, PDO::PARAM_STR);
        $requete->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Obtenir_5_Derniers_Article_Par_Utilisateur($id_user){
        $requete = $this->connexion->prepare("SELECT article.id, article.titre, article.description, article.date_heure_article, article.auteur, article.photo_article, article.tag, article.sous_titre FROM article JOIN _like ON article.id = _like.article_id WHERE _like.utilisateur_id = :id_user ORDER BY _like.date_heure_like DESC LIMIT 5;");
        $requete->bindParam(':id_user' ,$id_user,PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function SupprimerIdUtilisateur($id){
        $requete = $this->connexion->prepare('DELETE FROM utilisateur WHERE id=:id');
        $requete->bindParam(':id',$id);
        $resultat = $requete->execute();
        return $resultat;
    }
}


class Articles {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }

    public function Obtenir() {
        $requete = $this->connexion->prepare("SELECT * FROM articles ORDER BY date_heure_articles DESC");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Obtenir__OrdreCroissant() {
        $requete = $this->connexion->prepare("SELECT * FROM articles");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function Obtenir_Plus_Aimes__Limit5() {
        $requete = $this->connexion->prepare("SELECT articles.id, articles.titre, articles.photo_articles, articles.sous_titre, articles.date_heure_articles, COUNT(_like.id) AS nombre_likes, utilisateur.pseudo FROM articles LEFT JOIN _like ON articles.id = _like.articles_id LEFT JOIN utilisateur ON articles.auteur = utilisateur.id GROUP BY articles.id HAVING COUNT(_like.id) >= 1 ORDER BY nombre_likes DESC, articles.date_heure_articles DESC LIMIT 5;");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Profil_Utilisateurs_byid ($id) {
        $requete = $this->connexion->prepare("SELECT * FROM Utilisateurs WHERE id = :id");
        
        $requete->bindParam(':id', $id, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    public function Obtenir_Pseudo_Photo_ParId($id_user) {
        $requete = $this->connexion->prepare("SELECT pseudo, photo_profil FROM utilisateurs WHERE id = :id");
        $requete->bindParam(':id', $id_user, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }


    public function Obtenir_Plus_Recent__Limit5() {
        $requete = $this->connexion->prepare("SELECT articles.id, articles.titre, articles.photo_articles, articles.sous_titre, articles.date_heure_articles,utilisateur.pseudo FROM articles LEFT JOIN utilisateur ON articles.auteur = utilisateur.id ORDER BY date_heure_articles DESC LIMIT 5;");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Obtenir_Plus_Commentes__Limit5() {
        $requete = $this->connexion->prepare("SELECT articles.id, articles.titre, articles.photo_articles, articles.sous_titre, articles.date_heure_articles, COUNT(commentaire.articles_id) AS nombre_commentaire, utilisateur.pseudo FROM articles LEFT JOIN commentaire ON articles.id = commentaire.articles_id LEFT JOIN utilisateur ON articles.auteur = utilisateur.id GROUP BY articles.id HAVING COUNT(commentaire.articles_id) >= 1 ORDER BY nombre_commentaire DESC, articles.date_heure_articles DESC LIMIT 5;");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Ajouter($titre, $description, $date_heure_articles, $auteur, $photo_articles, $tag, $sous_titre) {
        $requete = $this->connexion->prepare("INSERT INTO articles(titre, description, date_heure_articles, auteur, photo_articles, tag, sous_titre) VALUES (:titre, :description, :date_heure_articles, :auteur, :photo_articles, :tag, :sous_titre)");
        $requete->bindParam(':titre', $titre, PDO::PARAM_STR);
        $requete->bindParam(':description', $description, PDO::PARAM_STR);
        $requete->bindParam(':date_heure_articles', $date_heure_articles, PDO::PARAM_STR);
        $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
        $requete->bindParam(':photo_articles', $photo_articles, PDO::PARAM_STR);
        $requete->bindParam(':tag', $tag, PDO::PARAM_STR);
        $requete->bindParam(':sous_titre', $sous_titre, PDO::PARAM_STR);
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Modifier($id_articles, $titre, $description, $date_heure_articles, $auteur, $photo_articles, $tag, $sous_titre) {
        $requete = $this->connexion->prepare("UPDATE articles SET titre = :titre, description = :description, date_heure_articles = :date_heure_articles, auteur = :auteur, photo_articles = :photo_articles, tag = :tag, sous_titre = :sous_titre WHERE id = :id_articles");
        $requete->bindParam(':id_articles', $id_articles, PDO::PARAM_INT);
        $requete->bindParam(':titre', $titre, PDO::PARAM_STR);
        $requete->bindParam(':description', $description, PDO::PARAM_STR);
        $requete->bindParam(':date_heure_articles', $date_heure_articles, PDO::PARAM_STR);
        $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
        $requete->bindParam(':photo_articles', $photo_articles, PDO::PARAM_STR);
        $requete->bindParam(':tag', $tag, PDO::PARAM_STR);
        $requete->bindParam(':sous_titre', $sous_titre, PDO::PARAM_STR);
        $resultat = $requete->execute();
        return $resultat;
        
    }    

    public function Supprimer($id_article) {
        $requete = $this->connexion->prepare("DELETE FROM articles WHERE id = :id_article");
        $requete->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $resultat = $requete->execute();
        return $resultat;
    }
    
        public function GetAllArticles() {
            $requete = $this->connexion->prepare("SELECT * FROM articles ORDER BY date_heure_articles DESC");
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);

    }

    public function Obtenir_Tous_Pour_Stat(){
        $requete = $this->connexion->prepare('SELECT COUNT(*) FROM articles');
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

}

class Commentaire {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }

    public function Obtenir_ParIdArticle($id_article) {
        $requete = $this->connexion->prepare("SELECT * FROM commentaire WHERE article_id = :id");
        $requete->bindParam(':id', $id_article, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Obtenir_ParAuteur($id_auteur) {
        $requete = $this->connexion->prepare("SELECT * FROM commentaire WHERE auteur = :id");
        $requete->bindParam(':id', $id_auteur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Ajouter($texte, $date_heure_commentaire, $auteur, $article_id){
        $requete = $this->connexion->prepare("INSERT INTO commentaire(texte, date_heure_commentaire, auteur, article_id) VALUES (:texte, :date_heure_commentaire, :auteur, :article_id)");
        $requete->bindParam(':texte', $texte, PDO::PARAM_STR);
        $requete->bindParam(':date_heure_commentaire', $date_heure_commentaire, PDO::PARAM_STR);
        $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
        $requete->bindParam(':article_id', $article_id, PDO::PARAM_STR);
        
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Modifier($texte, $date_heure_commentaire, $auteur, $article_id){
        $requete = $this->connexion->prepare("UPDATE commentaire SET texte = :texte, date_heure_commentaire = :date_heure_commentaire WHERE auteur = :auteur AND article_id = :article_id");
        $requete->bindParam(':texte', $texte, PDO::PARAM_STR);
        $requete->bindParam(':date_heure_commentaire', $date_heure_commentaire, PDO::PARAM_STR);
        $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
        $requete->bindParam(':article_id', $article_id, PDO::PARAM_STR);

        $resultat = $requete->execute();
        return $resultat;
    }

    public function Supprimer_Wadmin($texte, $auteur, $article_id, $utilisateur_id) {
    
        // Vérifier si l'utilisateur est l'auteur du commentaire ou s'il a des privilèges de modération
        if ($utilisateur_id === 1) {
            $requete = $this->connexion->prepare("DELETE FROM commentaire WHERE texte = :texte AND auteur = :auteur AND article_id = :article_id");
            $requete->bindParam(':texte', $texte, PDO::PARAM_STR);
            $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
            $requete->bindParam(':article_id', $article_id, PDO::PARAM_STR);
            
            $resultat = $requete->execute();
            return $resultat;
        } else {
            // L'utilisateur n'a pas les droits nécessaires
            return false;
        }
    }

    public function Supprimer($texte, $auteur, $article_id) {
        $requete = $this->connexion->prepare("DELETE FROM commentaire WHERE texte = :texte AND auteur = :auteur AND article_id = :article_id");
        $requete->bindParam(':texte', $texte, PDO::PARAM_STR);
        $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
        $requete->bindParam(':article_id', $article_id, PDO::PARAM_STR);
        
        $resultat = $requete->execute();
        return $resultat;
    }

    public function VerifierPrivilegesModerateur($utilisateur_id) {
        $requete = $this->connexion->prepare("SELECT * FROM utilisateurs WHERE id = :id AND role = 'moderateur'");
        $requete->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);
        $requete->execute();
        
        $resultat = $requete->fetch(PDO::FETCH_ASSOC);  // Utilisez fetch pour obtenir une seule ligne de résultat
        
        return $resultat;
    }

    public function Obtenir_Tous_Pour_Stat(){
        $requete = $this->connexion->prepare('SELECT COUNT(*) FROM commentaire');
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
} 
        
         

class Tags {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }

    public function Obtenir() {
        $requete = $this->connexion->prepare("SELECT * FROM tags");
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Obtenir_ParId($id) {
        $requete = $this->connexion->prepare("SELECT libelle FROM tags WHERE id = :id");
        $requete->bindParam(':id', $id, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    
    public function ObtenirId($tags) {
        $requete = $this->connexion->prepare("SELECT id FROM tags WHERE libelle = :libelle");
        $requete->bindParam(':libelle', $tags, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
    public function Ajouter($tags){
        $requete = $this->connexion->prepare("INSERT INTO `tags` (`libelle`) VALUES (:libelle);");
        $requete->bindParam(':libelle', $tags, PDO::PARAM_STR);
        $resultat = $requete->execute();
        return $resultat;

    }
}    

class Like {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }

    public function Obtenir_Par_IdArticle($id_article){
        $requete = $this->connexion->prepare("SELECT * FROM `_like` WHERE `article_id` = :id_article;");
        $requete->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Ajouter_ParUtilisateur_ParArticle($id_user,$id_article,$date_heure_like){
        $requete = $this->connexion->prepare("INSERT INTO _like (utilisateur_id, article_id, date_heure_like) VALUES (:id_user, :id_article, :date_heure_like)");
        $requete->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $requete->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $requete->bindParam(':date_heure_like', $date_heure_like, PDO::PARAM_STR);
        $resultat = $requete->execute();
        return $resultat;
    }


    public function Verifier_ParUtilisateur_ParArticle($id_user,$id_article){
        $requete = $this->connexion->prepare("SELECT * FROM _like WHERE utilisateur_id = :id_user AND article_id = :id_article");
        $requete->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $requete->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Supprimer_ParUtilisateur_ParArticle($id_user,$id_article){
        $requete = $this->connexion->prepare("DELETE FROM _like WHERE utilisateur_id = :id_user AND article_id = :id_article");
        $requete->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $requete->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Obtenir_5_Derniers_Article_Par_Utilisateur($id_user){
        $requete = $this->connexion->prepare("SELECT article.id, article.titre, article.description, article.date_heure_article, article.auteur, article.photo_article, article.tag, article.sous_titre FROM article JOIN _like ON article.id = _like.article_id WHERE _like.utilisateur_id = :id_user ORDER BY _like.date_heure_like DESC LIMIT 5;");
        $requete->bindParam(':id_user' ,$id_user,PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Obtenir_5_Derniers_Article_Par_Pseudo($pseudo){
        $requete = $this->connexion->prepare("SELECT article.id, article.titre, article.description, article.date_heure_article, article.auteur, article.photo_article, article.tag, article.sous_titre FROM article JOIN _like ON article.id = _like.article_id WHERE _like.utilisateur_id = :pseudo ORDER BY _like.date_heure_like DESC LIMIT 5;");
        $requete->bindParam(':pseudo' ,$pseudo,PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Follow {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }

    public function Ajouter_Follow_ParIdConnecte_ParIdVisite($following_id,$followed_id,$date_heure_follow){
        $requete = $this->connexion->prepare("INSERT INTO followers (following_id, followed_id,date_heure_follow) VALUES (:following_id, :followed_id, :date_heure_follow)");
        $requete->bindParam(':following_id', $following_id, PDO::PARAM_INT);
        $requete->bindParam(':followed_id', $followed_id, PDO::PARAM_INT);
        $requete->bindParam(':date_heure_follow', $date_heure_follow, PDO::PARAM_STR);
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Supprimer_Follow_ParIdConnecte_ParIdVisite($following_id,$followed_id){
        $requete = $this->connexion->prepare("DELETE FROM followers WHERE following_id = :following_id AND followed_id = :followed_id");
        $requete->bindParam(':following_id', $following_id, PDO::PARAM_INT);
        $requete->bindParam(':followed_id', $followed_id, PDO::PARAM_INT);
        $resultat = $requete->execute();
        return $resultat;
    }

    public function Verifier_Follow_ParIdConnecte_ParIdVisite($following_id,$followed_id){
        $requete = $this->connexion->prepare("SELECT * FROM followers WHERE following_id = :following_id AND followed_id = :followed_id");
        $requete->bindParam(':following_id', $following_id, PDO::PARAM_INT);
        $requete->bindParam(':followed_id', $followed_id, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

}

class Privileges {
    private $connexion;

    public function __construct() {
        global $connexion;
        $this->connexion = $connexion;
    }

    public function Verifier_Privileges($id){
        $requete = $this->connexion->prepare("SELECT * FROM privileges WHERE utilisateur_id = :id");
        $requete->bindParam(':id',$id);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    } 

    public function Obtenir_Tous_Pour_Stat(){
        $requete = $this->connexion->prepare('SELECT COUNT(*) FROM privileges');
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Moyenne_Commentaires_Par_Article(){
        $requete = $this->connexion->prepare('
            SELECT AVG(nb_commentaires) AS moyenne_commentaires_par_article FROM (
                SELECT COUNT(*) AS nb_commentaires FROM commentaire GROUP BY article_id
            ) AS commentaires_par_article');
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    public function Supprimer_Utilisateur($id) {
        // Suppression des articles de l'utilisateur
        $requete_articles = $this->connexion->prepare("DELETE FROM articles WHERE auteur = :id");
        $requete_articles->bindParam(':id', $id, PDO::PARAM_INT);
        $requete_articles->execute();
    
        // Suppression des commentaires de l'utilisateur
        $requete_commentaires = $this->connexion->prepare("DELETE FROM commentaire WHERE auteur = :id");
        $requete_commentaires->bindParam(':id', $id, PDO::PARAM_INT);
        $requete_commentaires->execute();
    
        // Suppression des likes de l'utilisateur
        $requete_likes = $this->connexion->prepare("DELETE FROM _like WHERE utilisateur_id = :id");
        $requete_likes->bindParam(':id', $id, PDO::PARAM_INT);
        $requete_likes->execute();
    
        // Suppression de l'utilisateur
        $requete_utilisateur = $this->connexion->prepare("DELETE FROM Utilisateurs WHERE id = :id");
        $requete_utilisateur->bindParam(':id', $id, PDO::PARAM_INT);
        $requete_utilisateur->execute();
    }
    
    

    public function Supprimer($texte, $auteur, $article_id) {
        $requete = $this->connexion->prepare("DELETE FROM commentaire WHERE texte = :texte ");
        $resultat = $requete->execute();

        $requete->bindParam(':texte', $texte, PDO::PARAM_STR);
        $requete->bindParam(':auteur', $auteur, PDO::PARAM_INT);
        $requete->bindParam(':article_id', $article_id, PDO::PARAM_STR);
        
        $resultat = $requete->execute();
        return $resultat;
    }
    
    
}

    
        // Supprimer l'utilisateur qui a pour ID == 2 
    

 