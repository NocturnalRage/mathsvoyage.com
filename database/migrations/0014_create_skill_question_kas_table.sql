CREATE TABLE skill_question_kas (
  skill_question_kas_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_question_id int unsigned NOT NULL,
  answer varchar(255) NOT NULL,
  form boolean NOT NULL,
  simplify boolean NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_kas_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_questions(skill_question_id)
);

insert into db_changes
values (
  14,
  'Create skill_question_kas table.',
  '0014_create_skill_question_kas.sql',
  now()
);
