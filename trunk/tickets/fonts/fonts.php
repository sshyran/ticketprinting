<?php
require_once('db_config.php');
require_once('open_db.php');
require_once 'includes/misc.inc';

function fonts_populate_form($f){  
  $query = sprintf("SELECT * FROM fonts");
  $result = mysql_query($query);
  while ($row = mysql_fetch_assoc($result)) {
    echo "<option " . selected($f,$row["id"]) . " value=\"" . $row["id"] . "\">" . $row["name"] . "</option>";
  }
}

function get_font($id){
  $font_array = array();
  $query = sprintf("SELECT * FROM fonts WHERE id = %d LIMIT 1",$id);
  $result = mysql_query($query);
  if($row = mysql_fetch_assoc($result)){
    return $row;
  }else{
    $font_array["file"] = 'arial.ttf';
    $font_array["size"] = "12";
    $font_array["wsize"] = "70";
    return $font_array;
  }
}
?>
