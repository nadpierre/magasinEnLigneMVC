<?php
class Connexion {

    /**
     * Créé les variables de session pour un usager
     * @param {Membre} - un membre déjà authentifié
     * @return void
     */
    public function creerConnexion(Membre $membre) {
        if(!isset($_SESSION['connexion'])){
            session_regenerate_id();
            $_SESSION['connexion']['estConnecte'] = true;
            $_SESSION['connexion']['nom'] = $membre->getPrenomMembre() . ' ' . $membre->getNomMembre();
            $_SESSION['connexion']['id'] = $membre->getNoMembre();
        }
    }

    /**
     * Retourne le nom complet de l'utilisateur
     * @return string
     */
    public function getNomUtilisateur(){
        return isset($_SESSION['connexion'])? $_SESSION['connexion']['nom'] : null;
    }

    /**
     * Retourne le numéro de l'utilisateur
     * @return string
     */
    public function getIdUtilisateur() {
        return isset($_SESSION['connexion'])? $_SESSION['connexion']['id'] : null;
    }

    /**
     * Vérifie si l'utilisateur est connecté
     * @return boolean
     */
    public function estConnecte(){
        return (isset($_SESSION['connexion']) && $_SESSION['connexion']['estConnecte'] == true);
    }

    /**
     * Déconnecter l'utilisateur
     * @return void
     */
    public function seDeconnecter(){
        if (isset($_SESSION['connexion'])) {
            unset($_SESSION['connexion']);
        }
    }
}