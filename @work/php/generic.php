<?php 
	
	$Include_Dir = $_SERVER["DOCUMENT_ROOT"]. "/includes/";
	include($Include_Dir. "dbopen.php"); 
	$containing_dir = basename(dirname(__FILE__));
	
	$sql_manyfields1row = mysql_query("SELECT * FROM table WHERE id = '1' LIMIT 1") or die (mysql_error());
	$sql_1fieldmanyrows = mysql_query("SELECT id FROM table WHERE id > 0") or die (mysql_error());

while($row_many = mysql_fetch_array($sql_manyfields1row))
	{
	$a = $row_many['a']; // get a from database
	$b = $row_many['b']; // get b from database
	$c = substr($row_many['c'], 0, 250); // get first 250 characters of c from database
	$d = $row_many['d'];
	$e = $row_many['e'];
	$f = $row_many['f'];
	$g = $row_many['g'];
	}

while($row_one = mysql_fetch_array($sql_1fieldmanyrows))
	{
	$one[]=$row_one['id'];
	}

include($Include_Dir. "dbclose.php");
include($Include_Dir. "html_show_array.php");

html_show_array($row_one);
?>