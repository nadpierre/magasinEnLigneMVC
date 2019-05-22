/**
 * Représente une classe de type RequeteAjax
 * son rôle est de faire des requêtes HTTP au serveur
 * Copié de l'exemple de projet fourni par l'enseignant
 */
class RequeteAjax {

    /**
     * CONSTRUCTEUR
     * @param {string} url - l'adresse du fichier PHP
     */
    constructor(url){
        this.url = url;
    }

    /**
     * Effectue une requête POST au serveur
     * @param {function} callback - la fonction à appeler après 
     * avoir reçu la réponse
     */
    getJSON(objJson, callback) {
        $.ajax({
            method : "POST",
            contentType: "application/json",
            url : this.url,
            data : JSON.stringify(objJson)
        })
        .done(function(reponse){
            callback(reponse);
            console.log(reponse);
        })
    }

    envoyerArticle(donnees, callback){
    
        $.ajax({
           method : "POST",
           url : this.url,
           enctype: 'multipart/form-data',
           processData: false,
           contentType: false,
           cache: false,
           data : donnees
       })
       .done(function(reponse){
           callback(reponse);
           console.log(reponse);
       })
   
   }
}