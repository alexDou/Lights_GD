<?php
require './Light_GDClass.php';

$thumb = new Light_GDClass(array(280,320), 400, 'preview');


$pane = new Pane(280, 400, $thumb);
$pane->addBorder(14);
$pane->addHandler(3);
$pane->addStar(1, 0, 1, 1);

$pane->drawPane();
$thumb->merge($pane, 1);

unset($pane);

$pane = new Pane(320, 400, $thumb);
$pane->addBorder(14);
$pane->addHandler(2);
$pane->addStar(0, 1, 1, 2);
$pane->addDeviders(9, 2, array('100:220','120'));

$pane->drawPane();
$thumb->merge($pane, 2);

echo $thumb->output();
?>