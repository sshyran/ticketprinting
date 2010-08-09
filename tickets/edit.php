<?php
require_once('db_config.php');
require_once('open_db.php');
require_once('includes/misc.inc');
session_start();

header("Cache-control: private");

$tickets = array();
$error_status = "";

if($_SESSION["access"] == "granted") {
	$uid = $_SESSION["uid"];
	
	if (isset($_POST['save'])){//if save was pressed
		if($_POST['name'] != ""){
			//update database
			do_update($error_status);
		}else{
			$error_status = "Upload failed: Name field must not be blank.";
		}
	}

	if (isset($_GET['ticket'])){
		$ticket = safe($_GET['ticket']);	
		$tickets = pullData($ticket);
	} else if(isset($_POST['ticket'])){
		$ticket = safe($_POST['ticket']);
		$tickets = pullData($ticket);
	}
} else {
    header("Location: ./login.php");
}
echo '<html>
<head><title>Edit ticket</title>';
	print $styles;
	print $scripts;
echo '</script>
</head>
<body>
<div style="padding-bottom:15px;"><a href="index.php">Home</a> | <a href="newticket.php">Upload a new ticket</a> | <a href="logout.php">Log out</a></div>
<form action="edit.php" enctype="multipart/form-data" method="post">
	<table>
		<tr>
			<td style="width:8em;"><p>Name of ticket: </p></td><td><p><input name="name" size="30" value="'.$tickets["name"].'"/></p></td>
		</tr><tr>
			<td colspan="2"><img src="'.$tickets["file"].'" /></td>
		</tr><tr>
			<td colspan="2"><p><input type="file" name="file"></input><span style="font-size:10pt">Upload your file.  Must be in png format, and should ideally be 800px wide by 300px high.</span></p></td>
		</tr><tr>
			<td colspan="2"><p><button value="save" name="save">Save changes</button><span>'.$error_status.'</span></p></td>
		</tr>
	</table>
<input id="ticket" name="ticket" type="hidden" value="'.$tickets["id"].'" />
</form>
</body>
</html>';

function do_update(&$error_status){//update info in the database
	//echo "< " . $_POST['name']." - ".$tickets["name"] . "  >";
	//echo isset($_POST['name']);
	/*if($_POST['name'] != "" && $_FILES['file']['name'] != ""){ //if user changed the name and file
		$cleanFileNames = upload_file();
		$query = sprintf("UPDATE `tickets` SET SET name = '%s', file = '%s', thumb = '%s' WHERE id = '%d' LIMIT 1",safe($_POST['name']),$cleanFileNames['file'],$cleanFileNames['thumb'], safe($_POST['ticket']));                       
		if(mysql_query($query)){ //preform querry and update error status accordingly
			$error_status = "Name and file update successful";		//update successful
		}else{
			$error_status = "update failed to save the new file and new name";	//update failed
		}
	}else if($_POST['name'] != "" && $_FILES['file']['name'] == ""){ //if user changed only the name
		$query = sprintf("UPDATE `tickets` SET name = '%s' WHERE id = '%d' LIMIT 1", safe($_POST['name']), safe($_POST['ticket']) );
		if(mysql_query($query)){ //preform querry and update error status accordingly
			$error_status = "Name updated successfully";		//update successful
		}else{
			$error_status = "update failed to save new name";	//update failed
		}
	}else if($_FILES['file']['name'] != ""){ //if user changed only the file
		$cleanFileNames = upload_file();
		$query = sprintf("UPDATE `tickets` SET file = '%s', thumb = '%s' WHERE id = '%d' LIMIT 1",$cleanFileNames['file'],$cleanFileNames['thumb'], safe($_POST['ticket']));                       
		if(mysql_query($query)){ //preform querry and update error status accordingly
			$error_status = "Background updated successfully";		//update successful
		}else{
			$error_status = "update failed to save new file";	//update failed
		}
	}*/
	if($_FILES['file']['name'] == ""){ //if user did not change file
		$query = sprintf("UPDATE `tickets` SET name = '%s' WHERE id = '%d' LIMIT 1", safe($_POST['name']), safe($_POST['ticket']) );
		if(mysql_query($query)){ //preform querry and update error status accordingly
			$error_status = "Name updated successfully";		//update successful
		}else{
			$error_status = "update failed to save new name";	//update failed
		}
	}else{ //if user changed file
		$cleanFileNames = upload_file();
		$query = sprintf("UPDATE `tickets` SET name = '%s', file = '%s', thumb = '%s' WHERE id = '%d' LIMIT 1",safe($_POST['name']),$cleanFileNames['file'],$cleanFileNames['thumb'], safe($_POST['ticket']));                       
		if(mysql_query($query)){ //preform querry and update error status accordingly
			$error_status = "Name and file update successful";		//update successful
		}else{
			$error_status = "update failed to save the new file and new name";	//update failed
		}
	}
}

function upload_file(){
	$uid = $_SESSION["uid"];
	$name = mysql_real_escape_string($_POST['name']);
    	$fileNames = array();
	$MAXIMUM_FILESIZE = 5 * 1024 * 1024; 
	//  Valid file extensions (images, word, excel, powerpoint) 
	$rEFileTypes = "/^\.(png){1}$/i"; 
	$dir_base = "files/"; 
	$isFile = is_uploaded_file($_FILES['file']['tmp_name']); 
	if($isFile){
		//print_r($_FILES);
		// sanatize file name 
			//     - remove extra spaces/convert to _, 
			//     - remove non 0-9a-Z._- characters, 
			//     - remove leading/trailing spaces 
			//  check if under 5MB, 
			//  check file extension for legal file types 
		$safe_filename = preg_replace( 
			     array("/\s+/", "/[^-\.\w]+/"), 
			     array("_", ""), 
			     trim($_FILES['file']['name'])); 
		if ($_FILES['file']['size'] <= $MAXIMUM_FILESIZE && preg_match($rEFileTypes, strrchr($safe_filename, '.'))) {
	     		move_uploaded_file ( 
		         $_FILES['file']['tmp_name'], 
		         $dir_base.$safe_filename);
		}
		$thumb = createthumb($safe_filename,250,93,$dir_base);
	}
     	$fileNames['file'] = $dir_base.$safe_filename;
     	$fileNames['thumb'] = $thumb;
	return $fileNames;   
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
	if (preg_match("/png/",$system[1])){
		imagepng($dst_img,$filename); 
	} else {
		imagejpeg($dst_img,$filename); 
	}
	imagedestroy($dst_img); 
	imagedestroy($src_img); 
	return $filename;
} 
function pullData($ticket){ //pulls the data from the database
	$query = sprintf("SELECT * FROM tickets WHERE id = %d LIMIT 1", $ticket);
  	$result = mysql_query($query);
  	while ($row = mysql_fetch_assoc($result)) {
	      $tickets = $row;
	      /*$id = $row["id"]; $name = $row["name"]; $file = $row["file"]; $thumb = $row["thumb"]; 
	      $count = $row["count"]; $created = $row["created"]; $updated = $row["updated"]; $active = $row["active"];*/
	}
	return $tickets;
}
?>
