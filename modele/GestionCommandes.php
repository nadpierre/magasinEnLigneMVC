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

        $requete = $this->bdd->prepare(
            'SELECT * FROM commande WHERE noCommande = ?'
        );
        $requete->bindValue(1, $noCommande, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch();
        $requete->closeCursor();

        return new Commande($donnees);
    }

    /**
     * Retourne la commande qui vient d'être créée
     * @return Commande - une instance de l'objet Commande
     */
    public function getDerniereCommande() {
        $requete = $this->bdd->query(
            'SELECT * FROM commande ORDER BY dateCommande DESC LIMIT 1'
        );
        $donnees = $requete->fetch();
        $requete->closeCursor();

        return new Commande($donnees);
    }

    /**
     * Ajoute une commande
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
        $requete->closeCursor();
         
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
        $requete->closeCursor();
    }

}

?>