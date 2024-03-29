# v1.0.1.0 add #__xbfilmgroup
CREATE TABLE IF NOT EXISTS `#__xbfilmgroup` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `film_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `role` varchar(255) NOT NULL DEFAULT '',
  `role_note` varchar(255) NOT NULL DEFAULT '',
  `listorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_film_id` (`film_id`),
  KEY `idx_group_id` (`group_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
