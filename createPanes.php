<?php
foreach($Panes as $k => $v) {
	if (!$v['empty'] || $v['empty'] == 2) {
		$pane = $thumb->setPane($v['width'], $v['borderColor']);
		if (is_numeric($v['border'])) {
			$pane->addBorder($v['border']);
		} else {
			$pane->addBorder();
		}
		if ($v['border2line'] == 2) {
			$pane->addPadding();
		}
		
		/*if ($v['openable'] == 2 || $v['openable'] == 3) {
			$pane->addOpen($v['openable']);
		}*/
		if ($v['doorknob'] >=2 && $v['doorknob'] <= 5) {
			$pane->addHandler($v['doorknob'], $v['typeDoorknob']);
		}
		if ($v['separator'] == 2 || $v['separator'] == 3) {
			if (!empty($v['separatorWidth'])) {
				$pane->addSeparator($v['separator'], $v['separatorWidth']);
			}
			$pane->addSeparator($v['separator']);
		}
		if (isset($v['gtStar']) || isset($v['ltStar']) || isset($v['upStar']) || isset($v['dnStar'])) {
			$pane->addStar((isset($v['gtStar']))?1:0, (isset($v['ltStar']))?1:0, (isset($v['upStar']))?1:0, (isset($v['dnStar']))?1:0, $v['typeostar']);
		}
		if (isset($v['dashSign'])) {
			$pane->addDashedLine();
		} 
		if (isset($v['arrowSign'])) {
			$pane->addSign('arrow');
		} else if (isset($v['plusSign'])) {
			$pane->addSign('plus');
		}
	
		if ($v['devide'] == 2) {
			if ($v['distanceDevider'] == 2) {
				if (!empty($v['setDistanceCol'])) {
					$distanceCols = $v['setDistanceCol'];
				}
				if (!empty($v['setDistanceRow'])) {
					$distanceRows = $v['setDistanceRow'];
				}
				$distanceCR_r = array($distanceCols, $distanceRows);
			} else {
				$distanceCR_r = false;
			}
			if (is_numeric($v['thicknessDevider'])) {
				$pane->addDeviderBorder($v['thicknessDevider']);
			}
			$pane->addDeviders($v['typeDevider'], $v['distanceDevider'], $distanceCR_r);
			if ($v['cell11'] == 2) {
				$pane->addModifyDevider(4, 'cellsTop');
			}
		}
		$pane->drawPane();
		$thumb->merge($pane, $k);
	} else {
		if ($v['emptyRightBorder'] == 2) {
			$thumb->drawEmptyRightBorder($k);
		}
		if ($v['emptyLeftBorder'] == 2) {
			$thumb->drawEmptyLeftBorder($k);
		}
	}
} 
