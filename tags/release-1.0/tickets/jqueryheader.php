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

$(function() {
	$("#tabs").tabs({
		ajaxOptions: {
			error: function(xhr, status, index, anchor) {
				$(anchor.hash).html("Couldn't load this tab.");
			}
		}
	});
});
</script>
</head>

<body style="background-color:#8cb2e7;margin:0;">
<div class="home">
	<div id="tabs">
		<ul>
			<li><a href="home_inject.php">Home</a></li>
			<li><a href="newticket.php">Upload a new ticket</a></li>
			<li><a href="logout.php">Log out</a></li>
		</ul>
	<div id="tabs-1" style="position:relative; height:50%">
		<p>home</p>
	</div>
</div>
</body>
</html>
