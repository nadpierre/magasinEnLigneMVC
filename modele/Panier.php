<?php

/**
 * Représente un objet de type Panier
 * Son rôle est de gérer un panier d'achats
 */
class Panier {

    const TAXES = 0.15;
    const FRAIS_LIVRAISON = 10;
    const MONTANT_MINIMUM = 75;
    const RABAIS = 0.20;

    /**
     * CONSTRUCTEUR : crée un objet de type Panier
     * Créé un tableau associatif avec des variables de session
     */
    public function __construct() {
        $this->creerPanier();
    }

    /**
     * Verifie si le panier existe, le créé sinon
     * @return boolean
     */
    public function creerPanier(){
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = array();
            $_SESSION['panier']['noArticle'] = array();
            $_SESSION['panier']['libelle'] = array();
            $_SESSION['panier']['cheminImage'] = array();
            $_SESSION['panier']['quantiteDansPanier'] = array();
            $_SESSION['panier']['prixUnitaire'] = array();
            $_SESSION['panier']['estVerrouille'] = false;
        }
        return true;
    }

    /**
     * Retourne le panier (réarrange les variables de session)
     * @return string - un JSON du tableau associatif
     */
    public function getPanier() {
        $listePanier = array();

        if (!$this->estVerrouille()) {
            for ($i = 0; $i < count($_SESSION['panier']['libelle']); $i++) {
                // Convertir le nombre décimal en format monétaire
                $prixTotal = $_SESSION['panier']['quantiteDansPanier'][$i] * $_SESSION['panier']['prixUnitaire'][$i];
                $ligne = array(
                    "noArticle" => (int) $_SESSION['panier']['noArticle'][$i],
                    "libelle" => $_SESSION['panier']['libelle'][$i],
                    "cheminImage" => $_SESSION['panier']['cheminImage'][$i],
                    "quantiteDansPanier" => (int) $_SESSION['panier']['quantiteDansPanier'][$i],
                    "prixUnitaire" => number_format($_SESSION['panier']['prixUnitaire'][$i], 2),
                    "prixTotal" => number_format($prixTotal, 2)
                );
                array_push($listePanier, $ligne);
            }
        }

        if(count($listePanier) == 0){
            return "PANIER VIDE";
        }
        
        return json_encode($listePanier);
    }

    /**
     * Retourne tous les numéros d'articles
     * @return array
     */
    public function getTabNoArticle(){
        if (!$this->estVerrouille()) {
            $tabNoArticle = array();
            for($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++){
                array_push($tabNoArticle, (int) $_SESSION['panier']['noArticle'][$i]);
            }  
        }
        return $tabNoArticle;
    }

    /**
     * Retourne toutes les quantités
     * @return array
     */
    public function getTabQuantite(){
        if (!$this->estVerrouille()) {
            $tabQuantite = array();
            for($i = 0; $i < count($_SESSION['panier']['quantiteDansPanier']); $i++){
                array_push($tabQuantite, (int) $_SESSION['panier']['quantiteDansPanier'][$i]);
            }  
        }
        return $tabQuantite;
    }


    /**
     * Retourne le nombre total d'articles
     * @return string
     */
    public function getNbArticlesTotal() {
        $compteur = 0;
        if (!$this->estVerrouille()) {
            for ($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++) {
                $compteur += $_SESSION['panier']['quantiteDansPanier'][$i];
            }
        }
        return json_encode($compteur);
    }

    /**
     * Retourne le montant total
     * @return double
     */
    public function getMontantTotal() {
        $somme = 0;

        if (!$this->estVerrouille()) {
            for ($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++) {
                $somme += $_SESSION['panier']['quantiteDansPanier'][$i] * $_SESSION['panier']['prixUnitaire'][$i];
            }
        }

        return $somme;
    }

    /**
     * Retourne le sommaire du panier
     * @param {boolean} - s'il y a un rabais ou pas
     * @return string - le JSON du tableau du panier
     */
    public function getSommaire(){

        $tabSommaire = array();

        if (!$this->estVerrouille()) {
            $sousTotal = $this->getMontantTotal();
            $taxes = self::TAXES * $sousTotal;
            $fraisLivraison = $sousTotal >= self::MONTANT_MINIMUM || $sousTotal == 0 ? 0 : (double) self::FRAIS_LIVRAISON;
            $rabais = self::RABAIS * $sousTotal;
            $total = $sousTotal + $taxes + $fraisLivraison - $rabais;

            $sommaire = array(
                "sousTotal" => number_format($sousTotal, 2),
                "taxes" => number_format($taxes, 2),
                "fraisLivraison" => number_format($fraisLivraison, 2),
                "rabais" => number_format((0.00 - $rabais), 2),
                "total" => number_format($total, 2)
            );

            array_push($tabSommaire, $sommaire);
        }

        return json_encode($tabSommaire);
    }

    /**
     * Ajoute un article dans le tableau de session
     * @param {string} $libelle - la libelle de l'article
     * @param {int} $quantiteDansPanier - la quantité par article
     * @param {double} $prixUnitaire - le prix à l'unité
     */
    public function ajouterArticle($noArticle, $libelle, $cheminImage, $quantite, $prixUnitaire){
        $noArticle = (int) $noArticle;

        if (!$this->estVerrouille()) {

            //Si le produit existe déjà on ajoute seulement la quantité
            $indexArticle = array_search($noArticle, $_SESSION['panier']['noArticle']);

            if ($indexArticle !== false) {
                $_SESSION['panier']['quantiteDansPanier'][$indexArticle] += $quantite;
            } 
            else {
                //Insérer les informations dans le tableau
                array_push($_SESSION['panier']['noArticle'], $noArticle);
                array_push($_SESSION['panier']['libelle'], $libelle);
                array_push($_SESSION['panier']['cheminImage'], $cheminImage);
                array_push($_SESSION['panier']['quantiteDansPanier'], $quantite);
                array_push($_SESSION['panier']['prixUnitaire'], round($prixUnitaire, 2));
            }
        }
    }

    /**
     * Vérifie si article est dans le panier
     * @param {int} $noArticle - le numéro de l'article
     * @return boolean
     */
    public function estDansLePanier($noArticle){     
        if(!$this->estVerrouille()) {
            for($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++){
                if($_SESSION['panier']['noArticle'][$i] == $noArticle){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Supprime un article dans le tableau de session
     * @param {string} $libelle - la libelle de l'article
     * @throws Exception si l'article n'est pas le panier
     */
    public function supprimerArticle($noArticle) {
        $noArticle = (int) $noArticle;

        if(!$this->estDansLePanier($noArticle)) {
            throw new Exception("L'article que vous tentez de supprimer n'existe pas.");
        }
      
        if (!$this->estVerrouille()) {
            $tmp = array();
            $tmp['noArticle'] = array();
            $tmp['libelle'] = array();
            $tmp['cheminImage'] = array();
            $tmp['quantiteDansPanier'] = array();
            $tmp['prixUnitaire'] = array();
            $tmp['estVerrouille'] = $_SESSION['panier']['estVerrouille'];

            for ($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++) {
                if ($_SESSION['panier']['noArticle'][$i] !== $noArticle) {
                    array_push($tmp['noArticle'], $_SESSION['panier']['noArticle'][$i]);
                    array_push($tmp['libelle'], $_SESSION['panier']['libelle'][$i]);
                    array_push($tmp['cheminImage'], $_SESSION['panier']['cheminImage'][$i]);
                    array_push($tmp['quantiteDansPanier'], $_SESSION['panier']['quantiteDansPanier'][$i]);
                    array_push($tmp['prixUnitaire'], $_SESSION['panier']['prixUnitaire'][$i]);
                }

            }
            $_SESSION['panier'] = $tmp;
            unset($tmp);
        } 
    }

    /**
     * Modifier la quantité de tous les articles
     * @param {array} $tabNoArticle - tous les numéros d'article
     * @param {array} $tabQteDansPanier - toutes les quantités
     */
    public function modifierQteArticles($tabNoArticle, $tabQuantite) {
        //Vérifier si le panier existe
        if (!$this->estVerrouille()) {
            for ($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++) {
                if ($tabQuantite[$i] > 0) {
                    //Recherche du produit
                    $index = array_search((int) $tabNoArticle[$i], $_SESSION['panier']['noArticle']);
                    if ($index !== false) {
                        $_SESSION['panier']['quantiteDansPanier'][$index] = (int) $tabQuantite[$i];
                    }

                } else {
                    $this->supprimerArticle((int) $tabNoArticle[$i]);
                }
            }

        } 
    }

    /**
     * Vérifie si le panier est vérouillé ou pas
     * @return boolean
     */
    public function estVerrouille() {
        return ($this->creerPanier() && $_SESSION['panier']['estVerrouille'] == true);
    }

    /**
     * Verouille le panier
     */
    public function verrouillerPanier() {
        if ($this->creerPanier()) {
            $_SESSION['panier']['estVerrouille'] == true;
        }
    }

    /**
     * Dévérouille le panier
     */
    public function deverrouillerPanier(){
        if ($this->creerPanier()) {
            $_SESSION['panier']['estVerrouille'] == false;
        }
    }

    /**
     * Supprime le panier
     */
    public function viderPanier() {
        if ($this->creerPanier()) {
            unset($_SESSION['panier']);
        }
    }
}
