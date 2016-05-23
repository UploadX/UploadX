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
) ENGINE=InnoDB DEFAULT CHARSET=latin1