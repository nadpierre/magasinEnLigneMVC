<?php

/**
 * Représente un objet de type Client
 */
class Client {

    /* ATTRIBUTS */
    private $_noClient;
    private $_nomClient;
    private $_prenomClient;
    private $_adresse;
    private $_ville;
    private $_province;
    private $_codePostal;
    private $_noTel;
    private $_pseudo;
    private $_motDePasse;
    private $_courriel;

    /* CONSTANTES (regex) */
    const LETTRES_SEULEMENT = '/[a-zA-ZáàäâéèëêíìïîóòöôúùüûçñÁÀÄÂÉÈËÊÍÌÏÎÓÒÖÔÚÙÜÛÑÇ\'\-]+/';
    const CODE_POSTAL = '/^[A-Z][0-9][A-Z] ?[0-9][A-Z][0-9]$/';
    const NO_TEL = '/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/';

    
    /**
     * CONSTRUCTEUR : crée un objet de type Client
     * @param {array} $donnees - tableau associatif contenant les attributs et leurs valeurs
     */
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }


    /* ACCESSEURS */
    
    public function getNoClient() {
        return $this->_noClient;
    }

    public function getNomClient() {
        return $this->_nomClient;
    }

    public function getPrenomClient() {
        return $this->_prenomClient;
    }

    public function getAdresse() {
        return $this->_adresse;
    }

    public function getVille() {
        return $this->_ville;
    }

    public function getProvince() {
        return $this->_province;
    }

    public function getCodePostal() {
        return $this->_codePostal;
    }

    public function getNoTel() {
        return $this->_noTel;
    }

    public function getPseudo() {
        return $this->_pseudo;
    }

    public function getMotDePasse() {
        return $this->_motDePasse;
    }

    public function getCourriel() {
        return $this->_courriel;
    }


    /* MUTATEURS */

    public function setNoClient($noClient) {
        $noClient = (int) $noClient;
        $this->_noClient = $noClient;
    }

    public function setNomClient($nomClient) {
        if(!preg_match(self::LETTRES_SEULEMENT, $nomClient)){
            throw new Exception('Le nom du client ne doit contenir que des lettres');
            return;
        }
        $this->_nomClient = $nomClient;
    }

    public function setPrenomClient($prenomClient) {
        if(!preg_match(self::LETTRES_SEULEMENT, $prenomClient)){
            throw new Exception('Le prénom ne doit contenir que des lettrees.');
            return;
        }
        $this->_prenomClient = $prenomClient;
    }

    public function setAdresse($adresse) {
        $this->_adresse = $adresse;
    }

    public function setVille($ville) {
        if(!preg_match(self::LETTRES_SEULEMENT, $ville)){
            throw new Exception('La ville ne doit contenir que des lettres.');
            return;
        }
        $this->_ville = $ville;
    }

    public function setProvince($province) {
        if(!preg_match(self::LETTRES_SEULEMENT, $province)){
            throw new Exception('La province ne doit contenir que des lettres.');
            return;
        }
        $this->_province = $province;
    }

    public function setCodePostal($codePostal) {
        if(!preg_match(self::CODE_POSTAL, $codePostal)){
            throw new Exception('Format de code postal invalide.');
            return;
        }
        $this->_codePostal = $codePostal;
    }

    public function setNoTel($noTel) {
        if(!preg_match(self::NO_TEL, $noTel)){
            throw new Exception('Format de numéro de téléphone invalide.');
            return;
        }
        $this->_noTel = $noTel;
    }

    public function setPseudo($pseudo) {
        $this->_pseudo = $pseudo;
    }

    public function setMotDePasse($motDePasse) {
        $this->_motDePasse = $motDePasse;
    }

    public function setCourriel($courriel) {
        $this->_courriel = $courriel;
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
     * Retourne les attributs et les valeurs du client
     * @return array - un tableau associatif (retire les "_" des attributs)
     */
    public function getTableau(){
        return array (
            "noClient" => $this->getNoClient(),
            "nomClient" => $this->getNomClient(),
            "prenomClient" => $this->getPrenomClient(),
            "adresse" => $this->getAdresse(),
            "ville" => $this->getVille(),
            "province" => $this->getProvince(),
            "codePostal" => $this->getCodePostal(),
            "noTel" => $this->getNoTel(),
            "pseudo" => $this->getPseudo() != null? $this->getPseudo() : null,
            "motDePasse" => $this->getMotDePasse() != null? $this->getMotDePasse() : null,
            "courriel" => $this->getCourriel()
        );
    }

}

?>