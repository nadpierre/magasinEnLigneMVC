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
        if(isset($objJSON->categorie)){
            echo $gestionArticles->listerParCategorie($objJSON->categorie);
        }
        elseif(isset($objJSON->mot)){
            echo $gestionArticles->listerParMot($objJSON->mot);
        }
        elseif(isset($objJSON->noArticle)){
            echo $gestionArticles->getArticle((int) $objJSON->noArticle);
        }
        else {
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
                    $gestionArticles->debuterTransaction();
                    try {
                        $gestionArticles->reserverArticle($noArticle, $quantite);
                        $gestionArticles->confirmer();
                        $panier->ajouterArticle($noArticle, $libelle, $cheminImage, $quantite, $prixUnitaire);
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "L'article a été ajouté au panier avec succès.";
                    }
                    catch (Exception $e) {
                        $gestionArticles->annuler();
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    }
                    echo json_encode($reponse);
                    break;
                case "supprimer" :
                    $noArticle = (int) $objJSON->noArticle;
                    $gestionArticles->debuterTransaction();
                    try {
                        $panier->supprimerArticle($noArticle);
                        $gestionArticles->supprimerDuPanier($noArticle);
                        $gestionArticles->confirmer();
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "L'article a été supprimé au panier avec succès.";
                    }
                    catch(Exception $e) {
                        $gestionArticles->annuler();
                        $reponse["statut"] = "echec";
                        $reponse["message"] = $e->getMessage();
                    } 
                    echo json_encode($reponse);
                    break;
                case "modifier" :
                    $tabNoArticle = json_decode($objJSON->tabNoArticle);
                    $tabQuantite = json_decode($objJSON->tabQuantite);
                    $gestionArticles->debuterTransaction();
                    try {
                        $gestionArticles->modifierPanier($tabNoArticle, $tabQuantite);
                        $gestionArticles->confirmer();
                        $panier->modifierQteArticles($tabNoArticle, $tabQuantite);
                        $reponse["statut"] = "succes";
                        $reponse["message"] = "Modification effectuée avec succès.";
                    }
                    catch (Exception $e) {
                        $gestionArticles->annuler();
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
                case "detruire" ://appel manuel seulement
                    $gestionArticles->detruirePanier(); 
                    $panier->supprimerPanier();    
                    break;
             }
        }
        break;
    case "membre" :
        if(isset($objJSON->requete)){
            switch ($objJSON->requete){
                case "anonyme" :
                    //Ajouter le membre
                    $donneesMembre = json_decode($objJSON->client, true);
                    $membreAnonyme = new Membre($donnees);
                    $membre = $gestionMembres->getMembre((int) $membreAnonyme->getNoMembre());
                    //Ajouter la commande
                    break;
            }
        }
}



/* REQUÊTES GET */
/*if(isset($_GET["q"])){
    
   
    if($_GET["q"] == "commande"){//retourner le numéro de confirmation et le courriel
        $commande = $gestionCommandes->getDerniereCommande();
        $paypalOrderId = $commande->getPaypalOrderId();
        $noMembre = $commande->getNoMembre();
        $courriel = $gestionMembres->getMembre((int) $noMembre)->getCourriel();
        echo json_encode(
            array(
                array(
                     "paypalOrderId" => $paypalOrderId,
                    "courriel" => $courriel)
                )
            );

    }
}*/

/* REQUÊTES POST */
/*elseif(isset($_POST["x"])){
    $obj = json_decode($_POST["x"], false);
    switch($obj->requete) {
        case "inscription" ://inscrire un client
            $donneesMembre = json_decode($obj->client, true);
            $client = new Membre($donneesMembre);
            try {
                $gestionMembres->ajouterMembre($client);
                $gestionMembres->confirmer();
                $reponse["statut"] = "succes";
                $reponse["client"] = $gestionMembres->getDernierMembre();
            }
            catch (Exception $e){
                $gestionMembres->annuler();
                $reponse["statut"] = "echec";
                $reponse["message"] = $e->getMessage();
            }
            echo json_encode($reponse);
            break;
        case "connexion" ://connexion d'un membre existant
            $courriel = $obj->courriel;
            $motDePasse = $obj->motDePasse;
            try {
                $gestionMembres->authentifierMembre($courriel, $motDePasse);
                $reponse["statut"] = "succes";
                $reponse["membre"] = $gestionMembres->authentifierMembre($courriel, $motDePasse);
            }
            catch (Exception $e) {
                $reponse["statut"] = "echec";
                $reponse["message"] = $e->getMessage();
            }
            echo json_encode($reponse);
            break;
        case "commande" : //placer une commande
            //Récupérer les données
            $paypalOrderId = $obj->paypalOrderId;
            $tabNoArticle = json_decode($obj->tabNoArticle);
            $tabQuantite = json_decode($obj->tabQuantite);
            $noMembre = $obj->noMembre;
            
            $commande = new Commande(array(
                "noMembre" => $noMembre,
                "paypalOrderId" => $paypalOrderId
            ));

            //Ajouter la commande
            $gestionCommandes->ajouterCommande($commande);
            //Ajouter les articles en commande
            $noCommande = $gestionCommandes->getDerniereCommande()->getNoCommande();
            $gestionAC->placerCommande($noCommande, $tabNoArticle, $tabQuantite);

            //Détruire le panier d'achat
            $gestionArticles->effacerQtePanierTous();
            $panier->verrouillerPanier();
            $panier->supprimerPanier();
            break;
    }
   
}*/

?>