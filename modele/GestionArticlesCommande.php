<?php

class GestionArticlesCommande extends GestionBD {

    /**
     * Ajoute les articles dans le panier dans la base de données
     * @param {int} $noCommande - le numéro de la commande
     * @param {Panier} $panier - le panier d'achat
     * @return void
     */
    public function placerCommande($noCommande, Panier $panier) {
        
        $tabNoArticle = $panier->getTabNoArticle();
        $tabQuantite = $panier->getTabQuantite();

        for($i = 0; $i < count($tabNoArticle); $i++) {
            
            $article = new ArticleEnCommande(
                array(
                    "noCommande" => (int) $noCommande,
                    "noArticle" => (int) $tabNoArticle[$i],
                    "quantite" => (int) $tabQuantite[$i]
                )
            );

            $requete = $this->bdd->prepare(
                'INSERT INTO article_en_commande
                VALUES (:noCommande, :noArticle, :quantite)'
            );

            $requete->bindValue(':noCommande', $article->getNoCommande(), PDO::PARAM_INT);
            $requete->bindValue(':noArticle', $article->getNoArticle(), PDO::PARAM_INT);
            $requete->bindValue(':quantite', $article->getQuantite(), PDO::PARAM_INT);
            $requete->execute();
            
        }

    }


    /**
     * Retourne un JSON avec les articles commandés
     * @param {int} $noCommande - le numéro de la commande
     * @return array
     */
    public function getArticlesCommande($noCommande){
        $listeArticles = array();
        
        $requete = $this->bdd->prepare(
            'SELECT * FROM article_en_commande WHERE noCommande = ?'
        );
        $requete->bindValue(1, (int) $noCommande, PDO::PARAM_INT);
        $requete->execute();
        while ($donnees = $requete->fetch()) {
            $article = new ArticleEnCommande($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        return $listeArticles;
    }

}
