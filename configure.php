<!DOCTYPE html>
<html lang="en" dir="ltr" itemscope itemtype="http://schema.org/Webpage">
<head>
  <meta charset="utf-8">
  <title>Configuration interface for Light_GDClass PHP Class</title>
  <meta name="description" content="settings to create a picture of window in GD">
  <meta name="keywords" content="configuration,settings,window,frame,gd">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

  <meta itemprop="name" content="Window Configure">
  <meta itemprop="description" content="settings to create a picture of window in GD">  

  <link rel="stylesheet" media="all" href="./style.css">
  <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,400italic,600,800" rel="stylesheet">
</head>
<body>
	<div id="wrapper">
		<header id="mainheader">
			<h1 class="title">Configure and create a picture of Window</h1>
			<article>
				Interface to set basic options for generation of a picture of window. <br>
				All work will be done in Light_GDClass which relies on gorgeous and majestic PHP GD.<br />
				<span style="font-size:8px;">&copy; <a style="color:#000;" href="http://www.webtag.ru">webtag.ru</a></span>
			</article>
		</header>
		<section class="settings">
			<h2>How many panes will be there in your window?</h2>
			<form name="lights" method="post" action="./fullsize.php" target="_blank">
			<select name="q_panes" class="q_panes">
				<option>Choose</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
			</select>
			<section id="tabs_container">
				<div id="flip-tabs" >
					<ul id="flip-navigation" ><li class="selected"><a href="#" id="tab-0">Pane 1</a></li></ul>
					<div id="flip-container" >
						<div>
							
						</div>
					</div>
				</div>
				<input type="button" value="Preview" class="preview_button">
			</section>
			</form>
		</section>
		<section class="preview"></section>
	</div>
<?php

?>
<div id="tab_temp">
	<h3>Settings for Pane %num%</h3>
	<label class="l_sizes">Width*</label><input type="text" class="i_sizes" name="Panes[%num%][width]" value="" placeholder="eg 100">
	%H<label class="l_sizes">Height*</label><input type="text" class="i_sizes" name="Panes[%num%][height]" value="" placeholder="eg 100">H%
	<label class="l_empty">Empty?</label>
	<select name="Panes[%num%][empty]" class="s_empty">
		<option value="1">Yes</option>
		<option value="2">No</option>
	</select>
	<div class="emptyRightBorder">
	<label class="l_emptyBorder">Right Border</label>
		<select name="Panes[%num%][emptyRightBorder]" class="s_emptyBorder">
			<option value="1">No</option>
			<option value="2">Yes</option>
		</select>
	</div>
	<div class="emptyLeftBorder">
	<label class="l_emptyBorder">Left Border</label>
		<select name="Panes[%num%][emptyLeftBorder]" class="s_emptyBorder">
			<option value="1">No</option>
			<option value="2">Yes</option>
		</select>
	</div>
	<div class="notEmptyBlock">
		<label class="l_border">Thickness of Border</label><input type="text" class="i_border" name="Panes[%num%][border]" value="" placeholder="eg 10">
		<label class="l_bColor">Border Color</label>
		<select name="Panes[%num%][borderColor]" class="s_bColor">
			<option value="1">White</option>
			<option value="2">Dark grey</option>
		</select>
		<label class="l_border2line">Thin Inner Padding</label>
		<select name="Panes[%num%][border2line]" class="s_border2line">
			<option value="1">No</option>
			<option value="2">Yes</option>
		</select>
		<label class="l_door">Handler</label>
		<select name="Panes[%num%][doorknob]" class="s_door">
			<option value="1">No Doorknob</option>
			<option value="3">On the right</option>
			<option value="2">On the left</option>
			<option value="4">At the top</option>
			<option value="5">At the bottom</option>
		</select>
		<div class="doorknobType">
		<label class="l_doorknob"> &nbsp; &nbsp;- <i>Handler Type</i></label>
		<select name="Panes[%num%][typeDoorknob]" class="s_doorknob">
			<option value="1">1</option>
			<option value="2">2</option>
		</select>
		</div>
		<label class="l_separator">Frame Separator</label>
		<select name="Panes[%num%][separator]" class="s_separator">
			<option value="1">No</option>
			<option value="3">On the right</option>
			<option value="2">On the left</option>
		</select>
		<div class="separatorWidth">
			<label class="l_separator"> &nbsp; &nbsp;- <i>Separator Width</i></label><input type="text" class="i_separator" name="Panes[%num%][separatorWidth]" value="" placeholder="default 8">
		</div>
		<label class="l_dash">Dashed</label><input type="checkbox" class="i_dash" name="Panes[%num%][dashSign]" value="">
		<label class="l_star clear">Pane Stars</label>
		<span class="sp_star">&gt; Star</span> <input type="checkbox" class="c_star" name="Panes[%num%][gtStar]" value="">
		<span class="sp_star">&lt; Star</span> <input type="checkbox" class="c_star" name="Panes[%num%][ltStar]" value="">
		<span class="sp_star">&#94; Star</span> <input type="checkbox" class="c_star" name="Panes[%num%][upStar]" value="">
		<span class="sp_star">v Star</span> <input type="checkbox" class="c_star" name="Panes[%num%][dnStar]" value="">
		<hr width="89%" align="left">
		<label class="l_star minp">Pane Signs</label>
		<span class="sp_star">Arrow</span> <input type="checkbox" class="c_star" name="Panes[%num%][arrowSign]" value="">
		<span class="sp_star">Plus</span> <input type="checkbox" class="c_star" name="Panes[%num%][plusSign]" value="">
		<hr width="89%" align="left">
		<label class="l_typeStar">Type of stars</label>
		<select name="Panes[%num%][typeostar]" class="s_star">
			<option value="1">Solid</option>
			<option value="2">Dotted</option>
		</select>
		<label class="l_devide">Deviders</label>
		<select name="Panes[%num%][devide]" class="s_devide">
			<option value="1">No</option>
			<option value="2">Yes</option>
		</select>
		<div class="%num%_deviders">
			<label class="l_typeDevider">Type of Deviders</label>
			<select name="Panes[%num%][typeDevider]" class="s_typeDevider">
				<option value="1">1:0</option>
				<option value="2">2:0</option>
				<option value="3">3:0</option>
				<option value="4">1:1</option>
				<option value="5">2:1</option>
				<option value="6">3:1</option>
				<option value="7">2:2</option>
				<option value="8">0:1</option>
				<option value="9">0:2</option>
				<option value="10">1:2</option>
			</select>
			<div class="cell11">
				<label class="l_cell11">Cells at the top?</label>
				<select name="Panes[%num%][cell11]" class="s_cell11">
					<option value="1">No</option>
					<option value="2">Yes</option>
				</select>
			</div>
			<label class="l_thicknessDeviders">Thickness of deviders</label><input type="text" class="i_thicknessDeviders" name="Panes[%num%][thicknessDevider]" value="" placeholder="eg 20">
			<label class="l_distanceDevider">Distance between Deviders</label>	
			<select name="Panes[%num%][distanceDevider]" class="s_distanceDevider">
				<option value="1">Proportional</option>
				<option value="2">Custom</option>
			</select>	
			<label class="l_setDistanceCols">Distance Vertical (from left)</label><input type="text" class="i_setDistanceCols" name="Panes[%num%][setDistanceCol]" value="" placeholder="eg 200 or 200:300" disabled="disabled">
			<label class="l_setDistanceRows">Distance Horizontal (from top)</label><input type="text" class="i_setDistanceRows" name="Panes[%num%][setDistanceRow]" value="" placeholder="eg 100 or 100:200" disabled="disabled">
		</div>
	</div>
</div>
<div id="tabs_nav"><li><a href="#" id="tab-%numP%">Pane %num%</a></li></div>

<script type="text/javascript" src="./jquery-1.4.4.min.js"></script>
<script type="text/javascript">
	$(function(){
		var tab = $('#tab_temp').html();
		$('#flip-container').find('div').eq(0).html(tab.replace(/%num%/g,'1').replace(/%H/,'').replace(/H%/,''));
		$('div[class$="deviders"]').hide();
		$('select[name="q_panes"]').change(function(){
			var p = $(this).val();
			p = parseInt(p);
			if(!p || p == 'Choose'){
				$('#tabs_container').fadeOut('fast');
				return false;
			}
			
			if(p == 1){
				clearTabs();
				if($('#tabs_container').css('display') == 'none'){
					$('#tabs_container').fadeIn('fast',function(){
						defineSelect();
					});
				}
			} else if (p > 1 && p <= 6){
				clearTabs();
				var n = $('#tabs_nav').html();
				for(i=2;i<=p;i++){
					$('#flip-navigation').append(n.replace(/%numP%/,(i-1)).replace(/%num%/,i));
					$('#flip-container').append($('<div></div>').html(tab.replace(/%num%/g,i).replace(/%H.*H%/,'')).hide());
				}
				$('div[class$="deviders"]').hide();
				$('#tabs_container').fadeIn('fast',function(){
					clili();
					defineSelect();
					
				});
			}
		});
		$('.preview_button').click(function(event){
			event.preventDefault();
			$.ajax({
			   type: 'post',
			   //url: $(this).attr('action'),
			   url: './preview.php',
			   data: $('form[name="lights"]').serialize(),   // I WANT TO ADD EXTRA DATA + SERIALIZE DATA
			   success: function(data){
			      $('section.preview').html(data);
			      $('section.preview').css('background','none');
			   },
			   error: function(jqXHR, errStatus, errThrown){
			   	  console.log(errStatus);
			   	  alert('There is an error processing your request. Err: ' +errThrown);
			   }
			})
		});
		$('#fullsize').live('click',function(){
			if (!$('form[name="lights"]').attr('action') || $('form[name="lights"]').attr('action') != './fullsize.php') {
				$('form[name="lights"]').removeAttr('action').attr('action', './fullsize.php');
			}
			$('form[name="lights"]').submit();
		})
		$('#pdf').live('click',function(){
			$('form[name="lights"]').removeAttr('action').attr('action', './pdf.php');
			$('form[name="lights"]').submit();
		})
	});
	function clili(){
		$('#flip-navigation li a').each(function(){
			$(this).click(function(){
				$('#flip-navigation li').each(function(){
					$(this).removeClass('selected');
				});
				$(this).parent().addClass('selected');
				//extract index of tab from id of the navigation item
				var flipid=$(this).attr('id').substr(4);
				//Flip to that content tab
				$('#flip-container > div').each(function(ind){
					if(ind != flipid && $(this).css('display') != 'none'){
						$(this).hide();
					}else if(ind == flipid && $(this).css('display') == 'none'){
						$(this).fadeIn('fast');
					}
				})
				return false;
			});
		});
	}
	function clearTabs(){
		if($('#flip-navigation li').length >= 1){
			$('#flip-container > div').each(function(ind){
				if(ind == 0) return true;
				$(this).remove();
			})
			if($('#flip-container > div').css('display') == 'none'){
				$('#flip-container > div').show()
			}
			$('#flip-navigation > li').each(function(ind){
				if(ind==0) return true;
				$(this).remove();
			})
			if($('#flip-navigation li').attr('class') != 'selected'){
				$('#flip-navigation li').addClass('selected');
			}
		}
	}
	function defineSelect(){
		$('select.s_distanceDevider').change(function(){
			var index = $(this).parent().parent().parent().index();
			if($(this).val() == 2 && $('input.i_setDistanceCols').eq(index).attr('disabled')){
				$('input.i_setDistanceCols').eq(index).removeAttr('disabled');
				$('input.i_setDistanceRows').eq(index).removeAttr('disabled');
			}
			else if($(this).val() == 1 && !$('input.i_setDistanceCols').eq(index).attr('disabled')){
				$('input.i_setDistanceCols').eq(index).attr('disabled','disabled');
				$('input.i_setDistanceRows').eq(index).attr('disabled','disabled');
			}
		});
		$('select.s_door').change(function(){
			var index = $(this).parent().parent().index();
			if(($(this).val() >= 2 && $(this).val() <= 5) && $('div.doorknobType').eq(index).css('display') == 'none'){
				$('div.doorknobType').eq(index).fadeIn('fast');
			}
			else if($(this).val() == 1 && $('div.doorknobType').eq(index).css('display') != 'none'){
				$('div.doorknobType').eq(index).fadeOut('fast');
			}
		});
		$('select.s_separator').change(function(){
			var index = $(this).parent().parent().index();
			if(($(this).val() == 2 || $(this).val() == 3) && $('div.separatorWidth').eq(index).css('display') == 'none'){
				$('div.separatorWidth').eq(index).fadeIn('fast');
			}
			else if($(this).val() == 1 && $('div.separatorWidth').eq(index).css('display') != 'none'){
				$('div.separatorWidth').eq(index).fadeOut('fast');
			}
		});
		$('select.s_devide').change(function(){
			var index = $(this).parent().parent().index();
			if($(this).val() == 2 && $('div[class$="deviders"]').eq(index).css('display') == 'none'){
				$('div[class$="deviders"]').eq(index).fadeIn('fast');
			}
			if($(this).val() == 1 && $('div[class$="deviders"]').eq(index).css('display') != 'none'){
				$('div[class$="deviders"]').eq(index).fadeOut('fast');
			}
		});
		$('select.s_typeDevider').change(function(){
			var index = $(this).parent().parent().parent().index();
			if($(this).val() == 4 && $('div.cell11').eq(index).css('display') == 'none'){
				$('div.cell11').eq(index).fadeIn('fast');
			}
			if($(this).val() != 4 && $('div.cell11').eq(index).css('display') != 'none'){
				$('div.cell11').eq(index).fadeOut('fast');
			}
		});
		$('select.s_empty').change(function(){
			var index = $(this).parent().index();
			if($(this).val() == 2 && $('.notEmptyBlock').eq(index).css('display') == 'none'){
				$('.notEmptyBlock').eq(index).fadeIn('fast',function(){
					if($('.emptyRightBorder').eq(index).css('display') != 'none'){
						$('.emptyRightBorder').eq(index).find('select').val(1);
						$('.emptyRightBorder').eq(index).hide();
					}
					if($('.emptyLeftBorder').eq(index).css('display') != 'none'){
						$('.emptyLeftBorder').eq(index).find('select').val(1);
						$('.emptyLeftBorder').eq(index).hide();
					}
				});
			}
			if($(this).val() == 1 && $('.notEmptyBlock').eq(index).css('display') != 'none'){
				$('.notEmptyBlock').eq(index).fadeOut('fast',function(){
					if($('.emptyRightBorder').eq(index).css('display') == 'none'){
						$('.emptyRightBorder').eq(index).show();
					}				
					if($('.emptyLeftBorder').eq(index).css('display') == 'none'){
						$('.emptyLeftBorder').eq(index).show();
					}				
				});
			}			
		});
	}
</script>
</body>
</html>