<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;


	$charset_collate = '';
	if ( ! empty($wpforo->db->charset) ) $charset_collate = "DEFAULT CHARACTER SET " . $wpforo->db->charset;
	if ( ! empty($wpforo->db->collate) ) $charset_collate .= " COLLATE " . $wpforo->db->collate;
	$engine = version_compare($wpforo->db->db_version(), '5.6.4', '>=') ? 'InnoDB' : 'MyISAM';

	$wpforo_sql = array(
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_forums`(  
		  `forumid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `title` VARCHAR(255) NOT NULL,
		  `slug` VARCHAR(255) NOT NULL,
		  `description` LONGTEXT,
		  `parentid` INT UNSIGNED NOT NULL DEFAULT 0,
		  `icon` VARCHAR(255),
		  `last_topicid` INT UNSIGNED NOT NULL DEFAULT 0,
		  `last_postid` INT UNSIGNED NOT NULL DEFAULT 0,
		  `last_userid` INT UNSIGNED NOT NULL DEFAULT 0,
		  `last_post_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `topics` INT NOT NULL DEFAULT 0,
		  `posts` INT NOT NULL DEFAULT 0,
		  `permissions` TEXT,
		  `meta_key` TEXT,
		  `meta_desc` TEXT,
		  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  `is_cat` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  `cat_layout` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  `order` INT UNSIGNED NOT NULL DEFAULT 0,
		  PRIMARY KEY (`forumid`),
  		  UNIQUE KEY `UNIQUE SLUG` (`slug`(191))
		) ENGINE=MyISAM $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_topics`(  
		  `topicid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `forumid` INT UNSIGNED NOT NULL,
		  `first_postid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
		  `userid` INT UNSIGNED NOT NULL,
		  `title` VARCHAR(255) NOT NULL,
		  `slug` VARCHAR(255) NOT NULL,
		  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `last_post` BIGINT UNSIGNED NOT NULL DEFAULT 0,
		  `posts` INT NOT NULL DEFAULT 0,
		  `votes` INT NOT NULL DEFAULT 0,
		  `answers` INT NOT NULL DEFAULT 0,
		  `views` INT UNSIGNED NOT NULL DEFAULT 0,
		  `meta_key` TEXT,
		  `meta_desc` TEXT,
		  `type` TINYINT NOT NULL DEFAULT 0,
		  `closed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  `has_attach` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  PRIMARY KEY (`topicid`),
  		  UNIQUE KEY `UNIQUE SLUG` (`slug`(191)),
  		  FULLTEXT KEY `title` (`title`)
		) ENGINE=$engine $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_posts`(  
		  `postid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `parentid` BIGINT UNSIGNED NOT NULL DEFAULT 0,
		  `forumid` INT UNSIGNED NOT NULL,
		  `topicid` BIGINT UNSIGNED NOT NULL,
		  `userid` INT UNSIGNED NOT NULL,
		  `title` varchar(255),
		  `body` LONGTEXT,
		  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `likes` INT UNSIGNED NOT NULL DEFAULT 0,
		  `votes` INT NOT NULL DEFAULT 0,
		  `is_answer` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  		  `is_first_post` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  PRIMARY KEY (`postid`),
		  FULLTEXT KEY `title`(`title`(191)),
		  FULLTEXT KEY `body` (`body`),
		  FULLTEXT KEY `title_plus_body` (`title`,`body`)
		) ENGINE=$engine $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_profiles` (
		  `userid` INT UNSIGNED NOT NULL,
		  `title` VARCHAR(255) NOT NULL DEFAULT 'member',
		  `username` VARCHAR(255) NOT NULL,
		  `groupid` INT UNSIGNED NOT NULL,
		  `posts` INT NOT NULL DEFAULT 0,
		  `questions` INT NOT NULL DEFAULT 0,
  		  `answers` INT NOT NULL DEFAULT 0,
  		  `comments` INT NOT NULL DEFAULT 0,
		  `site` VARCHAR(255) DEFAULT NULL,
		  `icq` VARCHAR(255) DEFAULT NULL,
		  `aim` VARCHAR(255) DEFAULT NULL,
		  `yahoo` VARCHAR(255) DEFAULT NULL,
		  `msn` VARCHAR(255) DEFAULT NULL,
		  `facebook` VARCHAR(255) DEFAULT NULL,
		  `twitter` VARCHAR(255) DEFAULT NULL,
		  `gtalk` VARCHAR(255) DEFAULT NULL,
		  `skype` VARCHAR(255) DEFAULT NULL,
		  `avatar` VARCHAR(255) DEFAULT NULL,
		  `signature` TEXT,
		  `about` TEXT,
		  `occupation` TEXT,
		  `location` VARCHAR(255) DEFAULT NULL,
		  `last_login` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `rank` INT UNSIGNED NOT NULL DEFAULT 0,
		  `like` INT UNSIGNED NOT NULL DEFAULT 0,
		  `status` VARCHAR(8) DEFAULT 'active' COMMENT 'active, blocked, trashed, spamer',
		  `timezone` VARCHAR(255),
		  PRIMARY KEY (`userid`),
		  UNIQUE KEY `UNIQUE ID` (`userid`),
		  UNIQUE KEY `UNIQUE USERNAME` (`username`(191))
		) ENGINE=MyISAM $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_usergroups`(  
		  `groupid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name` VARCHAR(255) NOT NULL,
			`cans` LONGTEXT NOT NULL COMMENT 'board permissions',
		  `description` TEXT,
		  PRIMARY KEY (`groupid`),
		  UNIQUE KEY `UNIQUE GROUP NAME` (`name`(191))
		) ENGINE=MyISAM $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_languages`(  
		  `langid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name` VARCHAR(255) NOT NULL,
		  PRIMARY KEY (`langid`),
		  UNIQUE KEY `UNIQUE language name` (`name`(191))
		) ENGINE=MyISAM $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_phrases` (
		  `phraseid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `langid` INT UNSIGNED NOT NULL,
		  `phrase_key` varchar(255) NOT NULL,
		  `phrase_value` text NOT NULL,
		  PRIMARY KEY (`phraseid`),
		  KEY `langid` (`langid`),
		  KEY `phrase_key` (`phrase_key`(191)),
		  UNIQUE KEY lng_and_key_uniq (`langid`, `phrase_key`(191))
		) ENGINE=MyISAM $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_likes`(  
		  `likeid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `userid` INT UNSIGNED NOT NULL,
		  `postid`  INT UNSIGNED NOT NULL,
		  `post_userid` INT UNSIGNED NOT NULL,
		  PRIMARY KEY (`likeid`),
		  UNIQUE KEY `userid` (`userid`,`postid`)
		) ENGINE=$engine $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_views`(  
		  `vid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `userid` INT UNSIGNED NOT NULL,
		  `topicid`  INT UNSIGNED NOT NULL,
		  `created` TIMESTAMP NOT NULL,
		  PRIMARY KEY (`vid`)
		) ENGINE=$engine $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_votes`(  
		  `voteid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `userid` INT UNSIGNED NOT NULL,
		  `postid`  INT UNSIGNED NOT NULL,
		  `reaction` TINYINT NOT NULL DEFAULT 1,
		  `post_userid` INT UNSIGNED NOT NULL,
		  PRIMARY KEY (`voteid`),
		  UNIQUE KEY `userid` (`userid`,`postid`)
		) ENGINE=$engine $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_accesses`(  
		  `accessid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `access` VARCHAR(255) NOT NULL,
		  `title` VARCHAR(255) NOT NULL,
		  `cans` LONGTEXT NOT NULL COMMENT 'forum permissions',
		  PRIMARY KEY (`accessid`),
		  UNIQUE KEY ( `access`(191) )
		) ENGINE=MyISAM $charset_collate;",
		"CREATE TABLE IF NOT EXISTS `{$wpforo->db->prefix}wpforo_subscribes` (
		  `subid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `itemid` BIGINT UNSIGNED NOT NULL,
		  `type` varchar(5) NOT NULL,
		  `confirmkey` varchar(32) NOT NULL,
		  `userid` BIGINT UNSIGNED NOT NULL,
		  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		  PRIMARY KEY (`subid`),
		  UNIQUE KEY `itemid` (`itemid`,`type`,`userid`),
		  UNIQUE KEY `confirmkey` (`confirmkey`),
		  KEY `itemid_2` (`itemid`),
		  KEY `userid` (`userid`)
		) ENGINE=$engine $charset_collate;"
	);
	
?>