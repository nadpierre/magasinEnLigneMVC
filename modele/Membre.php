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
    private $actif;
    private $adresse;
    private $ville;
    private $province;
    private $codePostal;
    private $noTel;
    private $courriel;
    private $motDePasse;
    
    
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

    public function getActif(){
        return $this->actif;
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
        $this->nomMembre = $nomMembre;
    }

    public function setPrenomMembre($prenomMembre) {  
        $this->prenomMembre = $prenomMembre;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    public function setActif($actif){
        if(is_int($actif)){
            $this->actif = $actif == 1 ? true : false;
        }
        elseif(is_bool($actif)){
            $this->actif = $actif;
        }
        else{
            $this->actif = true;
        }     
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    public function setVille($ville) {
        $this->ville = $ville;
    }

    public function setProvince($province) {
        $this->province = $province;
    }

    public function setCodePostal($codePostal) {
        $this->codePostal = $codePostal;
    }

    public function setNoTel($noTel) {
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