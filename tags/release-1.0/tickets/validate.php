<?php 

require_once('db_config.php');
require_once('open_db.php');
require_once 'includes/misc.inc';

session_start();
$dest = "index.php";
$dest = $_POST['dest'];
/* get the incoming ID and password hash */
$user = mysql_real_escape_string($_POST["username"]);
$pass = sha1($_POST["password"]);
/* SQL statement to query the database */
$query = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
/* query the database */

//echo $query;
$result = mysql_query($query);

if ($row = mysql_fetch_row($result)) {
  /* access granted */
  header("Cache-control: private");
  $_SESSION["access"] = "granted";
  $_SESSION["uid"] = $row[0];
  $_SESSION["username"] = $row[1];
  header("Location: ./" . $dest);
} else{
  /* access denied &#8211; redirect back to login */
$_SESSION["access"] = "denied";
  header("Location: ./login.php?valid=false");
}
mysql_close($db_connection);
?>
