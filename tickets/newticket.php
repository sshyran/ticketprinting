<?php
require_once('db_config.php');
require_once('open_db.php');
require_once 'includes/misc.inc';

session_start();

header("Cache-control: private");
$error_status = "";
if($_SESSION["access"] == "granted") {
  if (isset($_POST['submit'])){
    $uid = $_SESSION["uid"];
    
    if($_POST['name'] != ""){
    	$system=explode('.',$name);
  	//if (preg_match('/png|PNG/',$system[1])){
	    	$name = mysql_real_escape_string($_POST['name']);
	    	$file = upload_file($error_status);
    	//}else{
    		//$error_status = "Upload failed: Your image must be in PNG format";
    	//}
    }else{
    	$error_status = "Upload failed: you must first enter a name for the ticket.";
    }
  }
} else {
    header("Location: ./login.php?dest=newticket.php");
}
?>
<html>
<head>
<?php 
	print $styles;
 	print $scripts;
?>
</head>
<body>
<div style="padding-bottom:15px" ><a href="index.php">Home</a> | <a href="newticket.php">Upload a new ticket</a> | <a href="logout.php">Log out</a></div>
<div id="preview" style="display:none;"></div>

<form action="newticket.php" enctype="multipart/form-data" method="post">
<table id="upload_controls" name="upload_controls">
<tr><td style="width:8em"><p>Name of ticket: </p></td><td><p><input name="name" size="30" /></p></td></tr>
<tr><td colspan="2"><p><input type="file" name="file" />
<span style="font-size:10pt">Upload your file.  Must be in png format, and should ideally be 800px wide by 300px high.</span></p></td></tr>
<tr><td colspan="2"><p><button value="submit" name="submit">Submit</button></p></td></tr>
</table>
</form>
<?php
	if(isset($_FILES['file']['name'])){
		echo '<div><p>'.$error_status.'</p></div><br />';
		echo '<div><img src="files/'.$_FILES['file']['name'].'" /></div>';
	}
?>
</body>
</html>
<?php 
function upload_file(&$error_status){
    $uid = $_SESSION["uid"];
    $name = mysql_real_escape_string($_POST['name'] );
    

	$MAXIMUM_FILESIZE = 5 * 1024 * 1024; 
	//  Valid file extensions (images, word, excel, powerpoint) 
	$rEFileTypes = "/^\.(png){1}$/i"; 
	$dir_base = "files/"; 
	$isFile = is_uploaded_file($_FILES['file']['tmp_name']); 
	if($isFile){
	//  sanatize file name 
	    //     - remove extra spaces/convert to _, 
	    //     - remove non 0-9a-Z._- characters, 
	    //     - remove leading/trailing spaces 
	    //  check if under 5MB, 
	    //  check file extension for legal file types 
	    $safe_filename = preg_replace( 
		             array("/\s+/", "/[^-\.\w]+/"), 
		             array("_", ""), 
		             trim($_FILES['file']['name'])); 
	    if ($_FILES['file']['size'] <= $MAXIMUM_FILESIZE && 
		preg_match($rEFileTypes, strrchr($safe_filename, '.'))) 
	      {
	     move_uploaded_file ( 
		         $_FILES['file']['tmp_name'], 
		         $dir_base.$safe_filename);
	     }
	      $thumb = createthumb($safe_filename,250,93,$dir_base);
	      $count = 0;
	      $shared = 1;
	      $active = 1;
	      $query = sprintf("INSERT INTO `tickets` (`id`, `name`, `file`, `thumb`, `count`, `created`, `uid`, `shared`, `updated`, `active`) VALUES (NULL, '%s', '%s', '%s', '%d', 'NOW()', '%d', '%d', 'NOW()', '%d')",
		                $name, $dir_base.$safe_filename, $thumb, $count, $uid, $shared, $active);                       
	      if(mysql_query($query)){
		$error_status = "upload successful";
	      }else{
	      	$error_status = "upload failed";
	      }
	      
	}
	return $dir_base.$safe_filename;   
}
  
function createthumb($name,$thumb_w,$thumb_h,$dir_base){

$filename = "files/thumbs/" . $name;
  $system=explode('.',$name);
  if (preg_match('/jpg|jpeg/',$system[1])){
  	$src_img=imagecreatefromjpeg($dir_base.$name);
  }
  if (preg_match('/png/',$system[1])){
  	$src_img=imagecreatefrompng($dir_base.$name);
  }
  $old_x=imageSX($src_img);
  $old_y=imageSY($src_img);
  
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
