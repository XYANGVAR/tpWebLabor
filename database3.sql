-- MySQL dump 10.13  Distrib 5.7.21, for Win64 (x86_64)
--
-- Host: localhost    Database: lab_order
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.31-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `history`
--
DROP DATABASE IF EXISTS `lab_order`;
CREATE DATABASE lab_order;
USE lab_order;
DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `history` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `operator` int(10) NOT NULL COMMENT '操作人',
  `lab_name` char(30) NOT NULL,
  `begin_time` char(20) DEFAULT NULL,
  `end_time` char(20) DEFAULT NULL,
  `result` int(10) NOT NULL DEFAULT '0' COMMENT '审核结果0拒绝1同意',
  `applicant` int(10) NOT NULL COMMENT '申请人学号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `labs`
--

DROP TABLE IF EXISTS `labs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `num` int(10) NOT NULL COMMENT '实验室号',
  `name` char(30) NOT NULL,
  `applicant` char(20) DEFAULT NULL COMMENT '实验室预约人',
  `charge_person` char(20) DEFAULT NULL COMMENT '实验室负责人',
  `is_order` int(10) DEFAULT '0' COMMENT '是否被预约，0否1审核中2是',
  `begin_time` char(20) DEFAULT NULL COMMENT '预约开始时间',
  `end_time` char(20) DEFAULT NULL COMMENT '预约结束时间',
  `attention` varchar(255) NOT NULL COMMENT '注意事项',
  `function` varchar(255) NOT NULL COMMENT '主要功能',
  PRIMARY KEY (`id`),
  KEY `labs_ibfk_1` (`applicant`),
  KEY `charge_person` (`charge_person`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `labs`
--

LOCK TABLES `labs` WRITE;
/*!40000 ALTER TABLE `labs` DISABLE KEYS */;
/*!40000 ALTER TABLE `labs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '学号',
  `num` char(20) NOT NULL,
  `email` char(20) NOT NULL,
  `name` char(10) NOT NULL,
  `class` char(20) NOT NULL,
  `password` char(40) DEFAULT NULL,
  `lab_one` int(10) DEFAULT NULL COMMENT '预约的实验室1',
  `lab_two` int(10) DEFAULT NULL COMMENT '预约的实验室2',
  PRIMARY KEY (`id`),
  KEY `lab` (`lab_one`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'2016188001','123@qq.com','贵州伟','saab','123123',NULL,NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `num` char(20) NOT NULL COMMENT '工号',
  `name` char(10) NOT NULL,
  `email` char(20) NOT NULL,
  `lab` int(10) DEFAULT NULL COMMENT '所属实验室',
  `password` char(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lab` (`lab`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,'2006188001','伟老师','123@163.com',101,'123123');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-10 15:37:33
