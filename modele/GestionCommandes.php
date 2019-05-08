<?php
/**
 * Représente un objet de type GestionCommandes
 * Son rôle est de gérer les commandes dans la base de données MySQL
 * Hérite de la classe GestionBD
 */
class GestionCommandes extends GestionBD {

    /**
     * Retourne les informations d'une commande
     * @param {int} - l'identifiant de la commande
     * @return Commande - une instance de l'objet Commande
     */
    public function getCommande($noCommande) {
        $noCommande = (int) $noCommande;
    }

    /**
     * Ajoute une commande
     * @param {Commande} $noMembre - une instance de l'objet Commande
     * @return void
     */
    public function ajouterCommande(Commande $commande) {

        $requete = $this->_bdd->prepare(
            'INSERT INTO commande (dateCommande, noMembre, paypalOrderId)
            VALUES (NOW(), :noMembre, :paypalOrderId)'
        );
        $requete->bindValue(':noMembre', (int) $commande->getNoMembre(), PDO::PARAM_INT);
        $requete->bindValue(':paypalOrderId', $commande->getPaypalOrderId(), PDO::PARAM_STR);
        $requete->execute();
        $requete->closeCursor();
         
    }

    /**
     * Supprime une commande
     */
    public function supprimerCommande() {

    }

    


   
}

?>