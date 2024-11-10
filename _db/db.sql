-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              11.5.2-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win64
-- HeidiSQL Versione:            12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dump della struttura del database db_tecweb
CREATE DATABASE IF NOT EXISTS `db_tecweb` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `db_tecweb`;

-- Dump della struttura di tabella db_tecweb.dish_type
CREATE TABLE IF NOT EXISTS `dish_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_type` varchar(45) DEFAULT NULL,
  `rcp_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rcp_id` (`rcp_id`),
  CONSTRAINT `dish_type_ibfk_1` FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella db_tecweb.ingredient
CREATE TABLE IF NOT EXISTS `ingredient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `igr_name` varchar(45) DEFAULT NULL,
  `igr_amount` double DEFAULT NULL,
  `igr_unit` varchar(10) DEFAULT NULL,
  `igr_url_image` varchar(200) DEFAULT NULL,
  `igr_gprotein` double DEFAULT NULL,
  `igr_kcal` double DEFAULT NULL,
  `igr_gfat` double DEFAULT NULL,
  `igr_gcarb` double DEFAULT NULL,
  `rcp_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rcp_id` (`rcp_id`),
  CONSTRAINT `ingredient_ibfk_1` FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella db_tecweb.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) DEFAULT NULL,
  `rcp_id` int(11) DEFAULT NULL,
  `mnu_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usr_id` (`usr_id`),
  KEY `rcp_id` (`rcp_id`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`usr_id`) REFERENCES `user` (`id`),
  CONSTRAINT `menu_ibfk_2` FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella db_tecweb.rating
CREATE TABLE IF NOT EXISTS `rating` (
  `usr_id` int(11) NOT NULL,
  `rcp_id` int(11) NOT NULL,
  `rt_score` int(11) DEFAULT NULL,
  `rt_review` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`usr_id`,`rcp_id`),
  KEY `rcp_id` (`rcp_id`),
  CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`usr_id`) REFERENCES `user` (`id`),
  CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella db_tecweb.recipe
CREATE TABLE IF NOT EXISTS `recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rcp_title` varchar(100) NOT NULL,
  `rcp_image` varchar(200) DEFAULT NULL,
  `rcp_ready_minutes` int(11) DEFAULT NULL,
  `rcp_servings` int(11) DEFAULT NULL,
  `rcp_instructions` varchar(500) DEFAULT NULL,
  `rcp_price_servings` float DEFAULT NULL,
  `rcp_is_dairy_free` boolean DEFAULT NULL,
  `rcp_is_gluten_free` boolean DEFAULT NULL,
  `rcp_is_vegan` boolean DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella db_tecweb.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_name` varchar(45) DEFAULT NULL,
  `usr_mail` varchar(100) DEFAULT NULL,
  `usr_first_name` varchar(45) DEFAULT NULL,
  `usr_gender` varchar(10) DEFAULT NULL,
  `usr_birth_date` date DEFAULT NULL,
  `usr_password` varchar(16) DEFAULT NULL,
  `usr_is_vegan` boolean DEFAULT NULL,
  `usr_is_celiac` boolean DEFAULT NULL,
  `usr_is_lactose_intolerant` boolean DEFAULT NULL,
  `usr_is_admin` boolean DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;






/* 
SCRIPT SQL PER INSERIMENTO DATI DI PROVA
*/


INSERT INTO `recipe` (`id`,`rcp_title`,`rcp_image`,`rcp_ready_minutes`,`rcp_servings`,`rcp_instructions`,`rcp_price_servings`,`rcp_is_dairy_free`,`rcp_is_gluten_free`,`rcp_is_vegan`) VALUES
(1,'pasta al pomodoro',NULL,30,4,'Per preparare...',70,true,true,false),
(2,'carne in padella',NULL,30,4,'Per preparare...',70,true,true,false),
(3,'tiramisu',NULL,30,4,'Per preparare...',70,true,true,false);


INSERT INTO `user` (`id`,`usr_name`,`usr_mail`,`usr_first_name`,`usr_gender`,`usr_birth_date`,`usr_password`,`usr_is_vegan`,`usr_is_celiac`,`usr_is_lactose_intolerant`,`usr_is_admin`) VALUES
(1,'matteo','matteo@gmail.com','matteo','male','2003-02-07','provapsw',false,false,true,true),
(2,'marco','marco@gmail.com','marco','male','2009-02-07','provapsw2',false,true,true,false),
(3,'andrea','andrea@gmail.com','andrea','male','2000-02-07','provapsw3',false,false,false,false);



INSERT INTO `dish_type` (`id`, `dt_type`, `rcp_id`) VALUES
(1, 'primo', 1),
(2, 'dolce', 3),
(4, 'antipasto', 2);

INSERT INTO `ingredient` (`id`,`igr_name`,`igr_amount`,`igr_unit`,`igr_url_image`,`igr_gprotein`,`igr_kcal`,`igr_gfat`,`igr_gcarb`,`rcp_id`) VALUES
(1,'pasta',200,'grammi',NULL,20,40,14,100,1),
(2,'olio',100,'ml',NULL,20,40,14,100,1),
(3,'carne',50,'grammi',NULL,20,60,14,100,3),
(4,'farina',1,'kg',NULL,20,40,14,100,2);


INSERT INTO `menu` (`id`,`usr_id`,`rcp_id`,`mnu_name`) VALUES
(1,1,1,'Menu1'),
(2,1,2,'Menu2'),
(3,2,1,'Menu3');

INSERT INTO `rating` (`usr_id`,`rcp_id`,`rt_score`,`rt_review`) VALUES
(1,1,3,NULL),
(2,3,2,NULL),
(1,2,4,NULL);
