CREATE TABLE IF NOT EXISTS `#__fwg_files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `project_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL default '1',
  `user_id` int(10) unsigned NOT NULL default '0',
  `published` tinyint(4) default '0',
  `ordering` int(11) default '0',
  `hits` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `descr` text,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `selected` tinyint(3) unsigned NOT NULL default '0',
  `longitude` float NOT NULL default '0',
  `latitude` float NOT NULL default '0',
  `copyright` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fwg_files_vote` (
  `user_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  `value` tinyint(4) NOT NULL default '0',
  `ipaddr` bigint default NULL,
  KEY `user_id` (`user_id`),
  KEY `file_id` (`file_id`),
  KEY `ipaddr` (`ipaddr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fwg_projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `descr` text,
  `ordering` int(10) unsigned NOT NULL default '0',
  `published` tinyint(3) unsigned NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `is_public` tinyint(3) unsigned NOT NULL default '0',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `color` char(6) NOT NULL default 'aaaaaa',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`),
  KEY `published` (`published`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fwg_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `published` tinyint(4) default '0',
  `name` varchar(255) NOT NULL,
  `plugin` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


REPLACE INTO `#__fwg_types` (`id`, `published`, `name`, `plugin`) VALUES (1, 1, 'Image', 'image');