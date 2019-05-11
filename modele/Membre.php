<?php

/**
 * Représente un objet de type Membre
 */
class Membre {

    /* ATTRIBUTS */
    private $noMembre;
    private $nomMembre;
    private $prenomMembre;
    private $categorie;
    private $adresse;
    private $ville;
    private $province;
    private $codePostal;
    private $noTel;
    private $courriel;
    private $motDePasse;
    

    /* CONSTANTES (regex) */
    const LETTRES_SEULEMENT = '/[a-zA-ZáàäâéèëêíìïîóòöôúùüûçñÁÀÄÂÉÈËÊÍÌÏÎÓÒÖÔÚÙÜÛÑÇ\'\-]+/';
    const CODE_POSTAL = '/^[A-Z][0-9][A-Z] ?[0-9][A-Z][0-9]$/';
    const NO_TEL = '/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/';

    
    /**
     * CONSTRUCTEUR : crée un objet de type Membre
     * @param {array} $donnees - tableau associatif contenant les attributs et leurs valeurs
     */
    public function __construct(array $donnees){
        $this->hydrate($donnees);
    }


    /* ACCESSEURS */

    public function getNoMembre() {
        return $this->noMembre;
    }

    public function getNomMembre() {
        return $this->nomMembre;
    }

    public function getPrenomMembre() {
        return $this->prenomMembre;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getVille() {
        return $this->ville;
    }

    public function getProvince() {
        return $this->province;
    }

    public function getCodePostal() {
        return $this->codePostal;
    }

    public function getNoTel() {
        return $this->noTel;
    }

    public function getCourriel() {
        return $this->courriel;
    } 

    public function getMotDePasse() {
        return $this->motDePasse;
    }


    /* MUTATEURS */

    public function setNoMembre($noMembre) {
        $noMembre = (int) $noMembre;
        $this->noMembre = $noMembre;
    }

    public function setNomMembre($nomMembre) {
        if(!preg_match(self::LETTRES_SEULEMENT, $nomMembre)){
            throw new Exception('Le nom du membre ne doit contenir que des lettres');
            return;
        }
        $this->nomMembre = $nomMembre;
    }

    public function setPrenomMembre($prenomMembre) {
        if(!preg_match(self::LETTRES_SEULEMENT, $prenomMembre)){
            throw new Exception('Le prénom ne doit contenir que des lettrees.');
            return;
        }
        $this->prenomMembre = $prenomMembre;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    public function setVille($ville) {
        if(!preg_match(self::LETTRES_SEULEMENT, $ville)){
            throw new Exception('La ville ne doit contenir que des lettres.');
            return;
        }
        $this->ville = $ville;
    }

    public function setProvince($province) {
        if(!preg_match(self::LETTRES_SEULEMENT, $province)){
            throw new Exception('La province ne doit contenir que des lettres.');
            return;
        }
        $this->province = $province;
    }

    public function setCodePostal($codePostal) {
        if(!preg_match(self::CODE_POSTAL, $codePostal)){
            throw new Exception('Format de code postal invalide.');
            return;
        }
        $this->codePostal = $codePostal;
    }

    public function setNoTel($noTel) {
        if(!preg_match(self::NO_TEL, $noTel)){
            throw new Exception('Format de numéro de téléphone invalide.');
            return;
        }
        $this->noTel = $noTel;
    }

    public function setCourriel($courriel) {
        $this->courriel = $courriel;
    }

    public function setMotDePasse($motDePasse) {
        $this->motDePasse = $motDePasse;
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