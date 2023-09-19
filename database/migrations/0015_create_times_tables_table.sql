CREATE TABLE times_tables (
  id smallint unsigned NOT NULL,
  title varchar(100) NOT NULL,
  min_number smallint NOT NULL,
  max_number smallint NOT NULL,
  total_questions smallint NOT NULL,
  repetitions smallint NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (id)
);

INSERT INTO  times_tables VALUES (1, '2 Times Tables', 2, 2, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (2, '3 Times Tables', 3, 3, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (3, '2 & 3 Times Tables', 2, 3, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (4, '4 Times Tables', 4, 4, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (5, '5 Times Tables', 5, 5, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (6, '2 to 5 Times Tables', 2, 5, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (7, '6 Times Tables', 6, 6, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (8, '7 Times Tables', 7, 7, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (9, '6 & 7 Times Tables', 6, 7, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (10, '2 to 7 Times Tables', 2, 7, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (11, '8 Times Tables', 8, 8, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (12, '9 Times Tables', 9, 9, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (13, '2 to 9 Times Tables', 2, 9, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (14, '10 and 11 Times Tables', 10, 11, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (15, '2 to 11 Times Tables', 2, 11, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (16, '12 Times Tables', 2, 11, 50, 4, now(), now());
INSERT INTO  times_tables VALUES (17, '1 to 12 Times Tables', 1, 12, 50, 16, now(), now());

CREATE TABLE times_tables_attempts (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL,
  current_times_tables_id smallint unsigned NOT NULL,
  attempt smallint unsigned NOT NULL,
  started_at datetime NOT NULL,
  completed_at datetime NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE times_tables_scores (
  id int unsigned NOT NULL AUTO_INCREMENT,
  attempt_id int unsigned NOT NULL,
  times_tables_id smallint unsigned NOT NULL,
  attempt smallint unsigned NOT NULL,
  score smallint unsigned NOT NULL,
  started_at datetime NOT NULL,
  completed_at datetime NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (attempt_id) REFERENCES times_tables_attempts(id),
  FOREIGN KEY (times_tables_id) REFERENCES times_tables(id)
);

insert into db_changes
values (
  15,
  'Create teh times_tables table.',
  '0015_create_times_tables_table.sql',
  now()
);
