<?php
	//$host 	= "db2170.perfora.net"; // when it's on valleyforgepress.com
	//$host	= "localhost"; // when it's on my computer
	$host	= "\\\\VFP-SQL\pipe\sql\query"; // VFP SQL server - Goldmine, etc., 1433 is the port
	$user 	= "goldmine_reporting";
	$password = "goldmine_reporting";
	$dbname = "VFP_goldmine";
# Make the connection
	$connection = mssql_connect($host, $user, $password) or die ('Error connecting to mssql');
	mssql_select_db($dbname); 
	?>