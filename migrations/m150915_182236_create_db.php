<?php

use yii\db\Schema;
use yii\db\Migration;

class m150915_182236_create_db extends Migration
{
    public function safeUp()
    {
        $this->execute("
                SET FOREIGN_KEY_CHECKS=0;

                DROP TABLE IF EXISTS `comments_per_day`;
                CREATE TABLE `comments_per_day` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `owner_id` int(10) NOT NULL,
                  `media_id` varchar(70) NOT NULL DEFAULT '',
                  `count_comments` int(10) NOT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `owner_id` (`owner_id`) USING BTREE,
                  KEY `media_id` (`media_id`) USING BTREE,
                  KEY `date` (`date`),
                  CONSTRAINT `comments_per_day_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


                DROP TABLE IF EXISTS `likes_per_day`;
                CREATE TABLE `likes_per_day` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `owner_id` int(10) NOT NULL,
                  `media_id` varchar(70) NOT NULL DEFAULT '',
                  `count_likes` int(10) NOT NULL,
                  `date` timestamp NULL DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `owner_id` (`owner_id`) USING BTREE,
                  KEY `media_id` (`media_id`) USING BTREE,
                  KEY `date` (`date`),
                  CONSTRAINT `likes_per_day_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;


                DROP TABLE IF EXISTS `media_info`;
                CREATE TABLE `media_info` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `owner_id` int(10) NOT NULL,
                  `media_id` varchar(70) NOT NULL DEFAULT '',
                  `photo_url` varchar(150) NOT NULL DEFAULT '',
                  `photo_caption` varchar(150) NOT NULL DEFAULT '',
                  `count_likes` int(10) NOT NULL,
                  `count_comments` int(10) NOT NULL,
                  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `owner_id` (`owner_id`),
                  KEY `media_id` (`media_id`),
                  CONSTRAINT `media_info_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;


                DROP TABLE IF EXISTS `migration`;
                CREATE TABLE `migration` (
                  `version` varchar(180) NOT NULL,
                  `apply_time` int(11) DEFAULT NULL,
                  PRIMARY KEY (`version`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                INSERT INTO `migration` VALUES ('m000000_000000_base', '1442341919');


                DROP TABLE IF EXISTS `user`;
                CREATE TABLE `user` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `username` varchar(50) NOT NULL DEFAULT '',
                  `password` varchar(50) NOT NULL DEFAULT '',
                  `auth_key` varchar(50) NOT NULL DEFAULT '',
                  `access_token` varchar(100) NOT NULL DEFAULT '',
                  `refresh_token` varchar(100) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


                INSERT INTO `user` VALUES ('2', 'username', '', '', '2190483578.ff8a1d0.6a7c18dc783f4e468ecdc8test', '');
        ");
    }

    public function safeDown()
    {
        $this->execute("
                DROP TABLE IF EXISTS `comments_per_day`;
                DROP TABLE IF EXISTS `likes_per_day`;
                DROP TABLE IF EXISTS `media_info`;
                DROP TABLE IF EXISTS `migration`;
                DROP TABLE IF EXISTS `user`;
        ");
    }

}
