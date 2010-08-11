<?php
require_once('db_config.php');
require_once('open_db.php');
require_once('fonts/fonts.php');
require_once('includes/misc.inc');
session_start();

header("Cache-control: private");


if($_SESSION["access"] == "granted") {
  if (isset($_GET['submit'])){
      $uid = $_SESSION["uid"];
      $num = safe($_GET['num']);
      $start = safe($_GET['start']);
      $txt = safe($_GET['t']);
      $font = safe($_GET['f']);
      $wtxt = safe($_GET['wt']);
      $wfont = safe($_GET['wf']);
      $ticket = safe($_GET['ticket']);
      $num = $start + $num;
      $ticket_uri = ticket_find_one($ticket);
           
      define('FPDF_FONTPATH','/home/jonfhancock/Development/www/ticketnumber/fpdf16/font/');
      require('fpdf16/fpdf.php');
      $PDF = new FPDF();
      $PDF->SetFont('Helvetica', 'B', 16);
  } 
} else {
    header("Location: ./login.php");
}


?>
<html>
<head>
<style type="text/css">
#header {
	position:absolute;
	float:left;
	width:100%;
	min-width:1240px;
	background-color:#c1cccf;
	line-height:normal;
	padding-top:.5em;
	border-bottom-style:solid;
	border-bottom-width:1px;
	border-bottom-color:#000000;
}

#header ul {
	margin:0;
	padding:0;
	list-style:none;
}
#header li {
	float:left;
	background-color:#aac3e7;
	margin-left:.5em;
	margin-right:.5em;
	padding:.2em;
	border-style:solid;
	border-width:1px;
	border-color:#777777;
	border-bottom-style:none;
}

#header a {
	display:block;
	text-decoration:none;
	color:#444444;
}
#header a:hover {
	display:block;
	text-decoration:none;
	color:#a90628;
}

#header #current {
	background-color:#8cb2e7;
	border-style:solid;
	border-width:1px;
	border-color:#000000;
	border-bottom-style:none;
}
#header #current a {
	background-color:#8cb2e7;
	text-decoration:none;
	color:000000;
    }
</style>
<?php print $styles;?>
<?php print $scripts;?>
<script type="text/javaScript">
$(document).ready(function() {
	$("#create").click(create);
	$(".ticket-radio").click(function(){
		var count = $(this).closest('tr').find('.row_count').text();
		$("input[name=start]").val(function(){
			return (Number(count) + Number(1));
			});
		});
});

</script>
</head>

<body style="background-color:#8cb2e7;margin:0;">
	<div id="header">
		<ul>
			<li id="current"><a href="index.php">Home</a></li>
			<li><a href="newticket.php">Upload a new ticket</a> </li>
			<li><a href="logout.php">Log out</a></div></li>
		</ul>
	</div>
		<!--<div style="position:absolute; width:100%; top:0%; left:0%; margin-top:1%; padding-left:1%; padding-bottom:1%;border-bottom-style:solid; border-width:2px; border-color:#000000;"><a href="index.php">Home</a> | <a href="newticket.php">Upload a new ticket</a> | <a href="logout.php">Log out</a></div>-->
	
	
		<table style="position:absolute; left:0%; width:45%; top:20%; margin-left:1%; overflow:auto; border-style:none; border-width:1px; border-color:#000000;">
			<tr><td><p>How many tickets do you want: </p></td><td><p><input type="text" name="num" size="5" <?php if (isset($_GET['submit'])){echo "value=\"" . $num . "\""; }?> /></p></td></tr>
			<tr><td><p>What should the starting number be: </p></td><td><p><input type="text" name="start" size="5" value="<?php if (isset($_GET['submit'])){echo $start; }else{echo "1";}?>" /></p></td></tr>
			<tr><td><p>Any text to be added before the ticket number: </p></td><td><p><input type="text" name="t" size="20" <?php if (isset($_GET['submit'])){echo "value=\"" . $t . "\""; }?> /></p></td></tr>
			<tr><td><p>What font would you like: </p></td><td><p><select name="f">
			<?php fonts_populate_form($font);?>
			</select></p></td></tr>
			<tr><td><p>Watermak Text: </p>
			</td><td><p><input type="text" name="wt" size="20" <?php if (isset($_GET['submit'])){echo "value=\"" . $wt . "\""; }?> /></p></td></tr>
			<tr><td><p>What font would you like for the watermark: </p></td><td><p><select name="wf">

			<?php fonts_populate_form($wfont);?>
			</select></p></td>
			</tr>
			<tr><td>
				<button name="create" id="create" value="create">Submit</button>
				<div id="images" style="display:none;"><p id="loading"><img src="files/ajax-loader.gif" /> Generating Tickets</p><span id="imgp"></span></div>
				<div id="pdf" style="display:none;"><p id="loadingpdf"><img src="files/ajax-loader.gif" /> Creating PDF</p><span id="pdfp"></span></div>
				<div id="print_ready" style="display:none;"><p>PDF's ready to print</p></div>
				<div id="print_sent" style="display:none;"><p>PDF's sent to printer</p></div>
				<button name="print_bt" id="print_bt" style="display:none;" value="print">Print</button>
				<div id="print" style="display:none;"><p id="loadingprint"><img src="files/ajax-loader.gif" /> Printing the tickets</p><span id="printp"></span></div>
				<div id="count"></div>
			</td></tr>
		</table>
	
		<div style="position:absolute; left:46%; width:675px; height:85%; top:8%; overflow:auto; border-style:solid; border-width:1px; border-color:#000000;">
			<table>
				<?php 
				echo get_tickets($ticket);
				?>
			</table>
		</div>


<?php 

function print_txt($txt){
  if ($txt != NULL){
    $txt = preg_replace("{\s+}", '+', $txt);
    return ", t: \"" . $txt . "\"";
  }
}

function print_wtxt($wtxt,$wfont){
  if ($wtxt != NULL){
    $wtxt = preg_replace("{\s+}", '+', $wtxt);
    return ", wtxt: \"" . $wtxt ."\"". ", wfont: \"" . $wfont . "\"";
  }
}

function get_tickets($ticket){
    $query = sprintf("SELECT * FROM tickets WHERE active = 1 AND (uid = %d OR shared = 1)",$uid);
    $result = mysql_query($query);
    //$tickets = array();
    $return = "";
    while ($row = mysql_fetch_assoc($result)) {
      /*
      $id = $row["id"];
      $name = $row["name"];
      $file = $row["file"];
      $thumb = $row["thumb"];
      $count = $row["count"];
      $created = $row["created"];
      $updated = $row["updated"];
      $active = $row["active"];
      */
      $return .= "<tr id=\"row_" . $row["id"] . "\"><td class=\"ticket-radio\"><p><input type=\"radio\" name=\"ticket\"" . checked($row["id"],$ticket) . " value=\"" . $row["file"] . "\" id=\"ticket" . $row["id"] . "\" /><label for=\"ticket" . $row["id"] . "\"><img src=\"" . $row["thumb"] . "\" /></label></p></td>";
      $return .= "<td><p>" . $row["name"] . "<br><span class=\"row_count\" id=\"count_" . $row["id"] . "\">" . $row["count"] . "</span> tickets printed so far.<br>Last run was on: " . $row["updated"] . "<br>Created on: " . $row["created"] . "<br><a id=\"edit_bt\" name=\"edit_bt\" href=\"edit.php?ticket=". $row["id"] . "\">Edit</a></p></td>";
      $return .= "</tr>";
    }

return $return;
}


function get_count($id){
  $query = sprintf("SELECT * FROM tickets WHERE id = %d ", $id);
  $result = mysql_query($query);
  if ($row = mysql_fetch_row($result)){
    return $row[4];
  }
}



function ticket_find_one($id){
  $query = sprintf("SELECT * FROM tickets WHERE id = %d ", $id);
  $result = mysql_query($query);
  if ($row = mysql_fetch_row($result)){
    return $row[2];
  }
}

?>
</body>
</html>
