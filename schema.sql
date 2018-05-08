DROP DATABASE IF EXISTS yeticave;
CREATE DATABASE yeticave;
USE yeticave;

CREATE TABLE category (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR (100) NOT NULL,
  UNIQUE (name)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE lot (
  id SERIAL PRIMARY KEY,
  add_user_id BIGINT UNSIGNED NOT NULL,
  win_user_id BIGINT UNSIGNED,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR (200) NOT NULL,
  description TEXT NOT NULL,
  img VARCHAR (100) NOT NULL,
  price DECIMAL (10,4) UNSIGNED NOT NULL,
  price_step DECIMAL (10,4) UNSIGNED NOT NULL,
  add_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  INDEX (add_user_id, category_id, name)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE bet (
  id SERIAL PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  lot_id BIGINT UNSIGNED NOT NULL,
  add_time DATETIME NOT NULL,
  price DECIMAL (10,4) UNSIGNED NOT NULL,
  INDEX (user_id, lot_id)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE user (
  id SERIAL PRIMARY KEY,
  email VARCHAR (100) NOT NULL,
  name VARCHAR (100) NOT NULL,
  password_hash VARCHAR (255) NOT NULL,
  avatar VARCHAR (100),
  contact VARCHAR (200) NOT NULL,
  UNIQUE (email)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;