<?php
	session_start();
	
	if (isset($_POST) && !empty($_POST))
	{
		$data = 
		'<?php
			$db_host = "' . $_POST['db_host'] . '";
			$db_name = "' . $_POST['db_name'] . '";
			$db_user = "' . $_POST['db_username'] . '";
			$db_pass = "' . $_POST['db_password'] . '";
		?>';
		
		file_put_contents('../framework/db.conf.inc.php', $data);
		
		require_once('../framework/db.conf.inc.php');
		
		$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
			
		if ($mysqli->connect_errno)
		{
			die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
		
		$query = "CREATE TABLE `hash` (
		  `name` varchar(10) NOT NULL,
		  `description` varchar(250) NOT NULL,
		  PRIMARY KEY (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		
		INSERT INTO `hash` (`name`, `description`) VALUES
		('md5', 'MD5'),
		('sha1', 'SHA 1'),
		('sha256', 'SHA 256'),
		('sha512', 'SHA 512');
		
		CREATE TABLE `session` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_user` int(11) NOT NULL,
		  `sid` varchar(100) NOT NULL,
		  `expiredate` datetime DEFAULT NULL,
		  `device` varchar(100) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `id_user` (`id_user`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;
		
		CREATE TABLE `user` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_hash` varchar(10) NOT NULL,
		  `username` varchar(100) NOT NULL,
		  `password` varchar(200) NOT NULL,
		  `admin` bit(1) NOT NULL DEFAULT b'0',
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `username` (`username`),
		  KEY `id_hash` (`id_hash`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
		
		INSERT INTO `user` (`id`, `id_hash`, `username`, `password`, `admin`) VALUES
		(1, 'sha512', ?, ?, b'1');
		
		CREATE TABLE `user_widget` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_user` int(11) NOT NULL,
		  `id_widget` int(11) NOT NULL,
		  `id_html` varchar(250) NOT NULL,
		  `position` int(11) NOT NULL,
		  `enabled` bit(1) NOT NULL DEFAULT b'1',
		  `visible` bit(1) NOT NULL DEFAULT b'1',
		  PRIMARY KEY (`id`),
		  KEY `id_user` (`id_user`),
		  KEY `id_widget` (`id_widget`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;
		
		DECLARE @id_user int(11);
		SET @id_user = (SELECT id FROM `user`)
		
		INSERT INTO `user_widget` (`id`, `id_user`, `id_widget`, `id_html`, `position`, `enabled`, `visible`) VALUES
		(1, 1, 1, 'sys_info', 0, b'1', b'1'),
		(2, 1, 2, 'cpu_info', 1, b'1', b'1'),
		(3, 1, 3, 'camera', 2, b'1', b'1'),
		(4, 1, 4, 'usb_info', 3, b'1', b'0'),
		(5, 1, 5, 'network', 4, b'1', b'0'),
		(6, 1, 6, 'memory_info', 5, b'1', b'1'),
		(7, 1, 7, 'updates', 8, b'1', b'0'),
		(8, 1, 8, 'disks', 9, b'1', b'0'),
		(9, 1, 9, 'processes', 10, b'1', b'0'),
		(12, 1, 10, 'power', 7, b'1', b'1'),
		(13, 1, 11, 'transmission', 6, b'1', b'1'),
		(14, 1, 12, 'cpu_graph', 6, b'1', b'1'),
		(15, 1, 13, 'temp_graph', 6, b'1', b'1');
		
		CREATE TABLE `widget` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `columns` int(11) NOT NULL DEFAULT '6',
		  `updatetime` int(11) NOT NULL DEFAULT '1000',
		  `title` varchar(100) NOT NULL,
		  `phpfile` varchar(250) NOT NULL,
		  `templatefile` varchar(250) NOT NULL,
		  `requireadmin` bit(1) NOT NULL DEFAULT b'0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;
		
		INSERT INTO `widget` (`id`, `columns`, `updatetime`, `title`, `phpfile`, `templatefile`, `requireadmin`) VALUES
		(1, 4, 1000, 'System Info', 'system', 'general_info.tpl', b'0'),
		(2, 4, 1000, 'CPU Info', 'processor', 'cpu_info.tpl', b'0'),
		(3, 4, 300000, 'Camera Image', 'raspistill', 'raspistill.tpl', b'1'),
		(4, 4, 3600000, 'USB Info', 'usb', 'usb.tpl', b'0'),
		(5, 4, 1000, 'Network Usage', 'network', 'network.tpl', b'0'),
		(6, 4, 1000, 'Memory Usage', 'memory', 'memory.tpl', b'0'),
		(7, 6, 3600000, 'Updates', 'updates', 'updates.tpl', b'1'),
		(8, 6, 60000, 'Disk Usage', 'disk', 'disks.tpl', b'0'),
		(9, 6, 1000, 'Processes', 'process', 'process.tpl', b'0'),
		(10, 2, 0, 'Power', 'power', 'power.tpl', b'1'),
		(11, 4, 1000, 'Transmission', 'transmission', 'transmission.tpl', b'0'),
		(12, 4, 1000, 'CPU Load', 'cpu_graph', 'cpu_graph.tpl', b'0'),
		(13, 4, 1000, 'Temperature', 'temp_graph', 'temp_graph.tpl', b'0');
		
		ALTER TABLE `user`
		  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_hash`) REFERENCES `hash` (`name`) ON UPDATE CASCADE;
		
		ALTER TABLE `user_widget`
		  ADD CONSTRAINT `user_widget_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
		  ADD CONSTRAINT `user_widget_ibfk_3` FOREIGN KEY (`id_widget`) REFERENCES `widget` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
		";
		
		$stmt = $mysqli->stmt_init();
		$stmt->prepare($query);
		$stmt->bind_param("ss", $_POST['admin_username'], hash('sha512', $_POST['admin_password'])); 
		$stmt->execute();
		$stmt->close();
		
		$mysqli->close();	
	}
	else
	{
		require_once('Smarty.class.php');
		
		$smarty = new Smarty();
		
		$smarty->display('install.tpl');
	}
?>