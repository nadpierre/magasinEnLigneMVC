<?php

/**
 * Représente un objet de type Article
 */
class Article {

    /* ATTRIBUTS */
    private $noArticle;
    private $categorie;
    private $libelle;
    private $cheminImage;
    private $prixUnitaire;
    private $quantiteEnStock;
    private $quantiteDansPanier;



    /**
     * CONSTRUCTEUR : crée un objet de type Article
     * @param {array} $donnees - tableau associatif contenant les attributs et leurs valeurs
     */
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }


    /* ACCESSEURS */

    public function getNoArticle() {
        return $this->noArticle;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    public function getCheminImage() {
        return $this->cheminImage;
    }

    public function getPrixUnitaire() {
        return $this->prixUnitaire;
    }

    public function getQuantiteEnStock() {
        return $this->quantiteEnStock;
    }

    public function getQuantiteDansPanier() {
        return $this->quantiteDansPanier;
    }

    /* MUTATEURS */

    public function setNoArticle($noArticle) {
        $noArticle = (int) $noArticle;
        $this->noArticle = $noArticle;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    public function setLibelle($libelle) {
        $this->libelle = $libelle;
    }

    public function setCheminImage($cheminImage) {
        $this->cheminImage = $cheminImage;
    }

    public function setPrixUnitaire($prixUnitaire) {
        $prixUnitaire = (double) $prixUnitaire;
        $this->prixUnitaire = $prixUnitaire;
    }

    public function setQuantiteEnStock($quantiteEnStock) {
        $quantiteEnStock = (int) $quantiteEnStock;
        $this->quantiteEnStock = $quantiteEnStock;
    }

    public function setQuantiteDansPanier($quantiteDansPanier) {
        $quantiteDansPanier = (int) $quantiteDansPanier;
        $this->quantiteDansPanier = $quantiteDansPanier;
    }
    

    /* MÉTHODES GÉNÉRALES */

    /**
     * Assigne les bonnes valeurs aux attributs
     * @param {array} $donnes - tableau associatif contenant les attributs et les valeurs
     * @return void
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
     * Retourne les attributs et les valeurs de l'objet
     * @return array
     */
    public function getTableau(){
        return get_object_vars($this);
    }

    /**
     * Retourne le JSON de l'objet
     * @return string
     */
    public function __toString() {
        return json_encode(get_object_vars($this));
    }
}

?>