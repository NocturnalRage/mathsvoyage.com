CREATE TABLE password_reset_requests (
  user_id int unsigned NOT NULL,
  token varchar(255) NOT NULL,
  created_at datetime NOT NULL,
  processed_at datetime NULL,
  PRIMARY KEY (user_id, token, created_at),
  KEY pass_reset_check (token, processed_at, created_at),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
) COMMENT='Password reset requests and a token to validate them.';

insert into db_changes
values (
  4,
  'Create the password_reset_request table to validate password resets.',
  '0004_create_password_reset_request_table.sql',
  now()
);
