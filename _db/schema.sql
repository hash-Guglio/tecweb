CREATE DATABASE IF NOT EXISTS tecweb;
USE tecweb;

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS recipe;
DROP TABLE IF EXISTS dish_type_recipe; 

CREATE TABLE IF NOT EXISTS user (
  id BIGINT UNSIGNED,
  usr_name varchar(45) UNIQUE NOT NULL,
  usr_mail varchar(100) UNIQUE,
  usr_first_name varchar(45) NOT NULL,
  usr_gender ENUM('maschio', 'femmina', 'altro') DEFAULT 'altro',
  usr_birth_date date,
  usr_password varchar(255) NOT NULL,
  usr_is_admin boolean DEFAULT FALSE,
  usr_is_vegan boolean DEFAULT FALSE,
  usr_is_celiac boolean DEFAULT FALSE,
  usr_is_lactose_intolerant boolean DEFAULT FALSE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS recipe (
  id BIGINT UNSIGNED,
  rcp_title varchar(100) NOT NULL,
  rcp_image varchar(200) DEFAULT NULL,
  rcp_ready_minutes int(11) DEFAULT NULL,
  rcp_servings int(11) DEFAULT NULL,
  rcp_instructions varchar(500) DEFAULT NULL,
  rcp_price_servings float DEFAULT NULL,
  rcp_difficult varchar(20) DEFAULT NULL,
  rcp_is_dairy_free boolean DEFAULT NULL,
  rcp_is_gluten_free boolean DEFAULT NULL,
  rcp_is_vegan boolean DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS dish_type (
  id BIGINT UNSIGNED,
  dt_type varchar(45) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS dish_type_recipe (
    recipe BIGINT UNSIGNED,
    dish_type BIGINT UNSIGNED,
    PRIMARY KEY (recipe, dish_type),
    FOREIGN KEY (recipe) REFERENCES recipe(id) ON DELETE CASCADE,
    FOREIGN KEY (dish_type) REFERENCES dish_type(id)
)
