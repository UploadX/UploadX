CREATE TABLE IF NOT EXISTS `uploadx_files` (
  `rowid` int(9) NOT NULL AUTO_INCREMENT,
  `access_count` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(30) NOT NULL,
  `file_id` varchar(20) NOT NULL,
  `file_original` varchar(1024) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploader_id` varchar(20) NOT NULL,
  `uploader_ip` varchar(33) NOT NULL,
  `upload_time` varchar(20) NOT NULL,
  `upload_size` varchar(20) NOT NULL,
  `delete_after` varchar(20) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `file_name` (`file_name`),
  UNIQUE KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS uploadx_logs (
  rowid INT(9) AUTO_INCREMENT PRIMARY KEY,
  log_level VARCHAR(25) NOT NULL,
  log_type VARCHAR(100) NOT NULL,
  log_message VARCHAR(1024) NOT NULL,
  log_time VARCHAR(20) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS uploadx_users (
  rowid INT(9) AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(20) NOT NULL UNIQUE,
  access_key VARCHAR(20) NOT NULL,
  filesize_limit VARCHAR(100) NOT NULL,
  uploads INT (6) NOT NULL DEFAULT 0,
  enabled BOOL NOT NULL DEFAULT true
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
