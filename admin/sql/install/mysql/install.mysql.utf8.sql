DROP TABLE IF EXISTS `#__marketplace_extensions`;
CREATE TABLE `#__marketplace_extensions` (
  `store_extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_repository_id` int(11) DEFAULT '0',
  `extension_id` int(11) DEFAULT '0',
  `name` varchar(100) DEFAULT '',
  `description` text NOT NULL,
  `element` varchar(100) DEFAULT '',
  `type` varchar(255) DEFAULT '',
  `folder` varchar(20) DEFAULT '',
  `client_id` tinyint(3) DEFAULT '0',
  `version` varchar(80) DEFAULT '',
  `data` text NOT NULL,
  `detailsurl` text NOT NULL,
  `infourl` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `plan` varchar(30) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `reviews` int(11) NOT NULL DEFAULT '0',
  `rating` float(11,2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`store_extension_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Marketplace Extensions';

DROP TABLE IF EXISTS `#__marketplace_repositories`;
CREATE TABLE `#__marketplace_repositories` (
  `store_repository_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `location` text NOT NULL,
  `published` int(11) DEFAULT '0',
  `last_check_timestamp` bigint(20) DEFAULT '0',
  PRIMARY KEY (`store_repository_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='marketplace Repositories';