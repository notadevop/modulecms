
-- Хранит всех пользвателей которые зарегестрировались
-- имя все остальное
-- FIX HERE: https://dzone.com/articles/dealing-with-mysql-error-code-1215-cannot-add-foreign-key-constraint
-- FIXI HERE: https://www.percona.com/blog/2017/04/06/dealing-mysql-error-code-1215-cannot-add-foreign-key-constraint/


CREATE TABLE users (
  user_id int(255) NOT NULL AUTO_INCREMENT,
  user_name varchar(255) NOT NULL,
  user_email varchar(255) NOT NULL,
  user_password varchar(255) NOT NULL,
  user_hash varchar(255) NOT NULL,
  user_registration_date datetime(6) NOT NULL,

  PRIMARY KEY (user_id)
) ENGINE=INNODB CHARSET=utf8;

ALTER TABLE users ADD INDEX users_idx(`user_id`);

-- Хранит все именна ролей и их индекс 
-- например Администратор и его индекс 1 
-- Роли нужны для понимания кто какими правами обладает.

CREATE TABLE roles (
    role_id INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    role_name varchar(255) NOT NULL
) ENGINE=INNODB CHARSET=utf8;

ALTER TABLE roles ADD INDEX roles_idx(`role_id`);

-- Хранит все индекс привелегий и их пояснение 
-- например 1 и его пояснение. 
-- Привелегии нужны для работы по коду сайта и доступа в разные углы

CREATE TABLE permissions (
    perm_id INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    perm_desc varchar(255) NOT NULL
) ENGINE=INNODB CHARSET=utf8;

ALTER TABLE permissions ADD INDEX permissions_idx(`perm_id`);

-- В данной таблице склеивается привелегии и их роли 
-- чтобы было понятно за что отвечает какая роль 

CREATE TABLE role_perm (
    role_id INT(10) NOT NULL,
    perm_id INT(10) NOT NULL,

    FOREIGN KEY (role_id) REFERENCES `roles`(`role_id`),
    FOREIGN KEY (perm_id) REFERENCES `permissions`(`perm_id`)

) ENGINE=INNODB CHARSET=utf8;

-- В Данной таблице склеивается Пользователь с именем указанной роли
-- Чтобы было понятно, какая роль у какого пользователя и за что отвечает он отвечает 

CREATE TABLE user_role (
  user_id int(255) NOT NULL,
  role_id int(255) NOT NULL,

  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (role_id) REFERENCES roles(role_id)
) ENGINE=INNODB CHARSET=utf8;







