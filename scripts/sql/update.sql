#2010.10.11

CREATE TABLE `marketc` (
	`marketc_id` INT(11) NOT NULL AUTO_INCREMENT,
	`marketc_img` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	`marketc_title` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	`marketc_text` TEXT NULL COLLATE 'utf8_unicode_ci',
	`marketc_order` INT(11) NOT NULL DEFAULT '50',
	PRIMARY KEY (`marketc_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;

CREATE TABLE `marketd` (
	`marketd_id` INT(11) NOT NULL AUTO_INCREMENT,
	`marketd_img` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
	`marketd_text` TEXT NULL COLLATE 'utf8_unicode_ci',
	`marketd_order` INT(11) NOT NULL DEFAULT '50',
	`marketc_id` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`marketd_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;

#2010.10.13
ALTER TABLE `team`  ADD COLUMN `team_text` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `team_title`;

#2010.10.19
ALTER TABLE `news`  ADD COLUMN `is_deleted` INT(2) NOT NULL DEFAULT '1' AFTER `news_created_date`;
UPDATE `news` SET `is_deleted`=0;
ALTER TABLE `marketd`  ADD COLUMN `is_deleted` INT(2) NOT NULL DEFAULT '1' AFTER `marketc_id`;
UPDATE `marketd` SET `is_deleted`=0;
ALTER TABLE `services`  ADD COLUMN `is_deleted` INT(2) NOT NULL DEFAULT '1' AFTER `services_order`;
UPDATE `services` SET `is_deleted`=0;

CREATE TABLE IF NOT EXISTS `pphotos` (
  `photos_id` int(11) NOT NULL AUTO_INCREMENT,
  `photos_parent_id` int(11) NOT NULL,
  `photos_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `photos_title` varchar(255) DEFAULT NULL,
  `photos_main` smallint(1) DEFAULT NULL,
  `photos_order` int(4) NOT NULL DEFAULT '50',
  `photos_type` enum('news','market','services') NOT NULL DEFAULT 'news',
  PRIMARY KEY (`photos_id`),
  KEY `photos_parent_id` (`photos_parent_id`),
  KEY `photos_type` (`photos_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

#2010.11.03
ALTER TABLE `marketc`  ADD COLUMN `marketc_descr` TEXT NULL COLLATE 'utf8_unicode_ci' AFTER `marketc_text`;