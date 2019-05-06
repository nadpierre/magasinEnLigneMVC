<?php

class GestionArticlesCommande extends GestionBD {

    public function ajouterArticles($noCommande, $tabNoArticle, $tabQuantite) {
        
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
}