CREATE TABLE IF NOT EXISTS wp_blacklist
(
  user_id VARCHAR(255) NOT NULL,
  context_id VARCHAR(255) NOT NULL,
  expiration INT(11)
);
CREATE TABLE IF NOT EXISTS wp_messages
(
  id VARCHAR(255) PRIMARY KEY NOT NULL,
  context_id VARCHAR(255) NOT NULL,
  hook_point VARCHAR(255),
  subject VARCHAR(255),
  message TEXT,
  active TINYINT(4)
);
CREATE TABLE IF NOT EXISTS wp_rules
(
  id VARCHAR(255) PRIMARY KEY NOT NULL,
  start VARCHAR(31),
  end VARCHAR(31),
  pause_start VARCHAR(31),
  pause_end VARCHAR(31),
  slot_duration VARCHAR(31),
  registration_start VARCHAR(31),
  registration_end VARCHAR(31),
  one_schedule_by_day_and_user TINYINT(1),
  days INT(11)
);
CREATE TABLE IF NOT EXISTS wp_schedule
(
  id VARCHAR(255) PRIMARY KEY NOT NULL,
  user_id VARCHAR(255),
  workplace_id VARCHAR(255),
  start INT(11),
  duration VARCHAR(31),
  comment TEXT,
  type ENUM('normal', 'blocked')
);
CREATE TABLE IF NOT EXISTS wp_waiting_list
(
  counter INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  workplace_id VARCHAR(255),
  user_id VARCHAR(255),
  day INT(11),
  insertion_timestamp INT(11)
);
CREATE TABLE IF NOT EXISTS wp_workplaces
(
  id VARCHAR(255) PRIMARY KEY NOT NULL,
  name VARCHAR(255),
  description TEXT,
  active ENUM('on', 'off') DEFAULT 'off',
  context_id VARCHAR(255),
  rule_id VARCHAR(255)
);