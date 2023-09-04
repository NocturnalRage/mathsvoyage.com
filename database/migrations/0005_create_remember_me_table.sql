CREATE TABLE remember_me(
  user_id int unsigned NOT NULL,
  session_id varchar(255) NOT NULL,
  token varchar(255) NOT NULL,
  created_at datetime NOT NULL,
  expires_at datetime NOT NULL,
  PRIMARY KEY (user_id,session_id,token),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
) COMMENT='Remember me cookie information.';

insert into db_changes
values (
  5,
  'Create the remember_me table to validate remember me cookies.',
  '0005_create_remember_me_table.sql',
  now()
);
