CREATE TABLE worked_solutions (
  worked_solution_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_id int unsigned NOT NULL,
  question varchar(255) NULL,
  answer varchar(255) NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (worked_solution_id),
  FOREIGN KEY (skill_id) REFERENCES skills(skill_id)
);

insert into db_changes
values (
  12,
  'Create worked_solutions table.',
  '0012_create_worked_solutions_table.sql',
  now()
);
