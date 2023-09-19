CREATE TABLE skill_question_numbers (
  skill_question_number_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_question_id int unsigned NOT NULL,
  answer float NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_number_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_questions(skill_question_id)
);

INSERT INTO  skill_question_types VALUES (2, 'Numeric', now(), now());

ALTER TABLE quiz_questions ADD COLUMN answer float NULL after skill_question_option_id;

insert into db_changes
values (
  14,
  'Create skill_question_numbers table.',
  '0014_create_skill_question_numbers_table.sql',
  now()
);
