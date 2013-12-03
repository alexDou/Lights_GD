<?php
if(!$_POST || !$_POST['Panes'] || count($_POST['Panes']) < 1) {
	exit('empty request');
}

$Panes = $_POST['Panes'];

// Validation
if(!$Panes[1]['height'] || !is_numeric($Panes[1]['height']) || $Panes[1]['height'] < 100){
	exit('0:Window height is not set or less than 100');
}
if($Panes[1]['height'] > 600){
	exit('0:Window height exceeds limit of 600');
}

$width_r = array();
$total_w = 0;
foreach($Panes as $k => $v){
	if (!is_numeric($v['width']) || $v['width'] < 76 || $v['width'] > 600) {
		echo '0:Width of pane ', $k, ' is invalid';
		exit;
	}
	
	$total_w = $total_w + $v['width'];
	if($total_w > 1000) {
		exit('0:Overall width of window exceeds 1000');
	}
	
	if (!empty($v['border']) && !is_numeric($v['border'])) {
		echo '0:Border for pane ', $k, ' has not numeric value';
		exit;
	}
	
	if (!empty($v['border2line']) && !is_numeric($v['border2line'])) {
		echo '0:Thin inner padding for pane ', $k, ' has wrong value';
		exit;
	}
	
	if (!empty($v['borderColor']) && !is_numeric($v['borderColor'])) {
		echo '0:Border color setting for pane ', $k, ' has wrong value';
		exit;
	}
	/*if (!is_numeric($v['openable'])) {
		echo '0:Openable parameter for pane ', $k, ' has wrong value';
		exit;
	}*/
	if (!is_numeric($v['doorknob'])) {
		echo '0:Doorknob parameter for pane ', $k, ' has wrong value';
		exit;
	}
	if (!empty($v['doorknob']) && is_numeric($v['doorknob']) && !is_numeric($v['typeDoorknob'])) {
		echo '0:Border type for pane ', $k, ' has wrong value';
		exit;
	}
	if (!is_numeric($v['separator'])) {
		echo '0:Separator parameter for pane ', $k, ' has wrong value';
		exit;
	}
	if (is_numeric($v['separator']) && (!empty($v['separatorWidth']) && !is_numeric($v['separatorWidth']))) {
		echo '0:Separator Width setting for pane ', $k, ' has wrong value';
		exit;
	}
	if (!is_numeric($v['typeostar'])) {
		echo '0:Type of stars parameter for pane ', $k, ' has wrong value';
		exit;
	}
	if (!is_numeric($v['devide'])) {
		echo '0:Deviders parameter for pane ', $k, ' has wrong value';
		exit;
	}
	if (!is_numeric($v['typeDevider'])) {
		echo '0:Type of Devider parameter for pane ', $k, ' has wrong value';
		exit;
	}
	if (!is_numeric($v['distanceDevider'])) {
		echo '0:Distance between Deviders parameter for pane ', $k, ' has wrong value';
		exit;
	} else if ($v['distanceDevider'] == 2) {
		if (!empty($v['setDistanceCol']) && !preg_match('~^[0-9:]+$~', $v['setDistanceCol'])) {
			echo '0:Vertical distance for ', $k, ' pane is invalid';
			exit;
		}
		if (!empty($v['setDistanceRow']) && !preg_match('~^[0-9:]+$~', $v['setDistanceRow'])) {
			echo '0:Horizontal distance for ', $k, ' pane is invalid';
			exit;
		}
	}
	
	$width_r[$k] = $v['width'];
} 
