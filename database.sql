-- Wildflower Studio ecommerce database export.
-- Includes current user, order, and contact data; handle this file privately.
-- Import into a new/empty ecommerce database; project tables are dropped and recreated.
-- Product/category image files are stored separately under uploads/ and must be copied with the project.

-- MySQL dump 10.13  Distrib 8.2.0, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: ecommerce
-- ------------------------------------------------------
-- Server version	8.2.0

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
-- Current Database: `ecommerce`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ecommerce` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `ecommerce`;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(80) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','$2y$10$5xo1.cjKaRGY6ZdGDM32XOEcS6l5qv4n1b1rxMQWkcm8ZBdT0HFyy');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Rose Bouquets','14f03e0b6fb21249f007dceca058b024.jpg','Timeless roses for love and romance.','active','2026-10-14 09:21:00'),(2,'Wildflower Bouquets','b998e3ada2c4680be1214396872d0f07.jpg','Naturally beautiful, colourful blooms.','active','2026-10-14 09:22:00'),(3,'Sunflower Bouquets','f5a434323e38ab4fbd5f4d3bc16a9831.jpg','Bright flowers to spread happiness.','active','2026-10-14 09:22:12'),(4,'Tulip Bouquets','e66188a475de9b528b3f5116e68cffb7.jpg','Elegant blooms for thoughtful gifting.','active','2026-10-14 09:22:26'),(5,'Flower Gift Boxes','acca407caa7feb90e238b76be43373e7.jpg','Beautiful floral gifts for special moments.','active','2026-10-14 09:22:33');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'Aarav Patel',NULL,'aarav@example.com','I would like to know more about your flower bouquets.','2026-10-13 06:07:23'),(2,'Meera Shah',NULL,'meera@example.com','Do you provide wedding flower arrangements?','2026-10-13 06:07:23'),(3,'Riya Desai',NULL,'riya@example.com','Please share your delivery details.','2026-10-13 06:07:23'),(4,'Nikhil Suthar',NULL,'nikhilsuthar152@gmail.com','Subject: General Inquiry\nPhone: 1122334455\nMessage:\nGood','2026-10-14 08:26:58'),(5,'Nikhil Suthar',NULL,'nikhilsuthar152@gmail.com','Subject: General Inquiry\nPhone: 1122334455\nMessage:\nGood','2026-10-14 08:30:21'),(6,'Nikhil Suthar',NULL,'nikhilsuthar152@gmail.com','Subject: General Inquiry\nPhone: 1122334455\nMessage:\nGood','2026-10-14 08:30:26'),(7,'Nikhil Suthar','1122334455','nikhilsuthar152@gmail.com','Good','2026-10-14 08:35:46');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,'Wildflower Bouquet',1499.00,1),(2,2,7,'Forever Yours Gift Set',1999.00,1),(3,3,7,'Forever Yours Gift Set',1999.00,1),(4,4,1,'Wildflower Bouquet',1499.00,1);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `billing_address` text,
  `shipping_address` text,
  `city` varchar(80) DEFAULT NULL,
  `state` varchar(80) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(30) DEFAULT 'Pending',
  `order_status` varchar(30) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,'Demo Customer','customer@example.com','9876543210','12 Flower Street','12 Flower Street','Ahmedabad','Gujarat','380001',1499.00,'COD','Paid','Delivered','2026-10-13 06:06:48'),(2,3,'Nikhil Suthar','nikhilsuthar152@gmail.com','1122334455','','E-504 Yogeshwar Homes','Ahmedabad','Gujarat','382470',1999.00,'UPI','Cancelled','Cancelled','2026-10-13 08:10:43'),(3,3,'Nikhil Suthar','nikhilsuthar152@gmail.com','1122334455','E-504 Yogeshwar Homes','E-504 Yogeshwar Homes','Ahmedabad','Gujarat','382470',1999.00,'Cash on Delivery','Cancelled','Processing','2026-10-14 08:13:59'),(4,3,'Nikhil Suthar','nikhilsuthar152@gmail.com','1122334455','E-504 Yogeshwar Homes','E-504 Yogeshwar Homes','Ahmedabad','Gujarat','382470',1499.00,'Card','Pending','Confirmed','2026-10-14 08:19:54');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `stock` int DEFAULT '0',
  `category` varchar(100) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (2,'Blush Pink Roses','Soft pink roses for elegant and heartfelt moments.',1199.00,120.00,20,NULL,1,'1791972301_BlushPinkRoses.jpg','active','2026-10-14 10:05:01'),(3,'White Elegance Roses','Elegant white roses for peaceful and meaningful occasions.',1399.00,210.00,20,NULL,1,'1791972585_WhiteEleganceRoses.jpg','active','2026-10-14 10:09:45'),(4,'Pink & Red Romance','A beautiful mix of pink and red roses for someone special.',1499.00,150.00,20,NULL,1,'1791972685_Pink-RedRomance.jpg','active','2026-10-14 10:11:25'),(5,'Royal Rose Bouquet','A luxurious rose bouquet for unforgettable celebrations.',1799.00,270.00,20,NULL,1,'1791972789_RoyalRoseBouquet.jpg','active','2026-10-14 10:13:09'),(6,'Meadow Dreams','A charming mix of wildflowers inspired by natural meadows.',999.00,100.00,20,NULL,2,'1791973850_MeadowDreams.jpg','active','2026-10-14 10:30:50'),(7,'Pastel Wildflower Mix','Soft pastel blooms arranged for a gentle, elegant look.',1199.00,120.00,20,NULL,2,'1791973920_PastelWildflowerMix.jpg','active','2026-10-14 10:32:00'),(1,'Classic Red Roses','A timeless red rose bouquet to express love and romance.',1299.00,130.00,20,NULL,1,'1791972001_RoseBouquets.jpg','active','2026-10-14 10:00:01'),(8,'Rustic Garden Bouquet','A rustic floral arrangement with natural garden charm.',1099.00,55.00,20,NULL,2,'1791973981_RusticGardenBouquet.jpg','active','2026-10-14 10:33:01'),(9,'Summer Meadow Blooms','Colourful seasonal-style blooms that bring summer happiness.',1299.00,195.00,20,NULL,2,'1791974050_SummerMeadowBlooms.jpg','active','2026-10-14 10:34:10'),(10,'Cottage Garden Charm','A romantic mix of garden-inspired flowers for thoughtful gifting.',1499.00,150.00,20,NULL,2,'1791974101_CottageGardenCharm.jpg','active','2026-10-14 10:35:01'),(11,'Sunshine Blooms','Bright sunflowers to bring warmth and happiness.',899.00,90.00,20,NULL,3,'1791974401_SunshineBlooms.jpg','active','2026-10-14 10:40:01'),(12,'Golden Hour Bouquet','Golden sunflowers arranged for a cheerful gift.',1099.00,110.00,20,NULL,3,'1791974738_GoldenHourBouquet.jpg','active','2026-10-14 10:45:38'),(13,'Sunflower & Daisy Mix','A sunny combination of sunflowers and delicate daisies.',1199.00,180.00,20,NULL,3,'1791974760_SunflowerDaisyMix.jpg','active','2026-10-14 10:46:00'),(14,'Sunny Day Surprise','A cheerful sunflower bouquet for everyday surprises.',999.00,50.00,20,NULL,3,'1791974773_SunnyDaySurprise.jpg','active','2026-10-14 10:46:13'),(15,'Golden Fields Bouquet','A striking sunflower arrangement inspired by golden fields.',1399.00,210.00,20,NULL,3,'1791974787_GoldenFieldsBouquet.jpg','active','2026-10-14 10:46:27'),(16,'Blush Pink Tulips','Graceful pink tulips for sweet and thoughtful gestures.',999.00,100.00,20,NULL,4,'1791975399_BlushPinkTulips.jpg','active','2026-10-14 10:56:39'),(17,'Pure White Tulips','Elegant white tulips with a clean, timeless appeal.',1199.00,120.00,20,NULL,4,'1791975438_PureWhiteTulips.jpg','active','2026-10-14 10:57:18'),(18,'Sunset Tulip Mix','Colourful tulips in warm shades for joyful occasions.',1299.00,195.00,20,NULL,4,'1791975489_SunsetTulipMix.jpg','active','2026-10-14 10:58:09'),(19,'Lavender Dream Tulips','Beautiful lavender-toned tulips for a soft, elegant gift.',1399.00,140.00,20,NULL,4,'1791975532_LavenderDreamTulips.jpg','active','2026-10-14 10:58:52'),(20,'Spring Love Tulips','Fresh spring-inspired tulips to celebrate meaningful moments.',1599.00,240.00,20,NULL,4,'1791975572_SpringLoveTulips.jpg','active','2026-10-14 10:59:32'),(21,'Blush Rose Gift Box','Pink roses presented in an elegant gift box.',1499.00,150.00,20,NULL,5,'1791975980_BlushRoseGiftBox.jpg','active','2026-10-14 11:06:20'),(22,'Floral Surprise Box','A delightful floral gift box for someone special.',1299.00,130.00,20,NULL,5,'1791976024_FloralSurpriseBox.jpg','active','2026-10-14 11:07:04'),(23,'Rose & Keepsake Box','A rose gift box paired with a keepsake for lasting memories.',1799.00,270.00,20,NULL,5,'1791976119_RoseKeepsakeBox.jpg','active','2026-10-14 11:08:39'),(24,'Birthday Bloom Box','A cheerful floral gift box for birthday celebrations.',1599.00,160.00,20,NULL,5,'1791976157_BirthdayBloomBox.jpg','active','2026-10-14 11:09:17'),(25,'Luxury Bloom Hamper','A premium floral hamper for memorable gifting occasions.',2199.00,330.00,20,NULL,5,'1791976198_LuxuryBloomHamper.jpg','active','2026-10-14 11:09:58');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text,
  `city` varchar(80) DEFAULT NULL,
  `state` varchar(80) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Demo Customer','customer@example.com','9876543210','$2y$12$REPLACE_WITH_VALID_PASSWORD_HASH','12 Flower Street','Ahmedabad','Gujarat','380001','2026-10-13 06:06:32'),(2,'Priya Sharma','priya@example.com','9876501234','$2y$12$REPLACE_WITH_VALID_PASSWORD_HASH','25 Garden Road','Ahmedabad','Gujarat','380015','2026-10-13 06:06:32'),(3,'Nikhil Suthar','nikhilsuthar152@gmail.com','1122334455','$2y$10$0.mIlQUt2IWPLulhDejKZOdu2HE10fyfTEMesBxmRkik1K9GFMcA2','E-504 Yogeshwar Homes','Ahmedabad','Gujarat','382470','2026-10-13 07:05:14');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'ecommerce'
--

--
-- Dumping routines for database 'ecommerce'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-14 17:13:21
