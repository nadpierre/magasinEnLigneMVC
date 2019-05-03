<?php

/**
 * Représente un objet de type Panier
 * Son rôle est de gérer un panier d'achats
 */
class Panier {

    const TAXES = 0.15;
    const FRAIS_LIVRAISON = 10;
    const MONTANT_MINIMUM = 75;
    const RABAIS = 0.2;

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
            $_SESSION['panier']['description'] = array();
            $_SESSION['panier']['cheminImage'] = array();
            $_SESSION['panier']['quantiteDansPanier'] = array();
            $_SESSION['panier']['prixUnitaire'] = array();
            $_SESSION['panier']['estVerrouille'] = false;
        }
        return true;
    }

    /**
     * Retourne le panier (réarrange les variables de session)
     * @return array - un tableau associatif pour chaque élément du panier
     */
    public function getPanier() {
        $listePanier = array();

        if (!$this->estVerrouille()) {
            for ($i = 0; $i < count($_SESSION['panier']['description']); $i++) {
                // Convertir le nombre décimal en format monétaire
                $prixTotal = $_SESSION['panier']['quantiteDansPanier'][$i] * $_SESSION['panier']['prixUnitaire'][$i];
                $prixTotal = number_format($prixTotal, 2, ',', ' ') . ' $';
                $ligne = array(
                    "noArticle" => (int) $_SESSION['panier']['noArticle'][$i],
                    "description" => $_SESSION['panier']['description'][$i],
                    "cheminImage" => $_SESSION['panier']['cheminImage'][$i],
                    "quantiteDansPanier" => (int) $_SESSION['panier']['quantiteDansPanier'][$i],
                    "prixUnitaire" => number_format($_SESSION['panier']['prixUnitaire'][$i], 2, ',', '') . ' $',
                    "prixTotal" => $prixTotal,
                );
                array_push($listePanier, $ligne);
            }
        }
        return $listePanier;
    }

    /**
     * Retourne le nombre total d'articles
     * @return int
     */
    public function getNbArticlesTotal() {
        $compteur = 0;
        if (!$this->estVerrouille()) {
            for ($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++) {
                $compteur += $_SESSION['panier']['quantiteDansPanier'][$i];
            }
        }
        return $compteur;
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
     * @return array - un tableau associatif
     */
    public function getSommaire(){

        $tabSommaire = array();

        if (!$this->estVerrouille()) {
            $sousTotal = $this->getMontantTotal();
            $taxes = self::TAXES * $sousTotal;
            $fraisLivraison = $sousTotal >= self::MONTANT_MINIMUM || $sousTotal == 0 ? 0 : self::FRAIS_LIVRAISON;
            $rabais = self::RABAIS * $sousTotal;
            $total = $sousTotal + $taxes + $fraisLivraison - $rabais;

            $sommaire = array(
                "sousTotal" => number_format($sousTotal, 2, ',', '') . ' $',
                "taxes" => number_format($taxes, 2, ',', '') . ' $',
                "fraisLivraison" => number_format($fraisLivraison, 2, ',', '') . ' $',
                "rabais" => '-' . number_format($rabais, 2, ',', '') . ' $',
                "total" => number_format($total, 2, ',', '') . ' $',
            );

            array_push($tabSommaire, $sommaire);
        }

        return $tabSommaire;
    }

    /**
     * Ajoute un article dans le tableau de session
     * @param {string} $description - la description de l'article
     * @param {int} $quantiteDansPanier - la quantité par article
     * @param {double} $prixUnitaire - le prix à l'unité
     */
    public function ajouterArticle($noArticle, $description, $cheminImage, $quantite, $prixUnitaire){
        $noArticle = (int) $noArticle;
        if (!is_int($noArticle)) {
            error_log('Le numéro d\'un article doit être un nombre entier', 3, 'erreurs.txt');
            return;
        }

        if (!$this->estVerrouille()) {

            //Si le produit existe déjà on ajoute seulement la quantité
            $indexArticle = array_search($noArticle, $_SESSION['panier']['noArticle']);

            if ($indexArticle !== false) {
                $_SESSION['panier']['quantiteDansPanier'][$indexArticle] += $quantite;
            } 
            else {
                //Convertir le prix en numéro décimal
                $prixUnitaire = substr($prixUnitaire, 0, -2);
                $prixUnitaire = preg_replace('/,/', '.', $prixUnitaire);
                $prixUnitaire = number_format($prixUnitaire, 2);

                //Insérer les informations dans le tableau
                array_push($_SESSION['panier']['noArticle'], $noArticle);
                array_push($_SESSION['panier']['description'], $description);
                array_push($_SESSION['panier']['cheminImage'], $cheminImage);
                array_push($_SESSION['panier']['quantiteDansPanier'], $quantite);
                array_push($_SESSION['panier']['prixUnitaire'], $prixUnitaire);
            }
        } 
        else {
            echo "Un problème est survenu, contactez l'administrateur du site.";
        }
    }

    /**
     * Supprime un article dans le tableau de session
     * @param {string} $description - la description de l'article
     */
    public function supprimerArticle($noArticle) {
        $noArticle = (int) $noArticle;
        if (!is_int($noArticle)) {
            error_log('Le numéro d\'un article doit être un nombre entier', 3, 'erreurs.txt');
            return;
        }

        if (!$this->estVerrouille()) {
            $tmp = array();
            $tmp['noArticle'] = array();
            $tmp['description'] = array();
            $tmp['cheminImage'] = array();
            $tmp['quantiteDansPanier'] = array();
            $tmp['prixUnitaire'] = array();
            $tmp['estVerrouille'] = $_SESSION['panier']['estVerrouille'];

            for ($i = 0; $i < count($_SESSION['panier']['noArticle']); $i++) {
                if ($_SESSION['panier']['noArticle'][$i] !== $noArticle) {
                    array_push($tmp['noArticle'], $_SESSION['panier']['noArticle'][$i]);
                    array_push($tmp['description'], $_SESSION['panier']['description'][$i]);
                    array_push($tmp['cheminImage'], $_SESSION['panier']['cheminImage'][$i]);
                    array_push($tmp['quantiteDansPanier'], $_SESSION['panier']['quantiteDansPanier'][$i]);
                    array_push($tmp['prixUnitaire'], $_SESSION['panier']['prixUnitaire'][$i]);
                }

            }
            $_SESSION['panier'] = $tmp;
            unset($tmp);
        } 
        else {
            echo "Un problème est survenu, contactez l'administrateur du site.";
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
        else {
            echo "Un problème est survenu, contactez l'administrateur du site.";
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
     * Supprime le panier
     */
    public function supprimerPanier() {
        if ($this->creerPanier()) {
            unset($_SESSION['panier']);
            session_unset();
            session_destroy();
        }
    }
}
