<?php
class ArticleEnCommande {
    
    /* ATTRIBUTS */
    private $noCommande;
    private $noArticle;
    private $quantite;

    /**
     * CONSTRUCTEUR
     * @param {array} $donnees - un tableau associatif
     * avec les attributs et valeurs
     */
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }

    /* ACCESSEURS */
    public function getNoCommande() {
        return $this->noCommande;
    }

    public function getNoArticle() {
        return $this->noArticle;
    }

    public function getQuantite() {
        return $this->quantite;
    }

    /* MUTATEURS */
    public function setNoCommande($noCommande) {
        $this->noCommande = $noCommande;
    }

    public function setNoArticle($noArticle) {
        $this->noArticle = $noArticle;
    }

    public function setQuantite($quantite) {
        $this->quantite = $quantite;
    }


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