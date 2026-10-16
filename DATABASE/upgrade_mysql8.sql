-- ---------------------------------------------------------------------------
-- ZED CMS: MariaDB 10.4 -> MySQL 8.4 schema upgrade
--
-- Run once after importing zedcms.sql:
--   mysql -u root zedcms < upgrade_mysql8.sql
--
-- * zero-date defaults ('0000-00-00 00:00:00') are invalid under MySQL 8's
--   default sql_mode; they become NULL
-- * created_on no longer changes on every UPDATE (it had ON UPDATE
--   CURRENT_TIMESTAMP, which reshuffled every "newest first" list after edits)
-- * login_attempts.ip_address fits IPv6
-- * all tables move from latin1/utf8mb3 to utf8mb4
-- ---------------------------------------------------------------------------
SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
SET NAMES utf8mb4;

ALTER TABLE `rating_images`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_category`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_contact`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_discipline`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_faq`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_home_slider`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_homevideos`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_incentives`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_media`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_media_group`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_menu`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_notifications`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_pages`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_parameter`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_posts`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `zed_programs`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_rating_process`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_resources`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_sector`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;
ALTER TABLE `zed_stakeholders`
  MODIFY `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY `modified_on` timestamp NULL DEFAULT NULL;

UPDATE `rating_images` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_category` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_discipline` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_faq` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_incentives` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_media_group` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_notifications` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_programs` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_rating_process` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_resources` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_sector` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';
UPDATE `zed_stakeholders` SET `modified_on` = NULL WHERE `modified_on` < '1970-01-02';

ALTER TABLE `login_attempts` MODIFY `ip_address` varchar(45) NOT NULL;

ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `groups` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `login_attempts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `rating_images` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users_groups` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_category` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_contact` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_discipline` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_faq` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_global_config` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_guidance_document` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_home_slider` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_homevideos` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_incentives` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_media` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_media_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_menu` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_news_block` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_notifications` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_pages` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_parameter` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_posts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_programs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_rating_process` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_ratings` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_resources` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_sector` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_stakeholders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `zed_testimonials` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
