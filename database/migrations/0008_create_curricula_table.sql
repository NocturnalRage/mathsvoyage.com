CREATE TABLE curricula (
  curriculum_id smallint unsigned NOT NULL AUTO_INCREMENT,
  curriculum_name varchar(25) UNIQUE NOT NULL,
  curriculum_slug varchar(25) NOT NULL,
  display_order smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (curriculum_id),
  UNIQUE KEY (curriculum_slug)
) COMMENT='Used to store year levels.';

insert into db_changes
values (
  8,
  'Create the curricula table.',
  '0008_create_curricula_table.sql',
  now()
);
