<?php
require './Light_GDClass.php';

require './validateForm.php';

// create an object to work with
$thumb = new Light_GDClass($width_r, $Panes[1]['height'], 'preview');

require './createPanes.php';

echo $thumb->output();
?>