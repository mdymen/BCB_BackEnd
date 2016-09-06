CREATE DATABASE  IF NOT EXISTS `penca` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `penca`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: penca
-- ------------------------------------------------------
-- Server version	5.6.21

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
-- Table structure for table `championship`
--

DROP TABLE IF EXISTS `championship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `championship` (
  `ch_id` int(11) NOT NULL AUTO_INCREMENT,
  `ch_nome` varchar(100) NOT NULL,
  `ch_idfixture` int(11) DEFAULT NULL,
  `ch_started` tinyint(1) DEFAULT '0',
  `ch_atualround` int(4) DEFAULT '1',
  PRIMARY KEY (`ch_id`),
  UNIQUE KEY `ch_nome_UNIQUE` (`ch_nome`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `championship`
--

LOCK TABLES `championship` WRITE;
/*!40000 ALTER TABLE `championship` DISABLE KEYS */;
INSERT INTO `championship` VALUES (1,'Brasileirão 2016',NULL,0,1);
/*!40000 ALTER TABLE `championship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fixture`
--

DROP TABLE IF EXISTS `fixture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fixture` (
  `fx_id` int(11) NOT NULL AUTO_INCREMENT,
  `fx_match` int(11) NOT NULL,
  PRIMARY KEY (`fx_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fixture`
--

LOCK TABLES `fixture` WRITE;
/*!40000 ALTER TABLE `fixture` DISABLE KEYS */;
/*!40000 ALTER TABLE `fixture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match`
--

DROP TABLE IF EXISTS `match`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match` (
  `mt_id` int(11) NOT NULL AUTO_INCREMENT,
  `mt_idteam1` int(11) NOT NULL,
  `mt_idteam2` int(11) NOT NULL,
  `mt_date` datetime DEFAULT NULL,
  `mt_goal1` int(2) DEFAULT '0',
  `mt_goal2` int(2) DEFAULT '0',
  `mt_idchampionship` int(11) NOT NULL,
  `mt_round` int(3) NOT NULL,
  `mt_played` binary(1) DEFAULT '0',
  PRIMARY KEY (`mt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match`
--

LOCK TABLES `match` WRITE;
/*!40000 ALTER TABLE `match` DISABLE KEYS */;
INSERT INTO `match` VALUES (41,46,45,'2004-09-16 00:00:00',0,0,1,1,'1'),(42,47,48,'2004-09-16 00:00:00',0,0,1,1,'0'),(43,49,50,'2004-09-16 00:00:00',0,0,1,1,'0'),(44,51,52,'2004-09-16 00:00:00',0,0,1,1,'0'),(45,53,54,'2004-09-16 00:00:00',0,0,1,1,'0'),(46,55,56,'2004-09-16 00:00:00',0,0,1,1,'0'),(47,57,58,'2004-09-16 00:00:00',0,0,1,1,'0'),(48,59,60,'2004-09-16 00:00:00',0,0,1,1,'0'),(49,61,62,'2004-09-16 00:00:00',0,0,1,1,'0'),(50,63,64,'2004-09-16 00:00:00',0,0,1,1,'0');
/*!40000 ALTER TABLE `match` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penca`
--

DROP TABLE IF EXISTS `penca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `penca` (
  `pn_id` int(11) NOT NULL AUTO_INCREMENT,
  `pn_name` varchar(255) NOT NULL,
  `pn_value` decimal(3,0) NOT NULL,
  `pn_iduser` int(11) DEFAULT NULL,
  `pn_valueaccumulated` decimal(3,2) NOT NULL,
  `pn_idchampionship` int(11) NOT NULL,
  `pn_justfriends` binary(1) DEFAULT '0',
  `pn_password` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`pn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penca`
--

LOCK TABLES `penca` WRITE;
/*!40000 ALTER TABLE `penca` DISABLE KEYS */;
INSERT INTO `penca` VALUES (1,'penc1',10,1,0.00,1,'0',NULL),(2,'penca2',15,1,0.00,1,'0',NULL),(3,'p1',40,1,0.00,1,'0',NULL),(4,'penca1 teste 28-08-2016',10,1,0.00,1,NULL,''),(5,'Martin Dymenstein',40,1,0.00,1,'o','xx');
/*!40000 ALTER TABLE `penca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `result`
--

DROP TABLE IF EXISTS `result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `result` (
  `rs_id` int(11) NOT NULL AUTO_INCREMENT,
  `rs_idmatch` int(11) NOT NULL,
  `rs_res1` int(11) NOT NULL,
  `rs_res2` int(11) NOT NULL,
  `rs_date` datetime NOT NULL,
  `rs_idpenca` int(11) NOT NULL,
  `rs_iduser` int(11) NOT NULL,
  `rs_round` int(3) NOT NULL,
  `rs_result` varchar(100) DEFAULT NULL,
  `rs_points` int(3) DEFAULT '0',
  PRIMARY KEY (`rs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `result`
--

LOCK TABLES `result` WRITE;
/*!40000 ALTER TABLE `result` DISABLE KEYS */;
INSERT INTO `result` VALUES (11,41,9,2,'2004-09-16 00:00:00',1,1,1,NULL,0),(12,42,3,4,'2004-09-16 00:00:00',1,1,1,NULL,1),(13,43,5,7,'2004-09-16 00:00:00',1,1,1,NULL,1),(14,44,8,9,'2004-09-16 00:00:00',1,1,1,NULL,1),(15,45,0,0,'2004-09-16 00:00:00',1,1,1,NULL,1),(16,46,0,0,'2004-09-16 00:00:00',1,1,1,NULL,1),(17,47,0,0,'2004-09-16 00:00:00',1,1,1,NULL,1),(18,48,0,0,'2004-09-16 00:00:00',1,1,1,NULL,1),(19,49,0,0,'2004-09-16 00:00:00',1,1,1,NULL,1),(20,50,0,0,'2004-09-16 00:00:00',1,1,1,NULL,1);
/*!40000 ALTER TABLE `result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `tm_id` int(11) NOT NULL AUTO_INCREMENT,
  `tm_name` varchar(100) NOT NULL,
  `tm_idchampionship` int(11) NOT NULL,
  `tm_points` int(11) DEFAULT '0',
  PRIMARY KEY (`tm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team`
--

LOCK TABLES `team` WRITE;
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` VALUES (45,'Palmeiras',1,0),(46,'Atlético-MG',1,0),(47,'Flamengo',1,0),(48,'Santos',1,0),(49,'Grêmio',1,0),(50,'Corinthians',1,0),(51,'Atlético-PR',1,0),(52,'Ponte Preta',1,0),(53,'Chapecoense',1,0),(54,'Fluminense',1,0),(55,'São Paulo',1,0),(56,'Sport',1,0),(57,'Botafogo',1,0),(58,'Vitória',1,0),(59,'Internacional',1,0),(60,'Coritiba',1,0),(61,'Figueirense',1,0),(62,'Cruzeiro',1,0),(63,'Santa Cruz',1,0),(64,'América-MG',1,0);
/*!40000 ALTER TABLE `team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `us_id` int(11) NOT NULL AUTO_INCREMENT,
  `us_username` varchar(155) NOT NULL,
  `us_password` varchar(45) NOT NULL,
  `us_cash` varchar(45) DEFAULT '0',
  PRIMARY KEY (`us_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'mdymen','3345531','0');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_penca`
--

DROP TABLE IF EXISTS `user_penca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_penca` (
  `up_id` int(11) NOT NULL AUTO_INCREMENT,
  `up_idpenca` int(11) NOT NULL,
  `up_iduser` int(11) NOT NULL,
  `up_puntagem` int(11) DEFAULT NULL,
  PRIMARY KEY (`up_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_penca`
--

LOCK TABLES `user_penca` WRITE;
/*!40000 ALTER TABLE `user_penca` DISABLE KEYS */;
INSERT INTO `user_penca` VALUES (1,1,1,1);
/*!40000 ALTER TABLE `user_penca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'penca'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-09-06 12:32:15
