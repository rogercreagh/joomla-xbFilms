# v0.9.9.8 insert first_seen 
ALTER TABLE `#__xbfilms` ADD `first_seen` DATE NULL DEFAULT NULL AFTER `acq_date`;
#change last_seen and acq_date and rev_date from datetime to date
ALTER TABLE `#__xbfilms` CHANGE `last_seen` `last_seen` DATE NULL DEFAULT NULL;
ALTER TABLE `#__xbfilms` CHANGE `acq_date` `acq_date` DATE NULL DEFAULT NULL;
ALTER TABLE `#__xbfilmreviews` CHANGE `rev_date` `rev_date` DATE NULL DEFAULT NULL;
#set first_seen to first review date if there is one
UPDATE `#__xbfilms` AS a SET `first_seen` =  (SELECT MIN(r.rev_date) FROM `#__xbfilmreviews` AS r WHERE r.film_id=a.id  AND r.rev_date IS NOT NULL);
#if first_seen still null and no review but there is a last_seen date then set first_seen to last_seen
UPDATE `#__xbfilms` SET `first_seen` = `last_seen` WHERE ISNULL(`first_seen`) AND ;
#if first seen_still null and no review then set first_seen to acq_date.
UPDATE `#__xbfilms` SET `first_seen` = `acq_date` WHERE ISNULL(`first_seen`);
#if last_seen is null set it to first_seen (both should always be set if one is)
UPDATE `#__xbfilms` SET `last_seen` = `first_seen` WHERE ISNULL(`last_seen`);
