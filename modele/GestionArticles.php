<?php

/**
 * Représente un objet de type GestionArticles
 * Son rôle est de gérer les articles dans la base de données MySQL
 * Hérite de la classe GestionBD
 */
class GestionArticles extends GestionBD {

    /**
     * Retourne la liste de l'inventaire
     * @return {array} un tableau associatif contenant les articles
     */
    public function getListeArticles() {
        $listeArticles = array();

        $requete = $this->_bdd->query('SELECT * FROM article ORDER BY `description`');
       
        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        $requete->closeCursor();

        return $listeArticles;
    }

    /**
     * Retourne une liste d'articles ayant la même catégorie
     * @param {string} $categorie - la catégorie de l'article
     * @return array - un tableau associatif contenant les articles
     */
    public function listerParCategorie($categorie){

        $listeArticles = array();

        $requete = $this->_bdd->prepare('SELECT * FROM article WHERE categorie = ? ORDER BY `description`');
        $requete->bindValue(1, $categorie, PDO::PARAM_STR);
        $requete->execute();

        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        $requete->closeCursor();

        return $listeArticles;
    }

    /**
     * Retourne une liste d'article contant le même mot dans leur description
     * @param {string} $mot - le mot cherché
     * @return array - un tableau associatif contenant des articles
     */
    public function listerParMot($mot){
        //S'assurer que la paramètre ne contient pas du code SQL et/ou HTML
        $mot = $this->filtrerParametre($mot);

        $mot = strtolower($mot);
        $listeArticles = array();

        $requete = $this->_bdd->query("SELECT * FROM article WHERE LOWER(description) LIKE '%$mot%' ORDER BY `description`");
        
        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        $requete->closeCursor();

        return $listeArticles;

    }

    /**
     * Calcule le nombre total d'articles
     */
    function getNbArticles(){
        $requete = $this->_bdd->query('SELECT COUNT(*) FROM article');
        $somme = $requete->fetch(PDO::FETCH_NUM);
        $requete->closeCursor();
        return (int) $somme[0];
    }

    /**
     * Retourne un seul article
     * @param {int} $id - l'identifiant de l'article
     * @return array - un tableau associatif de l'instance d'un objet Article
     */
    public function getArticle($noArticle) {
        $listeArticles = array();
        $noArticle = (int) $noArticle;
       
        $requete = $this->_bdd->prepare('SELECT * FROM article WHERE noArticle = ?');
        $requete->bindValue(1, $noArticle, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();

        $article = new Article($donnees);
        array_push($listeArticles, $article->getTableau());
        return $listeArticles;
    }

    /**
     * Calcule la quantité en stock d'un article
     * @param $noArticle - l'identifiant de l'article
     * @return int
     */
    private function getQteStock($noArticle){
        $noArticle = (int) $noArticle;
      
        $requete = $this->_bdd->prepare('SELECT quantiteEnStock FROM article WHERE noArticle = ?');
        $requete->bindValue(1, $noArticle, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_NUM);
        $requete->closeCursor();
        
        return (int) $donnees[0];
    }

    /**
     * Calcule la quantité dans le panier d'un article
     * @param $noArticle - l'identifiant de l'article
     * @return int
     */
    private function getQteDansPanier($noArticle){
        $noArticle = (int) $noArticle;
       
        $requete = $this->_bdd->prepare('SELECT quantiteDansPanier FROM article WHERE noArticle = ?');
        $requete->bindValue(1, $noArticle, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_NUM);
        $requete->closeCursor();
        
        return (int) $donnees[0];
    }


    /**
     * Réserve un article dans l'inventaire
     * @param {int} $noArticle - l'identifiant de l'article
     * @param {int} $quantite - la quantité demandée
     */
    public function reserverArticle($noArticle, $quantite) {
        $noArticle = (int) $noArticle;
        $quantite = (int) $quantite;

        if($quantite > $this->getQteStock($noArticle)){
            throw new Exception("Il n'y a pas assez d'articles en stock. Veuillez choisir une plus petite quantité.");
        }
        else{
            $requete = $this->_bdd->prepare(
                'UPDATE article
                SET 
                    quantiteEnStock = quantiteEnStock - ?,
                    quantiteDansPanier = quantiteDansPanier + ?   
                WHERE noArticle = ?'
            );
            $requete->bindValue(1, $quantite, PDO::PARAM_INT);
            $requete->bindValue(2, $quantite, PDO::PARAM_INT);
            $requete->bindValue(3, $noArticle, PDO::PARAM_INT);
            $requete->execute();
            $requete->closeCursor();
        }

    }


    /**
     * Supprime un élément du panier
     * @param {string} $description - la description de l'article
     */
    public function supprimerDuPanier($noArticle){
        $noArticle = (int) $noArticle;

        $requete = $this->_bdd->prepare(
            'UPDATE article
            SET 
                quantiteEnStock = quantiteEnStock + quantiteDansPanier,
                quantiteDansPanier = 0
            WHERE noArticle = ?'
        );
        $requete->bindValue(1, $noArticle, PDO::PARAM_INT);
        $requete->execute();
        $requete->closeCursor();
    }


    /**
     * Modifie la quantité dans le panier
     * @param {array} $tabNoArticle - tous les numéros d'article
     * @param {array} $tabQteDansPanier - toutes les quantités
     */
    public function modifierPanier($tabNoArticle, $tabQuantite){
        
        for($i = 0; $i < count($tabNoArticle); $i++){
            $qteStock = $this->getQteStock((int)$tabNoArticle[$i]);
            $qtePanier = $this->getQteDansPanier((int)$tabNoArticle[$i]);
            $somme = $qteStock + $qtePanier;
            if((int) $tabQuantite[$i] > $somme){
                throw new Exception("La quantité est trop élevée pour un ou plusieurs articles.");
            }
            
            $requete = $this->_bdd->prepare(
                'UPDATE article
                SET 
                    quantiteEnStock = :somme - :quantite,
                    quantiteDansPanier = :quantite2
                WHERE noArticle = :noArticle'
            );

            $requete->bindValue(':somme', (int) $somme, PDO::PARAM_INT);
            $requete->bindValue(':quantite', (int) $tabQuantite[$i], PDO::PARAM_INT);
            $requete->bindValue(':quantite2', (int) $tabQuantite[$i], PDO::PARAM_INT);
            $requete->bindValue(':noArticle', (int) $tabNoArticle[$i], PDO::PARAM_INT);
            $requete->execute();
            $requete->closeCursor();  
        }
       
    }

    /**
     * Lorsqu'une commande est placée, annule la quantité dans le panier
     */
    public function effacerQtePanierTous(){
        $requete = $this->_bdd->query(
            'UPDATE article
            SET quantiteDansPanier = 0'
        );
        $requete->closeCursor();  
    }


    /**
     * Détruit le panier d'achat : rétablit la quantité en stock
     * et annule la quantité dans le panier
     */
    public function detruirePanier(){
          
       $nbArticles = $this->getNbArticles();
       
       for($i = 1; $i <= $nbArticles; $i++){
           $requete = $this->_bdd->prepare(
               'UPDATE article
               SET 
                    quantiteEnStock = quantiteEnStock + :quantite,
                    quantiteDansPanier = 0
               WHERE noArticle = :noArticle'
            );
            $requete->bindValue(':quantite', $this->getQteDansPanier($i), PDO::PARAM_INT);
            $requete->bindValue(':noArticle', $i, PDO::PARAM_INT);
            $requete->execute();
            $requete->closeCursor();
       }
    }

   

}

?>