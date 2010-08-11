<?php
require_once('includes/misc.inc');
session_start();

header("Cache-control: private");

if($_SESSION["access"] == "granted") {
  /*if (isset($_GET['submit'])){
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
  }*/
} else {
    header("Location: ./login.php");
}
?>
<html>
<head>
<?php print $styles;?>
<?php print $scripts;?>
<script type="text/javaScript">
$(document).ready(function() {
	$(function() {
		$("#tabs").tabs({
//			load: function(event, ui) {
//				alert('blah');
////				$.getScript('js/home.js', function() {
////					 alert('Load was performed.');
////				});
//			}
			ajaxOptions: {		
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html("Couldn't load this tab.");
				}
			}
		});
		//try another way of adding options
		//$("#tabs").tabs( "option" , load , [value] )
	});
});
</script>
</head>

<body style="background-color:#8cb2e7;margin:0;">
	<div id="tabs" style="height:95%;margin:1%;">
		<ul id="header_list" name="header_list">
			<li><a href="home_inject.php">Home</a></li>
			<li><a href="newticket_inject.php">Upload a new ticket</a></li>
			<li><a href="logout.php">Log out</a></li>
		</ul>
		<!-- tabs content is injected here -->
	</div>
</body>
</html>
