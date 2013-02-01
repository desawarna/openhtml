CREATE TABLE IF NOT EXISTS `sandbox` (
  `id` int(11) NOT NULL auto_increment,
  `javascript` text character set utf8,
  `html` text character set utf8,
  `created` datetime default NULL,
  `last_viewed` datetime default NULL,
  `url` char(255) default NULL,
  `customname` char(255) default NULL,
  `streaming` char(1) default 'n',
  `streaming_key` char(32) NOT NULL,
  `streaming_read_key` char(32) NOT NULL,
  `active_tab` varchar(10) NOT NULL,
  `active_cursor` int(11) NOT NULL,

  `revision` int(11) default 1,

  PRIMARY KEY  (`id`),
  KEY `viewed` (`last_viewed`),
  KEY `url` (`url`),
  KEY `streaming_key` (`streaming_key`),
  KEY `spam` (`created`,`last_viewed`),
  KEY `revision` (`url`, `revision`)
) character set utf8;

CREATE TABLE IF NOT EXISTS `owners` (
  `id` int(11) NOT NULL auto_increment,
  `name` char(255) NOT NULL,
  `url` char(255) NOT NULL,
  `revision` int(11) default 1,
  
  PRIMARY KEY  (`id`),
  KEY `name_url` (`name`, `url`, `revision`)
);

CREATE TABLE IF NOT EXISTS `ownership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL,
  `key` char(255) NOT NULL,
  `email` char(255) NOT NULL,
  `section` char(255) NOT NULL,
  `consent` tinyint(1) NOT NULL,
  
  PRIMARY KEY (`id`),
  KEY `name_key` (`name`,`key`)
);

CREATE TABLE IF NOT EXISTS `pagenames` (
  `url` char(255) NOT NULL,
  `customname` char(255)  NULL,
  `name` char(255) NOT NULL,

  PRIMARY KEY (`url`)
);

CREATE TABLE IF NOT EXISTS `replay_sessions` (
  `url` varchar(5) NOT NULL,
  `time` int(10) NOT NULL,
  `session` mediumtext NOT NULL
)

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` char(255) NOT NULL,
  `description` char(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `group_membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL,
  `section` char(255) NOT NULL,
  `role` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
);

