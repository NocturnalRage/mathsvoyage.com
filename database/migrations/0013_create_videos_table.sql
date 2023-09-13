CREATE TABLE videos (
  video_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_id int unsigned NOT NULL,
  youtube_id varchar(100) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (video_id),
  FOREIGN KEY (skill_id) REFERENCES skills(skill_id)
);

insert into db_changes
values (
  13,
  'Create videos table.',
  '0013_create_videos_table.sql',
  now()
);
