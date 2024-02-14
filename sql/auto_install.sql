-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_mosaico_template`;
DROP TABLE IF EXISTS `civicrm_mosaico_msg_template`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_mosaico_msg_template
-- *
-- * Mosaico Templates Table
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mosaico_msg_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Settings ID',
  `msg_tpl_id` int unsigned NOT NULL COMMENT 'FK to civicrm_msg_template.',
  `hash_key` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT 'name',
  `html` longtext NOT NULL COMMENT 'HTML',
  `metadata` longtext NOT NULL COMMENT 'metadata',
  `template` longtext NOT NULL COMMENT 'template',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_mosaico_msg_template_msg_tpl_id FOREIGN KEY (`msg_tpl_id`) REFERENCES `civicrm_msg_template`(`id`) ON DELETE CASCADE
)
ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- /*******************************************************
-- *
-- * civicrm_mosaico_template
-- *
-- * Standalone Mosaico Template
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mosaico_template` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Template ID',
  `title` varchar(255) COMMENT 'Title',
  `base` varchar(64) COMMENT 'Name of the Mosaico base template (e.g. versafix-1)',
  `html` longtext COMMENT 'Fully renderd HTML',
  `metadata` longtext COMMENT 'Mosaico metadata (JSON)',
  `content` longtext COMMENT 'Mosaico content (JSON)',
  `msg_tpl_id` int unsigned NULL COMMENT 'FK to civicrm_msg_template.',
  `category_id` int unsigned NULL COMMENT 'ID of the category this mailing template is currently belongs. Foreign key to civicrm_option_value.',
  `domain_id` int unsigned NULL COMMENT 'Domain ID this message template belongs to.',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_mosaico_template_msg_tpl_id FOREIGN KEY (`msg_tpl_id`) REFERENCES `civicrm_msg_template`(`id`) ON DELETE SET NULL,
  CONSTRAINT FK_civicrm_mosaico_template_domain_id FOREIGN KEY (`domain_id`) REFERENCES `civicrm_domain`(`id`) ON DELETE SET NULL
)
ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
