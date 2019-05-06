-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  lun. 06 mai 2019 à 23:38
-- Version du serveur :  10.1.36-MariaDB
-- Version de PHP :  7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `magasin_en_ligne`
--
CREATE DATABASE IF NOT EXISTS `magasin_en_ligne` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `magasin_en_ligne`;

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `noArticle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categorie` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  `cheminImage` varchar(255) DEFAULT NULL,
  `prixUnitaire` decimal(10,2) DEFAULT NULL,
  `quantiteEnStock` int(10) NOT NULL,
  `quantiteDansPanier` int(10) NOT NULL,
  PRIMARY KEY (`noArticle`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`noArticle`, `categorie`, `description`, `cheminImage`, `prixUnitaire`, `quantiteEnStock`, `quantiteDansPanier`) VALUES
(1, 'Cheveux', 'Alikay Naturals Lemongrass Leave In Conditioner', 'images/alikay_naturals_lemongrass_leave_in_conditioner.jpg', '28.99', 8, 0),
(2, 'Cheveux', 'ApHogee Curlific! Texture Treatment', 'images/aphogee_curlific_texture_treatment.jpg', '13.99', 8, 0),
(3, 'Cheveux', 'As I Am Coconut Cowash Cleansing Conditioner', 'images/as_i_am_coconut_cowash.jpeg', '15.99', 8, 0),
(4, 'Maquillage', 'Ardell Magnetic Lashes Double Wispies', 'images/ardell_magnetic_lashes_double_wispies.jpg', '21.99', 7, 0),
(5, 'Maquillage', 'Ardell Natural Lashes Wispies Brown', 'images/ardell_natural_lashes_wispies_brown.jpg', '6.99', 8, 0),
(6, 'Cheveux', 'BaByliss Pro Nano Titanium OPTIMA 3100 Straightening Iron', 'images/babylisspro_nano_titanium_optima_3100_straightening_iron_1_inch.jpg', '271.99', 9, 0),
(7, 'Hommes', 'Beard Guyz Beard Care And Grooming Kit', 'images/beard_guyz_beard_care_grooming_kit.jpg', '29.99', 10, 0),
(8, 'Cheveux', 'Camille Rose Naturals Curl Maker', 'images/camille_rose_curl_maker.jpg', '41.99', 10, 0),
(9, 'Cheveux', 'Cantu Shea Butter For Natural Hair Coconut Curling Cream', 'images/cantu_coconut_curling_cream.jpg', '31.99', 10, 0),
(10, 'Cheveux', 'Carol\'s daughter Black Vanilla Moisture And Shine Hydrating Conditioner', 'images/carols_daughter_black_Vanilla_moisture_and_shine_hydrating_conditioner.jpg', '29.99', 10, 0),
(11, 'Cheveux', 'Carol\'s daughter Hair Milk Curl Defining Moisture Mask', 'images/carols_daughter_hair_milk_curl_defining_moisture_mask.jpg', '34.99', 10, 0),
(12, 'Cheveux', 'Curls Blueberry Bliss Curl Control Paste', 'images/curls_blueberry_control_paste.jpg', '15.99', 10, 0),
(13, 'Cheveux', 'DevaCurl Supercream Coconut Curl Styler', 'images/devacurl_supercream_coconut_curl_styler.jpg', '55.99', 10, 0),
(14, 'Peau', 'Dudu-Osun Black Soap', 'images/dudu_osun_black_soap.jpg', '5.99', 7, 0),
(15, 'Maquillage', 'DUO Strip Lash Adhesive Tube Dark Tone', 'images/duo_strip_lash_adhesive_tube_dark_tone.jpg', '8.99', 10, 0),
(16, 'Cheveux', 'Eco Styler Olive Oil Styling Gel', 'images/eco_styler_olive_oil_gel.jpeg', '9.99', 10, 0),
(17, 'Cheveux', 'EDEN BodyWorks Coconut Shea Cleansing CoWash', 'images/eden_body_works_coconut_shea_cleansing_cowash.jpg', '17.99', 10, 0),
(18, 'Cheveux', 'Shea Moisture Jamaican Black Castor Oil Strengthen And Grow Thermal Protectant', 'images/shea_moisture_jbco_thermal_protectant.jpg', '19.99', 10, 0),
(19, 'Cheveux', 'Kera Care Edge Tamer', 'images/kera_care_edge_tamer.jpg', '11.99', 10, 0),
(20, 'Cheveux', 'Kinky Curly Come Clean Shampoo', 'images/kinky_curly_come_clean_shampoo.jpg', '21.99', 10, 0),
(21, 'Cheveux', 'Maui Moisture Curl Quench+ Coconut Oil Curl Milk', 'images/maui_moisture_curl_quench_coconut_oil_curl_milk.jpg', '10.99', 9, 0),
(22, 'Cheveux', 'Mielle Organics Babassu Mint Deep Conditioner', 'images/mielle_organics_babassu_oil_mint_deep_conditioner.jpg', '22.99', 10, 0),
(23, 'Cheveux', 'Moroccanoil Oil Treatment', 'images/moroccanoil_treatment.jpg', '59.99', 9, 0),
(24, 'Peau', 'TGIN Argan Replenishing Hair And Body Serum', 'images/tgin_argan_replenishing_hair_body_serum.jpg', '24.99', 10, 0),
(25, 'Cheveux', 'Denman Brush D4 Black', 'images/denman_brush_d4_black.jpg', '34.99', 10, 0),
(26, 'Hommes', 'The Mane Choice Head Honcho Hair And Beard Oil + Butter = The Balm ', 'images/tmc_head_honcho_the_balm.jpg', '16.99', 10, 0),
(27, 'Hommes', 'Shea Moisture Maracuja Oil And Shea Butter Full Beard Detangler', 'images/shea_moisture_maracuja_oil_beard_detangler.jpg', '15.99', 8, 0),
(28, 'Hommes', 'Uncle Jimmy Beard Softener Conditioning Balm', 'images/uncle_jimmy_beard_softener.jpg', '19.99', 9, 0);

-- --------------------------------------------------------

--
-- Structure de la table `article_en_commande`
--

DROP TABLE IF EXISTS `article_en_commande`;
CREATE TABLE IF NOT EXISTS `article_en_commande` (
  `noCommande` int(10) UNSIGNED NOT NULL,
  `noArticle` int(10) UNSIGNED NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`noCommande`,`noArticle`),
  KEY `commande_fk` (`noCommande`),
  KEY `article_fk` (`noArticle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `article_en_commande`
--

INSERT INTO `article_en_commande` (`noCommande`, `noArticle`, `quantite`) VALUES
(1, 3, 1),
(2, 11, 1),
(2, 17, 1),
(3, 2, 1),
(3, 5, 1),
(3, 8, 1),
(4, 1, 1),
(4, 4, 1),
(4, 6, 1),
(4, 7, 1),
(5, 20, 1),
(6, 6, 1),
(6, 14, 4),
(6, 15, 1),
(6, 16, 1),
(7, 7, 1),
(7, 14, 4),
(7, 22, 2),
(8, 4, 3),
(8, 14, 4),
(8, 17, 2),
(9, 2, 3),
(10, 2, 1),
(10, 3, 2),
(11, 4, 2),
(12, 1, 2),
(12, 5, 2),
(12, 28, 1),
(13, 1, 2),
(13, 5, 2),
(13, 28, 1),
(14, 1, 2),
(14, 5, 2),
(14, 28, 1),
(15, 1, 2),
(15, 5, 2),
(15, 28, 1),
(16, 1, 2),
(16, 5, 2),
(16, 28, 1),
(17, 6, 1),
(17, 14, 3),
(17, 27, 2),
(18, 21, 1),
(18, 23, 1),
(19, 2, 1),
(19, 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `noClient` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomClient` varchar(50) NOT NULL,
  `prenomClient` varchar(50) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `codePostal` varchar(10) NOT NULL,
  `noTel` varchar(25) NOT NULL,
  `pseudo` varchar(25) DEFAULT NULL,
  `motDePasse` varchar(255) DEFAULT NULL,
  `courriel` varchar(255) NOT NULL,
  PRIMARY KEY (`noClient`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`noClient`, `nomClient`, `prenomClient`, `adresse`, `ville`, `province`, `codePostal`, `noTel`, `pseudo`, `motDePasse`, `courriel`) VALUES
(1, 'Collins', 'Renee B', '2394 St Jean Baptiste St', 'Montréal', 'Québec', 'G0M 1W0', '819-548-2143', 'LAstAinV', 'jhULyARS', 'w8drqcfwb2o@payspun.com'),
(2, 'Kirk', 'Oscar M', '4277 40th Street', 'Calgary', 'Alberta', 'T2C 2P3', '403-236-7859', 'vaShosIX', 'MTyxIjsH', 'xt4v02xxx0g@thrubay.com'),
(3, 'Delossantos', 'Julia', '4603 Yonge Street', 'Toronto', 'Ontario', 'M4W 1J7', '416-301-6292', 'NciDeGON', 'pV2VAqRJ', 'sowl5hn2y9k@thrubay.com'),
(4, 'Desantiago', 'Ruben J', '1097 Mountain Rd', 'Moncton', 'Nouveau-Brunswick', 'E1C 1H6', '506-961-5510', 'STevERse', 'ENwSdBDW', 'e02n5x6ptto@payspun.com'),
(5, 'Rivera', 'Linda M', '496 2nd Street', 'Oakbank', 'Manitoba', 'R0E 1J0', '204-444-1472', 'eoNVOvar', 'S2tDbmiz', 'os8l3vscf7r@fakemailgenerator.net'),
(7, 'Soucy', 'Warrane', '4686 Roger Street', 'Oyster River', 'Colombie-Britannique', 'V9W 5N0', '250-337-5002', 'ouRYPtIC', 'BmwxI84I', 'warranesoucy@rhyta.com '),
(8, 'Kou', 'Mingmei', '3375 5th Avenue', 'Fort Vermilion', 'Alberta', 'T0H 1N0', '780-927-6217', 'TRATerfi', 'BuXVRdUl', 'mingmeikuo@armyspy.com '),
(9, 'Antoun', 'Rais Fathi', '3564 St Marys Rd', 'Winnipeg', 'Manitoba', 'R3C 0C4', '204-292-9473', 'aBlEUren', 'LwTtG3N5', 'raisfathiantoun@rhyta.com'),
(10, 'Chatigny', 'Dalmace', '3008 No. 3 Road', 'Richmond', 'Colombie-Britannique', 'V6X 2B8', '604-214-5060', 'siTaNtaN', 'ggCEbGjg', 'dalmacechatigny@armyspy.com'),
(11, 'Michel', 'Annot', '2516 Carling Avenue', 'Ottawa', 'Ontario', 'K1Z 7B5', '613-355-2003', 'Thempheince', 'Xaashoh3oh', 'annotmichel@jourrapide.com '),
(12, 'Ahmad Ba', 'Mufeed', '4458 Reserve St', 'Inverary', 'Ontario', 'K0H 1X0', '613-353-0555', 'Mussiout', 'ahlei3Sh', 'mufeedahmadBa@teleworm.us'),
(13, 'Sousa Pereira', 'Brenda', '4644 Dry Pine Bay Rd', 'Azilda', 'Ontario', 'H0M 1B0', '705-983-0538', 'Larearme', 'void2xu5Ii', 'brendasousapereira@jourrapide.com '),
(14, 'Robel', 'Zewdi', '3336, Water Street', 'Kitchener', 'Ontario', 'N2H 5A5', '519-744-5326', 'Oweept', 'RaiPh2vee', 'zewdirobel@teleworm.us'),
(15, 'Chieloka', 'Nkechiyerem', '4262 Orenda Rd', 'Brampton', 'Ontario', 'L6W 1Z2', '905-451-0542', 'Nowee1989', 'eiBie0zai', 'nkechiyeremchieloka@rhyta.com'),
(16, 'Arteaga Garay', 'Denna', '878 Speers Road', 'Brampton', 'Ontario', 'G0H 1H0', '905-790-4905', 'Morty1947', 'ayo6gooXuwae', 'dennaarteagagaray@rhyta.com '),
(17, 'Olivas', 'Jaclyn Lira', '3767 Wallace Street', 'Nanaimo', 'Colombie-Britannique', 'V9R 3A8', '250-755-5941', 'Acusid', 'vahWeiL2Ch', 'jaclynliraolivas@armyspy.com '),
(18, 'Bernier', 'Geneviève', '1805 Quayside Dr', 'New Westminster', 'Colombie-Britannique', 'V3M 6A1', '604-764-5263', 'Sucan1982', 'ahMobahtae2', 'genevievebernier@dayrep.com '),
(19, 'Bondy', 'Favor', '3295 2nd Street', 'Lac Du Bonnet', 'Manitoba', 'R0E 1A0', '204-345-8196', 'Heeut1980', 'Foe7zi9Othie', 'favorbondy@armyspy.com'),
(22, 'test', 'test', 'test test', 'test', 'Québec', 'A2B 2C4', '514-123-4567', 'test123', 'toto99', 'test@test.com'),
(23, 'test', 'test', 'test test', 'Montréal', 'Québec', 'A2B 2C4', '514-123-4567', 'test1234', 'toto99', 'test123@test.com'),
(24, 'Test', 'Nadine', '123, rue Test appartement 100', 'Montréal', 'Québec', 'A1B 2C3', '514-123-4567', 'nad2273', 'magasin', 'nadine@test.com'),
(25, 'Sandyman', 'Menegilda', '2470 Findlay Creek Road', 'Creston', 'Colombie-Britannique', 'V0B 1G0', '250-402-3442', 'Boodsom', 'aV8Ahghahnan', 'MenegildaSandyman@dayrep.com');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `noCommande` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateCommande` datetime NOT NULL,
  `noClient` int(10) UNSIGNED NOT NULL,
  `paypalOrderId` char(17) NOT NULL,
  PRIMARY KEY (`noCommande`),
  KEY `commande_noclient_idx` (`noClient`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`noCommande`, `dateCommande`, `noClient`, `paypalOrderId`) VALUES
(1, '2019-04-02 19:00:16', 1, 'PG9N8746L66G574L7'),
(2, '2019-04-02 19:00:17', 2, 'Z6G6FLEUYAS5QVDKG'),
(3, '2019-04-02 19:00:17', 3, 'DJ7PN4N20N23W68AA'),
(4, '2019-04-02 19:00:17', 4, '2QW9JOV2MQSIK62UO'),
(5, '2019-04-02 19:00:17', 4, 'LG7M12RBTV2YU85E0'),
(6, '2019-04-14 11:31:08', 1, '1FP03323RH1890633'),
(7, '2019-04-14 11:50:14', 2, '51U788521W1105035'),
(8, '2019-04-14 12:01:28', 5, '6SP278845A189145G'),
(9, '2019-04-14 12:31:11', 15, '47506035TU448745G'),
(10, '2019-04-23 23:50:41', 24, '8SP47294TD071174H'),
(11, '2019-04-24 00:40:36', 2, '22J515486N2962932'),
(12, '2019-04-25 21:32:50', 10, '6VH66797M3545032V'),
(13, '2019-04-25 21:36:50', 16, '0BG24831D59700828'),
(14, '2019-04-25 21:39:19', 12, '9CN53198D46468411'),
(15, '2019-04-25 21:45:54', 14, '5AK647786A433245Y'),
(16, '2019-04-25 21:55:59', 17, '0YJ40016H08239404'),
(17, '2019-04-25 22:35:02', 19, '4AW725945X636102U'),
(18, '2019-04-25 22:47:23', 25, '9R613830CD671870Y'),
(19, '2019-04-26 13:35:49', 4, '45P55170576160821');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_article`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `vue_article`;
CREATE TABLE IF NOT EXISTS `vue_article` (
`noArticle` int(10) unsigned
,`description` varchar(255)
,`cheminImage` varchar(255)
,`prixUnitaire` decimal(10,2)
,`quantiteEnStock` int(10)
,`quantiteDansPanier` int(10)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_commande`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `vue_commande`;
CREATE TABLE IF NOT EXISTS `vue_commande` (
`Nom complet` varchar(101)
,`ville` varchar(50)
,`noCommande` int(10) unsigned
,`dateCommande` datetime
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_commande_full`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `vue_commande_full`;
CREATE TABLE IF NOT EXISTS `vue_commande_full` (
`Nom complet` varchar(101)
,`ville` varchar(50)
,`noCommande` int(10) unsigned
,`dateCommande` datetime
,`Nb d'articles` decimal(32,0)
,`Prix total` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Structure de la vue `vue_article`
--
DROP TABLE IF EXISTS `vue_article`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_article`  AS  select `article`.`noArticle` AS `noArticle`,`article`.`description` AS `description`,`article`.`cheminImage` AS `cheminImage`,`article`.`prixUnitaire` AS `prixUnitaire`,`article`.`quantiteEnStock` AS `quantiteEnStock`,`article`.`quantiteDansPanier` AS `quantiteDansPanier` from `article` order by `article`.`quantiteEnStock` ;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_commande`
--
DROP TABLE IF EXISTS `vue_commande`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_commande`  AS  select concat(`client`.`prenomClient`,' ',`client`.`nomClient`) AS `Nom complet`,`client`.`ville` AS `ville`,`commande`.`noCommande` AS `noCommande`,`commande`.`dateCommande` AS `dateCommande` from (`client` join `commande` on((`client`.`noClient` = `commande`.`noClient`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_commande_full`
--
DROP TABLE IF EXISTS `vue_commande_full`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vue_commande_full`  AS  select concat(`client`.`prenomClient`,' ',`client`.`nomClient`) AS `Nom complet`,`client`.`ville` AS `ville`,`commande`.`noCommande` AS `noCommande`,`commande`.`dateCommande` AS `dateCommande`,sum(`article_en_commande`.`quantite`) AS `Nb d'articles`,sum((`article_en_commande`.`quantite` * `article`.`prixUnitaire`)) AS `Prix total` from (((`client` join `commande` on((`client`.`noClient` = `commande`.`noClient`))) join `article_en_commande` on((`commande`.`noCommande` = `article_en_commande`.`noCommande`))) join `article` on((`article_en_commande`.`noArticle` = `article`.`noArticle`))) group by `commande`.`noCommande` ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article_en_commande`
--
ALTER TABLE `article_en_commande`
  ADD CONSTRAINT `article_en_commande_ibfk_1` FOREIGN KEY (`noCommande`) REFERENCES `commande` (`noCommande`),
  ADD CONSTRAINT `article_en_commande_ibfk_2` FOREIGN KEY (`noArticle`) REFERENCES `article` (`noArticle`),
  ADD CONSTRAINT `article_fk` FOREIGN KEY (`noArticle`) REFERENCES `article` (`noArticle`),
  ADD CONSTRAINT `commande_fk` FOREIGN KEY (`noCommande`) REFERENCES `commande` (`noCommande`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `client_commande_fk` FOREIGN KEY (`noClient`) REFERENCES `client` (`noClient`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
