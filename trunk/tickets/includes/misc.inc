<?php
$debug = false;

$HOMEPATH = "/home/tickets/";

$ROOTPATH = "public_html/";

$SITEROOT = $HOMEPATH . $ROOTPATH;

$basepath = "";

function checked($id,$in){
  if($id == $in){
    return "checked=\"checked\"";
  }
}

function selected($id,$in){
  if($id == $in){
    return "selected=\"selected\"";
  }
}

function safe($str){
  return mysql_real_escape_string($str);
}

$stylesheets = array("css/smoothness/jquery-ui-1.8.custom.css","css/main.css");
$styles = "";
foreach($stylesheets as $sheet){
  $styles .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$basepath/$sheet\" />\n";
}

$scriptfiles = array(
        "js/jquery-1.4.2.min.js",
        "js/jquery-ui-1.8.custom.min.js"
        );
$scripts = "";
foreach($scriptfiles as $script){
  $scripts .= "<script type=\"text/javaScript\" src=\"$basepath/$script\"></script>\n";
}
?>
