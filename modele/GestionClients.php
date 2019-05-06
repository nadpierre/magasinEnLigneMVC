<?php
/**
 * Représente un objet de type GestionClients
 * Son rôle est de gérer les clients dans la base de données MySQL
 * Hérite de la classe GestionBD
 */
 class GestionClients extends GestionBD {

    /**
     * Retourne les informations du client
     * @param {int} noClient - l'identifiant du client
     * @return array - un tableau associatif si le client existe
     */
    public function getClient($info) {
        $tabClient = array();

        if(is_int($info)){//Il s'agit d'un numéro de client
            $info = (int) $info;
            $requete = $this->_bdd->prepare('SELECT * FROM client WHERE noClient = ?');
            $requete->bindValue(1, $info, PDO::PARAM_INT);
        }
        else {//Sinon, c'est un courriel
            $requete = $this->_bdd->prepare('SELECT * FROM client WHERE courriel = ?');
            $requete->bindValue(1, $info, PDO::PARAM_STR);
        }

        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();

        if($donnees === false){
           throw new Exception("Le client n'existe pas");
        }

        $client = new Client($donnees);
        array_push($tabClient, $client->getTableau());
        return $tabClient;
       
    }

    /**
     * Vérifie si le client existe déjà
     * @param {string} $courriel - le courriel du client
     * @return boolean
     */
    public function existeDeja($courriel) {
        try {
            $this->getClient($courriel);
            return true;
        }
        catch (Exception $e){
            return false;
        }
    }

    /**
     * Ajoute un nouveau client
     * @param {Client} $client - un client déjà instancié
     */
    public function ajouterClient(Client $client) {

        if($this->existeDeja($client->getCourriel())) {
           throw new Exception("Un compte est déjà associé à ce courriel");
        }

        $requete = $this->_bdd->prepare(
            'INSERT INTO client (nomClient, prenomClient, adresse, ville, province,
                codePostal, noTel, courriel, pseudo, motDePasse)
            VALUES (:nomClient, :prenomClient, :adresse, :ville, :province,
                :codePostal, :noTel, :courriel, :pseudo, :motDePasse)'
        );

        $requete->bindValue(':nomClient', $client->getNomClient(), PDO::PARAM_STR);
        $requete->bindValue(':prenomClient', $client->getPrenomClient(), PDO::PARAM_STR);
        $requete->bindValue(':adresse', $client->getAdresse(), PDO::PARAM_STR);
        $requete->bindValue(':ville', $client->getVille(), PDO::PARAM_STR);
        $requete->bindValue(':province', $client->getProvince(), PDO::PARAM_STR);
        $requete->bindValue(':codePostal', $client->getCodePostal(), PDO::PARAM_STR);
        $requete->bindValue(':noTel', $client->getNoTel(), PDO::PARAM_STR);
        $requete->bindValue(':courriel', $client->getCourriel(), PDO::PARAM_STR);
        $requete->bindValue(':pseudo', $client->getPseudo(), PDO::PARAM_STR);
        $requete->bindValue(':motDePasse', $client->getMotDePasse(), PDO::PARAM_STR);

        $requete->execute();
        $requete->closeCursor();
        
    }

    
    /**
     * Retourne les informations d'une personne déjà inscrite
     * @param {string} $pseuoo - le pseudonyme
     * @param {string} $motDePasse - le mot de passe
     * @return array - si le membre existe
     */
    public function getMembre($courriel, $motDePasse) {
        $tabMembre = array();
        $requete = $this->_bdd->prepare(
            'SELECT * FROM client WHERE courriel = :courriel AND motDePasse = :motDePasse'
        );
        $requete->bindValue(':courriel', $pseudo, PDO::PARAM_STR);
        $requete->bindValue(':motDePasse', $motDePasse, PDO::PARAM_STR);
        $requete->execute();
        
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();

        if($donnees === false ){
           throw new Exception("Nom d'utilisateur ou mot de passe non valide.");
        }
       
        $membre = new Client($donnees);
        array_push($tabMembre, $membre->getTableau());
        return $tabMembre;
       
    }

     /**
     * Récupère les information du dernier client ajouté
     * @return array - un tableau associatif
     */
    public function getDernierClient() {
        $tabClient = array();

        $requete = $this->_bdd->query('SELECT * FROM client ORDER BY noClient DESC LIMIT 1');
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();
        $client = new Client($donnees);
        array_push($tabClient, $client->getTableau());

        return $tabClient;
    }

}

?>