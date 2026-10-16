-- ---------------------------------------------------------------------------
-- ZED: settings for the security audit fixes (October 2026)
--
-- Run once, after upgrade_mysql8.sql:
--   mysql -u root zedcms < upgrade_2026_10_security.sql
--
-- Adds the CMS Settings > Security switches (safe to run more than once):
--   captcha_cms_login   1 = captcha on the CMS login and forgot-password forms
--   captcha_contact_us  1 = captcha on the site's own Contact Us form
--   contact_form_type   zoho = Zoho Desk form (as before), site = the site's own form
-- ---------------------------------------------------------------------------
SET NAMES utf8mb4;

INSERT INTO `zed_global_config` (`label`, `value`)
SELECT 'captcha_cms_login', '1' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `zed_global_config` WHERE `label` = 'captcha_cms_login');

INSERT INTO `zed_global_config` (`label`, `value`)
SELECT 'captcha_contact_us', '1' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `zed_global_config` WHERE `label` = 'captcha_contact_us');

INSERT INTO `zed_global_config` (`label`, `value`)
SELECT 'contact_form_type', 'zoho' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `zed_global_config` WHERE `label` = 'contact_form_type');
