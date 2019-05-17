<?php
/**
 * Représente un objet de type GestionMembres
 * Son rôle est de gérer les membres dans la base de données MariaDB
 * Hérite de la classe GestionBD
 */
 class GestionMembres extends GestionBD {

    /**
     * Retourne tous les membres
     * @return string - un JSON du tableau de membres
     */
    public function getListeMembres(){
        $listeMembres = array();

        $requete = $this->bdd->query('SELECT * FROM membre ORDER BY nomMembre');
       
        while ($donnees = $requete->fetch()) {
            $membre = new Membre($donnees);
            array_push($listeMembres, $membre->getTableau());
        }

        $requete->closeCursor();
  
        return json_encode($listeMembres);
    }

    /**
     * Retourne les informations du membre
     * @param {string} $info - le critère de recherche
     * @return Membre - une instance d'un objet Membre
     * @throws Exception si le membre n'existe pas
     */
    public function getMembre($info) {

        if(is_int($info)){//Il s'agit d'un numéro de membre
            $info = (int) $info;
            $requete = $this->bdd->prepare('SELECT * FROM membre WHERE (noMembre = ? AND categorie != 0)');
            $requete->bindValue(1, $info, PDO::PARAM_INT);
        }
        else {//Sinon, c'est un courriel
            $requete = $this->bdd->prepare('SELECT * FROM membre WHERE (courriel = ? AND categorie != 0)');
            $requete->bindValue(1, $info, PDO::PARAM_STR);
        }

        $requete->execute();
        $donnees = $requete->fetch();
        $requete->closeCursor();

        if($donnees === false){
           throw new Exception("Courriel invalide");
        }

        return new Membre($donnees);
          
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
        if($membre->getCategorie() != 0){
            $motDePasse = password_hash($membre->getMotDePasse(), PASSWORD_DEFAULT);
        }
        else {
            $motDePasse = "";
        }
        

        $requete = $this->bdd->prepare(
            'INSERT INTO membre (nomMembre, prenomMembre, categorie, adresse, ville, province,
                codePostal, noTel, courriel, motDePasse)
            VALUES (:nomMembre, :prenomMembre, :categorie, :adresse, :ville, :province,
                :codePostal, :noTel, :courriel, :motDePasse)'
        );

        $requete->bindValue(':nomMembre', $membre->getNomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':prenomMembre', $membre->getPrenomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':categorie', $membre->getCategorie(), PDO::PARAM_INT);
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

        $requete = $this->bdd->prepare(
            'UPDATE membre
            SET
                nomMembre = :nomMembre,
                prenomMembre = :prenomMembre,
                adresse = :adresse,
                ville = :ville,
                province = :province,
                codePostal = :codePostal,
                noTel = :noTel
            WHERE noMembre = :noMembre' 
        );

        $requete->bindValue(':nomMembre', $membre->getNomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':prenomMembre', $membre->getPrenomMembre(), PDO::PARAM_STR);
        $requete->bindValue(':adresse', $membre->getAdresse(), PDO::PARAM_STR);
        $requete->bindValue(':ville', $membre->getVille(), PDO::PARAM_STR);
        $requete->bindValue(':province', $membre->getProvince(), PDO::PARAM_STR);
        $requete->bindValue(':codePostal', $membre->getCodePostal(), PDO::PARAM_STR);
        $requete->bindValue(':noTel', $membre->getNoTel(), PDO::PARAM_STR);
        $requete->bindValue(':noMembre', $membre->getNoMembre(), PDO::PARAM_INT);

        $requete->execute();
        $requete->closeCursor();

    }


    /**
     * Génère un mot de passe temporaire de 8 caractères
     * @return string
     */
    function genererMotDePasse(){ 
        $characteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueur = strlen($characteres);
        $motDePasse = '';  
        for ($i = 0; $i < 8; $i++) {
            $motDePasse .= $characteres[rand(0, $longueur - 1)];
        }   
        return $motDePasse;
    }

    /**
     * Modifie ou réinitialise le mot de passe
     * @param {int} $noMembre - le numéro du membre
     * @param {string} $motDePasse - le nouveau mot de passe
     * @return void
     */
    public function changerMotDePasse($noMembre, $motDePasse) {    
        $motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
        
        $requete = $this->bdd->prepare(
            'UPDATE membre 
            SET motDePasse = :motDePasse 
            WHERE (noMembre = :noMembre AND categorie != 0)'
        );

        $requete->bindValue(':motDePasse', $motDePasse, PDO::PARAM_STR);
        $requete->bindValue(':noMembre', (int) $noMembre, PDO::PARAM_INT);
        $requete->execute();
    }


    /**
     * Supprime un membre
     * @param {Membre} - un membre déjà instancié
     * @return void
     */
    public function supprimerMembre($noMembre) {
        $requete = $this->bdd->prepare('DELETE FROM membre WHERE noMembre = ?');
        $requete->bindValue(1, $noMembre, PDO::PARAM_INT);
        $requete->execute();
        $requete->closeCursor();
    }


     /**
     * Récupère les information de l'invité (dernier membre ajouté)
     * @return Membre 
     */
    public function getInvite() {
        $requete = $this->bdd->query('SELECT * FROM membre ORDER BY noMembre DESC LIMIT 1');
        $donnees = $requete->fetch();
        $requete->closeCursor();
        return new Membre($donnees);
    }

}

?>