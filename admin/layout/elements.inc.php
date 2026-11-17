<style type="text/css">.logo{width:250px; display:inline-table}</style><?

function show_menus($tabs,$tabs2='')
{
	global $site_http_url;
	$i=0;
	$t = count($tabs);
	while($i < $t)
	{
		$tem_submenus=0;
		if(isset($tabs2[$i]))
		{
			$tem_submenus = count($tabs2[$i]);
			if($tem_submenus)
			{
				echo '<li class="has-sub "><a href="javascript:;"><b class="caret pull-right"></b><i class="fa fa-file-text-o" aria-hidden="true"></i><span>'.$tabs[$i][0].'</span></a>';
				echo '<ul class="sub-menu ">';
				$i=0;
				while($j < $tem_submenus)
				{
					echo '<li class=" "><a href="'.$tabs2[$i][$j]['novo'].'"><span>'.$tabs2[$i][$j][0].'</span></a></li>';
					$j++;
				}
				echo '</ul></li>';
			}
		}
		else
		{
				echo '<li class=" "><a href="'.$tabs[$i][1].'"><i class="fa fa-laptop"></i><span>'.$tabs[$i][0].'</span></a></li>';
		}
		$i++;
	}
}

function show_menus_($tabs,$tabs2='')
{
	global $site_http_url;
	$i=0;
	$t = count($tabs);

	echo '<div id="navigation_horiz"><ul>';
	$jq = '';
	while($i < $t)
	{
		$tem_submenus=0;
		if(isset($tabs2[$i])){
			$tem_submenus = count($tabs2[$i]);

			if($tem_submenus)
			{
				$url = '#';
				$jq .= "
				$('#li".$i."').stop('').click(function()
				{
					$('.dropdown').hide('slow');
					$('#dropdown_".$i."').stop('').show('slow');

				});";

			}else $url = $tabs[$i][1];
		}else $url = $tabs[$i][1];

		echo '<li>';


		if($tabs[$i][3]!=''){
			echo	'<a href="'.$url.'" class="navlink" onclick="'.$tabs[$i][3].'" title="'.$tabs[$i][0].'"><img src="'.$site_http_url.'admin/layout/'.$tabs[$i][2].'" border="0" id="li'.$i.'" /></a>';

		}else{
			echo	'<a href="'.$url.'" class="navlink" title="'.$tabs[$i][0].'"><img src="'.$site_http_url.'admin/layout/'.$tabs[$i][2].'" border="0" id="li'.$i.'" /></a>';

		}
			if($tem_submenus)
			{
				echo '<div class="dropdown" id="dropdown_'.$i.'" style="display:none;border:#000 1px solid"><div style="width:320px">';
				echo '<b>'.$tabs[$i][0].'</b>';
				for($a=0; $a<$tem_submenus; $a++)
				{
					if($tabs2[$i][$a]['list']) echo '<div onmouseover="this.style.background=\'#a0d345\'" onmouseout="this.style.background=\'#7eac30\'" style="border-bottom:#a0d345 1px solid;min-height:30px"><div style="float:left;width:190px;border:#000 0px solid;min-height:30px;font-size:12px;line-height:14px"><div style="padding-top:0px;font-weight:bold;"><a href="'.$tabs2[$i][$a]['list'].'">'.$tabs2[$i][$a][0].'</a></div></div>';
					else echo '<div onmouseover="this.style.background=\'#a0d345\'" onmouseout="this.style.background=\'#7eac30\'" style="border-bottom:#a0d345 1px solid;min-height:30px"><div style="float:left;width:190px;border:#000 0px solid;min-height:30px;font-size:12px;line-height:14px;"><div style="padding-top:8px;font-weight:bold">&nbsp;&nbsp;&nbsp;'.$tabs2[$i][$a][0].'</div></div>';

					if($tabs2[$i][$a]['novo']!='empty' && $tabs2[$i][$a]['novo']!='') echo '<div style="float:right;min-height:30px;width:65px;"><a href="'.$tabs2[$i][$a]['novo'].'"><img src="'.$site_http_url.'admin/layout/add.png" align="amsmiddle" />&nbsp;Add</a></div>';
					elseif($tabs2[$i][$a]['novo']) echo '<div style="float:right;min-height:30px;width:65px"><a href="'.$tabs2[$i][$a]['novo'].'">&nbsp;</a></div>';

					if($tabs2[$i][$a]['list']) echo '<div style="float:right;min-height:30px;width:65px"><a href="'.$tabs2[$i][$a]['list'].'"><img src="'.$site_http_url.'admin/layout/list.png" align="amsmiddle" />&nbsp;List</a></div>';
					echo '</div>';
				}
				echo '</div></div>';
			}
			echo '</li>';

		$i++;
    }
	echo '</ul></div>';

	 echo "<script>
	 $(function()
	 {
		$('.dropdown').mouseleave(function() { $(this).delay('500').hide('slow');	 });
		".$jq."
	});
	 </script>
	 ";
	 //definir tooltip do jquery.UI e CSS
	 echo 	'<script>
				$(".navlink").tooltip({
					show: null,
					position: {
						my: "center top",
						at: "center bottom"
					},
					open: function( event, ui ) {
						ui.tooltip.animate({ top: ui.tooltip.position().top + 5 }, "slow" );
					}
				});
			</script>
			<style>
				.ui-tooltip {
					padding: 5px 10px;
					color: white;
					border-radius: 20px;
					font:  Sans-Serif;
					text-transform: uppercase;
					box-shadow: 0 0 7px black;
					background:black;
				}
			</style>';
}

function replace_tochar($str = '')
{
	$str = trim($str,"\t\n\r\0\x0B");

    $str = str_replace(chr(130),',',$str);    // baseline single quote
    $str = str_replace(chr(131),'NLG',$str);  // florin
    $str = str_replace(chr(132),'"',$str);    // baseline double quote
    $str = str_replace(chr(133),'...',$str);  // ellipsis
    $str = str_replace(chr(134),'**',$str);   // dagger (a second footnote)
    $str = str_replace(chr(135),'***',$str);  // double dagger (a third footnote)
    $str = str_replace(chr(136),'^',$str);    // circumflex accent
    $str = str_replace(chr(137),'o/oo',$str); // permile
    $str = str_replace(chr(138),'Sh',$str);   // S Hacek
    $str = str_replace(chr(139),'<',$str);    // left single guillemet
    $str = str_replace(chr(140),'OE',$str);   // OE ligature
    $str = str_replace(chr(145),"'",$str);    // left single quote
    $str = str_replace(chr(146),"'",$str);    // right single quote
    $str = str_replace(chr(147),'"',$str);    // left double quote
    $str = str_replace(chr(148),'"',$str);    // right double quote
    $str = str_replace(chr(149),'-',$str);    // bullet
    $str = str_replace(chr(150),'-',$str);    // endash
    $str = str_replace(chr(151),'--',$str);   // emdash
    $str = str_replace(chr(152),'~',$str);    // tilde accent
    $str = str_replace(chr(153),'(TM)',$str); // trademark ligature
    $str = str_replace(chr(154),'sh',$str);   // s Hacek
    $str = str_replace(chr(155),'>',$str);    // right single guillemet
    $str = str_replace(chr(156),'oe',$str);   // oe ligature
    $str = str_replace(chr(159),'Y',$str);    // Y Dieresis

	$valores = array("(",")","³","°","²","+","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","?");
    $str = str_replace($valores,'',$str);

	$valores = array(" ","'","/","&",":",";","`","´","~","^","¨","_","+",",",".","+","|","\\","<",">","–","—","–","­","–","—","€","±","×","¿");
	for($c=123; $c<191; $c++)  $valores[] = chr($c);
    $str = str_replace($valores,'-',$str);

	$str = str_replace('-----','-',$str);
	$str = str_replace('----','-',$str);
	$str = str_replace('---','-',$str);
	$str = str_replace('--','-',$str);

	$valores = array("á","à","ã","â","ä"); 	$str = str_replace($valores,'a',$str);
	$valores = array("é","è","ê","ë"); 		$str = str_replace($valores,'e',$str);
	$valores = array("í","ì","î","ï"); 		$str = str_replace($valores,'i',$str);
	$valores = array("ó","ò","õ","ô","ö"); 	$str = str_replace($valores,'o',$str);
	$valores = array("ú","ù","û","ü"); 		$str = str_replace($valores,'u',$str);
	$valores = array("ñ"); 		$str = str_replace($valores,'n',$str);
	$valores = array("ç"); 		$str = str_replace($valores,'c',$str);

	$valores = array("Á","À","Ã","Â","Ä"); 	$str = str_replace($valores,'A',$str);
	$valores = array("É","È","Ê","Ë"); 		$str = str_replace($valores,'E',$str);
	$valores = array("Í","Ì","Î","Ï"); 		$str = str_replace($valores,'I',$str);
	$valores = array("Ó","Ò","Õ","Ô","Ö"); 	$str = str_replace($valores,'O',$str);
	$valores = array("Ú","Ù","Û","Ü"); 		$str = str_replace($valores,'U',$str);
	$valores = array("Ñ"); 		$str = str_replace($valores,'N',$str);
	$valores = array("Ç"); 		$str = str_replace($valores,'C',$str);

	if(strlen($str) > 100) $str = substr($str,0,100);

	return $str;
}
?>