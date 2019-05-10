<?php
/**
 * Représente un objet de type GestionMembres
 * Son rôle est de gérer les membres dans la base de données MariaDB
 * Hérite de la classe GestionBD
 */
 class GestionMembres extends GestionBD {

    /**
     * Retourne les informations du membre
     * @param {string} $info - le critère de recherche
     * @return Membre - une instance d'un objet Membre
     * @throws Exception si le membre n'existe pas
     */
    public function getMembre($info) {

        if(is_int($info)){//Il s'agit d'un numéro de membre
            $info = (int) $info;
            $requete = $this->bdd->prepare('SELECT * FROM membre WHERE noMembre = ?');
            $requete->bindValue(1, $info, PDO::PARAM_INT);
        }
        else {//Sinon, c'est un courriel
            $requete = $this->bdd->prepare('SELECT * FROM membre WHERE courriel = ?');
            $requete->bindValue(1, $info, PDO::PARAM_STR);
        }

        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();

        if($donnees === false){
           throw new Exception("Courriel invalide");
        }

        $membre = new Membre($donnees);
        
        return $membre;
       
    }

    /**
     * Vérifie si le membre existe déjà
     * @param {Membre} - l'instance d'un membre
     * @return boolean
     */
    public function existeDeja(Membre $membre) {
        try {
            $this->getMembre($membre->getCourriel());
            return true;
        }
        catch (Exception $e){
            return false;
        }
    }

    /**
     * Ajoute un nouveau membre
     * @param {Membre} $membre - un membre déjà instancié
     * @return void
     * @throws Exception si le membre existe déjà
     */
    public function ajouterMembre(Membre $membre) {

        if($this->existeDeja($membre)) {
           throw new Exception("Un compte est déjà associé à ce courriel");
        }

        //Encrypter le mot de passe
        $motDePasse = password_hash($membre->getMotDePasse(), PASSWORD_DEFAULT);

        $requete = $this->bdd->prepare(
            'INSERT INTO membre (nomMembre, prenomMembre, estAdmin, adresse, ville, province,
                codePostal, noTel, courriel, motDePasse)
            VALUES (:nomMembre, :prenomMembre, :estAdmin, :adresse, :ville, :province,
                :codePostal, :noTel, :courriel, :motDePasse)'
        );

        $requete->bindValue(':nomMembre', $membre->getNomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':prenomMembre', $membre->getPrenomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':estAdmin', $membre->getEstAdmin(), PDO::PARAM_INT);
        $requete->bindValue(':adresse', $membre->getAdresse(), PDO::PARAM_STR);
        $requete->bindValue(':ville', $membre->getVille(), PDO::PARAM_STR);
        $requete->bindValue(':province', $membre->getProvince(), PDO::PARAM_STR);
        $requete->bindValue(':codePostal', $membre->getCodePostal(), PDO::PARAM_STR);
        $requete->bindValue(':noTel', $membre->getNoTel(), PDO::PARAM_STR);
        $requete->bindValue(':courriel', $membre->getCourriel(), PDO::PARAM_STR);
        $requete->bindValue(':motDePasse', $motDePasse, PDO::PARAM_STR);

        $requete->execute();
        $requete->closeCursor();
    }   
        
    /**
     * Modifie les informations d'un membre existant
     * @param {Membre} - un membre déjà instancié
     * @return void
     */
    public function modifierMembre(Membre $membre) {

        //Encrypter le mot de passe
        $motDePasse = password_hash($membre->getMotDePasse(), PASSWORD_DEFAULT);

        $requete = $this->bdd->prepare(
            'UPDATE membre
            SET
                nomMembre = :nomMembre,
                prenomMembre = :prenomMembre,
                estAdmin = :estAdmin,
                adresse = :adresse,
                ville = :ville,
                province = :province,
                codePostal = :codePostal,
                noTel = :noTel,
                courriel = :courriel,
                motDePasse = :motDePasse
            WHERE noMembre = :noMembre' 
        );

        $requete->bindValue(':nomMembre', $membre->getNomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':prenomMembre', $membre->getPrenomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':estAdmin', $membre->getEstAdmin(), PDO::PARAM_INT);
        $requete->bindValue(':adresse', $membre->getAdresse(), PDO::PARAM_STR);
        $requete->bindValue(':ville', $membre->getVille(), PDO::PARAM_STR);
        $requete->bindValue(':province', $membre->getProvince(), PDO::PARAM_STR);
        $requete->bindValue(':codePostal', $membre->getCodePostal(), PDO::PARAM_STR);
        $requete->bindValue(':noTel', $membre->getNoTel(), PDO::PARAM_STR);
        $requete->bindValue(':courriel', $membre->getCourriel(), PDO::PARAM_STR);
        $requete->bindValue(':motDePasse', $motDePasse, PDO::PARAM_STR);
        $requete->bindValue(':noMembre', $membre->getNoMembre(), PDO::PARAM_INT);

        $requete->execute();
        $requete->closeCursor();

    }


    /**
     * Supprime un membre
     * @param {Membre} - un membre déjà instancié
     * @return void
     */
    public function supprimerMembre(Membre $membre) {
        $requete = $this->bdd->prepare('DELETE FROM membre WHERE noMembre = ?');
        $requete->bindValue(1, $membre->getNoMembre(), PDO::PARAM_INT);
        $requete->execute();
        $requete->closeCursor();
    }


     /**
     * Récupère les information du dernier membre ajouté
     * @return string - le JSON de l'objet si le membre existe
     */
    public function getDernierMembre() {
    
        $requete = $this->bdd->query('SELECT * FROM membre ORDER BY noMembre DESC LIMIT 1');
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();
        $membre = new Membre($donnees);

        return '[' . $membre . ']';
    }

}

?>