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
if(isset($_POST["requete"]) && isset($_FILES["image"])){
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
        $reponse["message"] = "L'article a été ajouté avec succès";

        if($_FILES["image"]["name"] != ""){
            try {
                $gestionArticles->uploadImage($article->getNoArticle(),$article->getLibelle(),$_FILES["image"]);
                $reponse["message"] .= " et l'image a été téléversée avec succès.";
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

if(isset($objJSON)){
    switch($objJSON->type){
        /**** INVENTAIRE ****/
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
                        $noArticle = (int) $objJSON->noArticle;
                        $gestionArticles->supprimerArticle($noArticle);
                         $reponse["statut"] = "succes";
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
       
        /**** PANIER D'ACHAT ****/
        case "panier":
            if(isset($objJSON->requete)){
                 switch($objJSON->requete){
                    case "compteur" ://compter le nombre d'articles
                        echo $panier->getNbArticlesTotal();
                        break;
                    case "ajouter" ://ajouter un article 
                        $article = $objJSON->article;
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
                    case "supprimer" ://supprimer un article
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
                    case "modifier" :// modifier la quantité
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
                    case "sommaire" :// sommaire du panier (ou de la facture)
                        echo $panier->getSommaire();
                        break;
                    case "liste" ://lister les éléments du panier (ou facture)
                        echo $panier->getPanier();
                        break;
                    case "vider" ://vider le panier
                        $gestionArticles->viderPanier(); 
                        $panier->viderPanier(); 
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "Le panier a été vidé.";
                        echo json_encode($reponse);
                        break;
                 }
            }
            break;

        /**** MEMBRE ****/    
        case "membre" :
            if(isset($objJSON->requete)){
                switch ($objJSON->requete){
                    case "inscription" : //inscription d'un membre
                        try{
                            //Ajouter le membre
                            $donneesMembre = json_decode($objJSON->membre, true);
                            $membre = new Membre($donneesMembre);
                            $gestionMembres->ajouterMembre($membre);
                            $dernierMembre = $gestionMembres->getMembre($membre->getCourriel());
    
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
                    case "connexion" ://authentification 
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
                    case "estConnecte" ://valider si le membre est connecté
                        $reponse["estConnecte"] = $connexion->estConnecte();
                        $reponse["categorie"] = $connexion->getCategorie();
                        echo json_encode($reponse);      
                        break;
                    case "deconnexion" ://déconnection
                        $connexion->seDeconnecter();
                        $reponse["estConnecte"] = $connexion->estConnecte();
                        echo json_encode($reponse);
                        break;
                    case "liste" : //afficher tous les membres (admin)
                        if($connexion->estConnecte() && $connexion->getCategorie() == 2){
                            $reponse["statut"] = "succes";
                            $reponse["membres"] = $gestionMembres->getListeMembres();
                        }
                        else{
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Vous n'êtes pas autorisé à consulter tous les membres.";
                        }
                        echo json_encode($reponse);
                        break;
                    case "recherche" : //recherche par nom (admin)
                        if($connexion->estConnecte() && $connexion->getCategorie() == 2){
                            $mot = $objJSON->mot;
                            $reponse["statut"] = "succes";
                            $reponse["membres"] = $gestionMembres->rechercherParNom($nom);
                        }
                        else{
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Vous n'êtes pas autorisé à consulter tous les membres.";
                        }
                        echo json_encode($reponse);
                        break;
                    case "profil" ://afficher un membre
                        if($connexion->estConnecte()){
                            //consulter son propre compte
                            if(!isset($objJSON->noMembre)){
                                $noMembre = $connexion->getIdUtilisateur();
                            }
                            //admin qui consulte le profil d'un autre membre
                            elseif($connexion->getCategorie() == 2 && isset($objJSON->noMembre)) {
                                $noMembre = (int) $objJSON->noMembre;
                            }
                            $membre = $gestionMembres->getMembre($noMembre); 
                            $reponse["statut"] = "succes";
                            $reponse["membre"] = '['.$membre.']';
                        }
                        else {
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Vous n'êtes pas connecté.";
                        }
                        echo json_encode($reponse);  
                        break;
                    case "modifier" ://modifier les renseignements
                        if($connexion->estConnecte()){
                            $donneesMembre = json_decode($objJSON->membre, true);
                            $membre = new Membre($donneesMembre);
                            //modifier son propre compte
                            if(!isset($objJSON->noMembre)){
                                $membre->setNoMembre($connexion->getIdUtilisateur());
                            }
                            //pour l'admin, le JSON reçu du front end contient déjà le numéro de membre
                            $gestionMembres->modifierMembre($membre);
                            $reponse["statut"] = "succes";
                        }
                        else {
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Vous n'êtes pas connecté.";
                        }
                        echo json_encode($reponse);  
                        break;
                    case "supprimer" ://supprimer un compte
                        if($connexion->estConnecte()){
                            //un membre qui se désabonne
                            if(!isset($objJSON->noMembre)){
                                $gestionMembres->desactiverMembre($connexion->getIdUtilisateur());
                                $connexion->seDeconnecter();
                                $reponse["message"] = "Vous êtes désabonné."; 
                            }
                            //un admin qui désactive un autre compte
                            elseif($connexion->getCategorie() == 2 && isset($objJSON->noMembre)) {
                                $gestionMembres->desactiverMembre((int) $objJSON->noMembre);
                                $reponse["message"] = "Le membre a bel et bien été désactivé."; 
                            }       
                            $reponse["statut"] = "succes";  
                        }
                        else {
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Vous n'êtes pas connecté.";
                        }
                        echo json_encode($reponse);  
                        break;        
                    case "motDePasse" ://modifier le mot de passe
                        if($connexion->estConnecte()){
                            $noMembre = $connexion->getIdUtilisateur();
                            $membre = $gestionMembres->getMembre($noMembre);
                            if(password_verify($objJSON->ancien, $membre->getMotDePasse())){
                                $gestionMembres->changerMotDePasse($noMembre, $objJSON->nouveau);
                                $reponse["statut"] = "succes";
                                $reponse["message"] = "Mot de passe modifié avec succès";
                            }
                            else{
                                $reponse["statut"] = "echec";
                                $reponse["message"] = "Mot de passe invalide";
                            }    
                        }
                        else {
                            $reponse["statut"] = "echec";
                            $reponse["message"] = "Vous n'êtes pas connecté.";
                        } 
                        echo json_encode($reponse); 
                        break;
                    case "oubli" : //réinitialiser le mot de passe
                        $courriel = $objJSON->courriel;
                        try{
                            $membre = $gestionMembres->getMembre($courriel);
                            $noMembre = $membre->getNoMembre();
                            $temp = $gestionMembres->genererMotDePasse();
                            $gestionMembres->changerMotDePasse($noMembre, $temp);
                            $reponse["statut"] = "succes";
                            $reponse["reset"] = array(
                                array(
                                    "message" => "Le mot de passe a été réinitialisé.",
                                    "courriel" => $courriel,
                                    "temp" => $temp
                                )
                            );  
                        }
                        catch(Exception $e){
                            $reponse["statut"] = "echec";
                            $reponse["message"] = $e->getMessage();
                        }
                        echo json_encode($reponse);
                        break;
                }
            }
            break;

        /**** COMMANDE ****/    
        case "commande" :
            if(isset($objJSON->requete)){
                switch ($objJSON->requete){
                    case "invite" : //effectuer une commande en tant qu'invité
                        try {
                            $panier->verrouillerPanier();
                            
                            // Ajouter le client
                            $donneesMembre = json_decode($objJSON->membre, true);
                            $membre = new Membre($donneesMembre);
                            $gestionMembres->ajouterMembre($membre);  
                            
                            // Ajouter la commande
                            $dernierMembre = $gestionMembres->getMembre($membre->getCourriel());
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
                    case "membre" ://commande pour un membre existant
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
                                $reponse["message"] = "Le panier a bel et bien été vidé";   
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
                    case "confirmation" ://afficher la confirmation de commande
                            $derniereCommande = $gestionCommandes->getDerniereCommande();
                            $paypalOrderId = $derniereCommande->getPaypalOrderId();
                        
                            if($connexion->estConnecte()) {
                                $noMembre = $connexion->getIdUtilisateur();
                                $courriel = $gestionMembres->getMembre($noMembre)->getCourriel();
                            }
                            else {
                                $noMembre = $derniereCommande->getNoMembre();
                                $dernierMembre = $gestionMembres->getMembre((int) $noMembre);
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

