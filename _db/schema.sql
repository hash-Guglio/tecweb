CREATE DATABASE IF NOT EXISTS `tecweb`;
USE `tecweb`;

DROP TABLE IF EXISTS user;

CREATE TABLE IF NOT EXISTS `user` (
  `id` BIGINT UNSIGNED,
  `usr_name` varchar(45) UNIQUE NOT NULL,
  `usr_mail` varchar(100) UNIQUE,
  `usr_first_name` varchar(45) NOT NULL,
  `usr_gender` ENUM('male', 'female', 'other') DEFAULT 'other',
  `usr_birth_date` date,
  `usr_password` varchar(255) NOT NULL,
  `usr_is_admin` boolean DEFAULT FALSE,
  `usr_is_vegan` boolean DEFAULT FALSE,
  `usr_is_celiac` boolean DEFAULT FALSE,
  `usr_is_lactose_intolerant` boolean DEFAULT FALSE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `user` (`id`, `usr_name`, `usr_mail`, `usr_first_name`, `usr_gender`, `usr_birth_date`, `usr_password`, `usr_is_admin`, `usr_is_vegan`, `usr_is_celiac`, `usr_is_lactose_intolerant`) VALUES
(0, 'admin', 'admin@example.com', 'Admin', 'male', '1990-01-01', 'pass', true, false, false, false),
(1, 'user', 'user@example.com', 'User', 'female', '1995-05-10', 'pass', false, false, false, false);


