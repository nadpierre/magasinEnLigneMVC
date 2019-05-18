<?php

/**
 * Représente un objet de type GestionCommandes
 * Son rôle est de gérer les commandes dans la base de données MySQL
 * Hérite de la classe GestionBD
 */
class GestionCommandes extends GestionBD {
     
    /**
     * Retourne la liste de toutes les commandes
     * @return string - le JSON de la liste
     */
    public function getListeCommandes(){
        $listeCommandes = array();

        $requete = $this->bdd->query('SELECT * FROM commande ORDER BY dateCommande DESC');
        while($donnees = $requete->fetch()){
            $commande = new Commande($donnees);
            array_push($listeCommandes, $commande->getTableau());
        }

        return json_encode($listeCommandes);
    }

    /**
     * Retourner les commandes associées à un membre
     * @param {int} $noMembre - le numéro du client
     * @return string - le JSON des commandes
     */
    public function trierParMembre($noMembre){
        $listeCommandes = array();

        $requete = $this->bdd->prepare(
            'SELECT * FROM commande WHERE noMembre = ? ORDER BY dateCommande DESC'
        );
        $requete->bindValue(1, (int) $noMembre, PDO::PARAM_INT);
        $requete->execute();

        while($donnees = $requete->fetch()){
            $commande = new Commande($donnees);
            array_push($listeCommandes, $commande->getTableau());
        }

        return json_encode($listeCommandes);
    }

    /**
     * Retourne les informations d'une commande
     * @param {int} $noCommande - l'identifiant de la commande
     * @return Commande
     */
    public function getCommande($noCommande) {
        $noCommande = (int) $noCommande;

        $requete = $this->bdd->prepare(
            'SELECT * FROM commande WHERE noCommande = ?'
        );
        $requete->bindValue(1, $noCommande, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch();
        
        return new Commande($donnees);
    }

    
    /**
     * Retourne la commande qui vient d'être créée
     * @return Commande
     */
    public function getDerniereCommande() {
        $requete = $this->bdd->query(
            'SELECT * FROM commande ORDER BY dateCommande DESC LIMIT 1'
        );
        $donnees = $requete->fetch();
        
        return new Commande($donnees);
    }


    /**
     * Retourne les informations d'une commande,
     * en plus du montant total et tous les articles achetés
     * @param {int} $noCommande - l'identifiant de la commande
     * @return string - le JSON de la commande
     */
    public function getCommandeDetaillee($noCommande){
        $gestionAC = new GestionArticlesCommande;
        return json_encode(
            array(
                array(
                    "commande" => $this->getCommande($noCommande)->getTableau(),
                    "total" => (double) $gestionAC->getMontantTotal($noCommande) * 
                                Panier::TAXES * (1 - Panier::RABAIS),
                    "articles" => $gestionAC->getArticlesCommande($noCommande)
                )
            )        
        );
    }

     
    /**
     * Ajoute une commande
     * @param {Panier} $panier - le panier d'achat
     * @param {Commande} $commande - une instance de l'objet Commande
     * @return void
     * @throws Exception si le panier est vide
     */
    public function ajouterCommande(Panier $panier, Commande $commande) {
        if(empty($panier->getPanier())) {
            throw new Exception("Le panier est vide.");
        }

        $requete = $this->bdd->prepare(
            'INSERT INTO commande (dateCommande, noMembre, paypalOrderId)
            VALUES (NOW(), :noMembre, :paypalOrderId)'
        );
        $requete->bindValue(':noMembre', (int) $commande->getNoMembre(), PDO::PARAM_INT);
        $requete->bindValue(':paypalOrderId', $commande->getPaypalOrderId(), PDO::PARAM_STR);
        $requete->execute();
         
    }

    /**
     * Supprime une commande
     * @param {Commande} $commande - une instance de l'objet Commande
     * @return void
     */
    public function supprimerCommande(Commande $commande) {
        $requete = $this->bdd->prepare('DELETE FROM commande WHERE noCommande = ?');
        $requete->bindValue(1, $commande->getNoCommande(), PDO::PARAM_INT);
        $requete->execute();     
    }

}

?>