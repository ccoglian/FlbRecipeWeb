# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.5.8
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-12-09 12:10:37
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
# Dumping data for table flb.units: ~18 rows (approximately)
DELETE FROM `units`;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` (`unit_id`, `unit_name`, `unit_name_plural`) VALUES
	(1, 'bunch', 'bunches'),
	(2, 'head', 'heads'),
	(3, 'lb', 'lbs'),
	(4, 'pound', 'pounds'),
	(5, 'ounce', 'ounces'),
	(6, 'cup', 'cups'),
	(7, 'tablespoon', 'tablespoons'),
	(8, 'teaspoon', 'teaspoons'),
	(9, 'clove', 'cloves'),
	(10, 'stalk', 'stalks'),
	(11, 'small', 'small'),
	(12, 'medium', 'medium'),
	(13, 'large', 'large'),
	(14, '', ''),
	(15, 'whisper', 'whisper'),
	(16, 'drop', 'drops'),
	(17, 'piece', 'pieces'),
	(18, 'recipe', 'recipes');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
