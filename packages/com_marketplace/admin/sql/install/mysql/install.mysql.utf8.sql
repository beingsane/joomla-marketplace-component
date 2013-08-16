DROP TABLE IF EXISTS `#__marketplace_extensions`;
CREATE TABLE `#__marketplace_extensions` (
  `marketplace_extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `marketplace_repository_id` int(11) DEFAULT '0',
  `ref_id` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `identifier` varchar(100) DEFAULT '',
  `section` varchar(100) DEFAULT '',
  `display` varchar(10) DEFAULT '',
  `type` varchar(255) DEFAULT '',
  `tags` varchar(255) DEFAULT '',
  `pathway` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `plan` varchar(30) DEFAULT NULL,
  `folder` varchar(100) DEFAULT NULL,
  `client_id` tinyint(3) DEFAULT '0',
  `purchased` tinyint(3) DEFAULT '0',
  `purchased_date` timestamp NULL DEFAULT NULL,
  `description` text,
  `gallery` text,
  `version` varchar(100) DEFAULT '',
  `reviews` int(11) NOT NULL DEFAULT '0',
  `rating` float(11,2) NOT NULL DEFAULT '0.00',
  `item_url` varchar(255) NOT NULL DEFAULT '',
  `author_url` varchar(255) NOT NULL DEFAULT '',
  `demo_url` varchar(255) NOT NULL DEFAULT '',
  `details_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`marketplace_extension_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Marketplace Extensions';

DROP TABLE IF EXISTS `#__marketplace_repositories`;
CREATE TABLE `#__marketplace_repositories` (
  `marketplace_repository_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `location` text NOT NULL,
  `published` int(11) DEFAULT '0',
  `last_check_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`marketplace_repository_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='marketplace Repositories';