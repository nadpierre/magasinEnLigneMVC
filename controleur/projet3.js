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

    /* $.ajax({
        url: 'php/main.php',
        type : "POST",
        data : "q=inventaire",
        sucess: function(code_html, status){
            let modeleListeArticle = new ModeleMagasin("modele-liste-articles");
            modeleListeArticle.appliquerModele(code_html, "liste-articles");
            
        }
    }) */
    
    //Afficher tous les articles
    let requete = new RequeteAjax("php/main.php?q=inventaire" +
        ((filtre != "" && valeur != "") ? "&" + filtre + "=" + valeur : ""));
    let modeleListeArticles = new ModeleMagasin("modele-liste-articles");
    requete.getJSON(function (reponse) {
        if (JSON.parse(reponse).length == 0) {
            document.getElementById("liste-articles").innerHTML = "AUCUN ARTICLE SÉLECTIONNÉ."
        }
        else {
            modeleListeArticles.appliquerModele(reponse, "liste-articles");
        }
    });

}


/**
 * Affiche un seul article
 * @param {string} noArticle - l'identifiant de l'article
 */
function afficherArticle(noArticle) {
    let requete = new RequeteAjax("php/main.php?q=inventaire&noArticle=" + noArticle);
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
function getTotalPanier(callback) {
    let requete = new RequeteAjax("php/main.php?q=panier&r=total");
    requete.getJSON(reponse => {
        document.getElementById("nombre-total").innerHTML = JSON.parse(reponse);
        callback();
    });
}

/**
 * Permet de choisir la quantité d'un article avant d'ajouter l'article dans le panier
 * @param {HTMLElement} bouton 
 */
function changerQuantite(bouton) {
    let valeur = parseInt(document.getElementById("quantity").value);
    if (bouton.dataset.type == "minus" && valeur > 0) {
        valeur--;
    }
    else if (bouton.dataset.type == "plus" && valeur < 100) {
        valeur++;
    }
    document.getElementById("quantity").value = valeur;
}

/**
 * Ajoute un article au panier d'achat
 */
function ajouterAuPanier() {
    let messageErreur = document.getElementById("message-erreur");

    let objJSON = {
        "requete": "ajouter",
        "noArticle": document.getElementById("identifiant").value,
        "description": document.getElementById("description").value,
        "cheminImage": document.getElementById("cheminImage").value,
        "prixUnitaire": document.getElementById("prix").value,
        "quantite": document.getElementById("quantity").value
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("php/main.php");
    requete.envoyerDonnees(txtJSON, function (reponse) {
        let objJSON = JSON.parse(reponse);
        messageErreur.classList.add('alert');
        if (objJSON["statut"] === "succes") {
            getTotalPanier();
            messageErreur.classList.remove('alert-danger');
            messageErreur.classList.add('alert-success');
            messageErreur.style.color = "green";   
        }
        else if (objJSON["statut"] === "echec") {
            messageErreur.classList.remove('alert-success');
            messageErreur.classList.add('alert-danger');
            messageErreur.style.color = "red";
        }
        messageErreur.innerHTML = objJSON["message"];
    });


}


/**
 * Afficher le sommaire du panier
 * @param {function} callback - la fonction à appeler après que le sommaire soit chargé
 */
function afficherSommaire() {
    let requete = new RequeteAjax("php/main.php?q=panier&r=sommaire");
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
    let requete = new RequeteAjax("php/main.php?q=panier&r=liste");
    let modeleListePanier = new ModeleMagasin("modele-liste-panier");
    requete.getJSON(function (reponse) {
        if (JSON.parse(reponse).length == 0) {
            document.getElementById("liste-panier").innerHTML = "PANIER VIDE."
        }
        else {
            modeleListePanier.appliquerModele(reponse, "liste-panier");
        }
    })
}

/**
 * Supprime un élément du panier
 */
function supprimerDuPanier() {

    let idBouton = event.target.getAttribute("id");
    let noArticle = document.getElementById(idBouton).dataset.value;

    let objJSON = {
        "requete": "supprimer",
        "noArticle": noArticle
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("php/main.php");
    requete.envoyerDonnees(txtJSON, getTotalPanier);
    afficherSommaire();
}

/**
 * Modifier les quantités du panier
 */
function modifierPanier() {
    let messageErreur = document.getElementById("message-erreur");

    //Tableau des numéros d'article
    let liensNoArticle = document.getElementsByClassName("closed");
    let tabNoArticle = new Array();
    for (let i = 0; i < liensNoArticle.length; i++) {
        tabNoArticle.push(liensNoArticle[i].dataset.value);
    }
    //Tableau des quantités
    let champsQuantite = document.getElementsByClassName("quantite");
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
    let requete = new RequeteAjax("php/main.php");
    requete.envoyerDonnees(txtJSON, function(reponse) {
        let objJSON = JSON.parse(reponse);
        if(objJSON["statut"] === "echec"){
            messageErreur.classList.add('alert');
            messageErreur.classList.add('alert-danger');
            messageErreur.style.color = "red";
            messageErreur.innerHTML = objJSON["message"];
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
 * Cacher ou afficher l'en tête et le pied de page
 * @param {string} etat 
 */
function enTetePiedPage(etat) {
    document.querySelector(".colorlib-nav").style.visibility = etat;
    document.getElementById("colorlib-footer").style.visibility = etat;
}

/**
  * Affiche le formulaire d'inscription
  */
function formulaireInscription() {
    let messageErreur = document.getElementById("message-erreur");
    let nbTotal = document.getElementById("nombre-total").innerText;
    if (nbTotal == "0") {
        messageErreur.classList.add('alert');
        messageErreur.classList.add('alert-danger');
        messageErreur.innerHTML = "Vous ne pouvez pas passer à la caisse si votre panier est vide.";
    }
    else {
        enTetePiedPage("hidden");
        let modeleInscription = new ModeleMagasin("modele-inscription");
        modeleInscription.appliquerModele('', "milieu-page");
    }
}

/**
 * Valide les données du formulaire
 */
function validerFormulaire() {
    //Message d'erreur
    messageErreur = document.getElementById("message-erreur");

    //Données du formulaire
    let nom = document.getElementById("lname").value;
    let prenom = document.getElementById("fname").value;
    let adresse1 = document.getElementById("address").value;
    let adresse2 = document.getElementById("address2").value;
    let adresse = adresse1 + (adresse2 !== "" ? " " + adresse2 : "");
    let ville = document.getElementById("towncity").value;
    let province = document.getElementById("province").value;
    let codePostal = document.getElementById("zippostalcode").value;
    let noTel = document.getElementById("phone").value;
    let courriel = document.getElementById("email").value;
    let pseudo = document.getElementById("pseudo").value;
    let motDePasse = document.getElementById("mot-de-passe").value;
    let confMotDePasse = document.getElementById("conf-mot-de-passe").value;

    //Expression régulières
    const LETTRES_SEULEMENT = /[a-zA-ZáàäâéèëêíìïîóòöôúùüûçñÁÀÄÂÉÈËÊÍÌÏÎÓÒÖÔÚÙÜÛÑÇ\'\-]+/;
    const CODE_POSTAL = /^[A-Z][0-9][A-Z] ?[0-9][A-Z][0-9]$/;
    const NO_TEL = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
    const COURRIEL = /[^@]+@[^\.]+\..+/g;

    //Vérifier si le nom, le prénom et la ville ont seulement des lettres
    if (!nom.match(LETTRES_SEULEMENT) || !prenom.match(LETTRES_SEULEMENT) ||
        !ville.match(LETTRES_SEULEMENT)) {
        messageErreur.classList.add('alert');
        messageErreur.classList.add('alert-danger');
        messageErreur.innerHTML = "Ce champ ne doit contenir que des lettres.";
        return;
    }

    //Vérifier si le code postal est valide
    if (!codePostal.match(CODE_POSTAL)) {
        messageErreur.classList.add('alert');
        messageErreur.classList.add('alert-danger');
        messageErreur.innerHTML = "Format de code postal invalide.";
        return;
    }

    //Vérifier si le numéro de téléphone est valide
    if (!noTel.match(NO_TEL)) {
        messageErreur.classList.add('alert');
        messageErreur.classList.add('alert-danger');
        messageErreur.innerHTML = "Format de numéro de téléphone invalide.";
        return;
    }

    //Vérifier si le courriel est valide
    if (!courriel.match(COURRIEL)) {
        messageErreur.classList.add('alert');
        messageErreur.classList.add('alert-danger');
        messageErreur.innerHTML = "Format de courriel invalide.";
        return;
    }

    //Vérifier que les deux mots de passes sont identiques
    if (motDePasse !== confMotDePasse) {
        messageErreur.classList.add('alert');
        messageErreur.classList.add('alert-danger');
        messageErreur.innerHTML = "Les deux mots de passe doivent être identiques.";
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
        "pseudo": pseudo,
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
    let messageErreur = document.getElementById("message-erreur");
    let requete = new RequeteAjax("php/main.php");
    requete.envoyerDonnees(txtJSON, function(reponse) {
        let objJSON = JSON.parse(reponse);
        if (objJSON["statut"] === "succes") {
            afficherCaisse(JSON.stringify(objJSON["client"]));
        }
        else if (objJSON["statut"] === "echec") {
            messageErreur.classList.add('alert');
            messageErreur.classList.add('alert-danger');
            messageErreur.innerHTML = objJSON["message"];
        }
    });
}


/**
* Affiche le formulaire de connexion
*/
function formulaireConnexion() {
    enTetePiedPage("hidden");
    let modeleConnexion = new ModeleMagasin("modele-connexion");
    modeleConnexion.appliquerModele('', "milieu-page");
}


/**
 * Permet à un client existant de se connecter
 */
function seConnecter() {
    let messageErreur = document.getElementById("message-erreur");
    let pseudo = document.getElementById("pseudo").value;
    let motDePasse = document.getElementById("mot-de-passe").value;

    let objJSON = {
        "requete": "connexion",
        "pseudo": pseudo,
        "motDePasse": motDePasse
    }

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("php/main.php");
    requete.envoyerDonnees(txtJSON, function (reponse) {
        let objJSON = JSON.parse(reponse);
        if (objJSON["statut"] == "succes") {
            afficherCaisse(JSON.stringify(objJSON["membre"]));
        }
        else if (objJSON["statut"] == "echec") {
            messageErreur.classList.add('alert');
            messageErreur.classList.add('alert-danger');
            messageErreur.innerHTML = objJSON["message"];
        }
    });

}


/**
 * -----------------------
 * COMMANDE
 * -----------------------
 */

/**
* Affiche les informations du client et la facture
*/
function afficherCaisse(reponse) {
    enTetePiedPage("visible");

    //Informations du client
    let modeleCaisse = new ModeleMagasin("modele-caisse");
    modeleCaisse.appliquerModele(reponse, "milieu-page");

    //Facture
    let requete = new RequeteAjax("php/main.php?q=panier&r=sommaire");
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
    let requete = new RequeteAjax("php/main.php?q=panier&r=liste");
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
                        value: document.getElementById("montant-total").innerText.replace(' $', '').replace(',', '.')
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
    let numeros = document.getElementsByClassName("numeros");
    let tabNoArticle = new Array();
    for (let i = 0; i < numeros.length; i++) {
        tabNoArticle.push(numeros[i].value);
    }
    //Tableau des quantités
    let quantites = document.getElementsByClassName("quantites");
    let tabQuantite = new Array();
    for (let i = 0; i < quantites.length; i++) {
        tabQuantite.push(quantites[i].value);
    }

    let objJSON = {
        "requete": "commande",
        "noClient": document.getElementById("numero-client").value,
        "paypalOrderId": paypalOrderId,
        "tabNoArticle": JSON.stringify(tabNoArticle),
        "tabQuantite": JSON.stringify(tabQuantite)
    };

    let txtJSON = JSON.stringify(objJSON);
    let requete = new RequeteAjax("php/main.php");
    requete.envoyerDonnees(txtJSON, (donnees) => {
        getTotalPanier(commandeTerminee);
    });

}

/**
 * Affiche que la commande est bel et bien complétée,
 * le numéro de confirmation et le courriel du client
 */
function commandeTerminee() {
    let requete = new RequeteAjax("php/main.php?q=commande");
    let modeleComplete = new ModeleMagasin("modele-commande-complete");
    requete.getJSON(reponse => {
        modeleComplete.appliquerModele(reponse, "milieu-page");
    });
    
}
