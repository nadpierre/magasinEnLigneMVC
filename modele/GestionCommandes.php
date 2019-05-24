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
     * Retourne le montant total de la commande
     * @param {int} $noCommande - le numéro de la commande
     * @return string
     */
    public function getMontantTotal($noCommande){
        $requete = $this->bdd->prepare(
            'SELECT
                SUM(ac.quantite * ar.prixUnitaire) AS "total"
            FROM article_en_commande ac
            JOIN article ar ON ac.noArticle = ar.noArticle
            WHERE ac.noCommande = ?'
        );
        $requete->bindValue(1, (int) $noCommande, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch();
    
        return (double) $donnees["total"];   
    }


    /**
     * Retourne les informations d'une commande,
     * en plus du montant total et tous les articles achetés
     * @param {int} $noCommande - l'identifiant de la commande
     * @return string - le JSON de la commande
     */
    public function getCommandeDetaillee($noCommande){
        $noCommande = (int) $noCommande;
        $gestionAC = new GestionArticlesCommande;
        $gestionArticles = new GestionArticles();
        $gestionMembres = new GestionMembres();

        $commande = $this->getCommande($noCommande);
        $membre = $gestionMembres->getMembre($commande->getNoMembre());
        $tabArticles = $gestionAC->getArticlesCommande($noCommande);
        
        $objJSON["paypalOrderId"] = $commande->getPaypalOrderId();
        $objJSON["prenomMembre"] = $membre->getPrenomMembre();
        $objJSON["adresse"] = $membre->getAdresse();
        $objJSON["ville"] = $membre->getVille();
        $objJSON["province"] = $membre->getProvince();
        $objJSON["codePostal"] = $membre->getCodePostal();
        $objJSON["noTel"] = $membre->getNoTel();
        $objJSON["courriel"] = $membre->getCourriel();
        $objJSON["dateCommande"] = $commande->getDateCommande();
        $objJSON["montantTotal"] = $this->getMontantTotal($noCommande);
        
        $objJSON["articles"] = array();

        for($i = 0; $i < count($tabArticles); $i++){
            $ac = $tabArticles[$i];
            $noArticle = $ac->getNoArticle();
            $article = $gestionArticles->getArticle($noArticle);
            $tableau = array(
                "libelle" => $article->getLibelle(),
                "quantite" => $ac->getQuantite(),
                "prixTotal" => $article->getPrixUnitaire() * $ac->getQuantite()
            );
            array_push($objJSON["articles"], $tableau);   
        }

        $sousTotal = $this->getMontantTotal($noCommande);
        $taxes = Panier::TAXES * $sousTotal;
        $livraison = $sousTotal >= Panier::MONTANT_MINIMUM || $sousTotal == 0 ? 0 : Panier::FRAIS_LIVRAISON;
        $rabais = 0 - (Panier::RABAIS * $sousTotal);
        $total = $sousTotal + $taxes + $livraison + $rabais;

        $objJSON["sommaire"] = array(
            "sousTotal" => $sousTotal,
            "taxes" => round($taxes, 2),
            "livraison" => round($livraison, 2),
            "rabais" => round($rabais, 2),
            "total" => round($total, 2)
        );

        $objJSON = array($objJSON);
       
        return $objJSON;

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