<?php

/**
 * Représente un objet de type GestionBD
 * Son rôle est de gérer la base de données MySQL
 */
abstract class GestionBD {

    /* ATTRIBUTS */
    private $nomBD = 'magasin_en_ligne';
    private $utilisateur = 'root';
    private $mdp = '';
    protected $bdd;

    /**
     * CONSTRUCTEUR : instanciation de l'objet
     * @param {string} $nomBD - le nom de la base de données
     * @param {string} $utilisateur - l'utilisateur
     * @param {string} $mdp - le mot de passe
     */
    public function __construct() {
        $this->connexionBD();
    }

    /**
     * Se connecte à la base de données
     * @param {string} $nomBD - le nom de la base de données
     * @param {string} $utilisateur - l'utilisateur
     * @param {string} $mdp - le mot de passe
     */
    public function connexionBD() {

        $nsd = 'mysql:host=localhost;dbname='.$this->nomBD.';charset=utf8';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->bdd = new PDO($nsd, $this->utilisateur, $this->mdp, $options);
        } 
        catch (PDOException $e) {
            echo 'Message : ' . $e->getMessage() . "\t Code : " . (int)$e->getCode(). "\n";
            exit;
        }

    }

}

?>