<?php
require_once('db_config.php');
require_once('open_db.php');
require_once('fonts/fonts.php');
require_once 'includes/misc.inc';
session_start();

header("Cache-control: private");

//if($_SESSION["access"] == "granted") {
$file = safe($_GET['file']);
$count = safe($_GET['count']);
print_r(update_count($file,$count));
//}
function update_count($file,$count){
  $currcount = 0;
  $query = sprintf("SELECT `count` FROM `tickets` WHERE `file` = '%s'",$file);
  $result = mysql_query($query);
  while ($row = mysql_fetch_assoc($result)) {
    $currcount = $row['count'];
  }
  $query = sprintf("UPDATE `tickets` SET `count` = %d, `updated` = NOW() WHERE `file` = '%s' LIMIT 1", $count + $currcount, $file);
  $result = mysql_query($query);
  if($result){
    $query2 = sprintf("SELECT * FROM `tickets` WHERE `file` = '%s'",$file);
    $result2 = mysql_query($query2);
    if($result2){
      while ($row = mysql_fetch_assoc($result2)) {
      return json_encode($row);
      }
    } else {
          echo "MySQL Error.  The Query was <br>$query<br>" . mysql_error();
    }
  } else {
    echo "MySQL Error.  The Query was <br>$query<br>" . mysql_error();
  }
}