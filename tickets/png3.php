<?php
require_once('fonts/fonts.php');
require_once('includes/misc.inc');



if(isset($_GET['submit'])){
$q = safe($_GET['q']);
$txt = safe($_GET['t']);
$f = safe($_GET['f']);
$wtxt = safe($_GET['wt']);
$wf = safe($_GET['wf']);
$img = safe($_GET['img']);
$tot = safe($_GET['tot']);
$submit = safe($_GET['submit']);
  create_page($q,$txt,$f,$wtxt,$wf,$img,$tot);
}




function create_page($q,$txt,$f,$wtxt,$wf,$img,$tot){


putenv('GDFONTPATH=' . realpath('./fonts'));


$src = ImageCreateFromPNG($img);

$image = imagecreatetruecolor(800, 960);
$bgcolor = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
$color = imagecolorallocate($image, 0x00, 0x00, 0x00);
$colorShadow = imagecolorallocate($image, 0x66, 0x66, 0x66);

$q2 = ($q + 1);
$q3 = ($q + 2);


imagefill($image,0,0,$bgcolor);

imagecopy($image , $src , 0 , 0 , 0 ,  0 ,  799 ,  299 );
if($q2 <= $tot ){
imagecopy($image , $src , 0 , 320 , 0 ,  0 ,  799 ,  299 );
}
if($q3 <= $tot){
imagecopy($image , $src , 0 , 640 , 0 ,  0 ,  799 ,  299 );
}

$font_array = get_font($f);
//print_r($font_array);
$font = $font_array["file"];
$fontSize = $font_array["size"];

$fontRotation = "0";
$imgx = imagesx($src);
$imgy = imagesy($src);
$placex = $imgx - 50;
$placey = 30;

for ($i=0;$i<3;$i++){

    $str = sprintf("%04d", $q);
    $qnum = $str;
    if ($txt != NULL){
      $str = $txt . " " . $str;
      $len = (strlen($str)*imagefontwidth($fontSize));
      $placex = $imgx  - $len;
    }

    if($q <= $tot){

        ImageTTFText($image, $fontSize, $fontRotation, $placex, $placey, $color, $font, $str);
    }
    $q++;
    $placey += 320;
}



if($wtxt != NULL){
    $wfont_array = get_font($wf);
    $wfont = $wfont_array["file"];
    $wfontSize = $wfont_array["wsize"];
    watermark($image,$imgx,$imgy,$wfontSize,$wfont,$wtxt,$q2,$q3);
}



ImagePng ($image,"files/mass/img_" . $qnum . ".png");
imagedestroy($image);
return "files/mass/img_$qnum.png";
}



function watermark(&$image,$imgx,$imgy,$font_size,$font,$str,$q2,$q3){
  $color = imagecolorallocatealpha($image, 0xAA, 0xAA, 0xAA,60);
  $angle = 15;
  $bbox = imagettfbbox($font_size, $angle, $font, $str);
  $placex = ($imgx / 2) - ($bbox[4] / 2);
  $placey = ($imgy / 2) - ($bbox[5] / 2);
  ImageTTFText($image,$font_size,$angle,$placex,$placey,$color,$font,$str);
  if($q2 <= $tot){
  ImageTTFText($image,$font_size,$angle,$placex,($placey+320),$color,$font,$str);
  }
  if($q3 <= $tot){
  ImageTTFText($image,$font_size,$angle,$placex,($placey+640),$color,$font,$str);
  }
}

?>
