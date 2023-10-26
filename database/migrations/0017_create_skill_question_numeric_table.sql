UPDATE skill_question_types
SET    description = 'Expression'
WHERE  skill_question_type_id = 2;

INSERT INTO skill_question_types values (3, 'Numeric', now(), now());

CREATE TABLE numeric_types (
  numeric_type_id smallint unsigned NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL,
  PRIMARY KEY (numeric_type_id)
);

INSERT INTO numeric_types values (1, 'Numbers');
INSERT INTO numeric_types values (2, 'Integers');
INSERT INTO numeric_types values (3, 'Decimals');
INSERT INTO numeric_types values (4, 'Rationals');
INSERT INTO numeric_types values (5, 'Improper numbers');
INSERT INTO numeric_types values (6, 'Mixed numbers');
INSERT INTO numeric_types values (7, 'Percents');
INSERT INTO numeric_types values (8, 'Numbers with pi');

CREATE TABLE skill_question_numeric (
  skill_question_numeric_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_question_id int unsigned NOT NULL,
  numeric_type_id smallint unsigned NOT NULL,
  answer varchar(255) NOT NULL,
  simplify boolean NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_numeric_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_questions(skill_question_id),
  FOREIGN KEY (numeric_type_id) REFERENCES numeric_types(numeric_type_id)
);

insert into db_changes
values (
  17,
  'Create skill_question_numeric table.',
  '0017_create_skill_question_numeric_table.sql',
  now()
);
