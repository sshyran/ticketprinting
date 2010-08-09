<?php
//echo $dbhost . ", " . $dbuser . ", " . $dbpass . ", " . $dbname;
$db_connection = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
mysql_select_db($dbname);
?>
