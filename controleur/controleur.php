<?php
header("Content-Type: application/json; charset=UTF-8");
// Débuter la session
session_start();

// Seulement charger la classe qu'on a besoin
function chargerClasse($classe) {
    require '../modele/'.$classe.'.php';
}
spl_autoload_register('chargerClasse');


/* Instanciation du gestionnaire de la BD et du panier */
$gestionArticles = new GestionArticles();
$gestionMembres = new GestionMembres();
$gestionCommandes = new GestionCommandes();
$gestionAC = new GestionArticlesCommande();
$panier = new Panier();


/** APPELER LA BONNE FONCTION EN FONCTION DE LA REQUÊTE */
$objJson = json_decode(file_get_contents("php://input"));

echo($objJson);
/* REQUÊTES GET */
/* if(isset($_GET["q"])){
    if($_GET["q"] == "inventaire"){
        if(isset($_GET["noArticle"])){//afficher un seul article
            $noArticle = (int) $_GET["noArticle"];
            echo $gestionArticles->getArticle($noArticle);
        }
        elseif(isset($_GET["categorie"])){//lister par catégorie
            echo $gestionArticles->listerParCategorie($_GET["categorie"]);
        }
        elseif(isset($_GET["mot"])){//lister par mot
            echo $gestionArticles->listerParMot($_GET["mot"]);
        }
        else {//lister tous les articles
            echo $gestionArticles->getListeArticles();
        }
        
    }
    if($_GET["q"] == "panier"){
        if(isset($_GET["r"])){
            switch($_GET["r"]) {
                case "total": //compter le nombre d'articles dans le panier
                    echo $panier->getNbArticlesTotal();
                    break;
                case "sommaire": //afficher le sommaire du panier
                    echo $panier->getSommaire();
                    break;
                case "liste": //afficher chaque article du panier
                    echo $panier->getPanier();
                    break;
                case "detruire" ://détruire le panier (appel manuel de la fonction pour tester)
                    $gestionArticles->detruirePanier(); 
                    $panier->supprimerPanier(); 
                    break;
            }
        }
    }
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
}

/* REQUÊTES POST */
/*elseif(isset($_POST["x"])){
    $obj = json_decode($_POST["x"], false);
    switch($obj->requete) {
        case "ajouter" : //ajouter un article dans le panier
            $noArticle = (int) $obj->noArticle;
            $libelle = $obj->libelle;
            $cheminImage = $obj->cheminImage;
            $quantite = (int) $obj->quantite;
            $prixUnitaire = $obj->prixUnitaire;
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
        case "supprimer" : //supprimer un article dans le panier
            $noArticle = (int) $obj->noArticle;
            $panier->supprimerArticle($noArticle);
            $gestionArticles->supprimerDuPanier($noArticle);
            break;
        case "modifier" : //modifier la quantité des articles dans le panier
            $tabNoArticle = json_decode($obj->tabNoArticle);
            $tabQuantite = json_decode($obj->tabQuantite);
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
   
} */

?>