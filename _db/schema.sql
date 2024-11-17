CREATE DATABASE IF NOT EXISTS `tecweb`;
USE `tecweb`;

DROP TABLE IF EXISTS user;

CREATE TABLE IF NOT EXISTS `user` (
  `id` BIGINT UNSIGNED,
  `usr_name` varchar(45) UNIQUE NOT NULL,
  `usr_mail` varchar(100) UNIQUE,
  `usr_first_name` varchar(45) NOT NULL,
  `usr_gender` ENUM('maschio', 'femmina', 'altro') DEFAULT 'altro',
  `usr_birth_date` date,
  `usr_password` varchar(255) NOT NULL,
  `usr_is_admin` boolean DEFAULT FALSE,
  `usr_is_vegan` boolean DEFAULT FALSE,
  `usr_is_celiac` boolean DEFAULT FALSE,
  `usr_is_lactose_intolerant` boolean DEFAULT FALSE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

