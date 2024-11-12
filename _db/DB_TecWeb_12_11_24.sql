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


CREATE TABLE IF NOT EXISTS `recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rcp_title` varchar(100) NOT NULL,
  `rcp_image` varchar(200) DEFAULT NULL,
  `rcp_ready_minutes` int(11) DEFAULT NULL,
  `rcp_servings` int(11) DEFAULT NULL,
  `rcp_instructions` varchar(500) DEFAULT NULL,
  `rcp_price_servings` float DEFAULT NULL,
  `rcp_difficult` varchar(20) DEFAULT NULL,
  `rcp_is_dairy_free` boolean DEFAULT NULL,
  `rcp_is_gluten_free` boolean DEFAULT NULL,
  `rcp_is_vegan` boolean DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `igr_url_image` varchar(200) DEFAULT NULL,
  `igr_gprotein` double DEFAULT NULL,
  `igr_kcal` double DEFAULT NULL,
  `igr_gfat` double DEFAULT NULL,
  `igr_gcarb` double DEFAULT NULL,
  PRIMARY KEY (`id`)
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
  /*`rt_review` varchar(300) DEFAULT NULL,*/
  PRIMARY KEY (`usr_id`,`rcp_id`),
  KEY `rcp_id` (`rcp_id`),
  CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`usr_id`) REFERENCES `user` (`id`),
  CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella db_tecweb.recipe

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


CREATE TABLE IF NOT EXISTS `recipe_ingredient` (
  `rcp_id` int(11) NOT NULL,
  `igr_id` int(11) NOT NULL,
  `igr_amount` int(11) NOT NULL,
  `igr_unit` varchar(20) NOT NULL,
  PRIMARY KEY (`rcp_id`, `igr_id`),
  FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`),
  FOREIGN KEY (`igr_id`) REFERENCES `ingredient` (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



CREATE TABLE IF NOT EXISTS `user_liked`(
  `usr_id` int(11) NOT NULL,
  `mnu_id` int(11) NOT NULL,
  PRIMARY KEY (`usr_id`, `mnu_id`),
  FOREIGN KEY (`usr_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`mnu_id`) REFERENCES `menu` (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `menu_recipe`(
  `rcp_id` int(11) NOT NULL,
  `mnu_id` int(11) NOT NULL,
  PRIMARY KEY (`rcp_id`, `mnu_id`),
  FOREIGN KEY (`rcp_id`) REFERENCES `recipe` (`id`),
  FOREIGN KEY (`mnu_id`) REFERENCES `menu` (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/* AGGIUNGERE TABELLA MENU-RICETTA
AGGIUNGERE TABELLA UTENTE_MENU (USER_LIKED)*/

/* MODIFICHE EFFETTUATE RISPETTO A VERSIONE 1.0 :
    - RIMOZIONE 'rt_review' SULLA TABELLA 'rating'
    - AGGIUNTA TABELLA 'recipe_ingredient'
    - AGGIUNTA TABELLA 'menu_recipe'
    - AGGIUNTA TABELLA 'user_liked' (serve per indicare quali menu piacciono agli utenti)
    - AGGIUNTA 'rcp_difficult' SU TABELLA 'recipe'
    - RIMOZIONE 'igr_amount' E 'igr_unit' DA TABELLA 'ingredient' E CONSEGUENTE AGGIUNTA DI TALI CAMPI SU TABELLA 'recipe_ingredient'
*/

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;






/* 
SCRIPT SQL CREATO DA IA PER INSERIMENTO DATI DI PROVA
*/


-- Inserimento dati per la tabella `user`
INSERT INTO `user` (`usr_name`, `usr_mail`, `usr_first_name`, `usr_gender`, `usr_birth_date`, `usr_password`, `usr_is_vegan`, `usr_is_celiac`, `usr_is_lactose_intolerant`, `usr_is_admin`)
VALUES
('jdoe', 'jdoe@example.com', 'John', 'M', '1990-05-14', 'password123', FALSE, FALSE, TRUE, FALSE),
('asmith', 'asmith@example.com', 'Alice', 'F', '1985-10-22', 'alicepass', TRUE, TRUE, FALSE, FALSE),
('bgordon', 'bgordon@example.com', 'Bob', 'M', '1992-08-30', 'bobsecure', FALSE, FALSE, TRUE, FALSE),
('cjones', 'cjones@example.com', 'Clara', 'F', '1988-01-17', 'clarapass', FALSE, TRUE, TRUE, FALSE),
('gwhite', 'gwhite@example.com', 'George', 'M', '1980-07-19', 'geowhite', TRUE, FALSE, FALSE, TRUE),
('kmorris', 'kmorris@example.com', 'Karen', 'F', '1995-03-10', 'karen1234', FALSE, FALSE, TRUE, FALSE);

-- Inserimento dati per la tabella `recipe`
INSERT INTO `recipe` (`rcp_title`, `rcp_image`, `rcp_ready_minutes`, `rcp_servings`, `rcp_instructions`, `rcp_price_servings`, `rcp_difficult`, `rcp_is_dairy_free`, `rcp_is_gluten_free`, `rcp_is_vegan`)
VALUES
('Pasta Primavera', 'pasta.jpg', 20, 2, 'Cook pasta and mix with vegetables.', 4.5, 'Easy', TRUE, FALSE, TRUE),
('Chicken Salad', 'chicken_salad.jpg', 15, 4, 'Mix chicken, lettuce, and dressing.', 6.0, 'Easy', FALSE, TRUE, FALSE),
('Vegan Curry', 'vegan_curry.jpg', 35, 3, 'Cook curry sauce with vegetables.', 7.0, 'Medium', TRUE, TRUE, TRUE),
('Beef Stew', 'beef_stew.jpg', 60, 4, 'Cook beef and vegetables in broth.', 10.0, 'Hard', FALSE, FALSE, FALSE),
('Gluten-Free Pancakes', 'pancakes.jpg', 25, 2, 'Mix ingredients and cook.', 5.0, 'Easy', TRUE, TRUE, FALSE),
('Vegan Pizza', 'vegan_pizza.jpg', 30, 4, 'Prepare dough, add toppings, and bake.', 8.0, 'Medium', TRUE, TRUE, TRUE);

-- Inserimento dati per la tabella `dish_type`
INSERT INTO `dish_type` (`dt_type`, `rcp_id`)
VALUES
('Main Course', 1),
('Appetizer', 2),
('Main Course', 3),
('Main Course', 4),
('Dessert', 5),
('Main Course', 6);

-- Inserimento dati per la tabella `ingredient`
INSERT INTO `ingredient` (`igr_name`, `igr_url_image`, `igr_gprotein`, `igr_kcal`, `igr_gfat`, `igr_gcarb`)
VALUES
('Pasta', 'pasta.jpg', 13.0, 300, 1.1, 60),
('Chicken', 'chicken.jpg', 27.0, 165, 3.6, 0),
('Lettuce', 'lettuce.jpg', 1.4, 15, 0.2, 3),
('Carrot', 'carrot.jpg', 0.9, 41, 0.2, 10),
('Curry Powder', 'curry_powder.jpg', 14.0, 325, 14.0, 55),
('Tomato', 'tomato.jpg', 0.9, 18, 0.2, 4);

-- Inserimento dati per la tabella `menu`
INSERT INTO `menu` (`usr_id`, `rcp_id`, `mnu_name`)
VALUES
(1, 1, 'Italian Dinner'),
(2, 2, 'Healthy Lunch'),
(3, 3, 'Vegan Feast'),
(4, 4, 'Winter Stew'),
(5, 5, 'Breakfast Specials'),
(6, 6, 'Family Pizza Night');

-- Inserimento dati per la tabella `rating`
INSERT INTO `rating` (`usr_id`, `rcp_id`, `rt_score`)
VALUES
(1, 1, 5),
(2, 2, 4),
(3, 3, 3),
(4, 4, 5),
(5, 5, 4),
(6, 6, 3);

-- Inserimento dati per la tabella `recipe_ingredient`
INSERT INTO `recipe_ingredient` (`rcp_id`, `igr_id`, `igr_amount`, `igr_unit`)
VALUES
(1, 1, 200, 'grams'),
(2, 2, 150, 'grams'),
(3, 4, 50, 'grams'),
(4, 2, 200, 'grams'),
(5, 6, 100, 'grams'),
(6, 5, 10, 'grams');

-- Inserimento dati per la tabella `user_liked`
INSERT INTO `user_liked` (`usr_id`, `mnu_id`)
VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6);

-- Inserimento dati per la tabella `menu_recipe`
INSERT INTO `menu_recipe` (`rcp_id`, `mnu_id`)
VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6);




