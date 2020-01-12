<?php

	DEFINE ('DB_USER', 'ch');
	DEFINE ('DB_PASSWORD', 'travel');
	DEFINE ('DB_HOST', 'localhost');
	DEFINE ('DB_NAME', 'ch');

	$CONNECT = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
	OR die('Could not connect to MySQL' .mysqli_connect_error());
?>