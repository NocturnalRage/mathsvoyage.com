CREATE TABLE lists (
  list_id int unsigned NOT NULL AUTO_INCREMENT,
  list_name varchar(50) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (list_id)
) COMMENT='Email lists that people can subscribe to.';

INSERT INTO lists VALUES
(1,'Free Newsletter',now(),now());

CREATE TABLE subscriber_status (
  subscriber_status_id smallint unsigned NOT NULL,
  description varchar(50) NOT NULL,
  PRIMARY KEY (subscriber_status_id)
) COMMENT='Records the status of a subscriber';

INSERT INTO subscriber_status VALUES
(1,'Subscribed'),
(2,'Unsubscribed'),
(3,'Bounced'),
(4,'Marked as spam');

CREATE TABLE subscribers (
  list_id int unsigned NOT NULL,
  user_id int unsigned NOT NULL,
  subscriber_status_id smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  subscribed_at datetime NOT NULL,
  last_campaign_at datetime NOT NULL,
  last_autoresponder_at datetime NOT NULL,
  PRIMARY KEY (list_id, user_id),
  KEY subscriber_user (user_id),
  FOREIGN KEY (list_id) REFERENCES lists(list_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (subscriber_status_id) REFERENCES subscriber_status(subscriber_status_id)
) COMMENT='Show which email lists a user has subscribed to.';

insert into db_changes
values (
  6,
  'Create subscriber related tables for handling email newsletters.',
  '0006_create_subscriber_table.sql',
  now()
);
