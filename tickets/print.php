<?php
echo "test";
require_once 'includes/misc.inc';

foreach (glob("files/tickets/*.pdf") as $filename) {

$printfile = $SITEROOT . $filename;

echo "Printing: " . $printfile . "<br>";
//exec("lp $printfile");

}
?>
