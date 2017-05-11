-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.1.51-community - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица service.records
CREATE TABLE IF NOT EXISTS `records` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TRANSACTION` int(11) unsigned NOT NULL DEFAULT '0',
  `FIELD_0` varchar(255) NOT NULL,
  `FIELD_1` varchar(255) NOT NULL,
  `FIELD_2` varchar(255) NOT NULL,
  `FIELD_3` varchar(255) NOT NULL,
  `FIELD_4` varchar(255) NOT NULL,
  `FIELD_5` varchar(255) NOT NULL,
  `FIELD_6` varchar(255) NOT NULL,
  `FIELD_7` varchar(255) NOT NULL,
  `FIELD_8` varchar(255) NOT NULL,
  `FIELD_9` varchar(255) NOT NULL,
  `FIELD_10` varchar(255) NOT NULL,
  `FIELD_11` varchar(255) NOT NULL,
  `FIELD_12` varchar(255) NOT NULL,
  `FIELD_13` varchar(255) NOT NULL,
  `FIELD_14` varchar(255) NOT NULL,
  `FIELD_15` varchar(255) NOT NULL,
  `FIELD_16` varchar(255) NOT NULL,
  `FIELD_17` varchar(255) NOT NULL,
  `FIELD_18` varchar(255) NOT NULL,
  `FIELD_19` varchar(255) NOT NULL,
  `FIELD_20` varchar(255) NOT NULL,
  `FIELD_21` varchar(255) NOT NULL,
  `FIELD_22` varchar(255) NOT NULL,
  `FIELD_23` varchar(255) NOT NULL,
  `FIELD_24` varchar(255) NOT NULL,
  `FIELD_25` varchar(255) NOT NULL,
  `FIELD_26` varchar(255) NOT NULL,
  `FIELD_27` varchar(255) NOT NULL,
  `FIELD_28` varchar(255) NOT NULL,
  `FIELD_29` varchar(255) NOT NULL,
  `FIELD_30` varchar(255) NOT NULL,
  `FIELD_31` varchar(255) NOT NULL,
  `FIELD_32` varchar(255) NOT NULL,
  `FIELD_33` varchar(255) NOT NULL,
  `FIELD_34` varchar(255) NOT NULL,
  `FIELD_35` varchar(255) NOT NULL,
  `FIELD_36` varchar(255) NOT NULL,
  `FIELD_37` varchar(255) NOT NULL,
  `FIELD_38` varchar(255) NOT NULL,
  `FIELD_39` varchar(255) NOT NULL,
  `FIELD_40` varchar(255) NOT NULL,
  `FIELD_41` varchar(255) NOT NULL,
  `FIELD_42` varchar(255) NOT NULL,
  `FIELD_43` varchar(255) NOT NULL,
  `FIELD_44` varchar(255) NOT NULL,
  `FIELD_45` varchar(255) NOT NULL,
  `FIELD_46` varchar(255) NOT NULL,
  `FIELD_47` varchar(255) NOT NULL,
  `FIELD_48` varchar(255) NOT NULL,
  `FIELD_49` varchar(255) NOT NULL,
  `FIELD_50` varchar(255) NOT NULL,
  `FIELD_51` varchar(255) NOT NULL,
  `FIELD_52` varchar(255) NOT NULL,
  `FIELD_53` varchar(255) NOT NULL,
  `FIELD_54` varchar(255) NOT NULL,
  `FIELD_55` varchar(255) NOT NULL,
  `FIELD_56` varchar(255) NOT NULL,
  `FIELD_57` varchar(255) NOT NULL,
  `FIELD_58` varchar(255) NOT NULL,
  `FIELD_59` varchar(255) NOT NULL,
  `FIELD_60` varchar(255) NOT NULL,
  `FIELD_61` varchar(255) NOT NULL,
  `FIELD_62` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FIELD_3` (`FIELD_3`,`TRANSACTION`,`FIELD_20`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы service.records: ~0 rows (приблизительно)
DELETE FROM `records`;
/*!40000 ALTER TABLE `records` DISABLE KEYS */;
/*!40000 ALTER TABLE `records` ENABLE KEYS */;


-- Дамп структуры для таблица service.reports
CREATE TABLE IF NOT EXISTS `reports` (
  `ID` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) DEFAULT NULL,
  `DATE` datetime NOT NULL,
  `FILE` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы service.reports: ~0 rows (приблизительно)
DELETE FROM `reports`;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;


-- Дамп структуры для таблица service.transaction
CREATE TABLE IF NOT EXISTS `transaction` (
  `ID` int(11) unsigned NOT NULL,
  `STATUS` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `DATE` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы service.transaction: ~0 rows (приблизительно)
DELETE FROM `transaction`;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
