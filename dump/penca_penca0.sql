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
  `pn_icone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`pn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penca`
--

LOCK TABLES `penca` WRITE;
/*!40000 ALTER TABLE `penca` DISABLE KEYS */;
INSERT INTO `penca` VALUES (1,'penc1',10,1,0.00,1,'0',NULL,'fa fa-globe green'),(2,'penca2',15,1,0.00,1,'0',NULL,'fa fa-location-arrow red'),(3,'p1',40,1,0.00,1,'0',NULL,'fa fa-meh-o yellow'),(4,'penca1 teste 28-08-2016',10,1,0.00,1,NULL,'','fa fa-globe green'),(5,'Martin Dymenstein',0,1,0.00,1,'o','xx','fa fa-plane blue'),(13,'',0,1,0.00,1,NULL,NULL,NULL),(14,'pencaTest1',20,1,0.00,1,NULL,NULL,NULL),(15,'',0,1,0.00,1,NULL,NULL,NULL),(16,'',0,1,0.00,1,NULL,NULL,NULL),(17,'',0,1,0.00,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `penca` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-14 12:10:56
