<?php
require_once('fonts/fonts.php');
require_once 'includes/misc.inc';
      define('FPDF_FONTPATH','/home/tickets/public_html/fpdf16/font/');
      require('fpdf16/fpdf.php');
      
      
      $i = $_GET['start'];
      $stop = ($i + 83);
      $stri = sprintf("%04d", $i);
      $stri_array = array($stri);
      for($j=$i;$j<$stop;$j = ($j + 3)){
        $stri_array[] = sprintf("%04d", ($j + 2));


      }
      $PDF = new FPDF();
      $PDF->SetFont('Helvetica', 'B', 16);
      $files = array();
      $count = $i;
      foreach (glob("files/mass/*.png") as $filename) {

        foreach($stri_array as $value){
          if (preg_match("/$value/",$filename)){
            //echo "file: $filename<br>\ncount: $count<br>\n";
             $PDF->AddPage();
             $PDF->Image($filename,5,5,200);
             if($count < $stop){
             $count++;
             }
             if($count < $stop){
             $count++;
             }
             if($count < $stop){
             $count++;
             }
          }
        }
        
      }

    $count = sprintf("%04d", ($count));
    $PDF->Output("files/tickets/tickets_$stri-$count.pdf",F);

    echo "files/tickets/tickets_$stri-$count.pdf";
    ?>
