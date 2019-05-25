/**
 * Représente une classe de type ModeleMagasin
 * Son rôle est d'identifier les différents modèles et
 * d'y insérer les bonnes données
 * Copié de l'exemple de projet fourni par l'enseignant
 */
class ModeleMagasin {

    /**
     * CONSTRUCTEUR
     * @param {string} idModele 
     */
    constructor(idModele) {
        this.modele = document.getElementById(idModele).innerHTML;
    }

    /**
     * Insère les bonnes données au bon endroit dans le modèle
     * @param {string} txtJSON - la réponse du serveur
     * @param {string} idElement - le modèle à afficher
     */
    appliquerModele(objJSON, idElement) {
        let codeHTML = "";
        
        if(objJSON != ""){
           
            for(let i = 0; i < objJSON.length; i++) {
                let modeleTemp = this.modele;
                //Si on affiche la liste d'articles, on ajoute un "retour à la ligne" après 4 articles
                if((idElement == "liste-articles") && (i % 4 == 0)){
                    codeHTML += "<div class='w-100'></div>";
                }
                for(let a in objJSON[i]) {
                    modeleTemp = modeleTemp.replace(new RegExp("\{" + a + "\}", "g"), objJSON[i][a]);
                }
                codeHTML += modeleTemp;
            }     
        }
        else {
            codeHTML = this.modele;
        }
       
        document.getElementById(idElement).innerHTML = codeHTML;
    }

}