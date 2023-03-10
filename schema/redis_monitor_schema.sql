/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.3.32-MariaDB-log : Database - redis_monitor
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`redis_monitor` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `redis_monitor`;

/*Table structure for table `redis_status` */

DROP TABLE IF EXISTS `redis_status`;

CREATE TABLE `redis_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(30) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `is_live` tinyint(4) DEFAULT NULL,
  `max_connections` int(11) DEFAULT NULL,
  `threads_connected` int(11) DEFAULT NULL,
  `blocked_connected` int(11) DEFAULT NULL,
  `rejected_connected` int(11) DEFAULT NULL,
  `qps` int(11) DEFAULT NULL,
  `maxmemory_human` varchar(30) DEFAULT NULL,
  `used_memory_rss_human` varchar(30) DEFAULT NULL,
  `used_memory_peak_human` varchar(30) DEFAULT NULL,
  `runtime` int(11) DEFAULT NULL,
  `db_version` varchar(100) DEFAULT NULL,
  `aof_enabled` int(11) DEFAULT NULL,
  `mode` varchar(100) DEFAULT NULL,
  `cluster_enabled` int(11) DEFAULT NULL,
  `Seconds_Behind_Master` varchar(100) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_h_d_p_c` (`host`,`tag`,`port`,`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `redis_status_history` */

DROP TABLE IF EXISTS `redis_status_history`;

CREATE TABLE `redis_status_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(30) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `is_live` tinyint(4) DEFAULT NULL,
  `max_connections` int(11) DEFAULT NULL,
  `threads_connected` int(11) DEFAULT NULL,
  `blocked_connected` int(11) DEFAULT NULL,
  `rejected_connected` int(11) DEFAULT NULL,
  `qps` int(11) DEFAULT NULL,
  `maxmemory_human` varchar(30) DEFAULT NULL,
  `used_memory_rss_human` varchar(30) DEFAULT NULL,
  `used_memory_peak_human` varchar(30) DEFAULT NULL,
  `runtime` int(11) DEFAULT NULL,
  `db_version` varchar(100) DEFAULT NULL,
  `aof_enabled` int(11) DEFAULT NULL,
  `mode` varchar(100) DEFAULT NULL,
  `cluster_enabled` int(11) DEFAULT NULL,
  `Seconds_Behind_Master` varchar(100) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_h_d_p_c` (`host`,`tag`,`port`,`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `redis_status_info` */

DROP TABLE IF EXISTS `redis_status_info`;

CREATE TABLE `redis_status_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(100) DEFAULT NULL COMMENT '???????????????Redis???IP??????',
  `tag` varchar(100) DEFAULT NULL COMMENT '???????????????Redis???????????????',
  `pwd` varchar(100) DEFAULT NULL COMMENT '???????????????Redis?????????',
  `port` int(11) DEFAULT NULL COMMENT '???????????????Redis????????????',
  `monitor` tinyint(4) DEFAULT 1 COMMENT '0???????????????;1???????????????',
  `send_mail` tinyint(4) DEFAULT 1 COMMENT '0?????????????????????;1?????????????????????',
  `send_mail_to_list` varchar(255) DEFAULT NULL COMMENT '???????????????',
  `send_weixin` tinyint(4) DEFAULT 1 COMMENT '0?????????????????????;1?????????????????????',
  `send_weixin_to_list` varchar(100) DEFAULT NULL COMMENT '???????????????',
  `alarm_threads_running` tinyint(4) DEFAULT NULL COMMENT '????????????????????????????????????1????????????',
  `threshold_alarm_threads_running` int(11) DEFAULT NULL COMMENT '?????????????????????',
  `alarm_used_memory_status` tinyint(4) DEFAULT NULL COMMENT '????????????????????????????????????1????????????',
  `threshold_warning_used_memory` varchar(100) DEFAULT NULL COMMENT '???????????????????????????',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_i_d_p` (`host`,`tag`,`port`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Redis???????????????';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
