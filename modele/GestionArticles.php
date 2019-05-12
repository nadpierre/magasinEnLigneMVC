<?php

/**
 * Représente un objet de type GestionArticles
 * Son rôle est de gérer les articles dans la base de données MySQL
 * Hérite de la classe GestionBD
 */
class GestionArticles extends GestionBD {

    /**
     * Retourne la liste de l'inventaire
     * @return string -  un JSON du tableau d'articles
     */
    public function getListeArticles() {
        $listeArticles = array();

        $requete = $this->bdd->query('SELECT * FROM article ORDER BY libelle');
       
        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        $requete->closeCursor();

        if(count($listeArticles) == 0) {
            return "AUCUN ARTICLE SÉLECTIONNÉ";
        }
        
        return json_encode($listeArticles);
          
    }

    /**
     * Retourne une liste d'articles ayant la même catégorie
     * @param {string} $categorie - la catégorie de l'article
     * @return string - un JSON du tableau d'articles
     */
    public function listerParCategorie($categorie){
        $listeArticles = array();

        $requete = $this->bdd->prepare('SELECT * FROM article WHERE categorie = ? ORDER BY libelle');
        $requete->bindValue(1, $categorie, PDO::PARAM_STR);
        $requete->execute();

        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        $requete->closeCursor();

        if(count($listeArticles) == 0) {
            return "AUCUN ARTICLE SÉLECTIONNÉ";
        }
        
        return json_encode($listeArticles);
    }

    /**
     * Retourne une liste d'article contant le même mot dans leur libelle
     * @param {string} $mot - le mot cherché
     * @return string - un JSON du tableau d'articles
     */
    public function listerParMot($mot){

        $mot = strtolower($mot);
        $listeArticles = array();

        $requete = $this->bdd->prepare(
            'SELECT * FROM article
            WHERE LOWER(libelle) LIKE :mot
            ORDER BY libelle'
        );

        $requete->bindValue(':mot', '%'.$mot.'%', PDO::PARAM_STR);
        $requete->execute();
        
        while ($donnees = $requete->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($donnees);
            array_push($listeArticles, $article->getTableau());
        }

        $requete->closeCursor();

        if(count($listeArticles) == 0) {
            return "AUCUN ARTICLE SÉLECTIONNÉ";
        }
        
        return json_encode($listeArticles);

    }

    /**
     * Calcule le nombre total d'articles
     * @return int
     */
    private function getNbArticles(){
        $requete = $this->bdd->query('SELECT COUNT(*) FROM article');
        $somme = $requete->fetch(PDO::FETCH_NUM);
        $requete->closeCursor();
        return (int) $somme[0];
    }

    /**
     * Retourne un seul article
     * @param {int} $id - l'identifiant de l'article
     * @return string - un JSON de l'article
     */
    public function getArticle($noArticle) {
        $noArticle = (int) $noArticle;
       
        $requete = $this->bdd->prepare('SELECT * FROM article WHERE noArticle = ?');
        $requete->bindValue(1, $noArticle, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        $requete->closeCursor();

        $article = new Article($donnees);
        return '[' . $article . ']';
    }

    /**
     * Calcule la quantité en stock d'un article
     * @param $noArticle - l'identifiant de l'article
     * @return int
     */
    private function getQteStock($noArticle){
        $noArticle = (int) $noArticle;
      
        $requete = $this->bdd->prepare('SELECT quantiteEnStock FROM article WHERE noArticle = ?');
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
       
        $requete = $this->bdd->prepare('SELECT quantiteDansPanier FROM article WHERE noArticle = ?');
        $requete->bindValue(1, $noArticle, PDO::PARAM_INT);
        $requete->execute();
        $donnees = $requete->fetch(PDO::FETCH_NUM);
        $requete->closeCursor();
        
        return (int) $donnees[0];
    }

    /**
     * Ajoute un article dans l'inventaire
     * @param {Article} - une instance de l'objet Article
     * @return void
     */
    public function ajouterArticle(Article $article){
        $requete = $this->bdd->prepare(
            'INSERT INTO article (
                categorie, 
                libelle, 
                cheminImage, 
                prixUnitaire, 
                quantiteEnStock, 
                quantiteDansPanier
            )
            VALUES (
                :categorie,
                :libelle,
                :cheminImage,
                :prixUnitaire,
                :quantiteEnStock,
                :quantiteDansPanier
            )
            '
        );

        $requete->bindValue(':categorie', $article->getCategorie(), PDO::PARAM_STR);
        $requete->bindValue(':libelle', $article->getLibelle(), PDO::PARAM_STR);
        $requete->bindValue(':cheminImage', $article->getCheminImage(), PDO::PARAM_STR);
        $requete->bindValue(':prixUnitaire', $article->getPrixUnitaire(), PDO::PARAM_STR);
        $requete->bindValue(':quantiteEnStock', $article->getQuantiteEnStock(), PDO::PARAM_INT);
        $requete->bindValue(':quantiteDansPanier', $article->getQuantiteDansPanier(), PDO::PARAM_INT);

        $requete->execute();
        $requete->closeCursor();
    }

    /**
     * Modifie les informations d'un article existant
     * @param {Article} - une instance de l'objet Article
     * @return void
     */
    public function modifierArticle(Article $article) {
        $requete = $this->bdd->prepare(
            'UPDATE article
            SET  
                categorie = :categorie, 
                libelle = :libelle,
                cheminImage = :cheminImage, 
                prixUnitaire = :prixUnitaire,
                quantiteEnStock = :quantiteEnStock,
                quantiteDansPanier = :quantiteDansPanier
            WHERE noArticle = :noArticle
            '
        );

        $requete->bindValue(':categorie', $article->getCategorie(), PDO::PARAM_STR);
        $requete->bindValue(':libelle', $article->getLibelle(), PDO::PARAM_STR);
        $requete->bindValue(':cheminImage', $article->getCheminImage(), PDO::PARAM_STR);
        $requete->bindValue(':prixUnitaire', $article->getPrixUnitaire(), PDO::PARAM_STR);
        $requete->bindValue(':quantiteEnStock', $article->getQuantiteEnStock(), PDO::PARAM_INT);
        $requete->bindValue(':quantiteDansPanier', $article->getQuantiteDansPanier(), PDO::PARAM_INT);
        $requete->bindValue(':noArticle', $article->getNoArticle(), PDO::PARAM_INT);

        $requete->execute();
        $requete->closeCursor();
    }

    /**
     * Supprime un article de l'inventaire
     * @param {Article} - une instance de l'objet Article
     * @return void
     */
    public function supprimerArticle(Article $article) {
        $requete = $this->bdd->prepare('DELETE FROM article WHERE noArticle = ?');
        $requete->bindValue(1, $article->getNoArticle(), PDO::PARAM_INT);
        $requete->execute();
        $requete->closeCursor();
    }


    /**
     * Réserve un article dans l'inventaire
     * @param {int} $noArticle - l'identifiant de l'article
     * @param {int} $quantite - la quantité demandée
     * @return void
     * @throws Exeption si l'article n'est pas disponible
     */
    public function reserverArticle($noArticle, $quantite) {
        $noArticle = (int) $noArticle;
        $quantite = (int) $quantite;

        if($quantite > $this->getQteStock($noArticle)){
            throw new Exception("Il n'y a pas assez d'articles en stock. Veuillez choisir une plus petite quantité.");
        }
        else{
            $requete = $this->bdd->prepare(
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
     * @param {string} $libelle - le libelle de l'article
     * @return void
     */
    public function supprimerDuPanier($noArticle){
        $noArticle = (int) $noArticle;

        $requete = $this->bdd->prepare(
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
     * @return void
     */
    public function modifierPanier($tabNoArticle, $tabQuantite){
        
        for($i = 0; $i < count($tabNoArticle); $i++){
            $qteStock = $this->getQteStock((int)$tabNoArticle[$i]);
            $qtePanier = $this->getQteDansPanier((int)$tabNoArticle[$i]);
            $somme = $qteStock + $qtePanier;
            if((int) $tabQuantite[$i] > $somme){
                throw new Exception("La quantité est trop élevée pour un ou plusieurs articles.");
            }
            
            $requete = $this->bdd->prepare(
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
     * @return void
     */
    public function effacerQtePanierTous(){
        $requete = $this->bdd->query(
            'UPDATE article
            SET quantiteDansPanier = 0'
        );
        $requete->closeCursor();  
    }


    /**
     * Vide le panier d'achat : rétablit la quantité en stock
     * et annule la quantité dans le panier
     * @return void
     */
    public function viderPanier(){
          
       $nbArticles = $this->getNbArticles();
       
       for($i = 1; $i <= $nbArticles; $i++){
           $requete = $this->bdd->prepare(
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