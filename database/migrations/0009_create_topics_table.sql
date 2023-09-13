CREATE TABLE topics (
  topic_id int unsigned NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL,
  slug varchar(105) NOT NULL,
  curriculum_id smallint unsigned NOT NULL,
  learning_order smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (topic_id),
  UNIQUE KEY slug (slug, curriculum_id),
  UNIQUE KEY title (title, curriculum_id),
  FOREIGN KEY (curriculum_id) REFERENCES curricula(curriculum_id)
) COMMENT='Every topic will have a number of lessons.';

insert into db_changes
values (
  9,
  'Create the topics table.',
  '0009_create_topics_table.sql',
  now()
);
