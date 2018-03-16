BEGIN;
CREATE TABLE `s1rate_results` (
  `page_id` int(10) UNSIGNED NOT NULL,
  `title` varbinary(255) NOT NULL DEFAULT '\0',
  `item1` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item2` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item3` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item4` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `item5` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `s1rate_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `score` tinyint(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) 

ALTER TABLE `s1rate_results`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `title` (`title`);

ALTER TABLE `s1rate_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`,`user_id`);
ALTER TABLE `s1rate_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;



