CREATE TABLE skill_question_types (
  skill_question_type_id tinyint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(100) DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (`skill_question_type_id`)
);

INSERT INTO  skill_question_types VALUES (1, 'Multiple choice', now(), now());
INSERT INTO  skill_question_types VALUES (2, 'Numeric', now(), now());

CREATE TABLE skill_question_categories (
  skill_question_category_id tinyint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(100) DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (`skill_question_category_id`)
);

INSERT INTO  skill_question_categories VALUES (1, 'Fluency', now(), now());
INSERT INTO  skill_question_categories VALUES (2, 'Problem Solving', now(), now());
INSERT INTO  skill_question_categories VALUES (3, 'Reasoning', now(), now());


CREATE TABLE skill_questions (
  skill_question_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_id int unsigned NOT NULL,
  skill_question_type_id tinyint unsigned NOT NULL,
  skill_question_category_id tinyint unsigned NOT NULL,
  question text NOT NULL,
  question_image varchar(255) NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_id),
  FOREIGN KEY (skill_id) REFERENCES skills(skill_id),
  FOREIGN KEY (skill_question_type_id) REFERENCES skill_question_types(skill_question_type_id),
  FOREIGN KEY (skill_question_category_id) REFERENCES skill_question_categories(skill_question_category_id)
);

CREATE TABLE skill_question_options (
  skill_question_option_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_question_id int unsigned NOT NULL,
  option_text varchar(1000) NOT NULL,
  option_order tinyint NOT NULL,
  correct bool NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_question_option_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_questions(skill_question_id)
);

CREATE TABLE mastery_levels (
  mastery_level_id tinyint unsigned NOT NULL,
  mastery_level_desc varchar(20) NOT NULL,
  points tinyint NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (mastery_level_id)
);

INSERT INTO mastery_levels VALUES (1, 'Attempted', 0, now(), now());
INSERT INTO mastery_levels VALUES (2, 'Familiar', 50, now(), now());
INSERT INTO mastery_levels VALUES (3, 'Proficient', 80, now(), now());
INSERT INTO mastery_levels VALUES (4, 'Mastered', 100, now(), now());

CREATE TABLE skill_mastery (
  skill_id int unsigned NOT NULL,
  user_id int unsigned NOT NULL,
  mastery_level_id tinyint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_id, user_id),
  FOREIGN KEY (skill_id) REFERENCES skills(skill_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (mastery_level_id) REFERENCES mastery_levels(mastery_level_id)
);

CREATE TABLE quiz_types (
  quiz_type_id tinyint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(30) NOT NULL,
  num_questions tinyint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (quiz_type_id)
);

INSERT INTO quiz_types values(1, 'Skill Quiz', 7, now(), now());
INSERT INTO quiz_types values(2, 'Topic Quiz', 20, now(), now());
INSERT INTO quiz_types values(3, 'Curriculum Quiz', 30, now(), now());
INSERT INTO quiz_types values(4, 'Mastery Quiz', 50, now(), now());

CREATE TABLE quizzes (
  quiz_id int unsigned NOT NULL AUTO_INCREMENT,
  quiz_type_id tinyint unsigned NOT NULL,
  skill_id int unsigned NULL,
  topic_id int unsigned NULL,
  curriculum_id smallint unsigned NULL,
  user_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  started_at datetime NULL,
  completed_at datetime NULL,
  PRIMARY KEY (quiz_id),
  FOREIGN KEY (quiz_type_id) REFERENCES quiz_types(quiz_type_id),
  FOREIGN KEY (skill_id) REFERENCES skills(skill_id),
  FOREIGN KEY (topic_id) REFERENCES topics(topic_id),
  FOREIGN KEY (curriculum_id) REFERENCES curricula(curriculum_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE quiz_questions (
  quiz_id int unsigned NOT NULL,
  skill_question_id int unsigned NOT NULL,
  skill_question_option_id int unsigned NULL,
  correct_unaided boolean NULL,
  created_at datetime NOT NULL,
  started_at datetime NULL,
  answered_at datetime NULL,
  PRIMARY KEY (quiz_id, skill_question_id),
  FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id),
  FOREIGN KEY (skill_question_id) REFERENCES skill_questions(skill_question_id),
  FOREIGN KEY (skill_question_option_id) REFERENCES skill_question_options(skill_question_option_id)
);

insert into db_changes
values (
  11,
  'Create tables for quizzes.',
  '0011_create_quiz_tables.sql',
  now()
);
