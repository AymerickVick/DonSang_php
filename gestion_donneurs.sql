-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: gestion_donneurs
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `autres_raisons`
--

DROP TABLE IF EXISTS `autres_raisons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autres_raisons` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_Donneur` int NOT NULL,
  `Raison_Supplementaire` text NOT NULL,
  `Date_Ajout` date DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_Donneur` (`ID_Donneur`),
  CONSTRAINT `autres_raisons_ibfk_1` FOREIGN KEY (`ID_Donneur`) REFERENCES `donneurs` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autres_raisons`
--

LOCK TABLES `autres_raisons` WRITE;
/*!40000 ALTER TABLE `autres_raisons` DISABLE KEYS */;
/*!40000 ALTER TABLE `autres_raisons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campagnes`
--

DROP TABLE IF EXISTS `campagnes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campagnes` (
  `ID_Campagne` int NOT NULL AUTO_INCREMENT,
  `Nom_Campagne` varchar(100) NOT NULL,
  `Date_Debut` date NOT NULL,
  `Date_Fin` date NOT NULL,
  `Lieu` varchar(100) NOT NULL,
  `Nombre_Donneurs` int DEFAULT '0',
  PRIMARY KEY (`ID_Campagne`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campagnes`
--

LOCK TABLES `campagnes` WRITE;
/*!40000 ALTER TABLE `campagnes` DISABLE KEYS */;
INSERT INTO `campagnes` VALUES (2,'Pour le Bien-etre','2025-03-23','2025-03-28','Yaounde',20);
/*!40000 ALTER TABLE `campagnes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conditions_sante`
--

DROP TABLE IF EXISTS `conditions_sante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conditions_sante` (
  `ID_Donneur` int NOT NULL,
  `Raison_Non_Eligibilite` varchar(255) DEFAULT NULL,
  `Porteur_HIV` tinyint(1) DEFAULT '0',
  `Porteur_HBS` tinyint(1) DEFAULT '0',
  `Porteur_HCV` tinyint(1) DEFAULT '0',
  `Opere` tinyint(1) DEFAULT '0',
  `Drepanocytaire` tinyint(1) DEFAULT '0',
  `Diabetique` tinyint(1) DEFAULT '0',
  `Hypertendu` tinyint(1) DEFAULT '0',
  `Asthmatique` tinyint(1) DEFAULT '0',
  `Cardiaque` tinyint(1) DEFAULT '0',
  `Tatoue` tinyint(1) DEFAULT '0',
  `Scarifie` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID_Donneur`),
  CONSTRAINT `conditions_sante_ibfk_1` FOREIGN KEY (`ID_Donneur`) REFERENCES `donneurs` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conditions_sante`
--

LOCK TABLES `conditions_sante` WRITE;
/*!40000 ALTER TABLE `conditions_sante` DISABLE KEYS */;
INSERT INTO `conditions_sante` VALUES (4,'',0,0,0,0,0,0,0,0,0,1,0),(6,'Rien a signaler',0,0,0,0,0,0,0,0,0,1,0);
/*!40000 ALTER TABLE `conditions_sante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donneurs`
--

DROP TABLE IF EXISTS `donneurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donneurs` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Date_Remplissage` date NOT NULL,
  `Date_Naissance` date NOT NULL,
  `Niveau_Etude` varchar(50) DEFAULT NULL,
  `Genre` varchar(10) NOT NULL,
  `Taille` float DEFAULT NULL,
  `Poids` float DEFAULT NULL,
  `Situation_Matrimoniale` varchar(20) DEFAULT NULL,
  `Profession` varchar(50) DEFAULT NULL,
  `Arrondissement_Residence` varchar(50) DEFAULT NULL,
  `Quartier_Residence` varchar(50) DEFAULT NULL,
  `Age` int DEFAULT NULL,
  `nom` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donneurs`
--

LOCK TABLES `donneurs` WRITE;
/*!40000 ALTER TABLE `donneurs` DISABLE KEYS */;
INSERT INTO `donneurs` VALUES (4,'2025-03-19','2002-03-21','BAC','F',1.8,80,'Celibataire','Ingenieur','NFOUNDI','OYOM',23,'Hba'),(5,'2025-03-21','2005-05-04','CEP','M',1.82,80,'Celibataire','Enseignant','Yaounde 7','Nkoldongo',19,'Habi'),(6,'2025-03-22','2000-03-23','BAC','M',1.62,70,'Celibataire','Etudiant','Yaounde 8','Bastos',24,'Raman'),(7,'2025-03-22','2001-03-07','BAC','M',1.86,90,'Celibataire','Etudiant','Yaounde 1','Bouda',24,'Abdou'),(8,'2025-03-22','2004-03-11','BAC','M',1.86,90,'Celibataire','Etudiant','Yaounde 1','Bouda',21,'Baba'),(9,'2025-03-22','2004-03-04','BAC','M',1.86,90,'Celibataire','Etudiant','Yaounde 1','Nkolouda',21,'Habiba');
/*!40000 ALTER TABLE `donneurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateurs` (
  `ID_Utilisateur` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mot_de_Passe` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Date_Creation` date DEFAULT NULL,
  `Derniere_Connexion` datetime DEFAULT NULL,
  PRIMARY KEY (`ID_Utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,'babaVict','aymerickbaba29@gmail.com','$2y$10$BaJtpU5LSJQrLSTrwoWVCuak5X795OTCIl/7lZdMHiqsykr6.aVJW','admin','2025-03-21','2025-03-22 16:10:47'),(2,'Habiba','aymerickbaba05@gmail.com','$2y$10$azulWQg7pPljm5I5jx76Z.HoBtUJqOhVxXinKuR1Ht1TZp0upnUAy','organisateur','2025-03-21','2025-03-22 15:01:28'),(3,'Hector','hecror@gmail.com','$2y$10$tZ/itCSXZ91l8wl5Ka1MvuFTZGCGu5yKtLm6ovuqG70bat2n0iwxK','analyste','2025-03-22','2025-03-22 07:51:17');
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-22 20:05:44
