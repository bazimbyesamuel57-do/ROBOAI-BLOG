-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: roboai_blog_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `roboai_blog_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `roboai_blog_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `roboai_blog_db`;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(225) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'RoboAIdemo','roboai@gmail.com','$2y$10$db0rXsuNGTUt1ochm1O0DO5jtLSXPAgCTZvyO1oV8Gxp7ynmg62J6');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `author` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(255) NOT NULL,
  `author_bio` text NOT NULL,
  `author_image` varchar(255) NOT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `author`
--

LOCK TABLES `author` WRITE;
/*!40000 ALTER TABLE `author` DISABLE KEYS */;
INSERT INTO `author` VALUES (1,'Danielle Kaufman','Vitae non non aspern','images/authors/author_6ae9c1144b9c6.jpeg'),(2,'Ralph Wilkerson','Voluptatem eos est','images/authors/author_6aeb71e20f77a.jpeg'),(3,'Mechelle Briggs','Temporibus magna mol',''),(4,'Marny Clarke','Sapiente sit quia i',''),(5,'Ori Harding','Est consequatur ali','images/authors/author_6aecbf0550866.jpeg'),(6,'Denise Hess','Officiis incidunt a','images/authors/author_6aece540895a6.jpeg'),(7,'Skyler Myers','Sunt incidunt quib','images/authors/author_6aece573dd8e8.jpeg'),(8,'Rana Townsend','Inventore officia di','images/authors/author_6aecf31c81489.jpeg'),(9,'Timon Noble','Est ratione quos rer','images/authors/author_6aecf367a051b.jpeg'),(10,'Stephen Tyler','Illo sed voluptatem','images/authors/author_6af2dc78df0f3.jpeg'),(11,'Keith Houston','Harum irure illo aut','images/authors/author_6af62742a6208.jpeg'),(12,'Constance Tucker','Vero consequatur vi','images/authors/author_6afdc290e813a.jpeg'),(13,'Cassady Jensen','Ad sed labore optio','images/authors/author_6afdce4c03fce.jpeg'),(14,'Blake Glenn','Doloribus nulla adip','images/authors/author_6afdcf0e3dae7.jpeg'),(15,'Dustin Dickson','Nam cupiditate saepe','../images/authors/author_6afdcf6ca4c70.jpeg'),(16,'Nicholas Puckett','Est qui dolorum dolo','../images/authors/author_6afdcf927521d.jpeg'),(17,'Hop Riley','Omnis ullamco volupt','../images/authors/author_6afdd1e722a97.jpeg'),(18,'Morgan Holden','Qui elit fugit dol','../images/authors/author_6afdd2141bf39.jpeg'),(19,'Lars Charles','Qui iure culpa quo a',''),(20,'Gray Miles','Culpa sequi voluptat','../images/authors/author_6afdd272147cb.jpeg'),(21,'Linda Boone','Officia accusantium','images/authors/author_6afdd442dc0a0.jpeg'),(22,'Geoffrey Richmond','Tempor consequuntur','images/authors/author_6afdd4ab9393d.jpeg'),(23,'Maxwell Kane','Iusto ipsum ipsum i','images/authors/author_6afdd79130d21.jpeg'),(24,'Justine Washington','Aliquid non officiis','images/authors/author_6afdd9bf6457d.jpeg'),(25,'Kay Sandoval','Anim voluptatibus do','images/authors/author_6afdda0342558.jpeg'),(26,'Maya Lin','Maya Lin is a tech writer and product strategist who breaks down complex software into practical habits. She has spent eight years helping startups ship tools people actually enjoy using. When she is not drafting posts, she hunts for espresso beans and trails around Seattle.','images/authors/author_6b0011de0e4f8.jpeg'),(27,'Allen Francis','Quae dolores ex non','images/authors/author_6b016ec2542c1.jpeg'),(28,'Cole Miranda','Quasi aliquam rem et','images/authors/author_6b01795c6afcf.jpeg'),(29,'Cooper Delgado','Veniam sed sapiente','images/authors/author_6b01f6ec740cc.jpeg');
/*!40000 ALTER TABLE `author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (5,'Armand Randolph'),(2,'Brooke Peck'),(13,'Camille Church'),(35,'Chandler Hardy'),(14,'Colette Bates'),(20,'Conan Chandler'),(33,'Demetrius Klein'),(6,'Devin Tanner'),(39,'Diana Boone'),(38,'Ethics In Technology'),(16,'Freya Tillman'),(32,'Gail Lynn'),(11,'Galvin Carrillo'),(4,'Garrett Allen'),(3,'Hamish Hendricks'),(17,'Harrison Dudley'),(10,'Heidi Patrick'),(26,'Hilel House'),(15,'Honorato Marks'),(19,'Jasper Freeman'),(41,'Jena Bruce'),(25,'Joel Olson'),(37,'Keelie Black'),(21,'kings'),(7,'Kylan Atkins'),(27,'Leilani Chase'),(34,'Marah Flowers'),(23,'Marny Evans'),(42,'May Blevins'),(18,'Miriam Diaz'),(30,'Morgan Dyer'),(36,'Nicole Dillard'),(8,'Norman Bird'),(9,'Pearl Moody'),(12,'Plato Stewart'),(40,'Quail Salazar'),(24,'Renee Simmons'),(44,'Sheila Robles'),(45,'that'),(28,'Thomas Collins'),(31,'Tiger Norton'),(43,'Time'),(29,'Wallace Morrow'),(1,'web design'),(22,'Zenaida Whitfield');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `Comments_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Email` varchar(225) NOT NULL,
  `Message` text NOT NULL,
  `Date_Time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Comments_id`),
  KEY `fk_post` (`post_id`),
  FULLTEXT KEY `Message` (`Message`),
  FULLTEXT KEY `Name` (`Name`),
  CONSTRAINT `fk_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_us`
--

DROP TABLE IF EXISTS `contact_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_us` (
  `contact_us_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  PRIMARY KEY (`contact_us_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_us`
--

LOCK TABLES `contact_us` WRITE;
/*!40000 ALTER TABLE `contact_us` DISABLE KEYS */;
INSERT INTO `contact_us` VALUES (1,'Dorian','Avery','zuqineba@mailinator.com','Et nostrum sapiente'),(2,'Sara','Navarro','tekoc@mailinator.com','Ab ea ducimus enim'),(3,'Walker','Hill','qebuzykas@mailinator.com','Ullam soluta invento'),(4,'Donovan','Robertson','cyxokag@mailinator.com','Veniam veritatis vo'),(5,'Mariko','Howard','rote@mailinator.com','Duis velit qui itaq'),(6,'Sylvester','Daniels','vovape@mailinator.com','Sunt quos omnis ut e'),(7,'Vielka','Pope','kesetuc@mailinator.com','Quidem ratione magna'),(8,'Meghan','Neal','xohycol@mailinator.com','Fugiat eu nihil ips'),(9,'Laurel','Mccoy','sepu@mailinator.com','Dolor magni officia'),(10,'Shaine','Benton','kijiqip@mailinator.com','Odio repudiandae est'),(11,'Stephen','Wolf','byjekif@mailinator.com','Eos autem do aperiam'),(12,'Judith','Cash','nurytajisy@mailinator.com','Magni quas unde non'),(13,'Fredericka','Harmon','mivonetusy@mailinator.com','Dolore deserunt nobi'),(14,'Ingrid','Massey','ryno@mailinator.com','In dolore quis error'),(15,'Martina','Burns','fynocar@mailinator.com','Vero culpa in porro'),(16,'Daniel','Mcbride','fefutepozy@mailinator.com','Porro quia sunt quia'),(17,'Mikayla','Gutierrez','gukolatagu@mailinator.com','Elit laborum qui er'),(18,'Lacy','Copeland','lizu@mailinator.com','Dolorem velit sit e'),(19,'Nathan','Cruz','zomafeb@mailinator.com','Ipsum voluptate cul'),(20,'Driscoll','Powell','gocapin@mailinator.com','Nihil consequatur Q'),(21,'Aiko','Patrick','norapemu@mailinator.com','Quam fugit exercita'),(22,'Justin','Vazquez','benywigu@mailinator.com','Nam voluptas itaque'),(23,'Evelyn','Battle','tihoqegin@mailinator.com','Ipsa dolorem aliqui'),(24,'Chastity','Schneider','puqyhuqat@mailinator.com','Rerum laboriosam vo'),(25,'Charles','Douglas','vobixi@mailinator.com','Eu error ullam alias'),(26,'Adria','Stevens','beka@mailinator.com','Hic minima esse fug'),(27,'Dylan','Ballard','kemoziveza@mailinator.com','Consequatur Non ita');
/*!40000 ALTER TABLE `contact_us` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `content` text NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `published_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `media` varchar(500) NOT NULL,
  `status` enum('draft','published') NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `fk_admin` (`admin_id`),
  KEY `category_id` (`category_id`),
  KEY `author_id_fk` (`author_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-21 23:17:45
