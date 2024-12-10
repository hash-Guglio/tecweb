CREATE DATABASE IF NOT EXISTS tecweb;
USE tecweb;

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS recipe;
DROP TABLE IF EXISTS ingredient;
DROP TABLE IF EXISTS dish_type;
DROP TABLE IF EXISTS dish_type_recipe; 
DROP TABLE IF EXISTS restriction;
DROP TABLE IF EXISTS nutrient;
DROP TABLE IF EXISTS user_restriction;
DROP TABLE IF EXISTS recipe_restriction;
DROP TABLE IF EXISTS ingredient_nutrient;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS ingredient_category;

CREATE TABLE IF NOT EXISTS user (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    usr_name varchar(45) UNIQUE NOT NULL,
    usr_mail varchar(100) UNIQUE DEFAULT NULL,
    usr_first_name varchar(45) DEFAULT NULL,
    usr_gender ENUM('maschio', 'femmina', 'altro') DEFAULT 'altro',
    usr_birth_date date,
    usr_password varchar(255) NOT NULL,
    usr_is_admin boolean DEFAULT FALSE,    
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS restriction (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    rst_type VARCHAR(45) NOT NULL,
    rst_disorder_name VARCHAR(100) DEFAULT NULL,
    UNIQUE (rst_type),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS recipe (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    rcp_title varchar(100) NOT NULL,
    rcp_image varchar(200) DEFAULT NULL,
    rcp_ready_minutes int(11) DEFAULT NULL,
    rcp_servings int(11) DEFAULT NULL,
    rcp_instructions varchar(500) DEFAULT NULL,
    rcp_price_servings float DEFAULT NULL,
    rcp_difficult varchar(20) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS ingredient (
  id BIGINT UNSIGNED AUTO_INCREMENT,
  igr_name varchar(45) NOT NULL,
  igr_image varchar(200) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS dish_type (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    dt_type varchar(45) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS nutrient (
    id varchar(6),
    ntr_name varchar(45) DEFAULT NULL,
    ntr_unit varchar(4) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS dish_type_recipe (
    recipe BIGINT UNSIGNED,
    dish_type BIGINT UNSIGNED,
    PRIMARY KEY (recipe, dish_type),
    FOREIGN KEY (recipe) REFERENCES recipe(id) ON DELETE CASCADE,
    FOREIGN KEY (dish_type) REFERENCES dish_type(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS ingredient_nutrient (
    ingredient BIGINT UNSIGNED,
    nutrient varchar(6),
    amount double NOT NULL,
    PRIMARY KEY (ingredient, nutrient),
    FOREIGN KEY (ingredient) REFERENCES ingredient(id) ON DELETE CASCADE,
    FOREIGN KEY (nutrient) REFERENCES nutrient(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS user_restriction (
    user BIGINT UNSIGNED,
    restriction BIGINT UNSIGNED,
    PRIMARY KEY (user, restriction),
    FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (restriction) REFERENCES restriction(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS recipe_restriction (
    recipe BIGINT UNSIGNED,
    restriction BIGINT UNSIGNED,
    PRIMARY KEY (recipe, restriction),
    FOREIGN KEY (recipe) REFERENCES recipe(id) ON DELETE CASCADE,
    FOREIGN KEY (restriction) REFERENCES restriction(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS category (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    cat_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (cat_name)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS ingredient_category (
    ingredient BIGINT UNSIGNED,
    category BIGINT UNSIGNED,
    PRIMARY KEY (ingredient, category),
    FOREIGN KEY (ingredient) REFERENCES ingredient(id) ON DELETE CASCADE,
    FOREIGN KEY (category) REFERENCES category(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;