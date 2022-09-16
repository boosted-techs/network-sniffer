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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interfaces`
--

LOCK TABLES `interfaces` WRITE;
/*!40000 ALTER TABLE `interfaces` DISABLE KEYS */;
INSERT INTO `interfaces` VALUES (39,'p2p0','','...','','','2022-09-15',''),(40,'llw0','fe80::ece4:47ff:feed:cd59','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(41,'utun0','fe80::b27d:92ab:acba:8744','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(42,'bridge0','','...','','','2022-09-15',''),(43,'en1','','...','','','2022-09-15',''),(44,'stf0','','...','','','2022-09-15',''),(45,'en0','192.168.59.240','...','ffffff00','','2022-09-15','255.255.255.0'),(46,'utun1','fe80::9d2e:1781:de85:5512','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(47,'lo0','fe80::1','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(48,'en2','','...','','','2022-09-15',''),(49,'gif0','','...','','','2022-09-15',''),(50,'awdl0','fe80::ece4:47ff:feed:cd59','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(51,'en1','','...','','','2022-09-15',''),(52,'en2','','...','','','2022-09-15',''),(53,'gif0','','...','','','2022-09-15',''),(54,'p2p0','','...','','','2022-09-15',''),(55,'awdl0','fe80::ece4:47ff:feed:cd59','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(56,'utun0','fe80::b27d:92ab:acba:8744','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(57,'utun1','fe80::9d2e:1781:de85:5512','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(58,'lo0','fe80::1','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(59,'en0','192.168.59.240','...','ffffff00','','2022-09-15','255.255.255.0'),(60,'llw0','fe80::ece4:47ff:feed:cd59','...','ffffffffffffffff0000000000000000','','2022-09-15','255.255.255.255'),(61,'bridge0','','...','','','2022-09-15',''),(62,'stf0','','...','','','2022-09-15','');
/*!40000 ALTER TABLE `interfaces` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-09-15 23:46:00
