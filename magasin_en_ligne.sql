-- MySQL dump 10.16  Distrib 10.1.36-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: magasin_en_ligne
-- ------------------------------------------------------
-- Server version	10.1.36-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Creating database `magasin_en_ligne`
--
DROP DATABASE IF EXISTS magasin_en_ligne;
CREATE DATABASE magasin_en_ligne CHARACTER SET utf8 COLLATE utf8_general_ci;
USE magasin_en_ligne;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article` (
  `noArticle` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categorie` varchar(25) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `cheminImage` varchar(255) DEFAULT NULL,
  `prixUnitaire` decimal(10,2) DEFAULT NULL,
  `quantiteEnStock` int(10) DEFAULT '0',
  `quantiteDansPanier` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`noArticle`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article`
--

LOCK TABLES `article` WRITE;
/*!40000 ALTER TABLE `article` DISABLE KEYS */;
INSERT INTO `article` VALUES (1,'Cheveux','Alikay Naturals Lemongrass Leave In Conditioner','images/alikay_naturals_lemongrass_leave_in_conditioner.jpg',28.99,8,0),(2,'Cheveux','ApHogee Curlific! Texture Treatment','images/aphogee_curlific_texture_treatment.jpg',13.99,2,0),(3,'Cheveux','As I Am Coconut Cowash Cleansing Conditioner','images/as_i_am_coconut_cowash.jpeg',15.99,8,0),(4,'Maquillage','Ardell Magnetic Lashes Double Wispies','images/ardell_magnetic_lashes_double_wispies.jpg',21.99,7,0),(5,'Maquillage','Ardell Natural Lashes Wispies Brown','images/ardell_natural_lashes_wispies_brown.jpg',6.99,8,0),(6,'Cheveux','BaByliss Pro Nano Titanium OPTIMA 3100 Straightening Iron','images/babylisspro_nano_titanium_optima_3100_straightening_iron_1_inch.jpg',271.99,9,0),(7,'Hommes','Beard Guyz Beard Care And Grooming Kit','images/beard_guyz_beard_care_grooming_kit.jpg',29.99,10,0),(8,'Cheveux','Camille Rose Naturals Curl Maker','images/camille_rose_curl_maker.jpg',41.99,10,0),(9,'Cheveux','Cantu Shea Butter For Natural Hair Coconut Curling Cream','images/cantu_coconut_curling_cream.jpg',31.99,10,0),(10,'Cheveux','Carol\'s daughter Black Vanilla Moisture And Shine Hydrating Conditioner','images/carols_daughter_black_Vanilla_moisture_and_shine_hydrating_conditioner.jpg',29.99,10,0),(11,'Cheveux','Carol\'s daughter Hair Milk Curl Defining Moisture Mask','images/carols_daughter_hair_milk_curl_defining_moisture_mask.jpg',34.99,10,0),(12,'Cheveux','Curls Blueberry Bliss Curl Control Paste','images/curls_blueberry_control_paste.jpg',15.99,10,0),(13,'Cheveux','DevaCurl Supercream Coconut Curl Styler','images/devacurl_supercream_coconut_curl_styler.jpg',55.99,10,0),(14,'Peau','Dudu-Osun Black Soap','images/dudu_osun_black_soap.jpg',5.99,7,0),(15,'Maquillage','DUO Strip Lash Adhesive Tube Dark Tone','images/duo_strip_lash_adhesive_tube_dark_tone.jpg',8.99,6,0),(16,'Cheveux','Eco Styler Olive Oil Styling Gel','images/eco_styler_olive_oil_gel.jpeg',9.99,10,0),(17,'Cheveux','EDEN BodyWorks Coconut Shea Cleansing CoWash','images/eden_body_works_coconut_shea_cleansing_cowash.jpg',17.99,10,0),(18,'Cheveux','Shea Moisture Jamaican Black Castor Oil Strengthen And Grow Thermal Protectant','images/shea_moisture_jbco_thermal_protectant.jpg',19.99,10,0),(19,'Cheveux','Kera Care Edge Tamer','images/kera_care_edge_tamer.jpg',11.99,10,0),(20,'Cheveux','Kinky Curly Come Clean Shampoo','images/kinky_curly_come_clean_shampoo.jpg',21.99,10,0),(21,'Cheveux','Maui Moisture Curl Quench+ Coconut Oil Curl Milk','images/maui_moisture_curl_quench_coconut_oil_curl_milk.jpg',10.99,9,0),(22,'Cheveux','Mielle Organics Babassu Mint Deep Conditioner','images/mielle_organics_babassu_oil_mint_deep_conditioner.jpg',22.99,10,0),(23,'Cheveux','Moroccanoil Oil Treatment','images/moroccanoil_treatment.jpg',59.99,9,0),(24,'Peau','TGIN Argan Replenishing Hair And Body Serum','images/tgin_argan_replenishing_hair_body_serum.jpg',24.99,10,0),(25,'Cheveux','Denman Brush D4 Black','images/denman_brush_d4_black.jpg',34.99,10,0),(26,'Hommes','The Mane Choice Head Honcho Hair And Beard Oil + Butter = The Balm ','images/tmc_head_honcho_the_balm.jpg',16.99,10,0),(27,'Hommes','Shea Moisture Maracuja Oil And Shea Butter Full Beard Detangler','images/shea_moisture_maracuja_oil_beard_detangler.jpg',15.99,8,0),(28,'Hommes','Uncle Jimmy Beard Softener Conditioning Balm','images/uncle_jimmy_beard_softener.jpg',19.99,9,0),(29,'','','blablabla',NULL,0,0),(30,NULL,'Array','images/design_essentials_define_and_shine_2_in_1_dry_finishing_lotion.jpg',NULL,NULL,0),(31,NULL,'Design Essentials Define And Shine 2 In 1 Dry Finishing Lotion','images/design_essentials_define_and_shine_2_in_1_dry_finishing_lotion.jpg',NULL,NULL,0),(32,NULL,'Design Essentials Define And Shine 2 In 1 Dry Finishing Lotion','images/design_essentials_define_and_shine_2_in_1_dry_finishing_lotion.jpg',NULL,NULL,0),(33,NULL,'Design Essentials Define And Shine 2 In 1 Dry Finishing Lotion','images/design_essentials_define_and_shine_2_in_1_dry_finishing_lotion.jpg',NULL,NULL,0),(34,'Cheveux',NULL,NULL,11.95,10,0),(35,'Cheveux',NULL,NULL,11.95,10,0),(36,NULL,'Design Essentials Define And Shine 2 In 1 Dry Finishing Lotion','images/design_essentials_define_and_shine_2_in_1_dry_finishing_lotion.jpg',NULL,0,0),(37,'Cheveux','Design Essentials Define And Shine 2 In 1 Dry Finishing Lotion','images/design_essentials_define_and_shine_2_in_1_dry_finishing_lotion.jpg',11.95,10,0);
/*!40000 ALTER TABLE `article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_en_commande`
--

DROP TABLE IF EXISTS `article_en_commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_en_commande` (
  `noCommande` int(10) unsigned NOT NULL,
  `noArticle` int(10) unsigned NOT NULL,
  `quantite` int(10) unsigned NOT NULL,
  PRIMARY KEY (`noCommande`,`noArticle`),
  KEY `commande_fk` (`noCommande`),
  KEY `article_fk` (`noArticle`),
  CONSTRAINT `article_en_commande_ibfk_1` FOREIGN KEY (`noCommande`) REFERENCES `commande` (`noCommande`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `article_en_commande_ibfk_2` FOREIGN KEY (`noArticle`) REFERENCES `article` (`noArticle`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_en_commande`
--

LOCK TABLES `article_en_commande` WRITE;
/*!40000 ALTER TABLE `article_en_commande` DISABLE KEYS */;
INSERT INTO `article_en_commande` VALUES (1,3,1),(2,11,1),(2,17,1),(3,2,1),(3,5,1),(3,8,1),(4,1,1),(4,4,1),(4,6,1),(4,7,1),(5,20,1),(6,6,1),(6,14,4),(6,15,1),(6,16,1),(7,7,1),(7,14,4),(7,22,2),(8,4,3),(8,14,4),(8,17,2),(9,2,3),(10,2,1),(10,3,2),(11,4,2),(12,1,2),(12,5,2),(12,28,1),(13,1,2),(13,5,2),(13,28,1),(14,1,2),(14,5,2),(14,28,1),(15,1,2),(15,5,2),(15,28,1),(16,1,2),(16,5,2),(16,28,1),(17,6,1),(17,14,3),(17,27,2),(18,21,1),(18,23,1),(19,2,1),(19,4,1),(29,2,3),(29,15,4),(30,2,3),(30,15,4),(31,2,3);
/*!40000 ALTER TABLE `article_en_commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commande` (
  `noCommande` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dateCommande` datetime NOT NULL,
  `noMembre` int(10) unsigned NOT NULL,
  `paypalOrderId` char(17) NOT NULL,
  PRIMARY KEY (`noCommande`),
  KEY `commande_noclient_idx` (`noMembre`),
  CONSTRAINT `client_commande_fk` FOREIGN KEY (`noMembre`) REFERENCES `membre` (`noMembre`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande`
--

LOCK TABLES `commande` WRITE;
/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
INSERT INTO `commande` VALUES (1,'2019-04-02 19:00:16',1,'PG9N8746L66G574L7'),(2,'2019-04-02 19:00:17',2,'Z6G6FLEUYAS5QVDKG'),(3,'2019-04-02 19:00:17',3,'DJ7PN4N20N23W68AA'),(4,'2019-04-02 19:00:17',4,'2QW9JOV2MQSIK62UO'),(5,'2019-04-02 19:00:17',4,'LG7M12RBTV2YU85E0'),(6,'2019-04-14 11:31:08',1,'1FP03323RH1890633'),(7,'2019-04-14 11:50:14',2,'51U788521W1105035'),(8,'2019-04-14 12:01:28',5,'6SP278845A189145G'),(9,'2019-04-14 12:31:11',15,'47506035TU448745G'),(10,'2019-04-23 23:50:41',24,'8SP47294TD071174H'),(11,'2019-04-24 00:40:36',2,'22J515486N2962932'),(12,'2019-04-25 21:32:50',10,'6VH66797M3545032V'),(13,'2019-04-25 21:36:50',16,'0BG24831D59700828'),(14,'2019-04-25 21:39:19',12,'9CN53198D46468411'),(15,'2019-04-25 21:45:54',14,'5AK647786A433245Y'),(16,'2019-04-25 21:55:59',17,'0YJ40016H08239404'),(17,'2019-04-25 22:35:02',19,'4AW725945X636102U'),(18,'2019-04-25 22:47:23',25,'9R613830CD671870Y'),(19,'2019-04-26 13:35:49',4,'45P55170576160821'),(29,'2019-05-10 20:27:59',35,'4VW3XH43TUB400204'),(30,'2019-05-10 20:34:29',36,'4VW3XH43TUB400204'),(31,'2019-05-10 20:43:55',37,'S8TQ8WL75WVQP2V7D');
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membre`
--

DROP TABLE IF EXISTS `membre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membre` (
  `noMembre` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomMembre` varchar(50) DEFAULT NULL,
  `prenomMembre` varchar(50) DEFAULT NULL,
  `categorie` tinyint(1) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `codePostal` varchar(10) NOT NULL,
  `noTel` varchar(25) DEFAULT NULL,
  `courriel` varchar(255) DEFAULT NULL,
  `motDePasse` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`noMembre`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membre`
--

LOCK TABLES `membre` WRITE;
/*!40000 ALTER TABLE `membre` DISABLE KEYS */;
INSERT INTO `membre` VALUES (1,'Collins','Renee B',1,'2394 St Jean Baptiste St','Montréal','Québec','G0M 1W0','819-548-2143','w8drqcfwb2o@payspun.com','$2y$10$BZ1RESQ8pMrZCIfqpmU/q.33O2zj9cNDGCaSTebT45pfjMSvgRKIK'),(2,'Kirk','Oscar M',1,'4277 40th Street','Calgary','Alberta','T2C 2P3','403-236-7859','xt4v02xxx0g@thrubay.com','$2y$10$/FrNytRpeKWUlUgolLZ0nuDwp7b15OE2hhtU5AtVtWYUaDWwox0ZC'),(3,'Delossantos','Julia',1,'4603 Yonge Street','Toronto','Ontario','M4W 1J7','416-301-6292','sowl5hn2y9k@thrubay.com','$2y$10$rypsxigmgz54J7mRYh8bP.p1ArsJkH4WXRIjSEIw9tYL4r/1l0vO2'),(4,'Desantiago','Ruben J',1,'1097 Mountain Rd','Moncton','Nouveau-Brunswick','E1C 1H6','506-961-5510','e02n5x6ptto@payspun.com','$2y$10$1YgeU1qZ97FV20ZZhMH6I.wjDhqLXGS2I4dFuOE5ElbynAt06njZu'),(5,'Rivera','Linda M',1,'496 2nd Street','Oakbank','Manitoba','R0E 1J0','204-444-1472','os8l3vscf7r@fakemailgenerator.net','$2y$10$2bcukBEi8wS07CbV5t2ape.R5wWqeuMO4KCfKsE82AYC51LlLOS6K'),(6,'Pierre','Nadine',2,'9429, avenue Christophe-Colomb','Montréal','Québec','H2M 1Z7','514-830-6966','nadine_pierre@hotmail.com','$2y$10$dnLQYO0RCutnKQp2u0smfOVkSZV8hRPd50E0p6jkjN1QS6VCKPlpq'),(7,'Soucy','Warrane',1,'4686 Roger Street','Oyster River','Colombie-Britannique','V9W 5N0','250-337-5002','warranesoucy@rhyta.com ','$2y$10$Ml5SaFSnXUvBBFXyZlDOrO9/VY3MCg1.e9O80kSR4ogw7qwCZFe.y'),(8,'Kou','Mingmei',1,'3375 5th Avenue','Fort Vermilion','Alberta','T0H 1N0','780-927-6217','mingmeikuo@armyspy.com ','$2y$10$6JxabKx9PCYf1/jiMrEWf.oUv9/ehYtzWICQzd5MVJGT6I/vQGkNC'),(9,'Antoun','Rais Fathi',1,'3564 St Marys Rd','Winnipeg','Manitoba','R3C 0C4','204-292-9473','raisfathiantoun@rhyta.com','$2y$10$QuFdjvldMBkSY6pcMJQ35OsnfOKjEdJPHt4xbou8r6egENe0JKUbW'),(10,'Chatigny','Dalmace',1,'3008 No. 3 Road','Richmond','Colombie-Britannique','V6X 2B8','604-214-5060','dalmacechatigny@armyspy.com','$2y$10$oNsgsLfw.LUXh4w/Egz/mu9z7vTc6SjqZc3O3GI6tX/JSQJK4pBOe'),(11,'Michel','Annot',1,'2516 Carling Avenue','Ottawa','Ontario','K1Z 7B5','613-355-2003','annotmichel@jourrapide.com ','$2y$10$/IBY46aPZXnOWO6ckCJXqOqq17KlEg4Psmwj9CR3ldRFXvQKalWuW'),(12,'Ahmad Ba','Mufeed',1,'4458 Reserve St','Inverary','Ontario','K0H 1X0','613-353-0555','mufeedahmadBa@teleworm.us','$2y$10$v.7bxYiox9JjG2Kh6W0GWOXmRNtuBmcisLe4KcmWJHxtHKGqCIAve'),(13,'Sousa Pereira','Brenda',1,'4644 Dry Pine Bay Rd','Azilda','Ontario','H0M 1B0','705-983-0538','brendasousapereira@jourrapide.com ','$2y$10$MbyfLftqMtK0LlvrcFf/0esXrwQGGuHPEX6GDQ7tQ04x28xYYWLoO'),(14,'Robel','Zewdi',1,'3336, Water Street','Kitchener','Ontario','N2H 5A5','519-744-5326','zewdirobel@teleworm.us','$2y$10$DanvUO7FWvBACexygdEjQ.OIKQVa65vlao2XBDgdfSiNwlh23ZONK'),(15,'Chieloka','Nkechiyerem',1,'4262 Orenda Rd','Brampton','Ontario','L6W 1Z2','905-451-0542','nkechiyeremchieloka@rhyta.com','$2y$10$jNklsBGryLVwzB3XAbc/I.j3mM095keo2ja9vxsNIE8HyqpGhpsza'),(16,'Arteaga Garay','Denna',1,'878 Speers Road','Brampton','Ontario','G0H 1H0','905-790-4905','dennaarteagagaray@rhyta.com ','$2y$10$xcOX5PYaK4/wswiSigNm4ucWBhblRX577Ol9GT/wmRcWyLd7iVSpe'),(17,'Olivas','Jaclyn Lira',1,'3767 Wallace Street','Nanaimo','Colombie-Britannique','V9R 3A8','250-755-5941','jaclynliraolivas@armyspy.com ','$2y$10$kFhiCFd45w1fysaHIsiFYO8AurtG0wdaVRp5wViCLXLXy3b9o9mx6'),(18,'Bernier','Geneviève',1,'1805 Quayside Dr','New Westminster','Colombie-Britannique','V3M 6A1','604-764-5263','genevievebernier@dayrep.com ','$2y$10$OlthK/n7lNhLPOdF5Y0ZCeoOUAsP2mLS3FdQwP7w8G9BX8UdlKvYq'),(19,'Bondy','Favor',1,'3295 2nd Street','Lac Du Bonnet','Manitoba','R0E 1A0','204-345-8196','favorbondy@armyspy.com','$2y$10$ES72kybSVGtF6a5Z/ohc5uxfQv88OuBkHVcs2hXtySk3Dc2i1Gp1a'),(20,'Pradel-Tessier','Shao',2,'123 rue ABC','Montréal','Québec','A1B 2C3','514-123-4567','Shao.P.Tessier@gmail.com','$2y$10$HIw1EcFTVbu3BV4xggYqPu9Dq.zI/0rsrQCZqIpBk2d2U8bzKWwle'),(21,'Pham','Binh',2,'456 rue DEF','Montréal','Québec','B1C 2D3','514-234-5678','binh.pham@gmail.com','$2y$10$o89MaPmHmCWFLMkSualWsunVeAtzeURRQobGDq2o2E79E1beA/9aS'),(24,'Test','Nadine',1,'123, rue Test appartement 100','Montréal','Québec','A1B 2C3','514-123-4567','nadine@test.com','$2y$10$jFhqBRWL55.QGe5Ek.xIPehMQS5AIHzGxtnDmGat4eiEgUukQNu9u'),(25,'Sandyman','Menegilda',1,'2470 Findlay Creek Road','Creston','Colombie-Britannique','V0B 1G0','250-402-3442','MenegildaSandyman@dayrep.com','$2y$10$H41vXFpDsEK6kuIZx4tInO6zAW9ncP3oIQuaVoPIB4CWCv52Km8U6'),(35,'Rivard','Corette',0,'1857 rue des Églises Est','Nedelec','Québec','J0Z 2Z0','819-784-8085','coretterivard@jourrapide.com ',''),(36,'Rivard','Corette',0,'1857 rue des Églises Est','Nedelec','Québec','J0Z 2Z0','819-784-8085','coretterivard2@hotmail.com ',''),(37,'Rivard','Corette',0,'1857 rue des Églises Est','Nedelec','Québec','J0Z 2Z0','819-784-8085','coretterivard3@hotmail.com ','');
/*!40000 ALTER TABLE `membre` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-05-15 19:16:59
