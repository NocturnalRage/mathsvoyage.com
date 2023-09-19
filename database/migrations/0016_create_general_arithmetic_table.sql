CREATE TABLE general_arithmetic_scores (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  correct smallint unsigned NOT NULL,
  question_count smallint unsigned NOT NULL,
  started_at datetime NOT NULL,
  completed_at datetime NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

insert into db_changes
values (
  16,
  'Create the general_arithmetic table.',
  '0016_create_general_arithmetic_table.sql',
  now()
);
