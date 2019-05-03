<?php
/**
 * Représente un objet de type Commande
 */
class Commande {

    /* ATTRIBUTS */

    private $_noCommande;
    private $_dateCommande;
    private $_noClient;
    private $_paypalOrderId;


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
        return $this->_noCommande;
    }

    public function getDateCommande(){
        return $this->_dateCommande;
    }

    public function getNoClient(){
        return $this->_noClient;
    }

    public function getPaypalOrderId(){
        return $this->_paypalOrderId;
    }


    /* MUTATEURS */
    public function setNoCommande($noCommande){
        $noCommande = (int) $noCommande;
        $this->_noCommande = $noCommande;
    }

    public function setDateCommande($dateCommande){
        if(!preg_match(self::DATE, $dateCommande)){
            throw new Exception('Format de date invalide.');
            return;
        }
        $this->_dateCommande = $dateCommande;
    }

    public function setNoClient($noClient){
        $noClient = (int) $noClient;
        $this->_noClient = $noClient;
    }

    public function setPaypalOrderId($paypalOrderId) {
        $this->_paypalOrderId = $paypalOrderId;
    }


     /* MÉTHODE GÉNÉRALE */

    /**
     * Assigne les bonnes valeurs aux attributs
     * @param {array} $donnees - tableau associatif contenant les attributs et les valeurs
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
     * Retourne les attributs et les valeurs de la commande
     * @return array - un tableau associatif (retire les "_" des attributs)
     */
    public function getTableau(){
        return array (
            "noCommande" => $this->getNoCommande(),
            "dateCommande" => $this->getDateCommande(),
            "noClient" => $this->getNoClient(),
            "paypalOrderId" => $this->getPaypalOrderId()
        );
    }

}

?>