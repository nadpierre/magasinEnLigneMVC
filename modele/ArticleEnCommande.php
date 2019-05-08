<?php
class ArticleEnCommande {
    
    /* ATTRIBUTS */
    private $_noCommande;
    private $_noArticle;
    private $_quantite;

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
        return $this->_noCommande;
    }

    public function getNoArticle() {
        return $this->_noArticle;
    }

    public function getQuantite() {
        return $this->_quantite;
    }

    /* MUTATEURS */
    public function setNoCommande($noCommande) {
        $this->_noCommande = $noCommande;
    }

    public function setNoArticle($noArticle) {
        $this->_noArticle = $noArticle;
    }

    public function setQuantite($quantite) {
        $this->_quantite = $quantite;
    }


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
            "noCommande" => $this->getNoCommande(),
            "noArticle" => $this->getNoArticle(),
            "quantite" => $this->getQuantite()
        );
    }
}