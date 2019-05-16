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

/* Ajouter ou modifier un article */
if(isset($_FILES["image"])){
    if($connexion->estConnecte() && $connexion->getCategorie() == 2){
        $donnees = json_decode($_POST["article"], true);
        $article = new Article($donnees);
        if($_POST["requete"] == "ajouter"){
            $gestionArticles->ajouterArticle($article);
            $article->setNoArticle($gestionArticles->getDernierArticle()->getNoArticle());
        }
        elseif($_POST["requete"] == "modifier"){
            $gestionArticles->modifierArticle($article);
        }

        $reponse["statut"] = "succes";
        $reponse["message"] = "L'article a été ajouté avec succès.";

        if($_FILES["image"]["name"] != ""){
            try {
                $article->setCheminImage($gestionArticles->uploadImage($article->getLibelle(), $_FILES["image"]));
                $gestionArticles->ajouterImage($article);
                $reponse["message"] = "L'image a été modifiée avec succès.";
            }
            catch(Exception $e) {
                $reponse["statut"] = "echec";
                $reponse["message"] = $e->getMessage();
            }   
        }
    }
    else {
        $reponse["statut"] = "echec";
        $reponse["message"] = "Vous n'êtes pas autorisé à ajouter ou modifier un article.";
    }

    echo json_encode($reponse);  
}


/* APPELER LA BONNE FONCTION EN FONCTION DU JSON REÇU */
$objJSON = json_decode(file_get_contents("php://input"));

if($objJSON !== null){
    switch($objJSON->type){
        case "inventaire" :
            if(isset($objJSON->categorie)){//lister par catégorie
                echo $gestionArticles->listerParCategorie($objJSON->categorie);
            }
            elseif(isset($objJSON->mot)){//rechercher par mot
                echo $gestionArticles->listerParMot($objJSON->mot);
            }
            elseif(isset($objJSON->noArticle)){//détails d'un article
                echo '[' . $gestionArticles->getArticle((int) $objJSON->noArticle) . ']';
            }
            elseif(isset($objJSON->requete)){//admin : supprimer un article
                if($objJSON->requete == "supprimer"){
                    if($connexion->estConnecte() && $connexion->getCategorie() == 2){
                        $noArticle = $objJSON->noArticle;
                        try {
                            $reponse["article"] = '[' . $gestionArticles->supprimerArticle($noArticle). ']';
                            $reponse["statut"] = "succes";

                        }
                        catch(Exception $e){
                            $reponse["statut"] = "echec";
                            $reponse["message"] = $e->getMessage();
                        }
                    }
                    else {
                        $reponse["statut"] = "echec";
                        $reponse["message"] = "Vous n'êtes pas autorisé à supprimer un article.";
                    }                        
                    echo json_encode($reponse);
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
                            $dernierMembre = $gestionMembres->getInvite();
    
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
                    case "motDePasse" :
                        if($connexion->estConnecte()){
                            $noMembre = $connexion->getIdUtilisateur();
                            $motDePasse = $objJSON->motDePasse;
                            $gestionMembres->changerMotDePasse($noMembre, $motDePasse);
                            $reponse["statut"] = "succes";
                            $reponse["membre"] = "Le mot de passe a été modifié avec succès"; 
                        }
                        else{
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
                            $dernierMembre = $gestionMembres->getInvite();
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
                                $dernierMembre = $gestionMembres->getInvite();
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
}

