<?php

/**
 * Représente un objet de type Article
 */
class Article {

    /* ATTRIBUTS */
    private $_noArticle;
    private $_categorie;
    private $_description;
    private $_cheminImage;
    private $_prixUnitaire;
    private $_quantiteEnStock;
    private $_quantiteDansPanier;

    /* CONSTANTES (regex) */
   const CHEMIN_IMAGE = '/^images\/(.*)\.(jpg|jpeg|png|gif)$/';

    /**
     * CONSTRUCTEUR : crée un objet de type Article
     * @param {array} $donnees - tableau associatif contenant les attributs et leurs valeurs
     */
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }


    /* ACCESSEURS */

    public function getNoArticle() {
        return $this->_noArticle;
    }

    public function getCategorie() {
        return $this->_categorie;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function getCheminImage() {
        return $this->_cheminImage;
    }

    public function getPrixUnitaire() {
        return $this->_prixUnitaire;
    }

    public function getQuantiteEnStock() {
        return $this->_quantiteEnStock;
    }

    public function getQuantiteDansPanier() {
        return $this->_quantiteDansPanier;
    }

    /* MUTATEURS */

    public function setNoArticle($noArticle) {
        $noArticle = (int) $noArticle;
        $this->_noArticle = $noArticle;
    }

    public function setCategorie($categorie) {
        $this->_categorie = $categorie;
    }

    public function setDescription($description) {
        $this->_description = $description;
    }

    public function setCheminImage($cheminImage) {
        if(!preg_match(self::CHEMIN_IMAGE, $cheminImage)){
            throw new Exception('Format de chemin d\'image invalide');
            return;
        }
        $this->_cheminImage = $cheminImage;
    }

    public function setPrixUnitaire($prixUnitaire) {
        $prixUnitaire = (double) $prixUnitaire;
        $this->_prixUnitaire = $prixUnitaire;
    }

    public function setQuantiteEnStock($quantiteEnStock) {
        $quantiteEnStock = (int) $quantiteEnStock;
        $this->_quantiteEnStock = $quantiteEnStock;
    }

    public function setQuantiteDansPanier($quantiteDansPanier) {
        $quantiteDansPanier = (int) $quantiteDansPanier;
        $this->_quantiteDansPanier = $quantiteDansPanier;
    }
    

    /* MÉTHODES GÉNÉRALES */

    /**
     * Assigne les bonnes valeurs aux attributs
     * @param {array} $donnes - tableau associatif contenant les attributs et les valeurs
     */
    public function hydrate(array $donnees) {
        foreach ($donnees as $attribut => $valeur) {
            $methode = 'set'.ucfirst($attribut);
            if(method_exists($this, $methode)) {
                $this->$methode($valeur);
            }
        }
    }


    /**
     * Retourne les attributs et les valeurs de l'article
     * @return array - un tableau associatif (retire les "_" des attributs)
     */
    public function getTableau(){
        return array (
            "noArticle" => $this->getNoArticle(),
            "categorie" => $this->getCategorie(),
            "description" => $this->getDescription(),
            "cheminImage" => $this->getCheminImage(),
            "prixUnitaire" => number_format($this->getPrixUnitaire(), 2, ',', ' ') . ' $',
            "quantiteEnStock" => $this->getQuantiteEnStock(),
            "quantiteDansPanier" => $this->getQuantiteDansPanier()
        );
    }


}

?>