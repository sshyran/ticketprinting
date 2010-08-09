<?php
require('fonts/fonts.php');
require_once('includes/misc.inc');

$q = safe($_GET['q']);
$txt = safe($_GET['t']);
$f = safe($_GET['f']);
$wtxt = safe($_GET['wt']);
$wf = safe($_GET['wf']);
$img = safe($_GET['img']);
putenv('GDFONTPATH=' . realpath('./fonts'));
$image = ImageCreateFromPNG($img);
$color = imagecolorallocate($image, 0x00, 0x00, 0x00);
$colorShadow = imagecolorallocate($image, 0x66, 0x66, 0x66);

$font_array = get_font($f);
//print_r($font_array);
$font = $font_array["file"];
$fontSize = $font_array["size"];

$fontRotation = "0";
$imgx = imagesx($image);
$imgy = imagesy($image);
$placex = $imgx - 50;
$str = sprintf("%04d", $q);
if ($txt != NULL){
  $str = $txt . " " . $str;
  $len = (strlen($str)*imagefontwidth($fontSize));
  $placex = $imgx  - $len;
}


ImageTTFText($image, $fontSize, $fontRotation, $placex, 30, $color, $font, $str);



function watermark(&$image,$imgx,$imgy,$font_size,$font,$str){
  $color = imagecolorallocatealpha($image, 0xAA, 0xAA, 0xAA,60);
  $angle = 15;
  $bbox = imagettfbbox($font_size, $angle, $font, $str);
  $placex = ($imgx / 2) - ($bbox[4] / 2);
  $placey = ($imgy / 2) - ($bbox[5] / 2);
  ImageTTFText($image,$font_size,$angle,$placex,$placey,$color,$font,$str);
}

if($wtxt != NULL){
$wfont_array = get_font($wf);
$wfont = $wfont_array["file"];
$wfontSize = $wfont_array["wsize"];
watermark($image,$imgx,$imgy,$wfontSize,$wfont,$wtxt);
}

header("Content-Type: image/PNG");
ImagePng($image);
//ImagePng ($image,"files/mass/img_" . $str . ".png");
imagedestroy($image);
echo "files/mass/img_$str.png";
?>
