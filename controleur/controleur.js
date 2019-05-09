/**
 * Affiche l'inventaire
 */
$(document).ready(function(){
    $("#gabarit").load("vue/gabarit.html", function(){
        afficherInventaire(listerArticles, '', '');
        getTotalPanier();
    })
});

/**
 * -----------------------
 * INVENTAIRE
 * -----------------------
 */

/**
* Affiche l'élément HTML qui la contenir la liste des articles
* @param {function} callback - la fonction à appeler après avoir affiché le HTML
* @param {string} filtre - le critère de sélection
* @param {string} valeur - la valeur du critère de sélection
*/
function afficherInventaire(callback, filtre, valeur) {
    let modeleInventaire = new ModeleMagasin("affichage-articles");
    modeleInventaire.appliquerModele("", "milieu-page");
    callback(filtre, valeur);
}

/**
 * Liste chaque élément de l'inventaire
 * @param {string} filtre - le critère de sélection
 * @param {string} valeur - la valeur du critère de sélection
 */
function listerArticles(filtre, valeur) {
    
    //Afficher tous les articles
    let requete = new RequeteAjax("controleur/controleur.php?q=inventaire" +
        ((filtre != "" && valeur != "") ? "&" + filtre + "=" + valeur : ""));
    let modeleListeArticles = new ModeleMagasin("modele-liste-articles");
    let objJSON = {
        "requete": "inventaire"
    };
    requete.getJSON(objJSON,function (reponse) {
        modeleListeArticles.appliquerModele(reponse, "liste-articles");
    });
}


/**
 * Affiche un seul article
 * @param {string} noArticle - l'identifiant de l'article
 */
function afficherArticle(noArticle) {
    let requete = new RequeteAjax("controleur/controleur.php?q=inventaire&noArticle=" + noArticle);
    let modeleArticle = new ModeleMagasin("modele-article");
    requete.getJSON(reponse => { modeleArticle.appliquerModele(reponse, "milieu-page"); });
}


/**
 * -----------------------
 * PANIER D'ACHAT
 * -----------------------
 */

/**
 * Affiche le nombre total d'éléments dans le panier
 */
function getTotalPanier() {
    let requete = new RequeteAjax("controleur/controleur.php?q=panier&r=total");
    requete.getJSON(reponse => {
        $("#nombre-total").html(reponse);
    });
}

/**
 * Permet de choisir la quantité d'un article avant d'ajouter l'article dans le panier
 * @param {HTMLElement} bouton 
 */
function changerQuantite(bouton) {
    let valeur = parseInt($("#quantity").val());
    if (bouton.dataset.type == "minus" && valeur > 0) {
        valeur--;
    }
    else if (bouton.dataset.type == "plus" && valeur < 100) {
        valeur++;
    }
    $("#quantity").val(valeur);
}

/**
 * Ajoute un article au panier d'achat
 */
function ajouterAuPanier() {
    let messageErreur = $("#message-erreur");

    let objJSON = {
        "requete": "ajouter",
        "noArticle": $("#identifiant").val(),
        "description": $("#description").val(),
        "cheminImage": $("#cheminImage").val(),
        "prixUnitaire": d$("#prix").val(),
        "quantite": $("#quantity").val()
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(txtJSON, function (reponse) {
        let objJSON = reponse;
        messageErreur.addClass('alert');
        if (objJSON["statut"] === "succes") {
            getTotalPanier();
            messageErreur.removeClass('alert-danger');
            messageErreur.addClass('alert-success');
            messageErreur.css("color", "green");   
        }
        else if (objJSON["statut"] === "echec") {
            messageErreur.removeClass('alert-success');
            messageErreur.addClass('alert-danger');
            messageErreur.css("color", "red");
        }
        messageErreur.html(objJSON["message"]);
    });


}


/**
 * Afficher le sommaire du panier
 * @param {function} callback - la fonction à appeler après que le sommaire soit chargé
 */
function afficherSommaire() {
    let requete = new RequeteAjax("controleur/controleur.php?q=panier&r=sommaire");
    let modelePanier = new ModeleMagasin("modele-panier");
    requete.getJSON(reponse => {
        modelePanier.appliquerModele(reponse, "milieu-page");
        listerPanier();
    });

}

/**
 * Affiche tous les éléments du panier
 */
function listerPanier() {
    let requete = new RequeteAjax("controleur/controleur.php?q=panier&r=liste");
    let modeleListePanier = new ModeleMagasin("modele-liste-panier");
    requete.getJSON(function (reponse) {
        modeleListePanier.appliquerModele(reponse, "liste-panier");
    })
}

/**
 * Supprime un élément du panier
 */
function supprimerDuPanier(noArticle) {

    let objJSON = {
        "requete": "supprimer",
        "noArticle": noArticle
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(txtJSON, getTotalPanier);
    afficherSommaire();
}

/**
 * Modifier les quantités du panier
 */
function modifierPanier() {
    let messageErreur = $("#message-erreur");

    //Tableau des numéros d'article
    let liensNoArticle = $(".closed");
    let tabNoArticle = new Array();
    for (let i = 0; i < liensNoArticle.length; i++) {
        tabNoArticle.push(liensNoArticle[i].dataset.value);
    }
    //Tableau des quantités
    let champsQuantite = $(".quantite");
    let tabQuantite = new Array();
    for (let i = 0; i < champsQuantite.length; i++) {
        tabQuantite.push(champsQuantite[i].value);
    }

    let objJSON = {
        "requete": "modifier",
        "tabNoArticle": JSON.stringify(tabNoArticle),
        "tabQuantite": JSON.stringify(tabQuantite)
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(txtJSON, function(reponse) {
        let objJSON = reponse;
        if(objJSON["statut"] === "echec"){
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.css("color", "red");
            messageErreur.html(objJSON["message"]);
        }
        else {
            getTotalPanier();
            afficherSommaire();  
        }    
    });     
}

/**
 * -----------------------
 * CLIENT
 * -----------------------
 */


/**
  * Affiche le formulaire d'inscription
  */
function formulaireInscription() {
    let messageErreur = $("#message-erreur");
    let nbTotal = $("#nombre-total").text();
    if (nbTotal == "0") {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.html("Vous ne pouvez pas passer à la caisse si votre panier est vide."); 
    }
    else {
        let modeleInscription = new ModeleMagasin("modele-inscription");
        modeleInscription.appliquerModele('', "milieu-page");
    }
}

/**
 * Valide les données du formulaire
 */
function validerFormulaire() {
    //Message d'erreur
    messageErreur = $("#message-erreur");

    //Données du formulaire
    let nom = $("#lname").val();
    let prenom = $("#fname").val();
    let adresse1 = $("#address").val();
    let adresse2 = $("#address2").val();
    let adresse = adresse1 + (adresse2 !== "" ? " " + adresse2 : "");
    let ville = $("#towncity").val();
    let province = $("#province").val();
    let codePostal = $("#zippostalcode").val();
    let noTel = $("#phone").val();
    let courriel = $("#email").val();
    let motDePasse = $("#mot-de-passe").val();
    let confMotDePasse = $("#conf-mot-de-passe").val();

    //Expression régulières
    const LETTRES_SEULEMENT = /[a-zA-ZáàäâéèëêíìïîóòöôúùüûçñÁÀÄÂÉÈËÊÍÌÏÎÓÒÖÔÚÙÜÛÑÇ\'\-]+/;
    const CODE_POSTAL = /^[A-Z][0-9][A-Z] ?[0-9][A-Z][0-9]$/;
    const NO_TEL = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
    const COURRIEL = /[^@]+@[^\.]+\..+/g;

    //Vérifier si le nom, le prénom et la ville ont seulement des lettres
    if (!nom.match(LETTRES_SEULEMENT) || !prenom.match(LETTRES_SEULEMENT) ||
        !ville.match(LETTRES_SEULEMENT)) {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.html("Ce champ ne doit contenir que des lettres.");
        return;
    }

    //Vérifier si le code postal est valide
    if (!codePostal.match(CODE_POSTAL)) {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.hml("Format de code postal invalide.");
        return;
    }

    //Vérifier si le numéro de téléphone est valide
    if (!noTel.match(NO_TEL)) {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.html("Format de numéro de téléphone invalide.");
        return;
    }

    //Vérifier si le courriel est valide
    if (!courriel.match(COURRIEL)) {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.html("Format de courriel invalide.");
        return;
    }

    //Vérifier que les deux mots de passes sont identiques
    if (motDePasse !== confMotDePasse) {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.html("Les deux mots de passe doivent être identiques.");
        return;
    }

    let client = {
        "nomClient": nom,
        "prenomClient": prenom,
        "adresse": adresse,
        "ville": ville,
        "province": province,
        "codePostal": codePostal,
        "noTel": noTel,
        "courriel": courriel,
        "motDePasse": motDePasse
    }

    let objJSON = {
        "requete": "inscription",
        "client": JSON.stringify(client)
    };

    let txtJSON = JSON.stringify(objJSON);
    ajouterClient(txtJSON);
}


/**
 * Ajoute le client à la base de données
 * @param {string} txtJSON - les données à envoyer
 */
function ajouterClient(txtJSON) {
    let messageErreur = $("#message-erreur");
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(txtJSON, function(reponse) {
        let objJSON = reponse;
        if (objJSON["statut"] === "succes") {
            afficherCaisse(JSON.stringify(objJSON["client"]));
        }
        else if (objJSON["statut"] === "echec") {
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(objJSON["message"]);
        }
    });
}


/**
* Affiche le formulaire de connexion
*/
function formulaireConnexion() {
    let modeleConnexion = new ModeleMagasin("modele-connexion");
    modeleConnexion.appliquerModele('', "milieu-page");
}


/**
 * Permet à un client existant de se connecter
 */
function seConnecter() {
    let messageErreur = $("#message-erreur");
    let courriel = $("#pseudo").val(); 
    let motDePasse = $("#mot-de-passe").val();

    let objJSON = {
        "requete": "connexion",
        "courriel": courriel,
        "motDePasse": motDePasse
    }

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(txtJSON, function (reponse) {
        let objJSON = reponse;
        if (objJSON["statut"] == "succes") {
            afficherCaisse(JSON.stringify(objJSON["membre"]));
        }
        else if (objJSON["statut"] == "echec") {

            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(objJSON["message"]);
        }
    });

}

/**
 * Déconnexion du client actif
 */

function seDeconnecter(){
    let objJSON = {
        "requete": "deconnexion",
        "courriel": courriel
    };

    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(objJSON, function(reponse){
        let objJSON = reponse;
        if (objJSON["statut"] == "succes") {
            afficherInventaire(listerArticles);
        }
        else if (objJSON["statut"] == "echec") {

            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(objJSON["message"]);
        }
    });
}

/**
* Supprime le compte du client
*/
function supprimerCompte(){}


/**
 * -----------------------
 * ADMINISTRATEUR
 * -----------------------
 */

 /**
* Ajout d'un article
*/

/**
* Modifier un article
*/

/**
* Supprimer d'un article
*/



/**
 * -----------------------
 * COMMANDE
 * -----------------------
 */

/**
* Affiche les informations du client et la facture
*/
function afficherCaisse(reponse) {
    //Informations du client
    let modeleCaisse = new ModeleMagasin("modele-caisse");
    modeleCaisse.appliquerModele(reponse, "milieu-page");

    //Facture
    let requete = new RequeteAjax("controleur/controleur.php?q=panier&r=sommaire");
    let modeleFacture = new ModeleMagasin("modele-facture");
    requete.getJSON(donnees => {
        modeleFacture.appliquerModele(donnees, "facture");
        listerFacture();
        afficherPaypal();
    });

}

/**
 * Liste chaque élément de la facture
 */
function listerFacture() {
    let requete = new RequeteAjax("controleur/controleur.php?q=panier&r=liste");
    let modeleDetailsFacture = new ModeleMagasin("modele-details-facture");
    requete.getJSON(donnees => {
        modeleDetailsFacture.appliquerModele(donnees, "details-facture");
    });
}


/**
 * Affiche le bouton Paypal et affiche la fenêtre de paypal
 * lorsqu'on clique dessus
 */
function afficherPaypal() {
    paypal.Buttons({
        locale: 'fr_CA',
        style: {
            layout: 'vertical',
            color: 'silver',
            shape: 'pill',
            label: 'paypal'
        },
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: $("#montant-total").text().replace(' $', '').replace(',', '.')
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                placerCommande(data.orderID);
            });
        }
    }).render('#paypal-button-container');
}


/**
 * Crée une commande avec les articles
 * @param {string} paypalOrderId - le numéro de confirmation de Paypal
 */
function placerCommande(paypalOrderId) {

    //Tableau des numéros d'article
    let numeros = $("#numeros");
    let tabNoArticle = new Array();
    for (let i = 0; i < numeros.length; i++) {
        tabNoArticle.push(numeros[i].value);
    }
    //Tableau des quantités
    let quantites = $("#quantites");
    let tabQuantite = new Array();
    for (let i = 0; i < quantites.length; i++) {
        tabQuantite.push(quantites[i].value);
    }

    let objJSON = {
        "requete": "commande",
        "noClient": $("#numero-client").val(),
        "paypalOrderId": paypalOrderId,
        "tabNoArticle": JSON.stringify(tabNoArticle),
        "tabQuantite": JSON.stringify(tabQuantite)
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerDonnees(txtJSON, (donnees) => {
        commandeTerminee();
    });
}

/**
 * Affiche que la commande est bel et bien complétée,
 * le numéro de confirmation et le courriel du client
 */
function commandeTerminee() {
    let requete = new RequeteAjax("controleur/controleur.php?q=commande");
    let modeleComplete = new ModeleMagasin("modele-commande-complete");
    requete.getJSON(reponse => {
        modeleComplete.appliquerModele(reponse, "milieu-page");
    });
    getTotalPanier();
}


