CREATE TABLE skills (
  skill_id int unsigned NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL,
  slug varchar(105) NOT NULL,
  topic_id int unsigned NOT NULL,
  learning_order smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (skill_id),
  UNIQUE KEY slug (slug, topic_id),
  UNIQUE KEY title (title, topic_id),
  FOREIGN KEY (topic_id) REFERENCES topics(topic_id)
) COMMENT='The mathematical skill to be taught';

insert into db_changes
values (
  10,
  'Create the skills table.',
  '0010_create_skills_table.sql',
  now()
);
