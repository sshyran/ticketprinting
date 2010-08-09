<?php
$counts = array();
foreach (glob("files/mass/*.png") as $filename){
  if(unlink($filename)){
    $counts[pngcount]++;
  } else {
    $counts[pngfail]++;
  }
}
foreach (glob("files/tickets/*.pdf") as $filename){
  if(unlink($filename)){
    $counts[pdfcount]++;
  } else {
    $counts[pdffail]++;
  }
}
foreach (glob("files/tickets/*.zip") as $filename){
  if(unlink($filename)){
    $count[zipcount]++;
  } else {
    $count[zipfail]++;
  }
}
echo json_encode($counts);
?>
