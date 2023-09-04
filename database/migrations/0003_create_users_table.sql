CREATE TABLE users (
  user_id int unsigned NOT NULL AUTO_INCREMENT,
  given_name varchar(100) NOT NULL,
  family_name varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  email_status_id smallint unsigned NOT NULL,
  bounce_count smallint unsigned NOT NULL,
  admin boolean DEFAULT 0 NOT NULL,
  token varchar(255) NOT NULL,
  activated_at datetime DEFAULT NULL,
  last_login_at datetime DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY email (email),
  UNIQUE KEY token (token),
  FOREIGN KEY (email_status_id) REFERENCES email_status(email_status_id)
) COMMENT='Users of the website.';

insert into db_changes
values (
  3,
  'Create the users table.',
  '0003_create_users_table.sql',
  now()
);
