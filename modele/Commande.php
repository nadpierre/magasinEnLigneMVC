<?php
/**
 * Représente un objet de type Commande
 */
class Commande {

    /* ATTRIBUTS */

    private $noCommande;
    private $dateCommande;
    private $noMembre;
    private $paypalOrderId;


    /* CONSTANTES (regex) */

    const DATE = '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/';


    /**
     * CONSTRUCTEUR : crée un objet de type Article
     * @param {array} $donnees - tableau associatif contenant les attributs et leurs valeurs
     */
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }


    /* ACCESSEURS */

    public function getNoCommande(){
        return $this->noCommande;
    }

    public function getDateCommande(){
        return $this->dateCommande;
    }

    public function getNoMembre(){
        return $this->noMembre;
    }

    public function getPaypalOrderId(){
        return $this->paypalOrderId;
    }


    /* MUTATEURS */
    public function setNoCommande($noCommande){
        $noCommande = (int) $noCommande;
        $this->noCommande = $noCommande;
    }

    public function setDateCommande($dateCommande){
        if(!preg_match(self::DATE, $dateCommande)){
            throw new Exception('Format de date invalide.');
            return;
        }
        $this->dateCommande = $dateCommande;
    }

    public function setNoMembre($noMembre){
        $noMembre = (int) $noMembre;
        $this->noMembre = $noMembre;
    }

    public function setPaypalOrderId($paypalOrderId) {
        $this->paypalOrderId = $paypalOrderId;
    }


     /* MÉTHODES GÉNÉRALES */

    /**
     * Assigne les bonnes valeurs aux attributs
     * @param {array} $donnees - tableau associatif contenant les attributs et les valeurs
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