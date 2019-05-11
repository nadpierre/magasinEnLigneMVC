<?php

class GestionArticlesCommande extends GestionBD {

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
            $requete->closeCursor();
        }

    }
}