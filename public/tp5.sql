-- MySQL dump 10.13  Distrib 5.6.30, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tp5
-- ------------------------------------------------------
-- Server version	5.6.30-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tp_admin`
--

DROP TABLE IF EXISTS `tp_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_admin` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '32位md5密码',
  `salt` char(6) NOT NULL DEFAULT '' COMMENT '6位随机字符密码盐值',
  `relaname` varchar(64) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '本次登录id',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '本次登录时间',
  `last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `tp_user_user_id_uindex` (`user_id`),
  UNIQUE KEY `tp_user_username_uindex` (`username`),
  UNIQUE KEY `tp_user_mobile_uindex` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_admin`
--

LOCK TABLES `tp_admin` WRITE;
/*!40000 ALTER TABLE `tp_admin` DISABLE KEYS */;
INSERT INTO `tp_admin` VALUES (1,'张波','6f3b8ded65bd7a4db11625ac84e579bb','abcdef','zhangbo','13026035350',1,48,'127.0.0.1',1528680316,'116.226.220.4',1526880876,1511703079),(2,'刘栋','196f09e9bd34168cbbb6481a5db67226','wnffto','刘栋','13026035351',4,6,'36.33.37.214',1512909127,'36.33.37.214',1512907210,1512546110);
/*!40000 ALTER TABLE `tp_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tp_button`
--

DROP TABLE IF EXISTS `tp_button`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_button` (
  `button_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '按钮主键id',
  `button_name` varchar(15) NOT NULL DEFAULT '' COMMENT '按钮名',
  `button_event` varchar(12) NOT NULL DEFAULT '' COMMENT '按钮对应的js事件',
  `button_type` varchar(32) NOT NULL DEFAULT '' COMMENT '按钮类型ajax，href，firame',
  `button_sort` int(11) NOT NULL DEFAULT '0' COMMENT '按钮排序',
  `button_desc` varchar(128) NOT NULL DEFAULT '' COMMENT '按钮的作用描述',
  PRIMARY KEY (`button_id`),
  UNIQUE KEY `tp_button_button_id_uindex` (`button_id`),
  UNIQUE KEY `tp_button_button_name_uindex` (`button_name`),
  UNIQUE KEY `tp_button_button_event_uindex` (`button_event`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='按钮表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_button`
--

LOCK TABLES `tp_button` WRITE;
/*!40000 ALTER TABLE `tp_button` DISABLE KEYS */;
INSERT INTO `tp_button` VALUES (1,'添加jia','add','iframe',0,'这是添加按钮'),(2,'修改','upd','iframe',1,'这是修改按钮啦啦啦'),(3,'删除','del','ajax',2,'这是删除按钮JoJo我不做人啦'),(4,'审核','isok','ajax',3,''),(5,'分配按钮','assignButton','iframe',4,''),(6,'屏蔽','isno','ajax',5,''),(8,'开启','open','ajax',6,''),(10,'关闭','close','ajax',7,''),(12,'模块权限','prevm','iframe',8,''),(14,'按钮权限','prevb','iframe',9,'奥赛');
/*!40000 ALTER TABLE `tp_button` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tp_module`
--

DROP TABLE IF EXISTS `tp_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(32) NOT NULL DEFAULT '' COMMENT '模块名',
  `module_pid` int(11) NOT NULL DEFAULT '0' COMMENT '模块父id',
  `module_code` varchar(1024) NOT NULL DEFAULT '' COMMENT '0-12-23类似这样的层级顺序拼接的模块id',
  `module_sort` int(11) NOT NULL DEFAULT '0' COMMENT '模块排序',
  `button_id` varchar(1024) NOT NULL DEFAULT '' COMMENT '1,2,3,4类似这样以逗号拼接的按钮id',
  `module_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '/idnex/Index/index类似这样的模块链接',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module_module_id_uindex` (`module_id`),
  UNIQUE KEY `module_module_name_uindex` (`module_name`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COMMENT='模块表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_module`
--

LOCK TABLES `tp_module` WRITE;
/*!40000 ALTER TABLE `tp_module` DISABLE KEYS */;
INSERT INTO `tp_module` VALUES (1,'系统管理',0,'0',0,'',''),(2,'权限管理',1,'1-2',0,'',''),(3,'按钮管理',2,'1-2-3',0,'1,2,3','/System/button'),(4,'模块管理',2,'1-2-4',1,'1,2,3,5','/System/module'),(5,'角色管理',2,'1-2-5',2,'1,2,3,12,14','/System/role'),(6,'管理员管理',2,'1-2-6',3,'1,2,3','/System/admin'),(7,'信息管理',0,'0',4,'',''),(8,'文章管理',7,'7-8',5,'',''),(9,'文章列表',8,'7-8-9',6,'1,2,3','/article/articleList'),(116,'原创文章ss',8,'7-8-116',122,'1,2,3,4,6','/article/articlesss'),(117,'SEO',1,'1-117',0,'',''),(119,'SEO管理',117,'1-117-119',0,'2','/Index/seo'),(120,'品牌故事',8,'7-8-120',0,'','/article/asd'),(122,'平台文章',8,'7-8-122',0,'','/article/lalala'),(124,'分类管理',7,'7-124',0,'',''),(126,'分类列表',124,'7-124-126',0,'','/category/lalal');
/*!40000 ALTER TABLE `tp_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tp_role`
--

DROP TABLE IF EXISTS `tp_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(32) NOT NULL DEFAULT '' COMMENT '角色名称',
  `role_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `module_id` varchar(1024) NOT NULL DEFAULT '' COMMENT '12,34,55类似这样的模块id拼接的字符串',
  `button_json` varchar(1024) NOT NULL DEFAULT '' COMMENT '由模块约束的json字符串',
  `addtime` int(11) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `tp_role_role_id_uindex` (`role_id`),
  UNIQUE KEY `tp_role_role_name_uindex` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tp_role`
--

LOCK TABLES `tp_role` WRITE;
/*!40000 ALTER TABLE `tp_role` DISABLE KEYS */;
INSERT INTO `tp_role` VALUES (1,'超级管理员','','7,124,126,8,122,120,116,9,1,117,119,2,6,5,4,3','{\"3\":[\"1\",\"2\",\"3\"],\"4\":[\"1\",\"2\",\"3\",\"5\"],\"5\":[\"1\",\"2\",\"3\",\"12\",\"14\"],\"6\":[\"1\",\"2\",\"3\"],\"119\":[\"2\"]}',1511703079),(4,'财务ss','财务是最漂亮的','7,8,9,116','{\"116\":[\"3\",\"4\",\"1\",\"2\",\"6\"]}',1512544821);
/*!40000 ALTER TABLE `tp_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-11 20:00:15
