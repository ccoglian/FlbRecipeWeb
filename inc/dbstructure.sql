# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.5.8
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-12-19 14:08:05
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping database structure for flb
CREATE DATABASE IF NOT EXISTS `flb` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `flb`;


# Dumping structure for table flb.extra_shopping_list_items
DROP TABLE IF EXISTS `extra_shopping_list_items`;
CREATE TABLE IF NOT EXISTS `extra_shopping_list_items` (
  `extra_shopping_list_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `item_name` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`extra_shopping_list_item_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_extra_shopping_list_items_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Data exporting was unselected.


# Dumping structure for table flb.recipes
DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `recipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(511) NOT NULL,
  `instructions` text NOT NULL,
  `serves` varchar(255) NOT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`recipe_id`),
  KEY `FK_recipes_users` (`created_by`),
  CONSTRAINT `FK_recipes_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table flb.recipe_items
DROP TABLE IF EXISTS `recipe_items`;
CREATE TABLE IF NOT EXISTS `recipe_items` (
  `recipe_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `quantity` double DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `item_name` varchar(127) NOT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `order_key` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`recipe_item_id`),
  KEY `FK_recipe_items_recipes` (`recipe_id`),
  KEY `FK_recipe_items_units` (`unit_id`),
  CONSTRAINT `FK_recipe_items_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_recipe_items_units` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table flb.recipe_reminders
DROP TABLE IF EXISTS `recipe_reminders`;
CREATE TABLE IF NOT EXISTS `recipe_reminders` (
  `recipe_reminder_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hours_ahead` double NOT NULL,
  PRIMARY KEY (`recipe_reminder_id`),
  KEY `FK_recipe_reminders_recipes` (`recipe_id`),
  CONSTRAINT `FK_recipe_reminders_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table flb.recipe_search
DROP TABLE IF EXISTS `recipe_search`;
CREATE TABLE IF NOT EXISTS `recipe_search` (
  `recipe_id` int(11) NOT NULL,
  `full_recipe_text` text NOT NULL,
  PRIMARY KEY (`recipe_id`),
  FULLTEXT KEY `SEARCH_KEY` (`full_recipe_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table flb.scheduled_makes
DROP TABLE IF EXISTS `scheduled_makes`;
CREATE TABLE IF NOT EXISTS `scheduled_makes` (
  `scheduled_make_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `local_time` datetime NOT NULL,
  `server_time` datetime DEFAULT NULL,
  PRIMARY KEY (`scheduled_make_id`),
  KEY `FK_scheduled_makes_recipes` (`recipe_id`),
  KEY `FK_scheduled_makes_users` (`user_id`),
  CONSTRAINT `FK_scheduled_makes_recipes` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`),
  CONSTRAINT `FK_scheduled_makes_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table flb.scheduled_reminders
DROP TABLE IF EXISTS `scheduled_reminders`;
CREATE TABLE IF NOT EXISTS `scheduled_reminders` (
  `scheduled_reminder_id` int(11) NOT NULL AUTO_INCREMENT,
  `scheduled_make_id` int(11) NOT NULL,
  `recipe_reminder_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `local_time` datetime NOT NULL,
  `server_time` datetime DEFAULT NULL,
  PRIMARY KEY (`scheduled_reminder_id`),
  KEY `FK_scheduled_reminders_scheduled_makes` (`scheduled_make_id`),
  CONSTRAINT `FK_scheduled_reminders_scheduled_makes` FOREIGN KEY (`scheduled_make_id`) REFERENCES `scheduled_makes` (`scheduled_make_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table flb.shopping_list_items
DROP TABLE IF EXISTS `shopping_list_items`;
CREATE TABLE IF NOT EXISTS `shopping_list_items` (
  `shopping_list_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `quantity` double DEFAULT '0',
  `unit_id` int(11) DEFAULT '0',
  `item_name` varchar(255) DEFAULT '',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`shopping_list_item_id`),
  KEY `Index 2` (`user_id`),
  CONSTRAINT `FK__users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Data exporting was unselected.


# Dumping structure for table flb.units
DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(63) NOT NULL,
  `unit_name_plural` varchar(63) NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Data exporting was unselected.


# Dumping structure for table flb.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
