/*
SQLyog Ultimate v10.42 
MySQL - 8.0.31 : Database - redis_monitor
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`redis_monitor` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `redis_monitor`;

/*Table structure for table `redis_status` */

DROP TABLE IF EXISTS `redis_status`;

CREATE TABLE `redis_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(30) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `is_live` tinyint DEFAULT NULL,
  `max_connections` int DEFAULT NULL,
  `threads_connected` int DEFAULT NULL,
  `blocked_connected` int DEFAULT NULL,
  `rejected_connected` int DEFAULT NULL,
  `qps` int DEFAULT NULL,
  `maxmemory_human` varchar(30) DEFAULT NULL,
  `used_memory_rss_human` varchar(30) DEFAULT NULL,
  `used_memory_peak_human` varchar(30) DEFAULT NULL,
  `runtime` int DEFAULT NULL,
  `db_version` varchar(100) DEFAULT NULL,
  `aof_enabled` int DEFAULT NULL,
  `mode` varchar(100) DEFAULT NULL,
  `cluster_enabled` int DEFAULT NULL,
  `Seconds_Behind_Master` varchar(100) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_h_d_p_c` (`host`,`tag`,`port`,`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

/*Table structure for table `redis_status_history` */

DROP TABLE IF EXISTS `redis_status_history`;

CREATE TABLE `redis_status_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(30) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `port` int DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `is_live` tinyint DEFAULT NULL,
  `max_connections` int DEFAULT NULL,
  `threads_connected` int DEFAULT NULL,
  `blocked_connected` int DEFAULT NULL,
  `rejected_connected` int DEFAULT NULL,
  `qps` int DEFAULT NULL,
  `maxmemory_human` varchar(30) DEFAULT NULL,
  `used_memory_rss_human` varchar(30) DEFAULT NULL,
  `used_memory_peak_human` varchar(30) DEFAULT NULL,
  `runtime` int DEFAULT NULL,
  `db_version` varchar(100) DEFAULT NULL,
  `aof_enabled` int DEFAULT NULL,
  `mode` varchar(100) DEFAULT NULL,
  `cluster_enabled` int DEFAULT NULL,
  `Seconds_Behind_Master` varchar(100) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_h_d_p_c` (`host`,`tag`,`port`,`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

/*Table structure for table `redis_status_info` */

DROP TABLE IF EXISTS `redis_status_info`;

CREATE TABLE `redis_status_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `host` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '输入被监控Redis的IP地址',
  `tag` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '输入被监控Redis的主机信息',
  `pwd` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '输入被监控Redis的密码',
  `port` int DEFAULT NULL COMMENT '输入被监控Redis的端口号',
  `monitor` tinyint DEFAULT '1' COMMENT '0为关闭监控;1为开启监控',
  `send_mail` tinyint DEFAULT '1' COMMENT '0为关闭邮件报警;1为开启邮件报警',
  `send_mail_to_list` varchar(255) DEFAULT NULL COMMENT '邮件人列表',
  `send_weixin` tinyint DEFAULT '1' COMMENT '0为关闭微信报警;1为开启微信报警',
  `send_weixin_to_list` varchar(100) DEFAULT NULL COMMENT '微信公众号',
  `alarm_threads_running` tinyint DEFAULT NULL COMMENT '记录活动连接数告警信息，1为已记录',
  `threshold_alarm_threads_running` int DEFAULT NULL COMMENT '设置连接数阀值',
  `alarm_repl_status` tinyint DEFAULT NULL COMMENT '记录主从复制告警信息，1为记录主从状态，3为记录主从延迟状态',
  `threshold_warning_repl_delay` int DEFAULT NULL COMMENT '设置主从复制延迟阀值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_i_d_p` (`host`,`tag`,`port`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8mb3 COMMENT='Redis监控信息表';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
