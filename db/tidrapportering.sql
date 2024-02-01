/*
SQLyog Community
MySQL - 8.0.31 : Database - tidrapportering
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `aktiviteter` */

CREATE TABLE `aktiviteter` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Namn` varchar(50) COLLATE utf8mb3_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UIX_Namn` (`Namn`)
) ENGINE=InnoDB AUTO_INCREMENT=1633 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

/*Data for the table `aktiviteter` */

insert  into `aktiviteter`(`ID`,`Namn`) values 
(1,'Aktivieter'),
(1602,'En rolig aktivit'),
(3,'Försöka'),
(2,'JavaScript'),
(1600,'ockej'),
(687,'okej'),
(685,'php'),
(40,'Spela'),
(4,'Sökt information');

/*Table structure for table `uppgifter` */

CREATE TABLE `uppgifter` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Datum` date NOT NULL,
  `Tid` time NOT NULL,
  `Beskrivning` varchar(100) COLLATE utf8mb3_swedish_ci DEFAULT NULL,
  `AktivitetID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `AktivitetID` (`AktivitetID`),
  CONSTRAINT `uppgifter_ibfk_1` FOREIGN KEY (`AktivitetID`) REFERENCES `aktiviteter` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_swedish_ci;

/*Data for the table `uppgifter` */

insert  into `uppgifter`(`ID`,`Datum`,`Tid`,`Beskrivning`,`AktivitetID`) values 
(336,'2024-01-29','04:00:00','Uppdatera uppgift och delete uppgift',687),
(337,'2024-01-29','03:55:00','nå, jo',2),
(338,'2024-01-19','00:01:00','Går bra',1600),
(339,'2024-01-29','04:44:00','Ett försök',3),
(340,'2024-01-29','00:00:00','Hitta meningen med livet',4),
(341,'2024-01-29','01:01:00','',4),
(342,'2024-01-29','08:00:00','Gick bra',3),
(343,'2024-01-28','04:30:00','',40),
(344,'2024-01-30','02:00:00','En lång kommentar som är väldigt lång och krånglig att hantera i detta sammanhang, den är så långa a',3);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
