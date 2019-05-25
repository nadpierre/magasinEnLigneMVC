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

        return json_encode($listeMembres);
    }

    /**
     * Permet de rechercher des membres par leur nom ou prénom
     * @param {string} $noMembre
     * @return string - un JSON du tableau de membres
     */
    public function rechercherParNom($nom){
        $listeMembres = array();
        $nom = strtolower($nom);

        $requete = $this->bdd->prepare(
            'SELECT * FROM membre 
            WHERE (LOWER(nomMembre) LIKE :nomMembre 
                OR LOWER(prenomMembre) LIKE :prenomMembre)'
        );

        $requete->bindValue(':nomMembre', $nom, PDO::PARAM_STR);
        $requete->bindValue(':prenomMembre', $nom, PDO::PARAM_STR);
        $requete->execute();

        while ($donnees = $requete->fetch()) {
            $membre = new Membre($donnees);
            array_push($listeMembres, $membre->getTableau());
        }

        return json_encode($listeMembres);
    }

    /**
     * Retourne les informations du membre
     * @param {any} $info - le critère de recherche
     * @return Membre
     * @throws Exception si le membre n'existe pas
     */
    public function getMembre($info) {

        if(is_int($info)){
            $info = (int) $info;
            $requete = $this->bdd->prepare('SELECT * FROM membre WHERE noMembre = ?');
            $requete->bindValue(1, $info, PDO::PARAM_INT);
        }
        else {
            $requete = $this->bdd->prepare('SELECT * FROM membre WHERE courriel = ?');
            $requete->bindValue(1, $info, PDO::PARAM_STR);
        }

        $requete->execute();
        $donnees = $requete->fetch();
        

        if($donnees === false){
           throw new Exception("Le compte n'a pas été trouvé.");
        }

        return new Membre($donnees);
          
    }

    /**
     * Vérifie si le membre existe déjà
     * @param {Membre} $membre
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
     * @param {Membre} $membre
     * @return void
     * @throws Exception si le membre est déjà inscrit
     */
    public function ajouterMembre(Membre $membre) {

        # Ne pas ajouter de mot de passe si c'est un invité
        if($membre->getCategorie() != 0){
            $motDePasse = password_hash($membre->getMotDePasse(), PASSWORD_DEFAULT);
        }
        else {
            $motDePasse = "";
        }

        
         # Vérifier si la personne a déjà passé une commande en tant qu'invité
         # et mettre à jour les renseignements si c'est le cas
        if($this->existeDeja($membre)) {     
            $invite = $this->getMembre($membre->getCourriel());
            if($invite->getCategorie() != 0){
                throw new Exception("Un compte est déjà associé à ce courriel");
            }
            else{
                $membre->setNoMembre($invite->getNoMembre());
                $this->modifierMembre($membre);
                $this->changerMotDePasse($membre->getNoMembre(), $motDePasse);
            }
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
        
    }   
        
    /**
     * Modifie les informations d'un membre existant
     * @param {Membre} $membre
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
     * Désactive un membre
     * @param {int} $noMembre
     * @return void
     */
    public function desactiverMembre($noMembre) {    
        $requete = $this->bdd->prepare('UPDATE membre SET actif = 0 WHERE noMembre = ?');
        $requete->bindValue(1, $noMembre, PDO::PARAM_INT);
        $requete->execute();
    }

}