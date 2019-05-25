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
            $_SESSION['connexion']['id'] = $membre->getNoMembre();
            $_SESSION['connexion']['categorie'] = $membre->getCategorie();
            $_SESSION['connexion']['estConnecte'] = true;
        }
    }


    /**
     * Retourne le numéro de l'utilisateur
     * @return string
     */
    public function getIdUtilisateur() {
        return isset($_SESSION['connexion'])? $_SESSION['connexion']['id'] : null;
    }

    /**
     * Retourne la catégorie de l'utilisateur
     * @return int - 1 pour membre, 2 pour administrateur
     */
    public function getCategorie(){
        return isset($_SESSION['connexion'])? $_SESSION['connexion']['categorie'] : null;
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