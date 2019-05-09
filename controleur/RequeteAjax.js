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
     * Effectue une requête GET au serveur
     * @param {function} callback - la fonction à appeler après 
     * avoir reçu la réponse
     */
    getJSON(callback) {
        $.get(this.url, function(response, status){
            if(status == "success"){
                callback(response);
            }
        });
    }

    /**
     * Effectue une requête POST au serveur
     * @param {string} objJson - les données à envoyer
     * @param {function} callback - la fonction à appeler après 
     * avoir reçu la réponse
     */
    envoyerDonnees(objJson, callback) {
        $.post(this.url, objJson, function(response, status){
            if(status == "success"){
                callback(response);
            }
        })
    }
}