# v0.9.9.9 insert tech_notes
ALTER TABLE `#__xbfilms` ADD `tech_notes` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'added v0.9.9.9' AFTER `filmsound`;
#merge cam_format and filmsound into tech_notes
UPDATE `#__xbfilms` SET `tech_notes` =  CONCAT('Camera : ',`cam_format`,'. ') WHERE`cam_format <> '';
UPDATE `#__xbfilms` SET `tech_notes` =  CONCAT(`tech_notes`,`filmsound`) WHERE`filmsound <> '';
