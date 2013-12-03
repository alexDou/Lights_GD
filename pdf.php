<?php
date_default_timezone_set('Asia/Kolkata');

require './Light_GDClass.php';

require './validateForm.php';

require_once './pdf/html2pdf.class.php';

// create an object to work with
$thumb = new Light_GDClass($width_r, $Panes[1]['height']);

require './createPanes.php';

foreach($Panes as $k => $v) {
	$thumb->addPDFPane($k, $v);
}

$content = $thumb->getPDFContent();

$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
$html2pdf->pdf->SetTitle(date('Y-m-d'). ' Window, generated at ' .date('i:h:s'));
$html2pdf->pdf->SetSubject('Window Maker');
$html2pdf->pdf->SetDisplayMode('real');
$html2pdf->writeHTML($content);
$html2pdf->Output(date('Ymd_ihs').'_Windows.pdf','D');
?>