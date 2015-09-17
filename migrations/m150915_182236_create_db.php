<?php

use yii\db\Schema;
use yii\db\Migration;

class m150915_182236_create_db extends Migration
{
    public function safeUp()
    {
        $this->execute("
                SET FOREIGN_KEY_CHECKS=0;

                DROP TABLE IF EXISTS `instagram_info`;
                CREATE TABLE `instagram_info` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `owner_id` int(11) NOT NULL,
                  `access_token` varchar(255) NOT NULL DEFAULT '',
                  `refresh_token` varchar(255) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `owner_id` (`owner_id`),
                  CONSTRAINT `instagram_info_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                DROP TABLE IF EXISTS `user`;
                CREATE TABLE `user` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `username` varchar(50) NOT NULL DEFAULT '',
                  `password` varchar(50) NOT NULL DEFAULT '',
                  `auth_key` varchar(50) NOT NULL DEFAULT '',
                  `access_token` varchar(50) NOT NULL,
                  `refresh_token` varchar(50) NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");
    }

    public function safeDown()
    {
        $this->execute("
                DROP TABLE IF EXISTS `instagram_info`;
                DROP TABLE IF EXISTS `user`;
        ");
    }

}
