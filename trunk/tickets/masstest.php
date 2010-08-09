<?php
require_once('db_config.php');
require_once('open_db.php');
require_once 'includes/misc.inc';
?>
<html>
<head>
<?php print $styles;?>
<?php print $scripts;?>
<script type="text/javaScript">
$(document).ready(function() {
	$("#create").click(create);
	
});
function create(){
	$("#images").show();
  for(i=1;i<=250;i++){

  	if(i==250){
  		  $.post("savepng.php", { q: i, f: "arial", img: "files/vip_ticket.png"},
  				   function(data){
				   $("#loading").hide();
	        	  	$("#progressbar").progressbar({
	        			value: (i/250 * 100)
	        		});
  		$("#images").append("<p>Done creating tickets. <button id=\"generate\" name=\"genrate\" value=\"generate\">Generate PDF</button></p>");
  		$("#generate").click(generate);
  				   });
    } else{
  	  $.post("savepng.php", { q: i, f: "arial", img: "files/vip_ticket.png"},
  			   function(data){
        	  	$("#progressbar").progressbar({
        			value: (i/250 * 100)
        		});
  	//$("#images").append("<p>" + data + "</p>");
  			   });

   }
  }
}

function generate(){
	$("#pdf").show();
	$.post("makepdf.php",{make:"true"},function(data){
		$("#loadingpdf").hide();
		$("#pdf").append("<p>PDF Finished. <a href=\"" + data + "\">Download it</a></p>");
	});
	
}
</script>

</head>
<body>
<div id="progressbar"></div>
<div id="images" style="display:none;"><p id="loading"><img src="files/ajax-loader.gif" /> Loading</p></div>
<div id="pdf" style="display:none;"><p id="loadingpdf"><img src="files/ajax-loader.gif" /> Loading</p></div>
<button id="create" name="create" value="create">Create</button>
</body>
</html>