<?php

echo "saving pdf";
$pdf = new Zend_Pdf();

$pdf->pages[] = new Zend__Pdf_Page(Zend_Pdf_Page::SIZE_A4);

$pdf->save("/home/tickets/public_html/files/zend.pdf");

echo "Done saving pdf";

?>
