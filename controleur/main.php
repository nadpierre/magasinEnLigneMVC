<?php
header("Content-Type: application/json; charset=UTF-8");
// Débuter la session
session_start();

// Seulement charger la classe qu'on a besoin
function chargerClasse($classe) {
    require $classe.'.php';
}
spl_autoload_register('chargerClasse');


/* Instanciation du gestionnaire de la BD et du panier */
$gestionArticles = new GestionArticles();
$gestionClients = new GestionClients();
$gestionCommandes = new GestionCommandes();
$panier = new Panier();


/** APPELER LA BONNE FONCTION EN FONCTION DE LA REQUÊTE */

/* REQUÊTES GET */
if(isset($_GET["q"])){
    if($_GET["q"] == "inventaire"){
        if(isset($_GET["noArticle"])){//afficher un seul article
            $noArticle = (int) $_GET["noArticle"];
            echo json_encode($gestionArticles->getArticle($noArticle));
        }
        elseif(isset($_GET["categorie"])){//lister par catégorie
            echo json_encode($gestionArticles->listerParCategorie($_GET["categorie"]));
        }
        elseif(isset($_GET["mot"])){//lister par mot
            echo json_encode($gestionArticles->listerParMot($_GET["mot"]));
        }
        else {//lister tous les articles
            echo json_encode($gestionArticles->getListeArticles());
        }
        
    }
    if($_GET["q"] == "panier"){
        if(isset($_GET["r"])){
            switch($_GET["r"]) {
                case "total": //compter le nombre d'articles dans le panier
                    echo json_encode($panier->getNbArticlesTotal());
                    break;
                case "sommaire": //afficher le sommaire du panier
                    echo json_encode($panier->getSommaire());
                    break;
                case "liste": //afficher chaque article du panier
                    echo json_encode($panier->getPanier());
                    break;
                case "detruire" ://détruire le panier (appel manuel de la fonction pour tester)
                    $gestionArticles->detruirePanier(); 
                    $panier->supprimerPanier(); 
                    break;
            }
        }
    }
    if($_GET["q"] == "commande"){
        echo json_encode($gestionCommandes->getConfirmation());
    }
}

/* REQUÊTES POST */
elseif(isset($_POST["x"])){
    $obj = json_decode($_POST["x"], false);
    switch($obj->requete) {
        case "ajouter" : //ajouter un article dans le panier
            $noArticle = (int) $obj->noArticle;
            $description = $obj->description;
            $cheminImage = $obj->cheminImage;
            $quantite = (int) $obj->quantite;
            $prixUnitaire = $obj->prixUnitaire;
            try {
                $gestionArticles->reserverArticle($noArticle, $quantite);
                $panier->ajouterArticle($noArticle, $description, $cheminImage, $quantite, $prixUnitaire);
                $reponse["statut"] = "succes";
                $reponse["message"] = "L'article a été ajouté au panier avec succès.";
            }
            catch (Exception $e) {
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
        case "inscription" ://inscrire un client
            $donneesClient = json_decode($obj->client, true);
            $client = new Client($donneesClient);
            try {
                $gestionClients->ajouterClient($client);
                $reponse["statut"] = "succes";
                $reponse["client"] = $gestionClients->getDernierClient();
            }
            catch (Exception $e){
                $reponse["statut"] = "echec";
                $reponse["message"] = $e->getMessage();
            }
            echo json_encode($reponse);
            break;
        case "connexion" ://connexion d'un membre existant
            $pseudo = $obj->pseudo;
            $motDePasse = $obj->motDePasse;
            try {
                $gestionClients->getMembre($pseudo, $motDePasse);
                $reponse["statut"] = "succes";
                $reponse["membre"] = $gestionClients->getMembre($pseudo, $motDePasse);
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
            $noClient = $obj->noClient;
            
            //Ajouter la commande
            $gestionCommandes->ajouterCommande($noClient, $paypalOrderId, $tabNoArticle, $tabQuantite);

            //Détruire le panier d'achat
            $gestionArticles->effacerQtePanierTous();
            $panier->verrouillerPanier();
            $panier->supprimerPanier();
            break;
    }
   
}

?>