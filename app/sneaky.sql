-- MySQL dump 10.13  Distrib 8.0.25, for macos11.3 (x86_64)
--
-- Host: localhost    Database: sneaky
-- ------------------------------------------------------
-- Server version	8.0.25

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alarm`
--

DROP TABLE IF EXISTS `alarm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alarm` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_added` date NOT NULL,
  `live_tf_in` char(100) DEFAULT NULL,
  `cum_tf_in` char(100) DEFAULT NULL,
  `_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cum_tf` char(100) DEFAULT NULL,
  `live_tf` char(100) DEFAULT NULL,
  `_type` smallint DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alarm_limits`
--

DROP TABLE IF EXISTS `alarm_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alarm_limits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `live_tf_limit` int DEFAULT NULL,
  `live_cum_limit` int DEFAULT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decoded_packets`
--

DROP TABLE IF EXISTS `decoded_packets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decoded_packets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `_from` char(100) DEFAULT NULL,
  `_to` char(100) DEFAULT NULL,
  `layer` smallint DEFAULT NULL,
  `device` char(100) NOT NULL,
  `packet_info` text,
  `_read` smallint DEFAULT '0',
  `date_added` date NOT NULL,
  `sequence_no` char(100) DEFAULT NULL,
  `eth_type` char(100) DEFAULT NULL,
  `protocal` char(100) DEFAULT NULL,
  `message` char(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16227 DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interfaces`
--

DROP TABLE IF EXISTS `interfaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interfaces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `interface` char(100) NOT NULL,
  `ipv4` char(32) DEFAULT NULL,
  `ipv6` char(70) DEFAULT NULL,
  `subnet` char(64) DEFAULT NULL,
  `description` text,
  `date_added` date DEFAULT NULL,
  `defaultMask` char(20) DEFAULT NULL,
  `_read` smallint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2177 DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `live_packets`
--

DROP TABLE IF EXISTS `live_packets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_packets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `_from` char(100) DEFAULT NULL,
  `_to` char(100) DEFAULT NULL,
  `layer` smallint DEFAULT NULL,
  `device` char(100) NOT NULL,
  `packet_info` text,
  `_read` smallint DEFAULT '0',
  `date_added` date NOT NULL,
  `_time` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=259758 DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `username` char(100) NOT NULL DEFAULT 'admin',
  `password` char(64) NOT NULL DEFAULT '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',
  PRIMARY KEY (`username`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-09-22 13:32:44
