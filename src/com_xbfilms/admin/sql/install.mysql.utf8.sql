# sql installation file for component xbFilms v0.9.9.3 13th July 2022
# NB no film data is installed with this file, default categories are created by the installation script

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `content_history_options`, `table`, `field_mappings`, `router`,`rules`) 
VALUES
('Xbfilms Film', 'com_xbfilms.film', 
'{"formFile":"administrator\\/components\\/com_xbfilms\\/models\\/forms\\/film.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbfilms","key":"id","type":"Film","prefix":"XbfilmsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "title",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "synopsis",
    "core_catid": "catid"
  }}',
'XbfilmsHelperRoute::getFilmRoute',''),

-- ('Xbfilms Person', 'com_xbfilms.person', 
-- '{"formFile":"administrator\\/components\\/com_xbfilms\\/models\\/forms\\/person.xml", 
--     "hideFields":["checked_out","checked_out_time"], 
--     "ignoreChanges":["checked_out", "checked_out_time"],
--     "convertToInt":[], 
--     "displayLookup":[
--         {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
--     ]
--  }',
-- '{"special":{"dbtable":"#__xbpersons","key":"id","type":"Person","prefix":"XbfilmsTable","config":"array()"},
--     "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
-- '{"common": {
--     "core_content_item_id": "id",
--     "core_title": "lastname",
--     "core_state": "state",
--     "core_alias": "alias",
--     "core_created_time": "created",
--     "core_body": "biography",
--     "core_catid": "catid"
--   }}',
-- 'XbfilmsHelperRoute::getPersonRoute',''),
-- 
-- ('Xbfilms Character', 'com_xbfilms.character', 
-- '{"formFile":"administrator\\/components\\/com_xbfilms\\/models\\/forms\\/character.xml", 
--     "hideFields":["checked_out","checked_out_time"], 
--     "ignoreChanges":["checked_out", "checked_out_time"],
--     "convertToInt":[], 
--     "displayLookup":[
--         {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
--     ]
--  }',
-- '{"special":{"dbtable":"#__xbcharacters","key":"id","type":"Character","prefix":"XbfilmsTable","config":"array()"},
--     "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
-- '{"common": {
--     "core_content_item_id": "id",
--     "core_title": "name",
--     "core_state": "state",
--     "core_alias": "alias",
--     "core_created_time": "created",
--     "core_body": "description",
--     "core_catid": "catid"
--   }}',
-- 'XbfilmsHelperRoute::getCharacterRoute',''),

('Xbfilms Review', 'com_xbfilms.review', 
'{"formFile":"administrator\\/components\\/com_xbfilms\\/models\\/forms\\/review.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbfilmreviews","key":"id","type":"Review","prefix":"XbfilmsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "title",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "review",
    "core_catid": "catid"
  }}',
'XbfilmsHelperRoute::getReviewRoute',''),

('XbFilms Category', 'com_xbfilms.category',
'{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], 
"ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
"convertToInt":["publish_up", "publish_down"], 
"displayLookup":[
{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}',
'{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},
"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
	"core_content_item_id":"id",
	"core_title":"title",
	"core_state":"published",
	"core_alias":"alias",
	"core_created_time":"created_time",
	"core_modified_time":"modified_time",
	"core_body":"description", 
	"core_hits":"hits",
	"core_publish_up":"null",
	"core_publish_down":"null",
	"core_access":"access", 
	"core_params":"params", 
	"core_featured":"null", 
	"core_metadata":"metadata", 
	"core_language":"language", 
	"core_images":"null", 
	"core_urls":"null", 
	"core_version":"version",
	"core_ordering":"null", 
	"core_metakey":"metakey", 
	"core_metadesc":"metadesc", 
	"core_catid":"parent_id", 
	"core_xreference":"null", 
	"asset_id":"asset_id"}, 
  "special":{
    "parent_id":"parent_id",
	"lft":"lft",
	"rgt":"rgt",
	"level":"level",
	"path":"path",
	"extension":"extension",
	"note":"note"}}',
'XbfilmsHelperRoute::getCategoryRoute','');

CREATE TABLE IF NOT EXISTS `#__xbpersons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `firstname` varchar(190) NOT NULL DEFAULT '',
  `lastname` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `biography` mediumtext,
  `portrait` mediumtext NOT NULL DEFAULT '',
  `nationality` varchar(100) NOT NULL DEFAULT '',
  `year_born` smallint,
  `year_died` smallint,
  `ext_links` mediumtext,
  `catid` int(10) NOT NULL  DEFAULT '0',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `modified` datetime,
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` mediumtext,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# CREATE UNIQUE INDEX `personaliasindex` ON `#__xbpersons` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbcharacters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext,
  `image` mediumtext NOT NULL DEFAULT '',
  `catid` int(10) NOT NULL  DEFAULT '0',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `modified` datetime,
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` mediumtext,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# CREATE UNIQUE INDEX `characteraliasindex` ON `#__xbcharacters` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbfilms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `subtitle` varchar(190) NOT NULL DEFAULT '',
  `summary` varchar(255) NOT NULL DEFAULT '',
  `synopsis` text,
  `setting` varchar(100) DEFAULT '',
  `poster_img` text,
  `rel_year` year, 
  `orig_lang` varchar(100) DEFAULT 'English',
  `studio` varchar(100) NOT NULL DEFAULT '',
  `country` varchar(100) DEFAULT '',
  `runtime` SMALLINT,
  `filmcolour` varchar(50) NOT NULL DEFAULT '',
  `aspect_ratio` varchar(50) NOT NULL DEFAULT '',
  `cam_format` varchar(50) NOT NULL DEFAULT '',
  `filmsound` varchar(50) NOT NULL DEFAULT '',
  `ext_links` text,
  `acq_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_seen` datetime,
  `last_seen` datetime,
  `catid` int(10) NOT NULL  DEFAULT '0',
  `access` int(10) NOT NULL  DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `version` int(10) NOT NULL DEFAULT '0',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(10) NOT NULL  DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# CREATE UNIQUE INDEX `filmaliasindex` ON `#__xbfilms` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbfilmperson` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `film_id` int(11) NOT NULL DEFAULT '0',
  `person_id` int(11) NOT NULL DEFAULT '0',
  `role` varchar(255) NOT NULL DEFAULT '',
  `role_note` varchar(255) NOT NULL DEFAULT '',
  `listorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_film_id` (`film_id`),
  KEY `idx_person_id` (`person_id`),
  KEY `idx_role` (`role`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xbfilmcharacter` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `film_id` int(11) NOT NULL DEFAULT '0',
  `char_id` int(11) NOT NULL DEFAULT '0',
  `actor_id` int(11) NOT NULL DEFAULT '0',
  `char_note` varchar(255) NOT NULL DEFAULT '',
  `listorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_film_id` (`film_id`),
  KEY `idx_person_id` (`char_id`),
  KEY `idx_actor_id` (`actor_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xbfilmreviews` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `film_id` int(10) NOT NULL DEFAULT '0',
  `summary` varchar(255),
  `rev_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `where_seen` varchar(100) NOT NULL DEFAULT '',
  `subtitled` BOOLEAN NOT NULL DEFAULT '0', 
  `reviewer` varchar(255) NOT NULL DEFAULT '',
  `review` mediumtext,
  `rating` int(4),
  `ext_links` mediumtext,
  `catid` int(10) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `modified` datetime,
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `metadata` mediumtext NOT NULL DEFAULT '',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL DEFAULT '',
  `note` mediumtext,
   PRIMARY KEY (`id`)
  )  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

# CREATE UNIQUE INDEX `reviewaliasindex` ON `#__xbfilmreviews` (`alias`);

