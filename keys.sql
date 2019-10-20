-- 
-- Table structure for table `messagekeys`
-- 

CREATE TABLE `messagekeys` (
	`user1` bigint(20) NOT NULL,
	`user2` bigint(20) NOT NULL,
	`mskey` varchar(255) NOT NULL,
	FOREIGN KEY (`user1`) REFERENCES users(`id`),
	FOREIGN KEY (`user2`) REFERENCES users(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;