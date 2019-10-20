-- 
-- Table structure for table `pm`
-- 

CREATE TABLE `pm` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`title` varchar(256) NOT NULL,
	`sender` bigint(20) NOT NULL,
	`recipient` bigint(20) NOT NULL,
	`message` text NOT NULL,
	`timestamp` int(10) NOT NULL,
	`tag` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`sender`) REFERENCES users(`id`),
	FOREIGN KEY (`recipient`) REFERENCES users(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
