<?php
header("Content-Type: application/json; charset=UTF-8");
// Débuter la session
session_start();

// Seulement charger la classe qu'on a besoin
function chargerClasse($classe) {
    require '../modele/'.$classe.'.php';
}
spl_autoload_register('chargerClasse');


/* Instanciation du gestionnaire de la BD, du panier et de la connexion */
$gestionArticles = new GestionArticles();
$gestionMembres = new GestionMembres();
$gestionCommandes = new GestionCommandes();
$gestionAC = new GestionArticlesCommande();
$panier = new Panier();
$connexion = new Connexion();


/* APPELER LA BONNE FONCTION EN FONCTION DU JSON REÇU */
$objJSON = json_decode(file_get_contents("php://input"));

switch($objJSON->type){
    case "inventaire" :
        if(isset($objJSON->categorie)){//lister par catégorie
            echo $gestionArticles->listerParCategorie($objJSON->categorie);
        }
        elseif(isset($objJSON->mot)){//rechercher par mot
            echo $gestionArticles->listerParMot($objJSON->mot);
        }
        elseif(isset($objJSON->noArticle)){//détails d'un article
            echo $gestionArticles->getArticle((int) $objJSON->noArticle);
        }
        elseif(isset($objJSON->requete)){
            switch($objJSON->requete){
                
            }
        }
        else {//lister tous les articles
            echo $gestionArticles->getListeArticles();
        }    
        break;
    case "panier":
        if(isset($objJSON->requete)){
             switch($objJSON->requete){
                case "compteur" :
                    echo $panier->getNbArticlesTotal();
                    break;
                case "ajouter" :
                    $article = json_decode($objJSON->article);
                    $noArticle = (int) $article->noArticle;
                    $libelle = $article->libelle;
                    $cheminImage = $article->cheminImage;
                    $quantite = (int) $article->quantite;
                    $prixUnitaire = $article->prixUnitaire;
                    try {
                        $gestionArticles->reserverArticle($noArticle, $quantite);
                        $panier->ajouterArticle($noArticle, $libelle, $cheminImage, $quantite, $prixUnitaire);
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "L'article a été ajouté au panier avec succès.";
                    }
                    catch (Exception $e) {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    }
                    echo json_encode($reponse);
                    break;
                case "supprimer" :
                    $noArticle = (int) $objJSON->noArticle;
                    try {
                        $panier->supprimerArticle($noArticle);
                        $gestionArticles->supprimerDuPanier($noArticle);
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "L'article a été supprimé au panier avec succès.";
                    }
                    catch(Exception $e) {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    } 
                    echo json_encode($reponse);
                    break;
                case "modifier" :
                    $tabNoArticle = json_decode($objJSON->tabNoArticle);
                    $tabQuantite = json_decode($objJSON->tabQuantite);
                    try {
                        $gestionArticles->modifierPanier($tabNoArticle, $tabQuantite);
                        $panier->modifierQteArticles($tabNoArticle, $tabQuantite);
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "Modification effectuée avec succès.";
                    }
                    catch (Exception $e) {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    }
                    echo json_encode($reponse);
                    break;
                case "sommaire" :
                    echo $panier->getSommaire();
                    break;
                case "liste" :
                    echo $panier->getPanier();
                    break;
                case "vider" :
                    $gestionArticles->viderPanier(); 
                    $panier->viderPanier();    
                    break;
             }
        }
        break;
    case "membre" :
        if(isset($objJSON->requete)){
            switch ($objJSON->requete){
                case "inscription" :
                    try{
                        //Ajouter le membre
                        $donneesMembre = json_decode($objJSON->membre, true);
                        $membre = new Membre($donneesMembre);
                        $gestionMembres->ajouterMembre($membre);
                        $dernierMembre = $gestionMembres->getDernierMembre();

                        //Créer une connexion
                        $connexion->creerConnexion($dernierMembre);
                        $reponse["statut"] = "succes";
                        $reponse["estConnecte"] = $connexion->estConnecte();
                    }
                    catch (Exception $e){
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    }
                    echo json_encode($reponse);
                    break;
                case "connexion" :
                    $courriel = $objJSON->courriel;
                    $motDePasse = $objJSON->motDePasse;
                    try {
                        $membre = $gestionMembres->getMembre($courriel);
                        if(password_verify($motDePasse, $membre->getMotDePasse())){
                            $connexion->creerConnexion($membre);
                            $reponse["statut"] = "succes";
                            $reponse["estConnecte"] = $connexion->estConnecte();
                        }
                        else {
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Mot de passe invalide."; 
                        }
                    }
                    catch (Exception $e) {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    }
                    echo json_encode($reponse);
                    break;
                case "deconnexion" :
                    $connexion->seDeconnecter();
                    $reponse["estConnecte"] = $connexion->estConnecte();
                    echo json_encode($reponse);
                    break;
                case "profil" :
                    if($connexion->estConnecte()){
                        $membre = $gestionMembres->getMembre($connexion->getIdUtilisateur());
                        $reponse["statut"] = "succes";
                        $reponse["membre"] = '['.$membre.']';
                    }
                    else {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = "Vous n'êtes pas connecté.";
                    }
                    echo json_encode($reponse);  
                    break;
                case "modifier" :
                    if($connexion->estConnecte()){
                        $donneesMembre = json_decode($objJSON->membre, true);
                        $donneesMembre["noMembre"] = $connexion->getIdUtilisateur();
                        $membre = new Membre($donneesMembre);
                        $gestionMembres->modifierMembre($membre);
                        $membre = $gestionMembres->getMembre($connexion->getIdUtilisateur());
                        $reponse["statut"] = "succes";
                        $reponse["membre"] = '['.$membre.']';   
                    }
                    else {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = "Vous n'êtes pas connecté.";
                    }
                    echo json_encode($reponse);  
                    break;
                case "supprimer" :
                if($connexion->estConnecte()){
                    $gestionMembres->supprimerMembre($connexion->getIdUtilisateur());
                    $connexion->seDeconnecter();
                    $reponse["statut"] = "succes";
                    $reponse["membre"] = "Le membre a bel et bien été supprimé."; 
                }
                else {
                    $reponse["statut"] = "echec";
                    $reponse["message"] = "Vous n'êtes pas connecté.";
                }
                echo json_encode($reponse);  
                break;
            }
        }
        break;
    case "commande" :
        if(isset($objJSON->requete)){
            switch ($objJSON->requete){
                case "invite" :
                    try {
                        $panier->verrouillerPanier();
                        
                        // Ajouter le client
                        $donneesMembre = json_decode($objJSON->membre, true);
                        $membre = new Membre($donneesMembre);
                        $gestionMembres->ajouterMembre($membre);  
                        
                        // Ajouter la commande
                        $dernierMembre = $gestionMembres->getDernierMembre();
                        $noMembre = $dernierMembre->getNoMembre();
                        $paypalOrderId = $objJSON->paypalOrderId;
                        $commande = new Commande(array(
                            "noMembre" => $noMembre,
                            "paypalOrderId" => $paypalOrderId
                        ));
                        $gestionCommandes->ajouterCommande($panier, $commande);
                        
                        //Ajouter les articles en commande
                        $derniereCommande = $gestionCommandes->getDerniereCommande();
                        $noCommande = (int) $derniereCommande->getNoCommande();
                        $gestionAC->placerCommande($noCommande, $panier);
                        
                        //Vider le panier
                        $gestionArticles->effacerQtePanierTous();
                        $panier->viderPanier();
                        
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "Commande effectuée avec succès.";   
                    }
                    catch(Exception $e) {
                        $panier->deverrouillerPanier();
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    }
                    echo json_encode($reponse);
                    break;
                case "membre" :
                    if($connexion->estConnecte()){
                        try {
                            $panier->verrouillerPanier();
                            
                            // Ajouter la commande
                            $noMembre = $connexion->getIdUtilisateur();
                            $paypalOrderId = $objJSON->paypalOrderId;
                            $commande = new Commande(array(
                                "noMembre" => $noMembre,
                                "paypalOrderId" => $paypalOrderId
                            ));
                            $gestionCommandes->ajouterCommande($panier, $commande);
                            
                            //Ajouter les articles en commande
                            $derniereCommande = $gestionCommandes->getDerniereCommande();
                            $noCommande = (int) $derniereCommande->getNoCommande();
                            $gestionAC->placerCommande($noCommande, $panier);
                            
                            //Vider le panier
                            $gestionArticles->effacerQtePanierTous();
                            $panier->viderPanier();
                            
                            $reponse["statut"] = "succes";
                            $reponse["message"] = "Commande effectuée avec succès.";   
                        }
                        catch(Exception $e) {
                            $panier->deverrouillerPanier();
                            $reponse["statut"] = "echec";
                            $reponse["message"] = $e->getMessage();
                        }    
                    }
                    else {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = "Vous n'êtes pas connecté.";
                    }
                    echo json_encode($reponse); 
                    break;
                case "confirmation" :
                        $derniereCommande = $gestionCommandes->getDerniereCommande();
                        $paypalOrderId = $derniereCommande->getPaypalOrderId();
                    
                        if($connexion->estConnecte()) {
                            $noMembre = $connexion->getIdUtilisateur();
                            $courriel = $gestionMembres->getMembre($noMembre)->getCourriel();
                        }
                        else {
                            $dernierMembre = $gestionMembres->getDernierMembre();
                            $courriel = $dernierMembre->getCourriel();
                        }
                    
                        echo json_encode(
                            array(
                                array(
                                    "paypalOrderId" => $paypalOrderId,
                                    "courriel" => $courriel
                                )
                            )
                        );
                        break;
  
                
            }
        }
        break;

}