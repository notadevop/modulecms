
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+02:00";

CREATE TABLE `permissions` (
  `perm_id` int(10) NOT NULL,
  `perm_desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `role_id` int(10) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role_perm` (
  `role_id` int(10) NOT NULL,
  `perm_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `user_id` int(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_last_visit` int(15) NOT NULL,
  `user_registration_date` int(15) NOT NULL,
  `user_activated` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_tokens` (
  `token_id` int(255) NOT NULL,
  `token_user_id` int(255) NOT NULL,
  `token_user_agent` varchar(255) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `token_created` int(11) NOT NULL,
  `token_expires` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `user_role` (
  `user_id` int(255) NOT NULL,
  `role_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_online` (
        `session`   varchar(255) NOT NULL,
        `visitime`  varchar(255) NOT NULL,
        `userip`  varchar(255) NOT NULL,
        `uagent`  varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_activation` (
  `activation_id`     int(255) NOT NULL,
  `activation_user_id`  int(255) NOT NULL,
  `activation_token`    varchar(255) NOT NULL,
  `activation_confirm`  varchar(255) NOT NULL,
  `activation_created`  int(12) NOT NULL,
  `activation_expired`  int(12) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `users_idx` (`user_id`),
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `user_tokens` 
  ADD PRIMARY KEY (`token_id`),
  MODIFY `token_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1,
  ADD KEY `user_tokens_idx` (`token_user_id`),
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`token_user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`),
  ADD KEY `permissions_idx` (`perm_id`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  MODIFY `role_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1,
  ADD KEY `roles_idx` (`role_id`);

ALTER TABLE `role_perm`
  ADD KEY `role_id` (`role_id`),
  ADD KEY `perm_id` (`perm_id`);

ALTER TABLE `user_role`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

ALTER TABLE `permissions`
  MODIFY `perm_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `role_perm`
  ADD CONSTRAINT `role_perm_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `role_perm_ibfk_2` FOREIGN KEY (`perm_id`) REFERENCES `permissions` (`perm_id`);

ALTER TABLE `users_activation`
  ADD PRIMARY KEY (`activation_id`),
  ADD KEY `user_activations_idx` (`activation_id`),
  MODIFY `activation_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1,
  ADD CONSTRAINT `user_activations_ibfk_1` FOREIGN KEY (`activation_user_id`) REFERENCES `users` (`user_id`);

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_registration_date`) VALUES
(3, 'jcmax', 'jcmaxuser@gmail.com', 'sdfkakjsfhwe92398sdf34bc8932lsdlf238sdf', '2019-12-16 00:00:00.000000');

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Moderator'),
(3, 'Author'),
(4, 'Subscriber'),
(5, 'BlockedUser'),
(6, 'DeletedUser'),
(7, 'Visitor');

INSERT INTO `permissions` (`perm_id`, `perm_desc`) VALUES
(1, 'Administrator main on the website.'),
(2, 'Moderator can do deletes, edit posts, comment of all users.'),
(3, 'Author is contain all it own priveleges, edit it posts, comments.'),
(4, 'Subscriber can comments only.'),
(5, 'Blocked cannot temporary login.'),
(6, 'Deleted deactivated accaunt permanently. no login, keep data, to prevent new registration.'),
(7, 'Guest no accaunt, anonymous user.');

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(3, 1);

INSERT INTO `role_perm` (`role_id`, `perm_id`) VALUES
(1, 1),
(3, 3),
(2, 2),
(4, 4),
(5, 5),
(6, 6),
(7, 7);
