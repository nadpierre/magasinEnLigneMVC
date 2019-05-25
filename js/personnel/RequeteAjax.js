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
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                callback(this.responseText);
            }
        };
        xhttp.open("GET", this.url, true);
        xhttp.send();
    }

    /**
     * Effectue une requête POST au serveur
     * @param {string} txtJSON - les données à envoyer
     * @param {function} callback - la fonction à appeler après 
     * avoir reçu la réponse
     */
    envoyerDonnees(txtJSON, callback) {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if(this.readyState == 4 && this.status == 200) {
                callback(this.responseText);
            }
        };
        xhttp.open("POST", this.url, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("x=" + txtJSON);
    }
}