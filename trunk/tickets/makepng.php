<?php
require_once('fonts/fonts.php');
require_once('includes/misc.inc');
require_once('png3.php');

if(isset($_GET['submit'])){
$q = safe($_GET['q']);
$txt = safe($_GET['t']);
$f = safe($_GET['f']);
$wtxt = safe($_GET['wt']);
$wf = safe($_GET['wf']);
$img = safe($_GET['img']);
$tot = safe($_GET['tot']);
$pages = safe($_GET['pages']);
$submit = safe($_GET['submit']);
  for($i=0;$i<$pages;$i++){
    if($q <= $tot)
    echo create_page($q,$txt,$f,$wtxt,$wf,$img,$tot) . "<br>\n";
    $q = ($q + 3);
  }
}


?>
