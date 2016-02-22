CREATE TABLE IF NOT EXISTS `civicrm_mosaico_msg_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msg_tpl_id` int(10) unsigned NOT NULL,
  `hash_key` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `html` longtext NOT NULL,
  `metadata` longtext NOT NULL,
  `template` longtext NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_civicrm_mosaico_msg_template_msg_tpl_id` FOREIGN KEY (`msg_tpl_id`) REFERENCES `civicrm_msg_template` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
