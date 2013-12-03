<?php

class Light_GDClass {
	
	/* width of resulting image */
	private $width = 0;
	/* height of resulting image */
	public $height = 0;
	/* Widths of all panes*/
	private $width_r = array();
	/* type of resulting image //preview, fullsize, pdf */
	public $type;
	/* image itself */
	public $image;
	/* preview height */
	public $preHeight = 100;
	/* Ratio to resize width (ina case of preview type) */
	public $ratio = 1;
	/* to store Pane object */
	public $pane;
	/* overall border */
	public $roundPad = 8;
	/* all Panes */
	public $htmlPanes;
	
	function __construct($width_r, $height, $type = 'fullsize') {
		
		$this->type = $type;
		$this->height = $height;
		$this->frames = $frames;
		$this->width_r = $width_r;
		$this->setOverallWidth();

		$this->generateImage();
	}
	
	/* Set an image width */
	private function setOverallWidth() {
		
		foreach($this->width_r as $v) {
			$this->width = $this->width + $v;
		}
		if (count($this->width_r) > 1) {
			if ($this->type == 'preview') {
				$this->width = $this->width - count($this->width_r) * 2;
			}
		}
	}
	
	/* Generate image sceleton */
	private function generateImage() {
		
		if ($this->type == 'preview') {
			// Set ration & calculate proportional dimensions for a small image
			$this->ratio = round((($this->preHeight*100)/$this->height)/100, 3);
			$this->width = round($this->width * $this->ratio);
			$this->roundPad = round($this->roundPad * $this->ratio);
			$this->height = $this->preHeight;
			
		}

		if (!$this->image = @imagecreatetruecolor($this->width + $this->roundPad * 2, $this->height + $this->roundPad * 2)) {
			exit('GD unavailable. Check your PHP installation');
		}
		$innerImg = imagecreatetruecolor($this->width, $this->height);
		$white = imagecolorallocate($this->image, 250, 250, 250);
		$rp_col = imagecolorallocate($innerImg, 240, 243, 245);
		$con_col = imagecolorallocate($this->image, 136, 140, 140);
		imagefilledrectangle($this->image, 1, 1, (($this->width + $this->roundPad * 2) - 2), (($this->height + $this->roundPad * 2) - 2), $rp_col);
		imagefilledrectangle($innerImg, 1, 1, $this->width-2, $this->height-2, $white);
		imagecopymerge($this->image, $innerImg, $this->roundPad, $this->roundPad, 0, 0, $this->width, $this->height, 100);
		imageline($this->image, 0, 0, $this->roundPad, $this->roundPad, $con_col);
		imageline($this->image, $this->width + $this->roundPad * 2, 0, $this->width + $this->roundPad - 2, $this->roundPad, $con_col);
		imageline($this->image, $this->width + $this->roundPad * 2, $this->height + $this->roundPad * 2, $this->width + $this->roundPad - 2, $this->height + $this->roundPad - 2, $con_col);
		imageline($this->image, 0, $this->height + $this->roundPad * 2, $this->roundPad, $this->height + $this->roundPad - 2, $con_col);
	}

	/* Draw right border for empty pane */
	public function drawEmptyRightBorder($n) {
		
		$pw = 0;
		for ($i=1;$i<=$n;$i++) {
			$pw = $pw + $this->width_r[$i];
		}
		if ($this->type == 'preview') {
			$pw = round($pw * $this->ratio);
			$corr = 2 * $this->ratio;
		}
		$im = imagecreatetruecolor(($this->roundPad + 2), $this->height + 2);
		$white = imagecolorallocate($this->image, 222, 234, 250);
		$black = imagecolorallocate($this->image, 0, 0, 0);
		$rp_col = imagecolorallocate($this->image, 240, 243, 245);
		imagefill($im, 0, 0, $rp_col);
		imageline($im, 0, 0, 0, ($this->height +2), $black);
		imageline($im, ($this->roundPad + 1), 0, ($this->roundPad + 1), $this->height, $black);
		imagecopymerge($this->image, $im, $pw - 2, $this->roundPad, 0, 0, ($this->roundPad + 2), $this->height, 100);
	}

	/* Draw left border for empty pane */
	public function drawEmptyLeftBorder($n) {
		
		$pw = 0;
		for ($i=1;$i<$n;$i++) {
			$pw = $pw + $this->width_r[$i];
		}
		if ($this->type == 'preview') {
			$pw = round($pw * $this->ratio);
		}

		$im = imagecreatetruecolor(($this->roundPad + 2), $this->height);
		$white = imagecolorallocate($this->image, 222, 234, 250);
		$black = imagecolorallocate($this->image, 0, 0, 0);
		$rp_col = imagecolorallocate($this->image, 240, 243, 245);
		imagefill($im, 0, 0, $rp_col);
		imageline($im, 0, 0, 0, ($this->height +2), $black);
		imageline($im, ($this->roundPad + 1), 0, ($this->roundPad + 1), $this->height, $black);
		imagecopymerge($this->image, $im, $this->roundPad + $pw, $this->roundPad, 0, 0, ($this->roundPad + 2), $this->height, 100);
	}
	
	/* Set a new pane to add to resulting picture */
	public function setPane($wid, $bc) {

		$this->pane = new Pane($wid, $this, $bc);
		return $this->pane;
	}
	
	/* Add a new pane to resulting image */
	public function merge(Pane $pane, $num) {

		$x_pos = $this->roundPad;
		if ($num > 1) {
			for($i=1;$i<$num;$i++) {
				if ($this->type == 'preview') {
					$x_pos = $x_pos + round($this->width_r[$i] * $this->ratio);
				} else {
					$x_pos = $x_pos + $this->width_r[$i];
				}
			}
			//$x_pos = round($x_pos - (2 * ($num - 1)));
		}

		imagecopymerge($this->image, $pane->paneIm, $x_pos, $this->roundPad, 0, 0, $pane->paneWidth, $pane->paneHeight, 100);
	}
	
	/* Output image */
	public function output() {

		if ($this->type == 'preview') {
            ob_start();
            imagepng($this->image);
            $raw = ob_get_clean();
            ob_end_clean();
			$data = chunk_split(base64_encode($raw));
            $tag  = '<img src="data:image/png;base64,'.$data.'"';
            $tag .= ' width="' .$this->width. '"';
            $tag .= ' height="' .$this->height. '"';
            $tag .= ' alt="" />';
			
			$html = $tag. 
			'<p>
				<a href="javascript:void(0)" id="fullsize">View Fullsize Image</a><br>
				<a href="javascript:void(0)" id="pdf">Generate PDF</a>
			</p>';
		} else {
            ob_start();
            imagepng($this->image);
            $raw = ob_get_clean();
            ob_end_clean();
			$data = chunk_split(base64_encode($raw));
            $tag  = '<p><img src="data:image/png;base64,'.$data.'"';
            $tag .= ' width="' .$this->width. '"';
            $tag .= ' height="' .$this->height. '"';
            $tag .= ' alt="" /></p>';
			
			$html = $tag;			
		}
		
		return $html;
	}

	/* create panes to use in createHTML */
	public function addPDFPane($k, $settingsPane) {
		
		if (empty($this->htmlPanes)) {
			$this->htmlPanes = $this->createHTML();
		}
		
$tblPane =<<<TBLP
		<td style="width:200px;">
			<table class="paneSettings">
				<tr>
					<th style="border-bottom:1px solid #454;">Pane $k</th>
				</tr>
				%onerow%
			</table>
		</td>
			%panes%
TBLP;
$tblOneRow =<<<TBLR
				<tr>
					<td style="border-bottom:1px dotted #454;">
						<table class="rowTbl" style="width:100%">
							<tr>
								<td style="width:46%;margin-right:3.5%">%name%</td>
								<td align="right" style="width:46%;">%value%</td>
							</tr>
						</table>
					</td>
				</tr>
				%onerow%
TBLR;

//$this->htmlPanes = str_replace('%panes%', $tblPane, $this->htmlPanes);

			$PDFrow = strtr($tblOneRow, array(
											'%name%' => 'Width',
											'%value%' => $settingsPane['width']
										));
			$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
			$PDFrow = strtr($tblOneRow, array(
											'%name%' => 'Height',
											'%value%' => $this->height
										));
			$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
		
		if (!$settingsPane['empty'] || $settingsPane['empty'] == 2) {
			if (is_numeric($settingsPane['border'])) {
				$PDFrow = strtr($tblOneRow, array(
													'%name%' => 'Border',
													'%value%' => $settingsPane['border']
												));
			} else {
				$defPane = new Pane(10, $this);
				$PDFrow = strtr($tblOneRow, array(
													'%name%' => 'Border',
													'%value%' => $defPane->border
												));
			}
			$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);

			if ($settingsPane['doorknob'] == 2) {
				$pos = 'Left';			
			} else if ($settingsPane['doorknob'] == 3) {
				$pos = 'Right';
			} else {
				$pos = 'None';
			}
			$PDFrow = strtr($tblOneRow, array(
											'%name%' => 'Doorknob',
											'%value%' => $pos
										));
			$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);	

			if ($settingsPane['separator'] == 2) {
				$PDFrow = strtr($tblOneRow, array(
												'%name%' => 'Frame Separator',
												'%value%' => 'Yes'
											));
			}
			$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
			
			$starDir = '';
			if (isset($settingsPane['gtStar'])) {
				$starDir .= 'Right,';
			} 
			if (isset($settingsPane['ltStar'])) {
				$starDir .= 'Left,';
			} 
			if (isset($settingsPane['upStar'])) {
				$starDir .= 'Up,';
			} 
			if (isset($settingsPane['dnStar'])) {
				$starDir .= 'Down';
			}
			if (!empty($starDir)) {
				$starDir = preg_replace('~,$~', '', $starDir);
				$PDFrow = strtr($tblOneRow, array(
												'%name%' => 'Open To',
												'%value%' => $starDir
											));
				$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
				if ($settingsPane['typeostar']) {
					$starType = ($settingsPane['typeostar'] == 1) ? 'Solid' : 'Dotted';
				}
				$PDFrow = strtr($tblOneRow, array(
												'%name%' => 'Open Line',
												'%value%' => $starType
											));
				$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
			}

			if ($settingsPane['devide'] == 2) {
					switch ($settingsPane['typeDevider']) {
						case 1:
							$dType = '1:0';
						break;
						case 2:
							$dType = '2:0';
						break;
						case 3:
							$dType = '3:0';
						break;
						case 4:
							$dType = '1:1';
						break;
						case 5:
							$dType = '2:1';
						break;
						case 6:
							$dType = '3:1';
						break;
						case 7:
							$dType = '2:2';
						break;
						case 8:
							$dType = '0:1';
						break;
						case 9:
							$dType = '0:2';
						break;
						case 10:
							$dType = '0:3';
						break;
						default:
							$dType = '1:0';
						break;
					}
				$PDFrow = strtr($tblOneRow, array(
												'%name%' => 'Inner Frames',
												'%value%' => $dType
											));
				$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);

				if ($settingsPane['distanceDevider'] == 2) {
					$PDFrow = strtr($tblOneRow, array(
													'%name%' => 'Inner Frame Cols',
													'%value%' => $settingsPane['setDistanceCol']
												));
					$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
					$PDFrow = strtr($tblOneRow, array(
													'%name%' => 'Inner Frame Rows',
													'%value%' => $settingsPane['setDistanceRow']
												));
					$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
				}
				
				if (is_numeric($settingsPane['thicknessDevider'])) {
					$PDFrow = strtr($tblOneRow, array(
													'%name%' => 'Devider\' Border',
													'%value%' => $settingsPane['thicknessDevider']
												));
					$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
				}
			}
		} else {
			
			$PDFrow = strtr($tblOneRow, array(
											'%name%' => 'Empty',
											'%value%' => 'Yes'
										));
			$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
			
			if ($settingsPane['emptyRightBorder'] == 2) {
				$PDFrow = strtr($tblOneRow, array(
												'%name%' => 'Right Border',
												'%value%' => 'Yes'
											));
				$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
			}
			if ($settingsPane['emptyLeftBorder'] == 2) {
				$PDFrow = strtr($tblOneRow, array(
												'%name%' => 'Left Border',
												'%value%' => 'Yes'
											));
				$tblPane = str_replace('%onerow%', $PDFrow, $tblPane);
			}
		}
		
		$tblPane = str_replace('%onerow%', '', $tblPane);
		$this->htmlPanes = str_replace('%panes%', $tblPane, $this->htmlPanes);	
	}

	/* create html to send to html2pdf */
	private function createHTML() {
		
        ob_start();
        	imagepng($this->image);
        $raw = ob_get_clean();
        ob_end_clean();
		$data = chunk_split(base64_encode($raw));
        $tag  = '<img src="data:image/png;base64,'.$data.'"';
        $tag .= ' width="' .$this->width. '"';
        $tag .= ' height="' .$this->height. '"';
        $tag .= ' alt="" />';
		
		$numPanes = count($this->width_r);		
		
		$this->htmlPanes =<<<HTPA
<!doctype html>
<html>
    <head>
    	<meta charset="utf-8">
		<title>$numPanes Pane(s) Window</title>
	</head>
    <body style="padding: 30px 10px 10px; font-size: 12px; font-family: Arial, Helvetica, sans-serif; background-color: white;">
    <!--
    	<style type="text/css">
    		#settingsBlock {
    			width:80%;
    			margin:0 auto;
    		}
    		.paneSettings {
    			width:100%;
    			height:auto;
    			margin-left:6px;
    		}
    		.paneSettings tr {
    			height:30px;
    			text-align:center;
    		}
    		.paneSettings th {
    			font-size:16px;
    			font-style:oblique;
    		}
    		.rowTbl {
    			width:100%;
    			height:inherit;
    			
    		}
    		.rowTbl tr {
    			width:100%;
    			height:26px;
    		}
    		.rowTbl td {
    			width:48%;
    			height:inherit;
    			padding-left:4px;
    			font-size:14px;
    			text-align:left;
    		}
    		-->
    	</style>
        <table cellspacing="0" style="width: 100%;">
        	<tr>
            	<td style="width:100%;" align="center">
           	    	$tag
                </td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br>
        <div id="settingsBlock">
        <table cellspacing="0" style="width:100%;">
        	<tr valign="top">
        		%panes%
        	</tr>
        </table>
        </div>
HTPA;
	
	$paneNum = count($this->width_r);
	$th = '';
	for ($i=0;$i<$paneNum;$i++) {
		$th .= "<th>Pane " .($i + 1). "</th>";
	}
	$this->htmlPane = str_replace('~%thPaneTbl%~', $th, $this->htmlPanes);
	
	return $this->htmlPanes;
	}

	/* output HTML content for PDF generation */
	public function getPDFContent() {
		
		return str_replace('%panes%', '', $this->htmlPanes). "</body></html>";
	}
}

class Pane extends Light_GDClass {
	
	/* pane width */
	public $paneWidth;
	/* pane height */
	public $paneHeight;
	/* If its openable*/
	private $openable = false;
	/* Thickness of border */
	public $border = 10;
	/* Color of the border */
	private $borderColor = 'white';
	/* if we'll have 2 lined inner border there on pane */
	private $innerPadding = false;
	/* If it has doorknob */
	private $doorknob = false;
	/* doorknob type */
	private $doorknobType;
	/* if it has separator */
	private $separator = false;
	/* width of separator if any */
	private $separatorWidth = 4;
	/* Stars */
	private $stars = false;
	/* Type of stars */
	private $typeOstar = 1;
	/* Dashed line */
	private $dashedLine = false;
	/* Sign */
	private $sign = false;
	/* If it has deviders */
	private $deviders = false;
	/* Type of deviders */
	private $typeDevider = false;
	/* set border Thickness of deviders */
	private $borderDevider = false;
	/* Deviders modificators array */
	private $modDevider_r = array();
	/* Column distance between deviders */
	private $distanceCol = false;
	/* Row distance between devideers */
	private $distanceRow = false;
	/* Pane image */
	public $paneIm;
	/* Pane Colors */
	private $paneColors = array();
	/* mainImg object */
	private $parObj;
	
	function __construct($wid, $parObj, $bcolor) {
		
		$this->parObj = $parObj;

		if ($this->parObj->type == 'preview') {
			$this->paneHeight = $this->parObj->preHeight;
			$this->paneWidth = round($wid * $this->parObj->ratio);
		} else {
			$this->paneHeight = $this->parObj->height;
			$this->paneWidth = $wid;		
		}
		if ($bcolor == 2) {
			$this->borderColor = 'dGrey';
		}

		$this->generatePane();
	}
	
	/* Set pane' border width */
	public function addBorder($border = false) {
		if ($border) {
			$this->border = $border;
		}
		if ($this->parObj->type == 'preview') {
			$this->border = round($this->border * $this->parObj->ratio) + 1;
		}
	}
	
	/* Inner Padding */
	public function addPadding() {
		
		$this->innerPadding = true;
	}
	
	/* Can we open that piece? (thought not sure how to use right now) */
	/*public function addOpen($direction) {
		
		$this->openable = $direction;
	}*/
	
	/* Put a doorknob on a frame */
	public function addHandler($position, $type) {
		
		$this->doorknob = $position;
		$this->doorknobType = $type;
	}
	
	/* put a 3 blocks line on a border */
	public function addSeparator($position, $wid = false) {
		
		$this->separator = $position;
		if ($wid) {
			$this->separatorWidth = $wid;
		}
		if ($this->parObj->type == 'preview') {
			$this->separatorWidth = round($this->separatorWidth * $this->parObj->ratio);
		}
	}
	
	/* Put stars to show possible opening directions */
	public function addStar($gt, $lt, $up, $dn, $stype) {
		
		$this->stars = array($gt, $lt, $up, $dn);
		$this->typeOstar = $stype;
	}
	
	/* add a thin dahed line */
	public function addDashedLine() {
		
		$this->dashedLine = true;
	}
	
	/* add a sign at the center of pane */
	public function addSign($sign) {
		
		$this->sign = $sign;
	}
	
	/* Set Thickness of Deviders */
	public function addDeviderBorder($d_border) {
		
		if ($this->parObj->type == 'preview') {
			$this->borderDevider = round($d_border * $this->parObj->ratio);
			return true;
		}
		$this->borderDevider = $d_border;
	}
	
	/* Devide window on smaller pieces */
	public function addDeviders($type, $distanceType, $proportions) {

		$this->deviders = true;
		$this->typeDevider = $type;
		if ($distanceType == 2) {
			if ($proportions[0]) $this->distanceCol = $proportions[0];
			if ($proportions[1]) $this->distanceRow = $proportions[1];
		}
	}
	
	/* Modificators for existing deviders */
	public function addModifyDevider($type, $mod) {
		
		$this->modDevider_r[$type][] = $mod;
	}
	
	/* All the heavy lifting */
	public function drawPane() {

		if ($this->deviders) {
			switch($this->typeDevider) {
				case 1:
					$b_overallWidth = ($this->borderDevider)?($this->borderDevider + $this->border * 2):($this->border * 3);
					($this->parObj->type == 'preview') ? $coeff = 1 - ($b_overallWidth * 100)/$this->preHeight/100 : $coeff = 1 - ($b_overallWidth * 100)/$this->paneHeight/100;
					if ($this->distanceRow) {
						list($distance,) = explode(':', $this->distanceRow);
						if (!$distance) {
							$distanceProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance = round($distance * $this->parObj->ratio);
							}
							$distance = round($distance * $coeff);
						}
					} 
					if (!$this->distanceRow || isset($distanceProportional)) {
						$distance = round(($this->paneHeight - $b_overallWidth)/2);
					}
					
					$border_width = $this->paneWidth - ($this->border * 2);
					$border1_height = ($this->paneHeight - $b_overallWidth) - (($this->paneHeight - $b_overallWidth) - $distance);
					$border2_height = $this->paneHeight - $b_overallWidth - $border1_height;

					$b1Im = imagecreatetruecolor($border_width, $border1_height);
					$b2Im = imagecreatetruecolor($border_width, $border2_height);
					imagefilledrectangle($b1Im, 1, 1, ($border_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border_width - 2), ($border2_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b2Im, $this->border, $this->border + $border1_height + ((is_numeric($this->borderDevider))?$this->borderDevider:$this->border), 0, 0, $border_width, $border2_height, 100);

					if ($this->innerPadding) {
						$this->drawSecondLine($border_width, $border1_height, $this->border, $this->border);
						$this->drawSecondLine($border_width, $border2_height, $this->border, $this->border + $border1_height + ((is_numeric($this->borderDevider))?$this->borderDevider:$this->border));				
					}
				break;
				case 2:
					($this->parObj->type == 'preview') ? $coeff = 1 - ($this->border * 4 * 100)/$this->preHeight/100 : $coeff = 1 - ($this->border * 4 * 100)/$this->paneHeight/100;
					if ($this->distanceRow) {
						list($distance1, $distance2,) = explode(':', $this->distanceRow);
						if (!$distance1) {
							$distance1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance1 = round($distance1 * $this->parObj->ratio);
							}
							$distance1 = round($distance1 * $coeff);							
						}
						if (!$distance2) {
							$distance2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance2 = round($distance2 * $this->parObj->ratio);
							}
							$distance2 = round($distance2 * $coeff) + $this->border;
						}
					}
					if (!$this->distanceRow || isset($distance1Proportional)) {
						$distance1 = round(($this->paneHeight - $this->border * 4)/3);
					}
					if (!$this->distanceRow || isset($distance2Proportional)) {
						$distance2 = round((($this->paneHeight - $this->border * 4) - $distance1)/2) + $distance1 + $this->border;
					}

					$border_width = $this->paneWidth - ($this->border * 2);
					$border1_height = ($this->paneHeight - $this->border * 4) - (($this->paneHeight - $this->border * 4) - $distance1);
					$border2_height = ($this->paneHeight - $this->border * 4) - (($this->paneHeight - $this->border * 4) - $distance2) - $border1_height - $this->border;
					$border3_height = ($this->paneHeight - $this->border * 4) - $border1_height - $border2_height;
					
					$b1Im = imagecreatetruecolor($border_width, $border1_height);
					$b2Im = imagecreatetruecolor($border_width, $border2_height);
					$b3Im = imagecreatetruecolor($border_width, $border3_height);
					imagefilledrectangle($b1Im, 1, 1, ($border_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border_width - 2), ($border3_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b2Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b3Im, $this->border, ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border_width, $border3_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border_width, $border1_height, $this->border, $this->border);
						$this->drawSecondLine($border_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));
						$this->drawSecondLine($border_width, $border3_height, $this->border, ($this->border * 3 + $border1_height + $border2_height));
					}
				break;
				case 3:
					($this->parObj->type == 'preview') ? $coeff = 1 - ($this->border * 5 * 100)/$this->preHeight/100 : $coeff = 1 - ($this->border * 5 * 100)/$this->paneHeight/100;
					if ($this->distanceRow) {
						list($distance1, $distance2, $distance3) = explode(':', $this->distanceRow);
						if (!$distance1) {
							$distance1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance1 = round($distance1 * $this->parObj->ratio);
							}
							$distance1 = round($distance1 * $coeff);							
						}
						if (!$distance2) {
							$distance2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance2 = round($distance2 * $this->parObj->ratio);
							}
							$distance2 = round($distance2 * $coeff) + $this->border;
						}
						if (!$distance3) {
							$distance3Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance3 = round($distance3 * $this->parObj->ratio);
							}
							$distance3 = round($distance3 * $coeff) + $this->border * 2;
						}
					}
					
					if (!$this->distanceRow || isset($distance1Proportional)) {
						$distance1 = round(($this->paneHeight - $this->border * 5)/4);
					}
					if (!$this->distanceRow || isset($distance2Proportional)) {
						$distance2 = round((($this->paneHeight - $this->border * 5) - $distance1)/3) + $distance1 + $this->border;
					}
					if (!$this->distanceRow || isset($distance3Proportional)) {
						$distance3 = round((($this->paneHeight - $this->border * 4) - $distance2)/2) + $distance2 + $this->border;
					}

					$border_width = $this->paneWidth - ($this->border * 2);
					$border1_height = ($this->paneHeight - $this->border * 5) - (($this->paneHeight - $this->border * 5) - $distance1);
					$border2_height = ($this->paneHeight - $this->border * 5) - (($this->paneHeight - $this->border * 5) - $distance2) - $border1_height - $this->border;
					$border3_height = ($this->paneHeight - $this->border * 5) - (($this->paneHeight - $this->border * 5) - $distance3) - $border1_height - $border2_height - $this->border * 2;
					$border4_height = ($this->paneHeight - $this->border * 5) - $border1_height - $border2_height - $border3_height;

					$b1Im = imagecreatetruecolor($border_width, $border1_height);
					$b2Im = imagecreatetruecolor($border_width, $border2_height);
					$b3Im = imagecreatetruecolor($border_width, $border3_height);					
					$b4Im = imagecreatetruecolor($border_width, $border4_height);
					imagefilledrectangle($b1Im, 1, 1, ($border_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border_width - 2), ($border3_height - 2), $this->paneColors['white']);					
					imagefilledrectangle($b4Im, 1, 1, ($border_width - 2), ($border4_height - 2), $this->paneColors['white']);					
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b2Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b3Im, $this->border, ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border_width, $border3_height, 100);				
					imagecopymerge($this->paneIm, $b4Im, $this->border, ($this->border * 4 + $border1_height + $border2_height + $border3_height), 0, 0, $border_width, $border4_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border_width, $border1_height, $this->border, $this->border);		
						$this->drawSecondLine($border_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));		
						$this->drawSecondLine($border_width, $border3_height, $this->border, ($this->border * 3 + $border1_height + $border2_height));		
						$this->drawSecondLine($border_width, $border4_height, $this->border, ($this->border * 4 + $border1_height + $border2_height + $border3_height));
					}
				break;
				case 4:
					($this->parObj->type == 'preview') ? $coeffR = 1 - ($this->border * 3 * 100)/$this->preHeight/100 : $coeffR = 1 - ($this->border * 3 * 100)/$this->paneHeight/100;
					$coeffC = 1 - ($this->border * 3 * 100)/$this->paneWidth/100;
					if ($this->distanceRow) {
						list($distanceR,) = explode(':', $this->distanceRow);
						if (!$distanceR) {
							$distanceRProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR = round($distanceR * $this->parObj->ratio);
							}
							$distanceR = round($distanceR * $coeffR);
						}
					}
					if ($this->distanceCol) {
						list($distanceC,) = explode(':', $this->distanceCol);
						if (!$distanceC) {
							$distanceCProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC = round($distanceC * $this->parObj->ratio);
							}
							$distanceC = round($distanceC * $coeffC);
						}
					}
					
					if (!$this->distanceRow || $distanceRProportional) {
						$distanceR = round(($this->paneHeight - $this->border * 3)/2);
					}
					if (!$this->distanceCol || $distanceCProportional) {
						$distanceC = round(($this->paneWidth - $this->border * 3)/2);
					}

					$border1_width = ($this->paneWidth - $this->border * 3) - (($this->paneWidth - $this->border * 3) - $distanceC);
					$border2_width = ($this->paneWidth - $this->border * 3) - $border1_width;
					$border1_height = ($this->paneHeight - $this->border * 3) - (($this->paneHeight - $this->border * 3) - $distanceR);
					$border2_height = ($this->paneHeight - $this->border * 3) - $border1_height;
				
					$b1Im = imagecreatetruecolor($border1_width, $border1_height);
					$b2Im = imagecreatetruecolor($border2_width, $border1_height);
					$b3Im = imagecreatetruecolor($border1_width, $border2_height);
					$b4Im = imagecreatetruecolor($border2_width, $border2_height);
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border1_height - 2), $this->paneColors['white']);					
					imagefilledrectangle($b3Im, 1, 1, ($border1_width - 2), ($border2_height - 2), $this->paneColors['white']);					
					imagefilledrectangle($b4Im, 1, 1, ($border2_width - 2), ($border2_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b2Im, ($border1_width + $this->border * 2), $this->border, 0, 0, $border2_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b3Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border1_width, $border2_height, 100);			
					imagecopymerge($this->paneIm, $b4Im, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height), 0, 0, $border2_width, $border2_height, 100);
							
					$this->drawSecondLine($border1_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));		
					$this->drawSecondLine($border2_width, $border2_height, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height));

					$normalSecondLine = true;
					if (isset($this->modDevider_r[4])) {
						foreach($this->modDevider_r[4] as $v) {
							if ($v == 'cellsTop') {
								$normalSecondLine = false;
								if ($this->innerPadding) {
									$this->drawSecondLine($border1_width/2, $border1_height/2, $this->border, $this->border);
									$this->drawSecondLine($border1_width/2, $border1_height/2, ($this->border + $border1_width/2), $this->border);
									$this->drawSecondLine($border1_width/2, $border1_height/2, $this->border, ($this->border + $border1_height/2));
									$this->drawSecondLine($border1_width/2, $border1_height/2, ($this->border + $border1_width/2), ($this->border + $border1_height/2));
									$this->drawSecondLine($border1_width/2, $border1_height/2, ($this->border * 2 + $border1_width), $this->border);
									$this->drawSecondLine($border1_width/2, $border1_height/2, ($this->border * 2 + $border1_width + $border1_width/2), $this->border);
									$this->drawSecondLine($border1_width/2, $border1_height/2, ($this->border * 2 + $border1_width), ($this->border + $border1_height/2));
									$this->drawSecondLine($border1_width/2, $border1_height/2, ($this->border * 2 + $border1_width + $border1_width/2), ($this->border + $border1_height/2));
								}
							}
						}
						if ($this->innerPadding) {
							$this->drawSecondLine(($border1_width + $this->border - 1), ($border1_height + $this->border - 2), $this->border, $this->border, true);
							$this->drawSecondLine(($border1_width + $border2_width + $this->border * 2 - 1), ($border1_height + $this->border - 2), ($this->border * 2 + $border1_width), $this->border, true);
						}
					}
					
					if ($normalSecondLine && $this->innerPadding) {
						$this->drawSecondLine($border1_width, $border1_height, $this->border, $this->border);		
						$this->drawSecondLine($border2_width, $border1_height, ($border1_width + $this->border * 2), $this->border);						
					}
				break;
				case 5:
					($this->parObj->type == 'preview') ? $coeffR = 1 - ($this->border * 4 * 100)/$this->preHeight/100 : $coeffR = 1 - ($this->border * 4 * 100)/$this->paneHeight/100;
					$coeffC = 1 - ($this->border * 3 * 100)/$this->paneWidth/100;
					if ($this->distanceRow) {
						list($distanceR1, $distanceR2) = explode(':', $this->distanceRow);
						if (!$distanceR1) {
							$distanceR1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR1 = round($distanceR1 * $this->parObj->ratio);
							}
							$distanceR1 = round($distanceR1 * $coeffR);
						}
						if (!$distanceR2) {
							$distanceR2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR2 = round($distanceR2 * $this->parObj->ratio);
							}
							$distanceR2 = round($distanceR2 * $coeffR);
						}
					}
					if ($this->distanceCol) {
						list($distanceC,) = explode(':', $this->distanceCol);
						if (!$distanceC) {
							$distanceCProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC = round($distanceC * $this->parObj->ratio);
							}
							$distanceC = round($distanceC * $coeffC);
						}
					}
					
					if (!$this->distanceRow || isset($distanceR1Proportional)) {
						$distanceR1 = round(($this->paneHeight - $this->border * 4)/3);
					}
					if (!$this->distanceRow || isset($distanceR2Proportional)) {
						$distanceR2 = round((($this->paneHeight - $this->border * 4) - $distanceR1)/2) + $distanceR1 + $this->border;
					}
					if (!$this->distanceCol || $distanceCProportional) {
						$distanceC = round(($this->paneWidth - $this->border * 3)/2);
					}
					
					$border1_width = ($this->paneWidth - $this->border * 3) - (($this->paneWidth - $this->border * 3) - $distanceC);
					$border2_width = ($this->paneWidth - $this->border * 3) - $border1_width;
					$border1_height = ($this->paneHeight - $this->border * 4) - (($this->paneHeight - $this->border * 4) - $distanceR1);
					$border2_height = ($this->paneHeight - $this->border * 4) - (($this->paneHeight - $this->border * 4) - $distanceR2) - $border1_height - $this->border;
					$border3_height = ($this->paneHeight - $this->border * 4) - $border1_height - $border2_height;
					
					$b1Im = imagecreatetruecolor($border1_width, $border1_height);
					$b2Im = imagecreatetruecolor($border2_width, $border1_height);
					$b3Im = imagecreatetruecolor($border1_width, $border2_height);
					$b4Im = imagecreatetruecolor($border2_width, $border2_height);
					$b5Im = imagecreatetruecolor($border1_width, $border3_height);
					$b6Im = imagecreatetruecolor($border2_width, $border3_height);
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border1_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b4Im, 1, 1, ($border2_width - 2), ($border2_height - 2), $this->paneColors['white']);									
					imagefilledrectangle($b5Im, 1, 1, ($border1_width - 2), ($border3_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b6Im, 1, 1, ($border2_width - 2), ($border3_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b2Im, ($border1_width + $this->border * 2), $this->border, 0, 0, $border2_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b3Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border1_width, $border2_height, 100);			
					imagecopymerge($this->paneIm, $b4Im, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height), 0, 0, $border2_width, $border2_height, 100);					
					imagecopymerge($this->paneIm, $b5Im, $this->border, ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border1_width, $border3_height, 100);					
					imagecopymerge($this->paneIm, $b6Im, ($border1_width + $this->border * 2), ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border2_width, $border3_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border1_width, $border1_height, $this->border, $this->border);				
						$this->drawSecondLine($border2_width, $border1_height, ($border1_width + $this->border * 2), $this->border);				
						$this->drawSecondLine($border1_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));				
						$this->drawSecondLine($border2_width, $border2_height, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height));				
						$this->drawSecondLine($border1_width, $border3_height, $this->border, ($this->border * 3 + $border1_height + $border2_height));				
						$this->drawSecondLine($border2_width, $border3_height, ($border1_width + $this->border * 2), ($this->border * 3 + $border1_height + $border2_height));
					}
				break;
				case 6:
					($this->parObj->type == 'preview') ? $coeffR = 1 - ($this->border * 5 * 100)/$this->preHeight/100 : $coeffR = 1 - ($this->border * 5 * 100)/$this->paneHeight/100;
					$coeffC = 1 - ($this->border * 3 * 100)/$this->paneWidth/100;
					if ($this->distanceRow) {
						list($distanceR1, $distanceR2, $distanceR3) = explode(':', $this->distanceRow);
						if (!$distanceR1) {
							$distanceR1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR1 = round($distanceR1 * $this->parObj->ratio);
							}
							$distanceR1 = round($distanceR1 * $coeffR);							
						}
						if (!$distanceR2) {
							$distanceR2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR2 = round($distanceR2 * $this->parObj->ratio);
							}
							$distanceR2 = round($distanceR2 * $coeffR) + $this->border;
						}
						if (!$distanceR3) {
							$distanceR3Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR3 = round($distanceR3 * $this->parObj->ratio);
							}
							$distanceR3 = round($distanceR3 * $coeffR) + $this->border * 2;
						}
					}
					if ($this->distanceCol) {
						list($distanceC,) = explode(':', $this->distanceCol);
						if (!$distanceC) {
							$distanceCProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC = round($distanceC * $this->parObj->ratio);
							}
							$distanceC = round($distanceC * $coeffC);
						}
					}
					
					if (!$this->distanceRow || isset($distanceR1Proportional)) {
						$distanceR1 = round(($this->paneHeight - $this->border * 5)/4);
					}
					if (!$this->distanceRow || isset($distanceR2Proportional)) {
						$distanceR2 = round((($this->paneHeight - $this->border * 5) - $distanceR1)/3) + $distanceR1 + $this->border;
					}
					if (!$this->distanceRow || isset($distanceR3Proportional)) {
						$distanceR3 = round((($this->paneHeight - $this->border * 4) - $distanceR2)/2) + $distanceR2 + $this->border;
					}
					if (!$this->distanceCol || $distanceCProportional) {
						$distanceC = round(($this->paneWidth - $this->border * 3)/2);
					}
					
					$border1_width = ($this->paneWidth - $this->border * 3) - (($this->paneWidth - $this->border * 3) - $distanceC);
					$border2_width = ($this->paneWidth - $this->border * 3) - $border1_width;
					$border1_height = ($this->paneHeight - $this->border * 5) - (($this->paneHeight - $this->border * 5) - $distanceR1);
					$border2_height = ($this->paneHeight - $this->border * 5) - (($this->paneHeight - $this->border * 5) - $distanceR2) - $border1_height - $this->border;
					$border3_height = ($this->paneHeight - $this->border * 5) - (($this->paneHeight - $this->border * 5) - $distanceR3) - $border1_height - $border2_height - $this->border * 2;
					$border4_height = ($this->paneHeight - $this->border * 5) - $border1_height - $border2_height - $border3_height;
					
					$b1Im = imagecreatetruecolor($border1_width, $border1_height);
					$b2Im = imagecreatetruecolor($border2_width, $border1_height);
					$b3Im = imagecreatetruecolor($border1_width, $border2_height);
					$b4Im = imagecreatetruecolor($border2_width, $border2_height);
					$b5Im = imagecreatetruecolor($border1_width, $border3_height);
					$b6Im = imagecreatetruecolor($border2_width, $border3_height);
					$b7Im = imagecreatetruecolor($border1_width, $border4_height);
					$b8Im = imagecreatetruecolor($border2_width, $border4_height);
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border1_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b4Im, 1, 1, ($border2_width - 2), ($border2_height - 2), $this->paneColors['white']);									
					imagefilledrectangle($b5Im, 1, 1, ($border1_width - 2), ($border3_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b6Im, 1, 1, ($border2_width - 2), ($border3_height - 2), $this->paneColors['white']);										
					imagefilledrectangle($b7Im, 1, 1, ($border1_width - 2), ($border4_height - 2), $this->paneColors['white']);										
					imagefilledrectangle($b8Im, 1, 1, ($border2_width - 2), ($border4_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b2Im, ($border1_width + $this->border * 2), $this->border, 0, 0, $border2_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b3Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border1_width, $border2_height, 100);			
					imagecopymerge($this->paneIm, $b4Im, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height), 0, 0, $border2_width, $border2_height, 100);					
					imagecopymerge($this->paneIm, $b5Im, $this->border, ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border1_width, $border3_height, 100);					
					imagecopymerge($this->paneIm, $b6Im, ($border1_width + $this->border * 2), ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border2_width, $border3_height, 100);															
					imagecopymerge($this->paneIm, $b7Im, $this->border, ($this->border * 4 + $border1_height + $border2_height + $border3_height), 0, 0, $border1_width, $border4_height, 100);															
					imagecopymerge($this->paneIm, $b8Im, ($border1_width + $this->border * 2), ($this->border * 4 + $border1_height + $border2_height + $border3_height), 0, 0, $border2_width, $border4_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border1_width, $border1_height, $this->border, $this->border);					
						$this->drawSecondLine($border2_width, $border1_height, ($border1_width + $this->border * 2), $this->border);					
						$this->drawSecondLine($border1_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));					
						$this->drawSecondLine($border2_width, $border2_height, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height));					
						$this->drawSecondLine($border1_width, $border3_height, $this->border, ($this->border * 3 + $border1_height + $border2_height));					
						$this->drawSecondLine($border2_width, $border3_height, ($border1_width + $this->border * 2), ($this->border * 3 + $border1_height + $border2_height));					
						$this->drawSecondLine($border1_width, $border4_height, $this->border, ($this->border * 4 + $border1_height + $border2_height + $border3_height));					
						$this->drawSecondLine($border2_width, $border4_height, ($border1_width + $this->border * 2), ($this->border * 4 + $border1_height + $border2_height + $border3_height));
					}
				break;
				case 7:
					($this->parObj->type == 'preview') ? $coeffR = 1 - ($this->border * 4 * 100)/$this->preHeight/100 : $coeffR = 1 - ($this->border * 4 * 100)/$this->paneHeight/100;
					$coeffC = 1 - ($this->border * 4 * 100)/$this->paneWidth/100;
					if ($this->distanceRow) {
						list($distanceR1, $distanceR2,) = explode(':', $this->distanceRow);
						if (!$distanceR1) {
							$distanceR1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR1 = round($distanceR1 * $this->parObj->ratio);
							}
							$distanceR1 = round($distanceR1 * $coeffR);							
						}
						if (!$distanceR2) {
							$distanceR2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR2 = round($distanceR2 * $this->parObj->ratio);
							}
							$distanceR2 = round($distanceR2 * $coeffR) + $this->border;
						}
					}
					if ($this->distanceCol) {
						list($distanceC1,$distanceC2,) = explode(':', $this->distanceCol);
						if (!$distanceC1) {
							$distanceC1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC1 = round($distanceC1 * $this->parObj->ratio);
							}
							$distanceC1 = round($distanceC1 * $coeffC);
						}
						if (!$distanceC2) {
							$distanceC2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC2 = round($distanceC2 * $this->parObj->ratio);
							}
							$distanceC2 = round($distanceC2 * $coeffC);
						}
					}
					
					if (!$this->distanceRow || isset($distanceR1Proportional)) {
						$distanceR1 = round(($this->paneHeight - $this->border * 4)/3);
					}
					if (!$this->distanceRow || isset($distanceR2Proportional)) {
						$distanceR2 = round((($this->paneHeight - $this->border * 4) - $distanceR1)/2) + $distanceR1 + $this->border;
					}
					if (!$this->distanceCol || $distanceC1Proportional) {
						$distanceC1 = round(($this->paneWidth - $this->border * 4)/3);
					}
					if (!$this->distanceRow || isset($distanceC2Proportional)) {
						$distanceC2 = round((($this->paneWidth - $this->border * 4) - $distanceC1)/2) + $distanceC1 + $this->border;
					}
				
					$border1_width = ($this->paneWidth - $this->border * 4) - (($this->paneWidth - $this->border * 4) - $distanceC1);
					$border2_width = ($this->paneWidth - $this->border * 4) - (($this->paneWidth - $this->border * 4) - $distanceC2) - $border1_width - $this->border;
					$border3_width = ($this->paneWidth - $this->border * 4) - $border1_width - $border2_width;
					$border1_height = ($this->paneHeight - $this->border * 4) - (($this->paneHeight - $this->border * 4) - $distanceR1);
					$border2_height = ($this->paneHeight - $this->border * 4) - (($this->paneHeight - $this->border * 4) - $distanceR2) - $border1_height - $this->border;
					$border3_height = ($this->paneHeight - $this->border * 4) - $border1_height - $border2_height;

					$b1Im = imagecreatetruecolor($border1_width, $border1_height);
					$b2Im = imagecreatetruecolor($border2_width, $border1_height);
					$b3Im = imagecreatetruecolor($border3_width, $border1_height);
					$b4Im = imagecreatetruecolor($border1_width, $border2_height);
					$b5Im = imagecreatetruecolor($border2_width, $border2_height);
					$b6Im = imagecreatetruecolor($border3_width, $border2_height);
					$b7Im = imagecreatetruecolor($border1_width, $border3_height);
					$b8Im = imagecreatetruecolor($border2_width, $border3_height);				
					$b9Im = imagecreatetruecolor($border3_width, $border3_height);				
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border3_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b4Im, 1, 1, ($border1_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b5Im, 1, 1, ($border2_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b6Im, 1, 1, ($border3_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b7Im, 1, 1, ($border1_width - 2), ($border3_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b8Im, 1, 1, ($border2_width - 2), ($border3_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b9Im, 1, 1, ($border3_width - 2), ($border3_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border1_height, 100);			
					imagecopymerge($this->paneIm, $b2Im, ($border1_width + $this->border * 2), $this->border, 0, 0, $border2_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b3Im, ($border1_width + $border2_width + $this->border * 3), $this->border, 0, 0, $border3_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b4Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border1_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b5Im, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height), 0, 0, $border2_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b6Im, ($border1_width + $border2_width + $this->border * 3), ($this->border * 2 + $border1_height), 0, 0, $border3_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b7Im, $this->border, ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border1_width, $border3_height, 100);
					imagecopymerge($this->paneIm, $b8Im, ($border1_width + $this->border * 2), ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border2_width, $border3_height, 100);
					imagecopymerge($this->paneIm, $b9Im, ($border1_width + $border2_width + $this->border * 3), ($this->border * 3 + $border1_height + $border2_height), 0, 0, $border3_width, $border3_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border1_width, $border1_height, $this->border, $this->border);
						$this->drawSecondLine($border2_width, $border1_height, ($border1_width + $this->border * 2), $this->border);
						$this->drawSecondLine($border3_width, $border1_height, ($border1_width + $border2_width + $this->border * 3), $this->border);
						$this->drawSecondLine($border1_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));
						$this->drawSecondLine($border2_width, $border2_height, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height));
						$this->drawSecondLine($border3_width, $border2_height, ($border1_width + $border2_width + $this->border * 3), ($this->border * 2 + $border1_height));
						$this->drawSecondLine($border1_width, $border3_height, $this->border, ($this->border * 3 + $border1_height + $border2_height));
						$this->drawSecondLine($border2_width, $border3_height, ($border1_width + $this->border * 2), ($this->border * 3 + $border1_height + $border2_height));
						$this->drawSecondLine($border3_width, $border3_height, ($border1_width + $border2_width + $this->border * 3), ($this->border * 3 + $border1_height + $border2_height));
					}
				break;
				case 8:
					$coeff = 1 - ($this->border * 3 * 100)/$this->paneWidth/100;
					if ($this->distanceCol) {
						list($distance,) = explode(':', $this->distanceCol);
						if (!$distance) {
							$distanceProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance = round($distance * $this->parObj->ratio);
							}
							$distance = round($distance * $coeff);
						}
					} 
					if (!$this->distanceCol || isset($distanceProportional)) {
						$distance = round(($this->paneWidth - $this->border * 3)/2);
					}
					
					$border_height = $this->paneHeight - ($this->border * 2);
					$border1_width = ($this->paneWidth - $this->border * 3) - (($this->paneWidth - $this->border * 3) - $distance);
					$border2_width = ($this->paneWidth - $this->border * 3) - $border1_width;

					$b1Im = imagecreatetruecolor($border1_width, $border_height);
					$b2Im = imagecreatetruecolor($border2_width, $border_height);
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border_height, 100);
					imagecopymerge($this->paneIm, $b2Im, ($this->border * 2 + $border1_width), $this->border, 0, 0, $border2_width, $border_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border1_width, $border_height, $this->border, $this->border);
						$this->drawSecondLine($border2_width, $border_height, ($this->border * 2 + $border1_width), $this->border);
					}
				break;
				case 9:
					$coeff = 1 - ($this->border * 4 * 100)/$this->paneWidth/100;
					if ($this->distanceRow) {
						list($distance1, $distance2,) = explode(':', $this->distanceCol);
						if (!$distance1) {
							$distance1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance1 = round($distance1 * $this->parObj->ratio);
							}
							$distance1 = round($distance1 * $coeff);							
						}
						if (!$distance2) {
							$distance2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distance2 = round($distance2 * $this->parObj->ratio);
							}
							$distance2 = round($distance2 * $coeff) + $this->border;
						}
					}
					if (!$this->distanceRow || isset($distance1Proportional)) {
						$distance1 = round(($this->paneWidth - $this->border * 4)/3);
					}
					if (!$this->distanceRow || isset($distance2Proportional)) {
						$distance2 = round((($this->paneWidth - $this->border * 4) - $distance1)/2) + $distance1 + $this->border;
					}

					$border_height = $this->paneHeight - ($this->border * 2);
					$border1_width = ($this->paneWidth - $this->border * 4) - (($this->paneWidth - $this->border * 4) - $distance1);
					$border2_width = ($this->paneWidth - $this->border * 4) - (($this->paneWidth - $this->border * 4) - $distance2) - $border1_width - $this->border;
					$border3_width = ($this->paneWidth - $this->border * 4) - $border1_width - $border2_width;
					
					$b1Im = imagecreatetruecolor($border1_width, $border_height);
					$b2Im = imagecreatetruecolor($border2_width, $border_height);
					$b3Im = imagecreatetruecolor($border3_width, $border_height);
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border3_width - 2), ($border_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border_height, 100);
					imagecopymerge($this->paneIm, $b2Im, ($this->border * 2 + $border1_width), $this->border, 0, 0, $border2_width, $border_height, 100);
					imagecopymerge($this->paneIm, $b3Im, ($this->border * 3 + $border1_width + $border2_width), $this->border, 0, 0, $border3_width, $border_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border1_width, $border_height, $this->border, $this->border);
						$this->drawSecondLine($border2_width, $border_height, ($this->border * 2 + $border1_width), $this->border);
						$this->drawSecondLine($border3_width, $border_height, ($this->border * 3 + $border1_width + $border2_width), $this->border);
					}
				break;
				case 10:
					($this->parObj->type == 'preview') ? $coeffR = 1 - ($this->border * 4 * 100)/$this->preHeight/100 : $coeffR = 1 - ($this->border * 4 * 100)/$this->paneHeight/100;
					$coeffC = 1 - ($this->border * 3 * 100)/$this->paneWidth/100;
					if ($this->distanceCol) {
						list($distanceC1, $distanceC2) = explode(':', $this->distanceCol);
						if (!$distanceC1) {
							$distanceC1Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC1 = round($distanceC1 * $this->parObj->ratio);
							}
							$distanceC1 = round($distanceC1 * $coeffC);
						}
						if (!$distanceC2) {
							$distanceC2Proportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceC2 = round($distanceC2 * $this->parObj->ratio);
							}
							$distanceC2 = round($distanceC2 * $coeffR);
						}
					}
					if ($this->distanceRow) {
						list($distanceR,) = explode(':', $this->distanceRow);
						if (!$distanceR) {
							$distanceRProportional = true;
						} else {
							if ($this->parObj->type == 'preview') {
								$distanceR = round($distanceR * $this->parObj->ratio);
							}
							$distanceR = round($distanceR * $coeffR);
						}
					}
					
					if (!$this->distanceCol|| isset($distanceC1Proportional)) {
						$distanceC1 = round(($this->paneWidth - $this->border * 4)/3);
					}
					if (!$this->distanceCol || isset($distanceC2Proportional)) {
						$distanceC2 = round((($this->paneWidth - $this->border * 4) - $distanceC1)/2) + $distanceC1 + $this->border;
					}
					if (!$this->distanceRow || $distanceRProportional) {
						$distanceR = round(($this->paneHeight - $this->border * 3)/2);
					}
					
					$border1_height = ($this->paneHeight - $this->border * 3) - (($this->paneHeight - $this->border * 3) - $distanceR);
					$border2_height = ($this->paneHeight - $this->border * 3) - $border1_height;
					$border1_width = ($this->paneWidth - $this->border * 4) - (($this->paneWidth - $this->border * 4) - $distanceC1);
					$border2_width = ($this->paneWidth - $this->border * 4) - (($this->paneWidth - $this->border * 4) - $distanceC2) - $border1_width - $this->border;
					$border3_width = ($this->paneWidth - $this->border * 4) - $border1_width - $border2_width;
					
					$b1Im = imagecreatetruecolor($border1_width, $border1_height);
					$b2Im = imagecreatetruecolor($border2_width, $border1_height);
					$b3Im = imagecreatetruecolor($border3_width, $border1_height);
					$b4Im = imagecreatetruecolor($border1_width, $border2_height);
					$b5Im = imagecreatetruecolor($border2_width, $border2_height);
					$b6Im = imagecreatetruecolor($border3_width, $border2_height);
					imagefilledrectangle($b1Im, 1, 1, ($border1_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b2Im, 1, 1, ($border2_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b3Im, 1, 1, ($border3_width - 2), ($border1_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b4Im, 1, 1, ($border1_width - 2), ($border2_height - 2), $this->paneColors['white']);									
					imagefilledrectangle($b5Im, 1, 1, ($border2_width - 2), ($border2_height - 2), $this->paneColors['white']);
					imagefilledrectangle($b6Im, 1, 1, ($border3_width - 2), ($border2_height - 2), $this->paneColors['white']);
					
					imagecopymerge($this->paneIm, $b1Im, $this->border, $this->border, 0, 0, $border1_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b2Im, ($border1_width + $this->border * 2), $this->border, 0, 0, $border2_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b3Im, ($border1_width + $border2_width + $this->border * 3), $this->border, 0, 0, $border3_width, $border1_height, 100);
					imagecopymerge($this->paneIm, $b4Im, $this->border, ($this->border * 2 + $border1_height), 0, 0, $border1_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b5Im, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height), 0, 0, $border2_width, $border2_height, 100);
					imagecopymerge($this->paneIm, $b6Im, ($border1_width + $border2_width + $this->border * 3), ($this->border * 2 + $border1_height), 0, 0, $border3_width, $border2_height, 100);
					
					if ($this->innerPadding) {
						$this->drawSecondLine($border1_width, $border1_height, $this->border, $this->border);
						$this->drawSecondLine($border2_width, $border1_height, ($border1_width + $this->border * 2), $this->border);
						$this->drawSecondLine($border3_width, $border1_height, ($border1_width + $border2_width + $this->border * 3), $this->border);
						$this->drawSecondLine($border1_width, $border2_height, $this->border, ($this->border * 2 + $border1_height));
						$this->drawSecondLine($border2_width, $border2_height, ($border1_width + $this->border * 2), ($this->border * 2 + $border1_height));
						$this->drawSecondLine($border3_width, $border2_height, ($border1_width + $border2_width + $this->border * 3), ($this->border * 2 + $border1_height));
					}
				break;
				default:
					
				break;
			}
		} else {
			$border_width = $this->paneWidth - ($this->border * 2);
			$border_height = $this->paneHeight - ($this->border * 2);
			$bIm = imagecreatetruecolor($border_width, $border_height);
			imagefilledrectangle($bIm, 1, 1, ($border_width - 2), ($border_height - 2), $this->paneColors['white']);
			// add $bIm to main Pane
			imagecopymerge($this->paneIm, $bIm, $this->border, $this->border, 0, 0, $border_width, $border_height, 100);
			
			if ($this->innerPadding) {
				$this->drawSecondLine($this->paneWidth - $this->border * 2, $this->paneHeight - $this->border * 2, $this->border, $this->border);
			}
		}
		
		// corners
		$this->drawCorners();
		
		// doorknob
		$this->drawDoorknob();
		
		// separator
		$this->drawSeparator();
		
		// stars
		$this->drawStars($gray);
		
		// signs
		$this->drawSign();
		
		// dashedline
		$this->drawDashed();
	}

	/* draw corners of a pane */
	private function drawCorners() {
		
		imageline($this->paneIm, 2, 2, $this->border, $this->border, $this->paneColors['l_gray']);
		imageline($this->paneIm, 2, ($this->paneHeight - 2), $this->border, ($this->paneHeight - 2 - $this->border), $this->paneColors['l_gray']);
		imageline($this->paneIm, ($this->paneWidth - 2), 2, ($this->paneWidth - 2 - $this->border), $this->border, $this->paneColors['l_gray']);
		imageline($this->paneIm, ($this->paneWidth - 2), ($this->paneHeight - 2), ($this->paneWidth - 2 - $this->border), ($this->paneHeight - 2 - $this->border), $this->paneColors['l_gray']);
	}
	
	/* draw pads to hold a glass */
	private function drawSecondLine($wid, $hei, $marg_X, $marg_Y, $transparent = false) {

		if ($this->parObj->type == 'preview') {
			$WHcorr = 4;
		} else {
			$WHcorr = 8;
		}
		
		$padW = $wid - $WHcorr;
		$padH = $hei - $WHcorr;
		
		if (!$transparent) {
			$pad = imagecreatetruecolor($padW, $padH);
			imagefilledrectangle($pad, 1, 1, $padW - 2, $padH - 2, $this->paneColors['internal']);
			imagecopymerge($this->paneIm, $pad, $marg_X + round($WHcorr/2), $marg_Y + round($WHcorr/2), 0, 0, $padW, $padH, 100);
		} else {
			$this->drawDirectionBorders($padW + round($WHcorr/2), $padH + round($WHcorr/2), $marg_X + round($WHcorr/2), $marg_Y + round($WHcorr/2));
		}
	}
	
	/* draw transparent rectangle (to create those weird overwhelming borders) */
	private function drawDirectionBorders($wid, $hei, $pos_x, $pos_y) {
		//echo $pos_x,' - ', $pos_y, ' - ', $wid,' - ', $hei;exit;
		imagerectangle($this->paneIm, $pos_x, $pos_y, $wid, $hei, $this->paneColors['black']);
	}
	
	/* draw pane's handler */
	private function drawDoorknob() {
		
		if ($this->doorknob >= 2 && $this->doorknob <= 5) {
			$dn_r = $this->createDoorknobType();
			if ($this->doorknob == 2) {
				if ($this->doorknobType == 1) {
					imagecopymerge($this->paneIm, $dn_r[0][0], round((($this->border - $dn_r[0][1])/2) - 1), round((($this->paneHeight - $dn_r[0][2])/2) - $dn_r[0][2]/2), 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round((($this->border - $dn_r[0][1])/2)), round((($this->paneHeight - $dn_r[0][2])/2) - $dn_r[0][2]/2), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				} else {
					imagecopymerge($this->paneIm, $dn_r[0][0], round((($this->border - $dn_r[0][1])/2) + 1), round((($this->paneHeight - $dn_r[0][2])/2) - $dn_r[0][2]/2), 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round((($this->border - $dn_r[0][1])/2) + ($dn_r[0][1] - $dn_r[1][1])/2 + 1), round((($this->paneHeight - $dn_r[1][2])/2) + 1), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				}
			} else if ($this->doorknob == 3) {
				if ($this->doorknobType == 1) {
					imagecopymerge($this->paneIm, $dn_r[0][0], round($this->paneWidth - $this->border/2 - 1), round((($this->paneHeight - $dn_r[0][2])/2) - $dn_r[0][2]/2), 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round($this->paneWidth - $this->border/2 - 1 - $dn_r[1][1] + $dn_r[0][1]), round((($this->paneHeight - $dn_r[0][2])/2) - $dn_r[0][2]/2), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				} else {
					imagecopymerge($this->paneIm, $dn_r[0][0], round($this->paneWidth - $this->border + (($this->border - $dn_r[0][1])/2 - 1)), round((($this->paneHeight - $dn_r[0][2])/2) - $dn_r[0][2]/2), 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round($this->paneWidth - $this->border + (($this->border - $dn_r[0][1])/2 - 1) + ($dn_r[0][1] - $dn_r[1][1])/2), round((($this->paneHeight - $dn_r[1][2])/2) + 1), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				}
			} else if ($this->doorknob == 4) {
				if ($this->doorknobType == 1) {
					imagecopymerge($this->paneIm, $dn_r[0][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1), round((($this->border - $dn_r[0][1])/2) - $dn_r[0][1]/2) + 3, 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1), round((($this->border - $dn_r[0][1])/2) - $dn_r[0][1]/2) + 3, 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				} else {
					$dn_r[0][0] = imagerotate($dn_r[0][0], 90, 0);
					$dn_r[1][0] = imagerotate($dn_r[1][0], 90, 0);
					$dn_r[0] = array($dn_r[0][0], $dn_r[0][2], $dn_r[0][1]);
					$dn_r[1] = array($dn_r[1][0], $dn_r[1][2], $dn_r[1][1]);
					imagecopymerge($this->paneIm, $dn_r[0][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1), round(($this->border - $dn_r[0][2])/2) + 1, 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1) + 4, round(($this->border - $dn_r[0][2])/2) + 1 + round(($dn_r[0][2] - $dn_r[1][2])/2), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				}						
			} else if ($this->doorknob == 5) {
				if ($this->doorknobType == 1) {
					imagecopymerge($this->paneIm, $dn_r[0][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1), round($this->paneHeight - $dn_r[0][2] - 3), 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1), round($this->paneHeight - $dn_r[1][2] - 3), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				} else {
					$dn_r[0][0] = imagerotate($dn_r[0][0], 90, 0);
					$dn_r[1][0] = imagerotate($dn_r[1][0], 90, 0);
					$dn_r[0] = array($dn_r[0][0], $dn_r[0][2], $dn_r[0][1]);
					$dn_r[1] = array($dn_r[1][0], $dn_r[1][2], $dn_r[1][1]);
					imagecopymerge($this->paneIm, $dn_r[0][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1), round($this->paneHeight - $dn_r[0][2] - ($this->border - $dn_r[0][2])/2) - 1, 0, 0, $dn_r[0][1], $dn_r[0][2], 80);
					imagecopymerge($this->paneIm, $dn_r[1][0], round(($this->paneWidth - $dn_r[0][1])/2 - 1) + 4, round($this->paneHeight - $dn_r[0][2] - ($this->border - $dn_r[0][2])/2) - 1 + round(($dn_r[0][2] - $dn_r[1][2])/2), 0, 0, $dn_r[1][1], $dn_r[1][2], 80);
				}				
			}
		}
	}
	
	/* create image of doorknob depending on chosen type */
	private function createDoorknobType() {

		if ($this->doorknobType == 1) {
			if ($this->parObj->type == 'preview') {
				$dk_dim1 = 2;
				$dk_dim2 = 6.5;
			} else {
				$dk_dim1 = 6;
				$dk_dim2 = 12;				
			}
	
			$dk1 = imagecreatetruecolor($dk_dim1, $dk_dim2);
			$dk2 = imagecreatetruecolor($dk_dim2, $dk_dim1);
			imagefilledrectangle($dk1, $dk_dim1, $dk_dim1, $dk_dim1, $dk_dim2, $this->paneColors['white']);
			imagefilledrectangle($dk2, $dk_dim1, $dk_dim1, $dk_dim2, $dk_dim1, $this->paneColors['white']);
		
			return array(array($dk1, $dk_dim1, $dk_dim2), array($dk2, $dk_dim2, $dk_dim1));
		} else {
			if ($this->parObj->type == 'preview') {
				$dk_im1_dim1 = 2;
				$dk_im1_dim2 = 4;
				$dk_im2_dim1 = 1;
				$dk_im2_dim2 = 6;
			} else {
				$dk_im1_dim1 = 7;
				$dk_im1_dim2 = 10;
				$dk_im2_dim1 = 3;
				$dk_im2_dim2 = 14;			
			}
		
			$dk1 = imagecreatetruecolor($dk_im1_dim1, $dk_im1_dim2);
			$dk2 = imagecreatetruecolor($dk_im2_dim1, $dk_im2_dim2);
			$white = imagecolorallocate($dk1, 250, 250, 250);	
			if ($this->parObj->type == 'preview') {
				imagefilledrectangle($dk1, 0, 0, 0, 0, $this->paneColors['white']);
				imagefilledrectangle($dk2, 0, 0, 0, 0, $this->paneColors['white']);				
			} else {
				imagefilledrectangle($dk1, 1, 1, ($dk_im1_dim1 - 2), ($dk_im1_dim2 - 2), $this->paneColors['white']);
				imagefilledrectangle($dk2, 1, 1, ($dk_im2_dim1 - 2), ($dk_im2_dim2 - 2), $this->paneColors['white']);
			}
							
			return array(array($dk1, $dk_im1_dim1, $dk_im1_dim2), array($dk2, $dk_im2_dim1, $dk_im2_dim2));
		}	
	}

	/* draw separator on a right border */
	private function drawSeparator() {
		
		if ($this->separator) {
			$imW = $this->separatorWidth;
			$imH = $this->paneHeight - $this->border * 2;
			if ($this->parObj->type == 'preview') {
				if ($imW <= 1) $imW = 2;
			}
			$imHZ = ($imH/3) * 0.25;
			$imBZ = ($imH/3) * 0.75;

			$im = imagecreatetruecolor($imW, $imH);
			$imWR = imagecreatetruecolor($imW, $imHZ);
			$white = imagecolorallocate($im, 250, 250, 250);
			$black = imagecolorallocate($im, 0, 0, 0);
			imagefill($im, 0, 0, $black);
			imagefilledrectangle($imWR, 0, 0, $imW, $imHZ, $this->paneColors['white']);
	
			imagecopymerge($im, $imWR, 0, 0, 0, 0, $imW, $imHZ, 100);
			imagecopymerge($im, $imWR, 0, ($imHZ + $imBZ), 0, 0, $imW, $imHZ, 100);
			imagecopymerge($im, $imWR, 0, ($imHZ*2 + $imBZ*2), 0, 0, $imW, $imHZ, 100);
			
			if ($this->separator == 2) {
				$im_x = ($this->border - $this->separatorWidth);
			} else {
				$im_x = ($this->paneWidth - $this->border);
			}

			imagecopymerge($this->paneIm, $im, $im_x, $this->border, 0, 0, $imW, $imH, 100);
		}
	}

	/* draw pane's stars */
	private function drawStars($gray) {
		
		if ($this->stars) {
			if ($this->typeOstar == 2) {
				$style = array($paneColors['gray'], $paneColors['gray'], $paneColors['gray'], $paneColors['gray'], IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT);
				imagesetstyle($this->paneIm, $style);
				$star_color = IMG_COLOR_STYLED;
			} else {
				$star_color = $paneColors['gray'];
			}

			if ($this->stars[0]) {
				imageline($this->paneIm, ($this->border + 2), ($this->border + 2), ($this->paneWidth - $this->border - 2), ($this->paneHeight/2), $star_color);
				imageline($this->paneIm, ($this->border + 2), ($this->paneHeight - $this->border - 2), ($this->paneWidth - $this->border - 2), ($this->paneHeight/2), $star_color);
			}
			if ($this->stars[1]) {
				imageline($this->paneIm, ($this->paneWidth - $this->border - 2), ($this->border + 2), ($this->border + 2), ($this->paneHeight/2), $star_color);
				imageline($this->paneIm, ($this->paneWidth - $this->border - 2), ($this->paneHeight - $this->border - 2), ($this->border - 2), ($this->paneHeight/2), $star_color);
			}
			if ($this->stars[2]) {
				imageline($this->paneIm, ($this->paneWidth/2), ($this->border + 2), ($this->border + 2), ($this->paneHeight - $this->border - 2), $star_color);
				imageline($this->paneIm, ($this->paneWidth/2), ($this->border + 2), ($this->paneWidth - $this->border - 2), ($this->paneHeight - $this->border - 2), $star_color);
			}
			if ($this->stars[3]) {
				imageline($this->paneIm, ($this->border + 2), ($this->border + 2), ($this->paneWidth/2), ($this->paneHeight - $this->border - 2), $star_color);
				imageline($this->paneIm, ($this->paneWidth - $this->border - 2), ($this->border + 2), ($this->paneWidth/2), ($this->paneHeight - $this->border - 2), $star_color);
			}
		}
	}

	/* draw signs */
	private function drawSign() {
		
		if ($this->sign) {
			switch($this->sign) {
				case 'arrow':
					$sides = round($this->paneWidth - $this->border * 2)/2;
					imageline($this->paneIm, round($this->border + $sides/2), $this->border + round(($this->paneHeight - $this->border * 2)/2 - $this->paneHeight * 0.085), round($this->border + $sides/2), $this->border + round(($this->paneHeight - $this->border * 2)/2), $this->paneColors['d_gray']);
					imageline($this->paneIm, round($this->border + $sides/2), $this->border + round(($this->paneHeight - $this->border * 2)/2), round($this->border + $sides/2) + $sides, $this->border + round(($this->paneHeight - $this->border * 2)/2), $this->paneColors['d_gray']);
					$s = $this->border + round(($this->paneHeight - $this->border * 2)/2) - 5;
					$e = $this->border + round(($this->paneHeight - $this->border * 2)/2) + 5;
					$yl = $this->border + round(($this->paneHeight - $this->border * 2)/2);
					for ($i=$s;$i<=$e;$i++) {
						imageline($this->paneIm, round($this->border + $sides/2) + $sides, $i, round($this->border + $sides/2) + $sides + 5, $yl, $this->paneColors['d_gray']);
					}
				break;
				case 'plus':
					$sides = round($this->paneWidth - $this->border * 2)/2;
					imageline($this->paneIm, round($this->border + $sides/2), $this->border + round(($this->paneHeight - $this->border * 2)/2), round($this->border + $sides + $sides/2), $this->border + round(($this->paneHeight - $this->border * 2)/2), $this->paneColors['d_gray']);
					imageline($this->paneIm, round($this->border + $sides), $this->border + ($this->paneHeight - $this->border * 2 - $sides)/2, round($this->border + $sides), $this->border + ($this->paneHeight - $this->border * 2 - $sides)/2 + $sides, $this->paneColors['d_gray']);					
				break;
			}
		}
	}

	/* draw dashed line */
	private function drawDashed() {
		
		if ($this->dashedLine) {
			$style = array($this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], $this->paneColors['black'], IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT);
			imagesetstyle($this->paneIm, $style);
			imagesetthickness($this->paneIm, 2);
			imageline($this->paneIm, ($this->paneWidth - 4), 0, ($this->paneWidth - 4), $this->paneHeight, IMG_COLOR_STYLED);
		}
	}
	
	/* generate a sceleton of the pane */
	private function generatePane() {

		$this->paneIm = imagecreatetruecolor($this->paneWidth, $this->paneHeight);
		$this->paneColors['white'] = imagecolorallocate($this->paneIm, 255, 255, 255);
		$this->paneColors['black'] = imagecolorallocate($this->paneIm, 0, 0, 0);
		$this->paneColors['internal'] = imagecolorallocate($this->paneIm, 252, 252, 254);
		$this->paneColors['gray'] = imagecolorallocate($this->paneIm, 110, 116, 118);
		$this->paneColors['l_gray'] = imagecolorallocate($this->paneIm, 212, 220, 222);
		$this->paneColors['d_gray'] = imagecolorallocate($this->paneIm, 188, 189, 193);

		imagefilledrectangle($this->paneIm, 2, 2, ($this->paneWidth - 3), ($this->paneHeight - 3), ($this->borderColor == 'white')?$this->paneColors['white']:$this->paneColors['d_gray']);
	}
}
?>