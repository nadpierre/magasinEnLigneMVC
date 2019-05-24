/**
 * Affiche l'inventaire
 */
$(document).ready(function () {
    $("#gabarit").load("vue/gabarit.html", function () {
        afficherInventaire(listerArticles);
        getTotalPanier();
        iconConnexion();
    })
});
/**
 * ============================== Function pour Afficher le profil ==============================
 */

function test() {

    estConnecte().then(reponse => {
        console.log("est-il connecte ?" + reponse);
    }).catch((err) => {
        console.log("est-il connecte ?" + err);
    })
}



/**
 * toggle le bon icon selon le statut connection
 * @param {*} categorie 
 */
function toggleIcon(categorie) {
    // A partir du id btn-login, chercher les tags 'i' et remove et ajouter la class selon le texte 
    $('#btn-login').find("i").removeClass(categorie != 0 ? "fas fa-user-alt" : "fas fa-user-check").addClass(categorie != 0 ? "fas fa-user-check" : "fas fa-user-alt");

    // Ajouter une balise 'a' selon le texte 
    $('.menu-login').append(categorie != 0 ? "<a href=\"index.html\" class=\"btn-logout\" onclick=\"seDeconnecter()\"><i class=\"fas fa-sign-out-alt\"></i>Logout</a>" : "");
}

function iconConnexion() {

    let requete = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "membre",
        "requete": "profil"
    };

    requete.getJSON(objJSON, function (reponse) {
        let temp = JSON.parse(reponse["membre"]);
        let categorie = temp[0].categorie;

        if (categorie == 2) {
            $("#btn-msg").text("Gestion");
            $("#btn-msg").attr("onclick", "profil()");
            toggleIcon(categorie);
        }
        else if (categorie == 1) {
            $("#btn-msg").text("Compte");
            $("#btn-msg").attr("onclick", "profil()");
            toggleIcon(categorie);
        }
    });
}

/** 
 * Affiche le profile selon la personne connectée
 * Le paramètre est seulement prit en compte si un admin est connecté
 * La validation est fait par le controleur.php
 * 
 * @param {*} noMembre 
 */
function profil(noMembre) {

    let requete = new RequeteAjax("controleur/controleur.php");
    // UPDATE
    if (typeof noMembre === "undefined" || noMembre === "") {
        let objJSON = {
            "type": "membre",
            "requete": "profil"
        };

        requete.getJSON(objJSON, function (reponse) {
            let temp = JSON.parse(reponse["membre"]);

            if (temp[0].categorie == 2) {
                let modele = new ModeleMagasin("modele-profil-admin");
                modele.appliquerModele(temp, "milieu-page");
            }
            else if (temp[0].categorie == 1) {
                let modele = new ModeleMagasin("modele-profil-client");
                modele.appliquerModele(temp, "milieu-page");
            }
        });
    }
    else {
        let objJSON = {
            "type": "membre",
            "requete": "profil",
            "noMembre": noMembre
        };
        requete.getJSON(objJSON, function (reponse) {
            let temp = JSON.parse(reponse["membre"]);

            let modele = new ModeleMagasin("modele-admin-affiche-profil-client");

            modele.appliquerModele(temp, "milieu-page");

            let courriel = temp[0]["courriel"];
            // changer le onclick pour rajouter réinitialiser mot de passe
            $(".btn-password").attr("onclick", "reinitialiserMotDePasse('" + courriel + "')");
        });
    }
}

function modifierprofil() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modele = new ModeleMagasin("modele-profil-modification");

    let objJSON = {
        "type": "membre",
        "requete": "profil"
    };

    requete.getJSON(objJSON, function (reponse) {
        let temp = JSON.parse(reponse.membre);
        modele.appliquerModele(temp, "milieu-page");
    });

}

/**
 * Afficher le formulaire modification du profil
 * @param {*} noMembre 
 */
function formulaireProfil(noMembre) {
    let requete = new RequeteAjax("controleur/controleur.php")
    let modele = new ModeleMagasin("modele-profil-modification");
    // si le parametre noMembre n'est pas defini
    if (typeof noMembre === "undefined" || noMembre === "") {
        let objJSON = {
            "type": "membre",
            "requete": "profil"
        };
        requete.getJSON(objJSON, function (reponse) {
            let temp = JSON.parse(reponse["membre"]);
            modele.appliquerModele(temp, "milieu-page");
        });
    }
    else { // parametre noMembre est defini
        let objJSON = {
            "type": "membre",
            "requete": "profil",
            "noMembre": noMembre
        };
        requete.getJSON(objJSON, function (reponse) {
            let temp = JSON.parse(reponse["membre"]);

            modele.appliquerModele(temp, "milieu-page");
            // Changer le titre de la page
            $(".formulaire-h2").text("INFORMATIONX DU CLIENT #" + noMembre);
            // changer le onclick pour rajouter le noMembre
            $("#btn-membre").attr("onclick", "modificationProfil(" + noMembre + ")");
        });
    }
}

/**
 * afficher le formulaire de modification mot de passe
 */
function modifierMotDePasse() {
    let modeleInventaire = new ModeleMagasin("modele-mot-de-passe");
    modeleInventaire.appliquerModele("", "milieu-page");
}

/**
 * Valider la modification mot de passe
 */
function validerModificationPassword() {
    let motDePasse = $("#mot-de-passe").val();
    let nouveauMotDePasse = $("#nouveau-mot-de-passe").val();
    let confMotDePasse = $("#confirme-mot-de-passe").val();

    if (nouveauMotDePasse == confMotDePasse) {
        let objJSON = {
            "type": "membre",
            "requete": "motDePasse",
            "ancien": motDePasse,
            "nouveau": confMotDePasse
        };
        modifierMembre(objJSON);
    }
}

/**
 * Reinitialiser  le mot de passe
 * @param {*} email 
 */
function reinitialiserMotDePasse(email) {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modele = new ModeleMagasin("modele-reset-password");

    let objJSON = {
        "type": "membre",
        "requete": "oubli",
        "courriel": email
    };

    requete.getJSON(objJSON, function (reponse) {
        let temp = reponse["reset"];
        modele.appliquerModele(temp, "milieu-page");
    });
}

/**
 * Vider le panier
 */
function viderPanier() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "panier",
        "requete": "vider"
    };
    requete.getJSON(objJSON, reponse => {
        console.log(reponse);
        getTotalPanier();
        afficherSommaire();
    });
}

/**
 * Afficher la caisse pour un invite
 */
function afficherCaisseInvite() {
    // afficher le formulaire
    let modeleCheckout = new ModeleMagasin("checkout-invite");
    modeleCheckout.appliquerModele("", "milieu-page");

    // afficher la "boite" de la facture
    let modeleSommaire = new ModeleMagasin("modele-facture");
    modeleSommaire.appliquerModele("", "sommaireFactureDetail");

    // remplire  les informations sommaire
    let requeteFacture = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "panier",
        "requete": "sommaire"
    };
    requeteFacture.getJSON(objJSON, reponse => {
        modeleSommaire.appliquerModele(reponse, "sommaireFactureDetail");
    });

    // remplire la facture avec les details des articles dans le panier
    let modeleSommaireFacture = new ModeleMagasin("modele-details-facture");
    let requeteFactureDetails = new RequeteAjax("controleur/controleur.php");
    let objJSONDetail = {
        "type": "panier",
        "requete": "liste"
    };
    requeteFactureDetails.getJSON(objJSONDetail, reponse => {
        modeleSommaireFacture.appliquerModele(reponse, "details-facture");
        afficherPaypal();
    });
}
/**
 * Afficher la caisse pour un membre
 */

async function afficherCaisseMembre() {
    // afficher  les information du client 

    await new Promise((resolve) => {
        let modeleCaisse = new ModeleMagasin("modele-caisse");
        let requete = new RequeteAjax("controleur/controleur.php");
        modeleCaisse.appliquerModele("", "milieu-page");
        let objJSON = {
            "type": "membre",
            "requete": "profil"
        };

        requete.getJSON(objJSON, reponse => {
            let temp = JSON.parse(reponse["membre"]);
            modeleCaisse.appliquerModele(temp, "milieu-page");
            resolve("Promise 1");
        });
        
    });


    await  new Promise((resolve) => {
        // afficher la facture dans le CALLBACK => remplir les details facture et button paypal
        let modeleFacture = new ModeleMagasin("modele-facture");
        let requeteFacture = new RequeteAjax("controleur/controleur.php");
        let objJSONPanier = {
            "type": "panier",
            "requete": "sommaire"
        };

        requeteFacture.getJSON(objJSONPanier, reponse => {
            modeleFacture.appliquerModele(reponse, "facture");
            console.log("promise2");
            resolve("Promise 2");
        });
        
    });

    await new Promise((resolve) => {

        let modeleSommaireFacture = new ModeleMagasin("modele-details-facture");
        let requeteFactureDetails = new RequeteAjax("controleur/controleur.php");
        let objJSONDetail = {
            "type": "panier",
            "requete": "liste"
        };

        requeteFactureDetails.getJSON(objJSONDetail, reponse => {
            modeleSommaireFacture.appliquerModele(reponse, "details-facture");
            
            resolve("succes p3");
            afficherPaypal();
        });
    });
}


/**
 * Afficher le bon formulaire de la caisse selon le statut de connection
 */
function caisseCheckout() {
    estConnecte().then(() => {
        afficherCaisseMembre().then();
    }).catch(()=> {
        afficherCaisseInvite();
    })
}

/**
 * ==============================FIN Function pour Afficher le profil ==============================
 */

/**
 * -----------------------
 * INVENTAIRE
 * -----------------------
 */

/**
* Affiche l'élément HTML qui la contenir la liste des articles
* @param {function} callback - la fonction à appeler après avoir affiché le HTML
*/
function afficherInventaire(callback) {
    let modeleInventaire = new ModeleMagasin("affichage-articles");
    modeleInventaire.appliquerModele("", "milieu-page");
    callback();
}

/**
 * Liste chaque élément de l'inventaire
 */
function listerArticles() {
    //Afficher tous les articles
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleListeArticles = new ModeleMagasin("modele-liste-articles");
    let objJSON = {
        "type": "inventaire"
    };

    requete.getJSON(objJSON, function (reponse) {
        modeleListeArticles.appliquerModele(reponse, "liste-articles");
    });
}

/**
 * Liste chaque élément par catégorie de l'inventaire
 * @param {string} valeur - la valeur du critère de sélection
 */
function listerParCategorie(valeur) {

    //Afficher tous les articles
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleListeArticles = new ModeleMagasin("modele-liste-articles");
    let objJSON = {
        "type": "inventaire",
        "categorie": valeur
    };
    requete.getJSON(objJSON, function (reponse) {

        modeleListeArticles.appliquerModele(reponse, "liste-articles");
    });
}

/**
 * Liste chaque élément de l'inventaire qui sont recherché par le client
 */
function listerRecherche() {
    let valeur = $("#recherche").val();
    //Afficher tous les articles
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleListeArticles = new ModeleMagasin("modele-liste-articles");
    let objJSON = {
        "type": "inventaire",
        "mot": valeur
    };
    requete.getJSON(objJSON, function (reponse) {
        modeleListeArticles.appliquerModele(reponse, "liste-articles");
    });
}

/**
 * Affiche un seul article
 * @param {string} noArticle - l'identifiant de l'article
 */
function afficherArticle(noArticle) {
    let requete = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "inventaire",
        "noArticle": noArticle
    };
    let modeleArticle = new ModeleMagasin("modele-article");
    requete.getJSON(objJSON, reponse => { modeleArticle.appliquerModele(reponse, "milieu-page"); });
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
    let requete = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "panier",
        "requete": "compteur"
    };
    requete.getJSON(objJSON, reponse => {
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
        "type": "panier",
        "requete": "ajouter",
        "article": {
            "noArticle": $("#identifiant").val(),
            "libelle": $("#libelle").val(),
            "cheminImage": $("#cheminImage").val(),
            "prixUnitaire": $("#prix").val(),
            "quantite": $("#quantity").val()
        }

    };

    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, function (reponse) {
        messageErreur.addClass('alert');
        if (reponse["statut"] === "succes") {
            getTotalPanier();
            messageErreur.removeClass('alert-danger');
            messageErreur.addClass('alert-success');
            messageErreur.css("color", "green");
        }
        else if (reponse["statut"] === "echec") {
            messageErreur.removeClass('alert-success');
            messageErreur.addClass('alert-danger');
            messageErreur.css("color", "red");
        }
        messageErreur.html(reponse["message"]);
    });


}


/**
 * Afficher le sommaire du panier
 * @param {function} callback - la fonction à appeler après que le sommaire soit chargé
 */
function afficherSommaire() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modelePanier = new ModeleMagasin("modele-panier");
    let objJSON = {
        "type": "panier",
        "requete": "sommaire"
    };
    requete.getJSON(objJSON, reponse => {
        modelePanier.appliquerModele(reponse, "milieu-page");
        listerPanier();
    });

}

/**
 * Affiche tous les éléments du panier
 */
function listerPanier() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleListePanier = new ModeleMagasin("modele-liste-panier");
    let objJSON = {
        "type": "panier",
        "requete": "liste"
    };
    requete.getJSON(objJSON, function (reponse) {
        modeleListePanier.appliquerModele(reponse, "liste-panier");
    })
}

/**
 * Supprime un élément du panier
 */
function supprimerDuPanier(noArticle) {

    let objJSON = {
        "type": "panier",
        "requete": "supprimer",
        "noArticle": noArticle
    };
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, getTotalPanier);
    afficherSommaire();
}

/**
 * Modifier les quantités du panier
 */
function modifierPanier() {
    let messageErreur = $("#message-erreur");

    //Tableau des numéros d'article
    let liensNoArticle = $(".noArticle");
    let tabNoArticle = new Array();
    for (let i = 0; i < liensNoArticle.length; i++) {
        tabNoArticle.push(liensNoArticle[i].value);
    }
    //Tableau des quantités
    let champsQuantite = $(".quantite");
    let tabQuantite = new Array();
    for (let i = 0; i < champsQuantite.length; i++) {
        tabQuantite.push(champsQuantite[i].value);
    }

    let objJSON = {
        "type": "panier",
        "requete": "modifier",
        "tabNoArticle": JSON.stringify(tabNoArticle),
        "tabQuantite": JSON.stringify(tabQuantite)
    };

    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, function (reponse) {
        if (reponse["statut"] === "echec") {
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.css("color", "red");
            messageErreur.html(reponse["message"]);
        }
        else {
            getTotalPanier();
            afficherSommaire();
        }
    });
}

/**
 * Vide le panier
 */
function viderPanier() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "panier",
        "requete": "vider"
    };

    requete.getJSON(objJSON, reponse => {
        console.log(reponse);
        getTotalPanier();
        afficherSommaire();
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
 * 0 = invité
 * 1 = membre
 * 2 = admin
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
    const CODE_POSTAL = /^[A-Z][0-9][A-Z] ?[0-9][A-Z][0-9]/;
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
        messageErreur.html("Format de code postal invalide.");
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

    let membre = {
        "nomMembre": nom,
        "prenomMembre": prenom,
        "adresse": adresse,
        "ville": ville,
        "province": province,
        "codePostal": codePostal,
        "noTel": noTel,
        "courriel": courriel,
        "motDePasse": motDePasse,
        "categorie": 1
    }

    let objJSON = {
        "type": "membre",
        "requete": "inscription",
        "membre": JSON.stringify(membre)
    };

    ajouterMembre(objJSON);
}


/**
 * Ajoute le client à la base de données
 * @param {string} objJSON - les données à envoyer
 */
function ajouterMembre(objJSON) {
    let messageErreur = $("#message-erreur");
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, function (reponse) {
        if (reponse["statut"] === "succes") {
            afficherCaisse(reponse["membre"]);
        }
        else if (reponse["statut"] === "echec") {
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(reponse["message"]);
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
    let courriel = $("#courriel").val();
    let motDePasse = $("#mot-de-passe").val();

    let objJSON = {
        "type": "membre",
        "requete": "connexion",
        "courriel": courriel,
        "motDePasse": motDePasse
    };

    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, function (reponse) {
        if (reponse["statut"] == "succes") {
            profil("");
            iconConnexion();


        }
        else if (reponse["statut"] == "echec") {
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(reponse["message"]);
        }
    });

}

/**
 * Déconnexion du client actif
 */

function seDeconnecter() {
    let objJSON = {
        "type": "membre",
        "requete": "deconnexion"
    };

    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, function (reponse) {
        if (reponse["statut"] == "succes") {
            afficherInventaire(listerArticles);
            iconConnexion();

        }
        else if (reponse["statut"] == "echec") {
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(reponse["message"]);
        }
    });
}

/**
* Désabonne le compte du client
*/
function desabonner(courriel) {
    let objJSON = {
        "type": "membre",
        "requete": "desabonner",
        "courriel": courriel
    };
    if (confirm("Êtes-vous sûr de vouloir vous désabonnez?")){
        let requete = new RequeteAjax("controleur/controleur.php");
        requete.getJSON(objJSON, function (reponse) {
            if (reponse["statut"] == "succes") {
                afficherInventaire(listerArticles);
                getTotalPanier();
            }
            else if (reponse["statut"] == "echec") {
                messageErreur.addClass('alert');
                messageErreur.addClass('alert-danger');
                messageErreur.html(reponse["message"]);
            }
        })
    }
    
}

/**
 * Valider si le client est connecté
 */
function estConnecte() {
    return new Promise((resolve, reject) => {
        let objJSON = {
            "type": "membre",
            "requete": "estConnecte"
        };
        let requete = new RequeteAjax("controleur/controleur.php");
        requete.getJSON(objJSON, function (reponse) {

            if (reponse["statut"] == "succes") {
                resolve(true)
            }
            else if (reponse["statut"] == "echec") {
                reject(false)
            }
        });
    })
}

function modificationProfil(noMembre) {
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
    let objJSON = {};
    let membre = {};

    //Expression régulières
    const LETTRES_SEULEMENT = /[a-zA-ZáàäâéèëêíìïîóòöôúùüûçñÁÀÄÂÉÈËÊÍÌÏÎÓÒÖÔÚÙÜÛÑÇ\'\-]+/;
    const CODE_POSTAL = /^[A-Z][0-9][A-Z] ?[0-9][A-Z][0-9]$/;
    const NO_TEL = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;

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

    if (noMembre == "") {
        membre = {
            "nomMembre": nom,
            "prenomMembre": prenom,
            "adresse": adresse,
            "ville": ville,
            "province": province,
            "codePostal": codePostal,
            "noTel": noTel
        };
        objJSON = {
            "type": "membre",
            "requete": "modifier",
            "membre": JSON.stringify(membre)
        };
    }
    else {
        membre = {
            "noMembre": noMembre,
            "nomMembre": nom,
            "prenomMembre": prenom,
            "adresse": adresse,
            "ville": ville,
            "province": province,
            "codePostal": codePostal,
            "noTel": noTel
        };

        objJSON = {
            "type": "membre",
            "requete": "modifier",
            "membre": JSON.stringify(membre),
            "noMembre": noMembre
        };
    }
    modifierMembre(objJSON, noMembre);
}

function modifierMembre(objJSON, noMembre) {
    console.log(objJSON);
    let messageErreur = $("#message-erreur");
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, function (reponse) {
        if (reponse["statut"] === "succes") {
            if (noMembre == "") {
                profil();
            }
            else {
                profil(noMembre);
            }

        }
        else if (reponse["statut"] === "echec") {
            messageErreur.addClass('alert');
            messageErreur.addClass('alert-danger');
            messageErreur.html(objetJSON["message"]);
        }
    });
}

/**
 * -----------------------
 * ADMINISTRATEUR
 * -----------------------
 */

/**
 * Affiche le profil d'un administrateur 
 */
function profilAdmin() {
    let requete = new RequeteAjax("controleur/controleur.php")
    let modele = new ModeleMagasin("modele-profil-admin");

    let objJSON = {
        "type": "membre",
        "requete": "profilAdmin"
    };

    requete.getJSON(objJSON, function (reponse) {
        let temp = JSON.parse(reponse.membre);
        modele.appliquerModele(temp, "milieu-page");

    });
}

function soloArticleAdmin(noArticle) {
    let requete = new RequeteAjax("controleur/controleur.php");
    let objJSON = {
        "type": "inventaire",
        "noArticle": noArticle
    };
    let modeleArticle = new ModeleMagasin("modele-article-admin");
    requete.getJSON(objJSON, reponse => { modeleArticle.appliquerModele(reponse, "milieu-page"); });
}

/** 
 * Ajout article template
 */
function templateAjoutAA() {
    let modele = new ModeleMagasin("modele-ajout-article-admin");
    modele.appliquerModele("", "milieu-page");
}

/** 
* Ajout d'un article
*/
function ajouterArticle() {
    event.preventDefault();

    let objArticle = {
        "libelle": $("#libelle").val(),
        "categorie": $("#categorie").val(),
        "prixUnitaire": $("#prix").val(),
        "quantiteEnStock": $("#quantite").val()
    }

    var donnees = new FormData();
    donnees.append("requete", "ajouter");
    donnees.append("image", $("#photo").get(0).files[0]);
    donnees.append("article", JSON.stringify(objArticle));

    let requete = new RequeteAjax("controleur/controleur.php")

    requete.envoyerArticle(donnees, reponse => {
        afficherListeArticles();
    });

}

/** 
* Modifier un article
*/
function modifierArticle(noArticle) {
    event.preventDefault();
    let objArticle = {
        "noArticle": noArticle,
        "libelle": $("#libelle").val(),
        "categorie": $("#categorie").val(),
        "prixUnitaire": $("#prix").val(),
        "quantiteEnStock": $("#quantite").val()
    }

    var donnees = new FormData();
    donnees.append("requete", "modifier");
    donnees.append("image", $("#photo").get(0).files[0]);
    donnees.append("article", JSON.stringify(objArticle));

    let requete = new RequeteAjax("controleur/controleur.php");
    requete.envoyerArticle(donnees, reponse => {
        console.log(reponse);
        afficherListeArticles();
    });
}

/** 
* Supprimer un article
*/
function supprimerArticle(noArticle) {
    event.preventDefault();
    let messageErreur = $("#message-erreur");
    if (confirm("Voulez-vous supprimer l'article " + noArticle + "?")) {
        let objJSON = {
            "type": "inventaire",
            "requete": "supprimer",
            "noArticle": noArticle
        }
        let requete = new RequeteAjax("controleur/controleur.php");
        requete.getJSON(objJSON, function (reponse) {
            console.log(reponse);
            if (reponse["statut"] === "succes") {
                afficherListeArticles();
            }
            else if (reponse["statut"] === "echec") {
                messageErreur.addClass('alert');
                messageErreur.addClass('alert-danger');
                messageErreur.html(objetJSON["message"]);
            }
        });
    }
}

/*
* Rechercher l'article
*/
function rechercherArticleAdmin(mot) {
    if (event.keyCode == 13) {
        let modele = new ModeleMagasin("modele-inventaire-admin");
        modele.appliquerModele("", "milieu-page");

        let requete = new RequeteAjax("controleur/controleur.php");
        let objJSON = {
            "type": "inventaire",
            "mot": mot
        };
        requete.getJSON(objJSON, reponse => {
            let modele1 = new ModeleMagasin("modele-liste-panier-admin");
            modele1.appliquerModele(reponse, "liste-panier");
        });
    }
}

/**  
 * Affiche la liste d'articles
 */
function afficherListeArticles() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modele = new ModeleMagasin("modele-inventaire-admin");
    let objJSON = {
        "type": "panier",
        "requete": "sommaire"
    };

    requete.getJSON(objJSON, reponse => {
        modele.appliquerModele(reponse, "milieu-page");
        articleLigne();
    });
}

/**
 * Affiche la ligne du tableau dans la liste d'article
 */
function articleLigne() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modele = new ModeleMagasin("modele-liste-panier-admin");
    let objJSON = {
        "type": "inventaire"
    };
    requete.getJSON(objJSON, function (reponse) {
        console.log(reponse);
        modele.appliquerModele(reponse, "liste-panier");
    })
}


/** 
 * Affiche la liste de membres
 */
function afficherListeMembres() {
    let modele = new ModeleMagasin("modele-liste-membres-admin");

    modele.appliquerModele("", "milieu-page");
    membreLigne();
}

/** 
 * Affiche la ligne du membre dans le tableau
 */
function membreLigne() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modele = new ModeleMagasin("modele-membre-admin");
    let objJSON = {
        "type": "membre",
        "requete": "liste"
    };
    requete.getJSON(objJSON, function (reponse) {
        let membres = JSON.parse(reponse["membres"]);
        modele.appliquerModele(membres, "liste-panier");
    });
}

/* 
* Rechercher un membre
*/
function rechercherMembreAdmin(nom) {
    if (event.keyCode == 13) {
        let modele = new ModeleMagasin("modele-liste-membres-admin");
        modele.appliquerModele("", "milieu-page");

        let requete = new RequeteAjax("controleur/controleur.php");
        let objJSON = {
            "type": "membre",
            "requete": "recherche",
            "nom": nom
        };
        requete.getJSON(objJSON, reponse => {
            let membres = JSON.parse(reponse["membres"]);
            let modele1 = new ModeleMagasin("modele-membre-admin");
            modele1.appliquerModele(membres, "liste-panier");
        });
    }
}

/** 
 * Supprime le compte choisi
 */
function supprimerMembre(noMembre) {
    let messageErreur = $("#message-erreur");
    if (confirm("Voulez-vous supprimer le membre " + noMembre + "?")) {
        let objJSON = {
            "type": "membre",
            "requete": "supprimer",
            "noMembre": noMembre
        }
        let requete = new RequeteAjax("controleur/controleur.php");
        requete.getJSON(objJSON, function (reponse) {
            if (reponse["statut"] === "succes") {
                afficherListeMembres();
            }
            else if (reponse["statut"] === "echec") {
                messageErreur.addClass('alert');
                messageErreur.addClass('alert-danger');
                messageErreur.html(objetJSON["message"]);
            }
        });
    }
}

/**
 * Affiche le modèle pour l'ajout d'un administrateur
 */
function modeleAjoutMembreAdmin(){
    let modele = new ModeleMagasin("modele-ajout-admin");
    modele.appliquerModele("", "milieu-page");
}

/**
 * Valide et ajoute un membre administrateur
 */
function ajouterAdmin(){
    //Message d'erreur
    messageErreur = $("#message-erreur");

    //Données du formulaire
    let nom = $("#lname").val();
    let prenom = $("#fname").val();
    let courriel = $("#email").val();
    let motDePasse = $("#mot-de-passe").val();
    let confMotDePasse = $("#conf-mot-de-passe").val();

    //Expression régulières
    const LETTRES_SEULEMENT = /[a-zA-ZáàäâéèëêíìïîóòöôúùüûçñÁÀÄÂÉÈËÊÍÌÏÎÓÒÖÔÚÙÜÛÑÇ\'\-]+/;
    const COURRIEL = /[^@]+@[^\.]+\..+/g;

    //Vérifier si le nom, le prénom et la ville ont seulement des lettres
    if (!nom.match(LETTRES_SEULEMENT) || !prenom.match(LETTRES_SEULEMENT)) {
        messageErreur.addClass('alert');
        messageErreur.addClass('alert-danger');
        messageErreur.html("Ce champ ne doit contenir que des lettres.");
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

    let membre = {
        "nomMembre": nom,
        "prenomMembre": prenom,
        "courriel": courriel,
        "motDePasse": motDePasse,
        "categorie": 2
    }

    let objJSON = {
        "type": "membre",
        "requete": "inscription",
        "membre": JSON.stringify(membre)
    };

    ajouterMembre(objJSON);
    afficherListeMembres();
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
    //Informations du client
    let modeleCaisse = new ModeleMagasin("modele-caisse");
    modeleCaisse.appliquerModele(reponse, "milieu-page");

    //Facture
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleFacture = new ModeleMagasin("modele-facture");
    let objJSON = {
        "type": "panier",
        "requete": "sommaire"
    };
    requete.getJSON(objJSON, donnees => {
        modeleFacture.appliquerModele(donnees, "facture");
        listerFacture();
        afficherPaypal();
    });
}

/**
 * Liste chaque élément de la facture
 */
function listerFacture() {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleDetailsFacture = new ModeleMagasin("modele-details-facture");
    let objJSON = {
        "type": "panier",
        "requete": "liste"
    };
    requete.getJSON(objJSON, donnees => {
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
    let objJSON;
    let messageErreur;
    
    //Message d'erreur
    messageErreur = $("#message-erreur");

   estConnecte().then(() =>{

        objJSON = {
            "type" : "commande",
            "requete" : "membre",
            "paypalOrderId" : paypalOrderId
        }    

        envoyerCommande(objJSON);

    }).catch(() =>{

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

        let membre = {
            "nomMembre": nom,
            "prenomMembre": prenom,
            "adresse": adresse,
            "ville": ville,
            "province": province,
            "codePostal": codePostal,
            "noTel": noTel,
            "courriel": courriel,
            "categorie": 0
        }

        objJSON = {
            "type": "commande",
            "requete": "invite",
            "membre" : JSON.stringify(membre),
            "paypalOrderId" : paypalOrderId
        };
        
       
       envoyerCommande(objJSON);
   });

}

/**
 * Affiche que la commande est bel et bien complétée,
 * le numéro de confirmation et le courriel du client
 */
/**
 * Affiche que la commande est bel et bien complétée,
 * le numéro de confirmation et le courriel du client
 */
function commandeTerminee(objCommande) {
    let requete = new RequeteAjax("controleur/controleur.php");
    let modeleComplete = new ModeleMagasin("modele-commande-complete");
    modeleComplete.appliquerModele(objCommande, "milieu-page");
    getTotalPanier();

}

/**
 * Envoie la commande au serveur
 * @param {Object} objJSON 
 */
function envoyerCommande(objJSON) {
    let requete = new RequeteAjax("controleur/controleur.php");
    requete.getJSON(objJSON, (reponse) => {
        if (reponse["statut"] == "succes") {
            const objCommande = reponse["commande"];
            commandeTerminee(objCommande);
        }
    });
}

/**
 * Lister commande
 */

 function listerCommande(noMembre){
    let modele = new ModeleMagasin("");
    let requete = new RequeteAjax("controleur/controleur.php");

    let objJSON = {
        "type" : "commande",
        "requete" : "listeMembre",
        "noMembre" : noMembre
    }

    requete.getJSON(objJSON, function(reponse){
        modele.appliquerModele(reponse,"");
    })
 }