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