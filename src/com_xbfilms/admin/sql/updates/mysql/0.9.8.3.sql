# v0.9.8.3 change cat_date to acq_date
ALTER TABLE `#__xbfilms` CHANGE `cat_date` `acq_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
# v0.9.8 adding last_seen date column to xbfilms
ALTER TABLE `#__xbbooks` ADD `last_seen` DATETIME NULL DEFAULT NULL AFTER `acq_date`;
# set last_seen to latest review date
UPDATE `#__xbfilms`  AS a SET `last_seen` =  (SELECT MAX(r.rev_date) FROM `#__xbfilmreviews` AS r WHERE r.film_id=a.id  AND r.rev_date IS NOT NULL);
