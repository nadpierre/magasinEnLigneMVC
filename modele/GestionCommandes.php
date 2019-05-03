<?php

/**
 * Représente un objet de type GestionCommandes
 * Son rôle est de gérer les commandes dans la base de données MySQL
 * Hérite de la classe GestionBD
 */
class GestionCommandes extends GestionBD {

    /**
     * Ajoute une commande
     * @param {int} $noClient - le numéro du client
     * @param {string} $paypalOrderId - le numéro de confirmation de Paypal
     * @param {array} $tabNoArticle - tableau des numéros d'article
     * @param {array} $tabQuantite - tableau avec les quantités respectives
     */
    public function ajouterCommande($noClient, $paypalOrderId, array $tabNoArticle, array $tabQuantite) {

        //Insérer la commande
        $requete = $this->_bdd->prepare(
            'INSERT INTO commande (dateCommande, noClient, paypalOrderId)
            VALUES (NOW(), :noClient, :paypalOrderId)'
        );
        $requete->bindValue(':noClient', (int) $noClient, PDO::PARAM_INT);
        $requete->bindValue(':paypalOrderId', $paypalOrderId, PDO::PARAM_STR);
        $requete->execute();
        $requete->closeCursor();

        //Insérer les articles en commande
        $noCommande = (int) $this->_bdd->lastInsertId();
        for($i = 0; $i < count($tabNoArticle); $i++) {
            $requete = $this->_bdd->prepare(
                'INSERT INTO article_en_commande (noCommande, noArticle, quantite)
                VALUES (:noCommande, :noArticle, :quantite)'
            );
            $requete->bindValue(':noCommande', $noCommande, PDO::PARAM_INT);
            $requete->bindValue(':noArticle', (int) $tabNoArticle[$i], PDO::PARAM_INT);
            $requete->bindValue(':quantite', (int) $tabQuantite[$i], PDO::PARAM_INT);
            $requete->execute();
            $requete->closeCursor();
        }

    }

     /**
     * Retourne le numéro de confirmation et le courriel de l'utilisateur
     * @return array - un tableau associatif
     */
    public function getConfirmation(){
        $tabCommande = array();

        $requete = $this->_bdd->query(
            'SELECT
                commande.paypalOrderId,
                client.courriel
            FROM commande
            JOIN client ON commande.noClient = client.noClient
            ORDER BY dateCommande DESC
            LIMIT 1'
        );
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();
        
        
        array_push($tabCommande, $donnees);
        return $tabCommande;
    }


   
}

?>