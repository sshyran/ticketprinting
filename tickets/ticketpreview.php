<?php
require_once('db_config.php');
require_once('open_db.php');
require_once 'includes/misc.inc';

    $uid = $_POST["uid"];
    $name = $_POST['name'];
    $file = $_POST['file'];
    
    $filename = end(explode('/',$file));
    $thumb = createthumb($file,"files/thumbs/" . $filename,250,250);
    $query = "INSERT INTO `hanchan5_tickets`.`tickets` (`id`, `name`, `file`, `thumb`, `count`, `created`, `uid`, `shared`, `updated`, `active`) VALUES (NULL, '$name', '$file', '$thumb', 0, NOW(), $uid, 1, NOW(), 1)";
    $result = mysql_query($query);
  if($result){
  $jsondata = array();
  $jsondata[name] = $name;
  $jsondata[file] = $file;
  $jsondata[thumb] = $thumb;
  print json_encode($jsondata);
//  echo "<p>" . $_POST['name'] . "<p>";
//  echo "<p><img src=\"" . $thumb . "\"/></p><br>";
//  echo "<p><img src=\"" . $file . "\"/></p><br><br>"; 
//  echo "<p><img src=\"makepng.php?img=" . $file . "&q=120\"/></p>"; 
  }
  else{
//    echo "<p>Your upload failed for some reason.</p>";
//    echo "<p>Your query was: " . $query;

  }



function createthumb($name,$filename,$new_w,$new_h){
  $system=explode('.',$name);
  if (preg_match('/jpg|jpeg/',$system[1])){
  	$src_img=imagecreatefromjpeg($name);
  }
  if (preg_match('/png/',$system[1])){
  	$src_img=imagecreatefrompng($name);
  }
  $old_x=imageSX($src_img);
  $old_y=imageSY($src_img);
  if ($old_x > $old_y) {
  	$thumb_w=$new_w;
  	$thumb_h=$old_y*($new_h/$old_x);
  }
  if ($old_x < $old_y) {
  	$thumb_w=$old_x*($new_w/$old_y);
  	$thumb_h=$new_h;
  }
  if ($old_x == $old_y) {
  	$thumb_w=$new_w;
  	$thumb_h=$new_h;
  }
  $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
  imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
  if (preg_match("/png/",$system[1]))
  {
  	imagepng($dst_img,$filename); 
  } else {
  	imagejpeg($dst_img,$filename); 
  }
  imagedestroy($dst_img); 
  imagedestroy($src_img); 
  return $filename;
} 
  
?>