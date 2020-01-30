CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_email` varchar(255) NOT NULL,
  `user_login` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_last_login` int(11) UNSIGNED,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `token_id` int(255) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token_user_id` int(255) UNSIGNED NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token_token` varchar(255) NOT NULL,
  `token_created` int(11) UNSIGNED NOT NULL,
  `token_expires` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY  (`token_id`),
  UNIQUE KEY `token_token` (`token_token`),
  KEY `fk_user_id` (`token_user_id`),
  KEY `token_expires` (`token_expires`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`token_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;