CREATE TABLE IF NOT EXISTS /*_*/s1rate_results (
  `page_id` int(10) UNSIGNED NOT NULL PRIMARY KEY ,
  `title` varbinary(255) NOT NULL ,
  `item1` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item2` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item3` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item4` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item5` int(10) UNSIGNED NOT NULL DEFAULT '0',
  INDEX (`title`)
) /*$wgDBTableOptions*/;


CREATE TABLE IF NOT EXISTS /*_*/s1rate_records (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `page_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_name` varbinary(255) NOT NULL,
  `score` tinyint(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (`page_id`,`user_id`)
) /*$wgDBTableOptions*/;