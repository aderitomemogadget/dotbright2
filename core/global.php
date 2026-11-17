<?PHP
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function htmlentities_memo($str=''){$str=htmlspecialchars($str,ENT_COMPAT,'ISO-8859-1',true);return $str;}
function htmlentities_b($s){return $s;}

function color_inverse2($color)
{
    $color=str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb='';
    for ($x=0;$x<3;$x++){
        $c=255 - hexdec(substr($color,(2*$x),2));
        $c=($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
    return '#'.$rgb;
}

function color_inverse($bg)
{
    $r=hexdec(substr($bg,0,2));
    $g=hexdec(substr($bg,2,2));
    $b=hexdec(substr($bg,4,2));

    $contrast=sqrt(
        $r * $r * .241 +
        $g * $g * .691 +
        $b * $b * .068
    );

    if($contrast > 130){
       return '#000000';
    }else{
        return '#FFFFFF';
    }
}
function strip_html_tags($string)
{
	$regExp="<"."p"."[^>]*>";
	$string=str_replace("</p>",'',$string);
	$string=preg_replace ("( ".$regExp." )","<p>",$string);
	$regExp="<"."P"."[^>]*>";
	$string=str_replace("</P>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."div"."[^>]*>";
	$string=str_replace("</div>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."DIV"."[^>]*>";
	$string=str_replace("</DIV>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."span"."[^>]*>";
	$string=str_replace("</span>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."SPAN"."[^>]*>";
	$string=str_replace("</SPAN>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."font"."[^>]*>";
	$string=str_replace("</font>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."FONT"."[^>]*>";
	$string=str_replace("</FONT>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."table"."[^>]*>";
	$string=str_replace("</table>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TABLE"."[^>]*>";
	$string=str_replace("</TABLE>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."tbody"."[^>]*>";
	$string=str_replace("</tbody>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TBODY"."[^>]*>";
	$string=str_replace("</TBODY>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."COL"."[^>]*>";
	$string=str_replace("</COL>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."col"."[^>]*>";
	$string=str_replace("</col>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."colgroup"."[^>]*>";
	$string=str_replace("</colgroup>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."COLGROUP"."[^>]*>";
	$string=str_replace("</COLGROUP>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."td"."[^>]*>";
	$string=str_replace("</td>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TD"."[^>]*>";
	$string=str_replace("</TD>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."tr"."[^>]*>";
	$string=str_replace("</tr>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TR"."[^>]*>";
	$string=str_replace("</TR>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."STRONG"."[^>]*>";
	$string=str_replace("</STRONG>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."strong"."[^>]*>";
	$string=str_replace("</strong>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h1"."[^>]*>";
	$string=str_replace("</h1>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H1"."[^>]*>";
	$string=str_replace("</H1>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h2"."[^>]*>";
	$string=str_replace("</h2>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H2"."[^>]*>";
	$string=str_replace("</H2>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h3"."[^>]*>";
	$string=str_replace("</h3>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H3"."[^>]*>";
	$string=str_replace("</H3>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."li"."[^>]*>";
	$string=str_replace("</li>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."LI"."[^>]*>";
	$string=str_replace("</LI>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."ul"."[^>]*>";
	$string=str_replace("</ul>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."UL"."[^>]*>";
	$string=str_replace("</UL>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."ol"."[^>]*>";
	$string=str_replace("</ol>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."OL"."[^>]*>";
	$string=str_replace("</OL>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."img"."[^>]*>";
	$string=str_replace("</img>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."IMG"."[^>]*>";
	$string=str_replace("</IMG>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."obj"."[^>]*>";
	$string=str_replace("</obj>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."OBJ"."[^>]*>";
	$string=str_replace("</OBJ>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$string=str_replace("<o:p>",'',$string);
	$string=str_replace("</o:p>",'',$string);
	$string=str_replace('<?xml:namespace prefix=o ns="urn:schemas-microsoft-com:office:office" />','',$string);
	$string=str_replace('<P><SPAN>&nbsp;</SPAN></P>','',$string);
	$string=str_replace('<SPAN>&nbsp;</SPAN>','',$string);
	$string=str_replace('<SPAN></SPAN>','',$string);
	$string=str_replace('</P>','',$string);
	$string=str_replace('<P>'," ",$string);
	$string=str_replace('</p>','',$string);
	$string=str_replace('<p>'," ",$string);
	$string=str_replace('<FONT>','',$string);
	$string=str_replace('</FONT>','',$string);
	$string=str_replace('<STRONG>','',$string);
	$string=str_replace('</STRONG>','',$string);
	$string=str_replace('<strong>','',$string);
	$string=str_replace('</strong>','',$string);
	$string=str_replace('<B>','',$string);
	$string=str_replace('</B>','',$string);
	$string=str_replace('<b>','',$string);
	$string=str_replace('</b>','',$string);
	$string=str_replace('<div>','',$string);
	$string=str_replace('</div>','',$string);
	$string=str_replace('<DIV>','',$string);
	$string=str_replace('</DIV>','',$string);
	$string=str_replace("'",'',$string);
	$string=str_replace("<br>",'',$string);
	$string=str_replace("<BR>",'',$string);
	$string=str_replace("\n",'',$string);
	$string=str_replace("\r",'',$string);
	$string=str_replace("/"," ",$string);
	$string=str_replace("&nbsp;"," ",$string);
	$string=str_replace("<div>&nbsp;</div>"," ",$string);
	$string=removeUnsafeAttributesAndGivenTags($string);
	return $string;
}
function strip_html_tags_header($string)
{
	$regExp="<"."p"."[^>]*>";
	$string=str_replace("</p>",' ',$string);
	$string=preg_replace ("( ".$regExp." )","<p>",$string);
	$regExp="<"."P"."[^>]*>";
	$string=str_replace("</P>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."div"."[^>]*>";
	$string=str_replace("</div>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."DIV"."[^>]*>";
	$string=str_replace("</DIV>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."span"."[^>]*>";
	$string=str_replace("</span>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."SPAN"."[^>]*>";
	$string=str_replace("</SPAN>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."font"."[^>]*>";
	$string=str_replace("</font>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."FONT"."[^>]*>";
	$string=str_replace("</FONT>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."table"."[^>]*>";
	$string=str_replace("</table>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TABLE"."[^>]*>";
	$string=str_replace("</TABLE>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."tbody"."[^>]*>";
	$string=str_replace("</tbody>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TBODY"."[^>]*>";
	$string=str_replace("</TBODY>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."COL"."[^>]*>";
	$string=str_replace("</COL>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."col"."[^>]*>";
	$string=str_replace("</col>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."colgroup"."[^>]*>";
	$string=str_replace("</colgroup>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."COLGROUP"."[^>]*>";
	$string=str_replace("</COLGROUP>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."td"."[^>]*>";
	$string=str_replace("</td>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TD"."[^>]*>";
	$string=str_replace("</TD>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."tr"."[^>]*>";
	$string=str_replace("</tr>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TR"."[^>]*>";
	$string=str_replace("</TR>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."STRONG"."[^>]*>";
	$string=str_replace("</STRONG>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."strong"."[^>]*>";
	$string=str_replace("</strong>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h1"."[^>]*>";
	$string=str_replace("</h1>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H1"."[^>]*>";
	$string=str_replace("</H1>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h2"."[^>]*>";
	$string=str_replace("</h2>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H2"."[^>]*>";
	$string=str_replace("</H2>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h3"."[^>]*>";
	$string=str_replace("</h3>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H3"."[^>]*>";
	$string=str_replace("</H3>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."li"."[^>]*>";
	$string=str_replace("</li>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."LI"."[^>]*>";
	$string=str_replace("</LI>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."ul"."[^>]*>";
	$string=str_replace("</ul>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."UL"."[^>]*>";
	$string=str_replace("</UL>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."ol"."[^>]*>";
	$string=str_replace("</ol>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."OL"."[^>]*>";
	$string=str_replace("</OL>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."img"."[^>]*>";
	$string=str_replace("</img>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."IMG"."[^>]*>";
	$string=str_replace("</IMG>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."obj"."[^>]*>";
	$string=str_replace("</obj>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."OBJ"."[^>]*>";
	$string=str_replace("</OBJ>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$string=str_replace("<o:p>",'',$string);
	$string=str_replace("</o:p>",'',$string);
	$string=str_replace('<?xml:namespace prefix=o ns="urn:schemas-microsoft-com:office:office" />','',$string);
	$string=str_replace('<P><SPAN>&nbsp;</SPAN></P>','',$string);
	$string=str_replace('<SPAN>&nbsp;</SPAN>','',$string);
	$string=str_replace('<SPAN></SPAN>','',$string);
	$string=str_replace('</P>','',$string);
	$string=str_replace('<P>'," ",$string);
	$string=str_replace('</p>','',$string);
	$string=str_replace('<p>'," ",$string);
	$string=str_replace('<FONT>','',$string);
	$string=str_replace('</FONT>','',$string);
	$string=str_replace('<STRONG>','',$string);
	$string=str_replace('</STRONG>','',$string);
	$string=str_replace('<strong>','',$string);
	$string=str_replace('</strong>','',$string);
	$string=str_replace('<B>','',$string);
	$string=str_replace('</B>','',$string);
	$string=str_replace('<b>','',$string);
	$string=str_replace('</b>','',$string);
	$string=str_replace('<div>','',$string);
	$string=str_replace('</div>','',$string);
	$string=str_replace('<DIV>','',$string);
	$string=str_replace('</DIV>','',$string);
	$string=str_replace("'",'',$string);
	$string=str_replace("<br>",' ',$string);
	$string=str_replace("<br/>",' ',$string);
	$string=str_replace("<br />",' ',$string);
	$string=str_replace("<BR>",' ',$string);
	$string=str_replace("<BR/>",' ',$string);
	$string=str_replace("<BR />",' ',$string);
	$string=str_replace("\n",' ',$string);
	$string=str_replace("\r",' ',$string);
	$string=str_replace("/"," ",$string);
	$string=str_replace("&nbsp;"," ",$string);
	$string=str_replace("<div>&nbsp;</div>"," ",$string);
	$string=removeUnsafeAttributesAndGivenTags($string);
	return $string;
}

function strip_html_tags_fp($string)
{
	$regExp="<"."p"."[^>]*>";
	$string=str_replace("</p>",' ',$string);
	$string=preg_replace ("( ".$regExp." )","<p>",$string);
	$regExp="<"."P"."[^>]*>";
	$string=str_replace("</P>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."div"."[^>]*>";
	$string=str_replace("</div>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."DIV"."[^>]*>";
	$string=str_replace("</DIV>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."span"."[^>]*>";
	$string=str_replace("</span>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."SPAN"."[^>]*>";
	$string=str_replace("</SPAN>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."font"."[^>]*>";
	$string=str_replace("</font>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."FONT"."[^>]*>";
	$string=str_replace("</FONT>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."table"."[^>]*>";
	$string=str_replace("</table>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TABLE"."[^>]*>";
	$string=str_replace("</TABLE>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."tbody"."[^>]*>";
	$string=str_replace("</tbody>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TBODY"."[^>]*>";
	$string=str_replace("</TBODY>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."COL"."[^>]*>";
	$string=str_replace("</COL>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."col"."[^>]*>";
	$string=str_replace("</col>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."colgroup"."[^>]*>";
	$string=str_replace("</colgroup>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."COLGROUP"."[^>]*>";
	$string=str_replace("</COLGROUP>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."td"."[^>]*>";
	$string=str_replace("</td>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TD"."[^>]*>";
	$string=str_replace("</TD>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."tr"."[^>]*>";
	$string=str_replace("</tr>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."TR"."[^>]*>";
	$string=str_replace("</TR>",'',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."STRONG"."[^>]*>";
	$string=str_replace("</STRONG>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."strong"."[^>]*>";
	$string=str_replace("</strong>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h1"."[^>]*>";
	$string=str_replace("</h1>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H1"."[^>]*>";
	$string=str_replace("</H1>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h2"."[^>]*>";
	$string=str_replace("</h2>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H2"."[^>]*>";
	$string=str_replace("</H2>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."h3"."[^>]*>";
	$string=str_replace("</h3>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."H3"."[^>]*>";
	$string=str_replace("</H3>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."li"."[^>]*>";
	$string=str_replace("</li>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."LI"."[^>]*>";
	$string=str_replace("</LI>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."ul"."[^>]*>";
	$string=str_replace("</ul>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."UL"."[^>]*>";
	$string=str_replace("</UL>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."ol"."[^>]*>";
	$string=str_replace("</ol>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."OL"."[^>]*>";
	$string=str_replace("</OL>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."img"."[^>]*>";
	$string=str_replace("</img>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."IMG"."[^>]*>";
	$string=str_replace("</IMG>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."obj"."[^>]*>";
	$string=str_replace("</obj>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$regExp="<"."OBJ"."[^>]*>";
	$string=str_replace("</OBJ>",' ',$string);
	$string=preg_replace("(".$regExp." )",'',$string);
	$string=str_replace("<o:p>",'',$string);
	$string=str_replace("</o:p>",'',$string);
	$string=str_replace('<?xml:namespace prefix=o ns="urn:schemas-microsoft-com:office:office" />','',$string);
	$string=str_replace('<P><SPAN>&nbsp;</SPAN></P>','',$string);
	$string=str_replace('<SPAN>&nbsp;</SPAN>','',$string);
	$string=str_replace('<SPAN></SPAN>','',$string);
	$string=str_replace('</P>','',$string);
	$string=str_replace('<P>'," ",$string);
	$string=str_replace('</p>','',$string);
	$string=str_replace('<p>'," ",$string);
	$string=str_replace('<FONT>','',$string);
	$string=str_replace('</FONT>','',$string);
	// $string=str_replace('<STRONG>','',$string);
	// $string=str_replace('</STRONG>','',$string);
	// $string=str_replace('<strong>','',$string);
	// $string=str_replace('</strong>','',$string);
	$string=str_replace('<B>','',$string);
	$string=str_replace('</B>','',$string);
	$string=str_replace('<b>','',$string);
	$string=str_replace('</b>','',$string);
	$string=str_replace('<div>','',$string);
	$string=str_replace('</div>','',$string);
	$string=str_replace('<DIV>','',$string);
	$string=str_replace('</DIV>','',$string);
	$string=str_replace("'",'',$string);

	// $string=str_replace("<BR>",'',$string);
	// $string=str_replace("\n",'<br>',$string);
	// $string=str_replace("\r",'',$string);
	$string=str_replace("<br><br>",'<br>',$string);
	$string=str_replace("/"," ",$string);
	$string=str_replace("&nbsp;"," ",$string);
	$string=str_replace("<div>&nbsp;</div>"," ",$string);
	$string=removeUnsafeAttributesAndGivenTags($string,"<br>");
	return $string;
}

function strip_html_tagsc($string)
{
	$string=preg_replace("/\[(\w*)\]/", "", $string);
	$string=preg_replace('#\[.*\]#', null, $string);
	$string=strip_html_tags_header($string);
	// $string=html_entity_decode($string);
	// $string=($string);
	$string=preg_replace('#<[^>]+>#', ' ', $string);
	$string=trim($string);
	$string=str_replace('"',"“",$string);
	$string=str_replace("'","´",$string);
	$string=preg_replace('!\s+!', ' ', $string);

	return $string;
}

function wordWrapIgnoreHTML($string,$length=45,$wrapString="\n")
{
	 $wrapped='';
	 $word='';
	 $html=false;
	 $string=(string) $string;
	 for($i=0;$i<strlen($string);$i+=1)
	 {
	   $char=$string[$i];
	   /** HTML Begins */
	   if($char === '<')
	   {
		 if(!empty($word))
		 {
		   $wrapped .= $word;
		   $word='';
		 }
		 $html=true;
		 $wrapped .= $char;
	   }
	   /** HTML ends */
	   elseif($char === '>')
	   {
		 $html=false;
		 $wrapped .= $char;
	   }
	   /** If this is inside HTML -> append to the wrapped string */
	   elseif($html)
	   {
		 $wrapped .= $char;
	   }
	   /** Whitespace characted / new line */
	   elseif($char === ' ' || $char === "\t" || $char === "\n")
	   {
		 $wrapped .= $word.$char;
		 $word='';
	   }
	   /** Check chars */
	   else
	   {
		 $word .= $char;
		 if(strlen($word) > $length)
		 {
		   $wrapped .= $word.$wrapString;
		   $word='';
		 }
	   }
	 }
	if($word !== ''){
		$wrapped .= $word;
	}
	return $wrapped;
}
function removeUnsafeAttributesAndGivenTags($input,$validTags='')
{
	$regex='#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+?
\(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is';
	return preg_replace($regex,'<${1}>',strip_tags($input,$validTags));
}
function get_uri()
{
	$pageURL=(@$_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
	if($_SERVER['SERVER_PORT'] != '80') $pageURL.=$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
	else $pageURL.=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	return $pageURL;
}
function option_data($D,$p="")
{
	echo "<select name=\"dia".$p."\">";
	for($i=1;$i<32;$i++){echo "<option value=\"";if($i<10)echo "0";echo "$i\"";if($D["mday"]==$i)echo " selected";echo">";if($i<10) echo "0";echo "$i</option>\n";}
	echo "</select>\n";
	echo ".\n";
	echo "<select name=\"mes".$p."\">\n";
	for($i=1;$i<13;$i++){echo "<option value=\"";if($i<10) echo "0";echo "$i\"";if($D["mon"]==$i)echo " selected";echo">";if($i<10) echo "0";echo "$i</option>\n";}
	echo "</select>\n";
	echo ".\n";
	echo "<select name=\"ano".$p."\">";
	for($i=2000;$i<(date("Y")+5);$i++){echo "<option value=\"";if($i<10) echo "0";echo "$i\"";if($D["year"]==$i)echo " selected";echo ">$i</option>\n";}
	echo "</select>\n";
}
function browser_detect()
{
	if(isset($_SERVER['HTTP_USER_AGENT'])){
		if(strpos($_SERVER['HTTP_USER_AGENT'],'Gecko'))
		{
		   if(strpos($_SERVER['HTTP_USER_AGENT'],'Netscape'))$browser='Netscape';
		   else if(strpos($_SERVER['HTTP_USER_AGENT'],'Firefox')){$browser='Firefox';}else $browser='Mozilla';
		}
		else if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
		{
		   if(strpos($_SERVER['HTTP_USER_AGENT'],'Opera'))$browser='Opera';
		   else $browser='IE';
		}
		else $browser='Other';
	}else $browser='Other';
	return $browser;
}
function show_content($page_id=0,$draft_id=0)
{
	global $site_lang;
	if(!isset($_SESSION['ss_admin'])) $admin=0; else $admin=$_SESSION['ss_admin'];
	$version=1;//para poder ter varias versoes antes de as publicar
	global $DB_host,$DB_user,$DB_pass,$DB_name,$prev,$online_edit,$usememorytables;

	$ntable='pages';

	global $online_edit,$site_http_url;
	$admin=$online_edit;
	if($admin>0)
	{
		//echo '<script type="text/javascript" src="'.$site_http_url.'admin/editor/ckeditor_4_5_1/ckeditor.js"></script><div id="editor'.$page_id.'" contenteditable="true">';
	}

	$rs=new DataObj;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($draft_id) $rs->db_open($db_link,$DB_name,"Select * from drafts where ID='$draft_id' order by Ordem Asc");
	else $rs->db_open($db_link,$DB_name,"Select * from ".$ntable." where ID='$page_id' order by Ordem Asc");
	$mostra=$rs->db_data['sim'];
	$pag_visivel=$rs->db_data['Visible'];
	$cod=$rs->db_data['ID'];
	$tipo=$rs->db_data['Tipo'];
	$page_grupo_id=$rs->db_data['ID_Grupo'];
	$bootstrap=$rs->db_data['bootstrap'];
	$bootstrap_p=$rs->db_data['bootstrap_p'];
	$rs->db_close();
	if($tipo==2) // noticia
	{
		//Vamos ver se e para ficar visivel
		if($draft_id) $rs->db_open($db_link,$DB_name,"Select * from drafts where ID='$draft_id' AND Tipo=2 AND ((curdate() between Data1 and Data2) OR Permanente=1)");
		else $rs->db_open($db_link,$DB_name,"Select * from ".$ntable." where ID='$page_id' AND Tipo=2 AND ((curdate() between Data1 and Data2) OR Permanente=1)"); // AND Tipo=2 and ((curdate() between Data1 and Data2) OR Permanente=1) and Familia_noticias=0
		if(!$rs->EOF())
		{
			$permited_to=$rs->db_data['Ticker']; // 0 - Para todos // 1- Para Membros do Club
			$bootstrap=$rs->db_data['bootstrap'];
			$bootstrap_p=$rs->db_data['bootstrap_p'];
		}
		else $pag_visivel=0;
		$rs->db_close();
	}
	if($pag_visivel || $prev)
	{
		if($draft_id) $rs->db_open($db_link,$DB_name,"Select * from drafts_lang where ID_PAI='$draft_id' and Lang='$site_lang'");
		else $rs->db_open($db_link,$DB_name,"Select * from pages_lang where ID_PAI='$page_id' and Lang='$site_lang'");
		$nome_temp=$rs->db_data['Cabecalho'];

		$draft_id=0; // Temos de limpar a variavel para não dar problemas
		$Texto3=limpa_editor_lixo($rs->db_data['Texto3']);
		$Texto  =(limpa_editor_lixo($rs->db_data['Texto']));
		$Texto10=(limpa_editor_lixo($rs->db_data['Texto10']));
		$Texto11=(limpa_editor_lixo($rs->db_data['Texto11']));
		$Texto12=(limpa_editor_lixo($rs->db_data['Texto12']));
		$Texto13=(limpa_editor_lixo($rs->db_data['Texto13']));
		$Texto14=(limpa_editor_lixo($rs->db_data['Texto14']));
		$Texto15=(limpa_editor_lixo($rs->db_data['Texto15']));
		$Texto16=(limpa_editor_lixo($rs->db_data['Texto16']));
		$Texto17=(limpa_editor_lixo($rs->db_data['Texto17']));
		$Texto18=(limpa_editor_lixo($rs->db_data['Texto18']));
		$Texto19=(limpa_editor_lixo($rs->db_data['Texto19']));
		$Texto20=(limpa_editor_lixo($rs->db_data['Texto20']));
		$rs->db_close();
		if($tipo==1)
		{
			// pagina privada
			global $cli_id,$cli_token,$site_lang;
			if((!$cli_id || !$cli_token) && $permited_to==0) {}
			$sql="Select Grupo_ID from clientes where ID='$cli_id' and Ativo=1 and Token='$cli_token'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$grupo_id=$rs->db_data['Grupo_ID'];
				if($page_grupo_id != $grupo_id) return false;
			}
			$rs->db_close(1);
		}
		if($online_edit && !$prev)edit_content($page_id,$Texto,1);
		else
		{
			if(!$bootstrap || $bootstrap==1)put_content($Texto,$Texto3);

			if($bootstrap_p != '')
			{
				$colunas=explode(';',$bootstrap_p);
				echo '<div class="row">';
				$i=0;
				while($i<count($colunas))
				{
					echo '<div class="col-md-'.$colunas[$i].' col-sm-6 col-xs-12">';
					if($i==0)put_content($Texto);
					if($i==1)put_content($Texto10);
					if($i==2)put_content($Texto11);
					if($i==3)put_content($Texto12);
					if($i==4)put_content($Texto13);
					if($i==5)put_content($Texto14);
					if($i==6)put_content($Texto15);
					if($i==7)put_content($Texto16);
					if($i==8)put_content($Texto17);
					if($i==9)put_content($Texto18);
					if($i==10)put_content($Texto19);
					if($i==11)put_content($Texto20);
					echo '</div>';
					$i++;
				}
				echo '</div>';
			}
			else
			{
				if($bootstrap==2)
				{
					echo '<div class="row">';
					echo '<div class="col-md-3 col-sm-3 col-xs-12">';
					put_content($Texto);
					echo '</div><div class="col-md-8 col-sm-8 col-xs-12">';
					put_content($Texto10);
					echo '</div></div>';
				}
				if($bootstrap==3)
				{
					echo '<div class="row">';
					echo '<div class="col-md-6 col-sm-6 col-xs-12">';
					put_content($Texto);
					echo '</div><div class="col-md-6 col-sm-6 col-xs-12">';
					put_content($Texto10);
					echo '</div></div>';
				}
				if($bootstrap==4)
				{
					echo '<div class="row">';
					echo '<div class="col-md-4 col-sm-4 col-xs-12">';
					put_content($Texto);
					echo '</div><div class="col-md-4 col-sm-4 col-xs-12">';
					put_content($Texto10);
					echo '</div><div class="col-md-4 col-sm-4 col-xs-12">';
					put_content($Texto11);
					echo '</div></div>';
				}
				if($bootstrap==5)
				{
					echo '<div class="row">';
					echo '<div class="col-md-3 col-sm-6 col-xs-12">';
					put_content($Texto);
					echo '</div><div class="col-md-3 col-sm-6 col-xs-12">';
					put_content($Texto10);
					echo '</div><div class="col-md-3 col-sm-6 col-xs-12">';
					put_content($Texto11);
					echo '</div><div class="col-md-3 col-sm-6 col-xs-12">';
					put_content($Texto12);
					echo '</div></div>';
				}
				if($bootstrap==6)
				{
					echo '<div class="row">';
					echo '<div class="col-md-2 col-sm-2 col-xs-12">';
					put_content($Texto);
					echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
					put_content($Texto10);
					echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
					put_content($Texto11);
					echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
					put_content($Texto12);
					echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
					put_content($Texto13);
					echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
					put_content($Texto14);
					echo '</div></div>';
				}
				if($bootstrap==7)
				{
					echo '<div class="row">';
					echo '<div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto10);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto11);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto12);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto13);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto14);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto15);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto16);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto17);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto18);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto19);
					echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
					put_content($Texto20);
					echo '</div></div>';
				}
			}
		}
	}

	if($admin>0)
	{
		/*echo '</div><script>
			    CKEDITOR.inline( \'editor'.$page_id.'\' );
			</script>';*/
	}
}
function limpa_editor_lixo($str)
{
	global $site_http_url,$dev;

	$str=str_replace('/admin/pages/pages.php?page_id',$site_http_url.'pages.php?page_id',$str);
	$str=str_replace("/novo/novo","/novo",$str);
	$str=str_replace("pre.dotbright.pt","www.dotbright.pt",$str);
	$pos1=strpos($str,"<body>");
	$pos2=strpos($str,"</body>");
	if($pos1 && $pos2)$str=substr($str,$pos1+6,$pos2);
	else
	{
		$pos1=strpos($str,"<BODY>");
		$pos2=strpos($str,"</BODY>");
		if($pos1 && $pos2)$str=substr($str,$pos1+6,$pos2);
	}


	return $str;
}
function page_name()
{
	global $page_name;
	return $page_name;
}
function put_content($txt='',$script='',$admin=0)
{
	//echo $txt;
	echo trata_tags(clean_code($txt),$admin);
}
function clean_code($str)
{
	global $m_http_url_images,$dev;
	$m_array=array(".php?id=");
	$m_array=array("../../../files/","../../files/","../files/","http://192.168.1.201/dev/farmaciafs/files/","/dev/farmaciafs/files/");
	$str=str_replace($m_array,$m_http_url_images,$str);
	if(!$dev) $str=str_replace("/dev/farmaciafs",'',$str);
	$str=str_replace('<PARAM NAME="WMode" VALUE="Window">','<param name="wmode" value="transparent">',$str);
	$str=str_replace('Opaque','transparent',$str);
	$str=str_replace('<PARAM NAME="Play" VALUE="-1">','<PARAM NAME="Play" VALUE="1">',$str);
	$str=str_replace('<PARAM NAME="Play" VALUE="0">','<PARAM NAME="Play" VALUE="1">',$str);
	$str=str_replace('quality=high','quality=high wmode="transparent"',$str);
	$xscrol=scroll_ini(650,350);
	$str=str_replace('[scroll]',$xscrol,$str);
	$str=str_replace('[/scroll]',scroll_fim(),$str);
	$str=altera_codigo_flash($str);
	$str=comprime($str);
	return str_replace($m_array,$m_http_url_images,$str);
}
function altera_codigo_flash($str)
{
	if(strpos($str,"</OBJECT>"))
	{
		$pos1=strpos($str,"<OBJECT");
		$pos2=strpos($str,"</OBJECT>");
		$str1=substr($str,0,$pos1);
		$novo_codigo=substr($str,$pos1,($pos2-$pos1+9));
		$largura=substr($novo_codigo,strpos($novo_codigo,'width=')+6,strlen($novo_codigo));
		$largura=substr($largura,0,strpos($largura,' '));
		$altura=substr($novo_codigo,strpos($novo_codigo,'height=')+7,strlen($novo_codigo));
		$altura=substr($altura,0,strpos($altura,' '));
		$movie=substr($novo_codigo,strpos($novo_codigo,'<embed src=')+12,strlen($novo_codigo));
		$movie=substr($movie,0,strpos($movie,'.swf'));
		return $str1.show_flash($movie,$largura,$altura,"['quality']='high';['bgcolor']='#ffffff';['wmode']='transparent'",1).altera_codigo_flash(substr($str,strpos($str,"</OBJECT>")+9,strlen($str)));
	}
	return $str;
}
function comprime($str)
{
	$str=str_replace("\r"," ",$str);
	$str=str_replace("\n"," ",$str);
	$str=str_replace("	"," ",$str);
	$str=str_replace("Verdana,Arial,Helvetica,sans-serif","Verdana",$str);
	$str=str_replace('<SPAN style="FONT-SIZE: 10px; FONT-FAMILY: Verdana"></SPAN>','',$str);
	while(substr_count($str,"  ") > 1)	$str=str_replace("  "," ",$str);
	return $str;
}
function optimize_js($destino,$files)
{
	global $site_shell_url;
	$array_files=explode(';',$files);
	$fp=fopen($site_shell_url.$destino,'w');
	$total=count($array_files);
	for($i=0;$i<$total;$i++)
	{
		if(filesize($array_files[$i])>0)
		{
			$fp2=fopen($site_shell_url.$array_files[$i],'r');
			$conteudo=comprime_code(fread($fp2, filesize($array_files[$i])));
			fwrite($fp,$conteudo);
			fwrite($fp,"\n");
			fclose($fp2);
		}
	}
	fclose($fp);
	echo '<script language="javascript" type="text/javascript" src="'.$destino.'?dp='.dumpcache.'"></script>';
}
function optimize_css($destino,$files)
{
	global $site_shell_url;
	$array_files=explode(';',$files);
	$fp=fopen($site_shell_url.$destino,'w');
	$total=count($array_files);
	for($i=0;$i<$total;$i++)
	{
		if(filesize($array_files[$i])>0)
		{
			$fp2=fopen($site_shell_url.$array_files[$i],'r');
//			$conteudo=comprime_code(fread($fp2, filesize($array_files[$i])));
			$conteudo=(fread($fp2, filesize($array_files[$i])));
			fwrite($fp,$conteudo);
			fwrite($fp,"\n");
			fclose($fp2);
		}
	}
	fclose($fp);
	echo '<link rel="stylesheet" href="'.$destino.'?dp='.dumpcache.'" type="text/css" media="screen" />';
}
function comprime_code($s){$s=str_replace('	',' ',$s);while(substr_count($s,'  ')>1)$s=str_replace('  ',' ',$s);return $s;}
function compress_code($b)
{
	/* remove comments */
	$b=preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$b);
	/* remove tabs, spaces, newlines, etc. */
	$b=str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '),'',$b);
	/* remove other spaces before/after ; */
	$b=preg_replace(array('(( )+{)','({( )+)'),'{',$b);
	$b=preg_replace(array('(( )+})','(}( )+)','(;( )*})'),'}',$b);
	$b=preg_replace(array('(;( )+)','(( )+;)'),';',$b);
	$b=str_replace(' >','>',$b);
	$b=str_replace('"100%"','100%',$b);
	$b=str_replace('"0"','0',$b);
	return $b;
}
function tags_site($tag='')
{
	if($tag)
	{
		$arr_tags['EMPRESA']=$GLOBALS['TAG_EMPRESA'];
		$arr_tags['DESIGSOCIAL']=$GLOBALS['TAG_DESIGSOCIAL'];
		if(isset($GLOBALS['TAG_DOMINIO']))$arr_tags['DOMINIO']=$GLOBALS['TAG_DOMINIO'];
		$arr_tags['PAGINA_REGISTO']=seo('pages.php?page_id='.$GLOBALS['registo_page_id'].'?login=1');
		$arr_tags['COPYRIGHT']=$GLOBALS['TAG_COPYRIGHT'];

		return $arr_tags[$tag];
	}
	return;
}
function callfunction($functionName)
{
    echo call_user_func($functionName);
}
function trata_tags_($str,$admin=0)
{
	global $m_http_url_images;
	global $online_edit;
	$admin=$online_edit;
	$str=".".$str;
	$str2=strtolower($str);
	$pos[0]=strpos($str2,'[form(');
	$pos[1]=strpos($str2,'[media(');
	$pos[2]=strpos($str2,'[show_page(');
	$pos[3]=strpos($str2,'pages.php?page_id=');
	$pos[4]=strpos($str2,'[menu(');
	$pos[5]=strpos($str2,'[callfunction(');
	$pos[6]=strpos($str2,'html5.png');
	$pos[7]=strpos($str2,'[elemento(');
	$pos[8]=strpos($str2,'[shop]');
	$pos[9]=strpos($str2,'[product]');
	$pos[10]=strpos($str2,'[registo_cliente]');
	$pos[11]=strpos($str2,'[shopping_cart3]');
	$pos[12]=strpos($str2,'[pesquisa]');


	$i=0;
	while(isset($pos[$i]) && intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
	if(isset($pos[$i])) $lower_value=$pos[$i];
	for($k=$i;$k<sizeof($pos);$k++)
	{
		if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
	}
	$flag=array_sum($pos);
	while($flag)
	{
		switch ($lower_value)
		{
			case $pos[0]:
			
				if($pos[0]>1) echo substr($str,1,$pos[0]-1);
				$str=substr($str,$pos[0]+6);
				$numero=substr($str,0,strpos($str,")"));
				$str=substr($str,strpos($str,")")+2);
				if($admin)echo '<a class="edit" href="admin/forms/pagina.php?ID='.$numero.'" target="_blank">Edit form</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
				show_forms($numero);
				break;
			case $pos[1]:
			
				if($pos[1]>1) echo substr($str,1,$pos[1]-1);
				$str=substr($str,$pos[1]+7);
				$numero=substr($str,0,strpos($str,")"));
				$str=substr($str,strpos($str,")")+2);
				if($admin)echo '<a class="edit" href="admin/media_type/pagina.php?ID='.$numero.'" target="_blank">Edit media</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
				echo show_media($numero);
				break;
			case $pos[2]:
			
				if($pos[2]>1) echo substr($str,1,$pos[2]-1);
				$str=substr($str,$pos[2]+11);
				$numero=substr($str,0,strpos($str,")"));
				$str=substr($str,strpos($str,")")+2);
				show_content($numero);
				break;
			case $pos[3]:
			
				if($pos[3]>1) echo substr($str,1,$pos[3]-1);
				$str=substr($str,$pos[3]+18);
				$numero=substr($str,0,strpos($str,'"'));

				$str=substr($str,strpos($str,'"'));
				if(substr_count($numero, '#'))
				{
					$hastag=substr($numero, strpos($numero, "#"),strlen($numero));
					$numero=substr($numero, 0,strpos($numero, "#"));
				}
				else $hastag='';
				echo seo('pages.php?page_id='.$numero).$hastag;
				break;
			case $pos[4]:
			
				if($pos[4]>1) echo substr($str,1,$pos[4]-1);
				$str=substr($str,$pos[4]+6);
				$numero=substr($str,0,strpos($str,')'));
				$str=substr($str,strpos($str,')')+2);
				if($admin)echo '<a class="edit" href="admin/menus/pagina.php?ID='.$numero.'" target="_blank">Edit menu</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
				echo show_menu($numero);
				break;
			case $pos[5]:
			
	            if($pos[5]>1) echo substr($str,1,$pos[5]-1);
	            $str=substr($str,$pos[5]+14);
				$numero=substr($str,0,strpos($str,')'));
				$str=substr($str,strpos($str,')')+2);
				$pars=explode(',',$numero);
				if(count($pars) > 1) $use=1; else $use=0;
				$function=$pars[0];
				if($use==1 && $function != ''){ @array_shift($pars); echo call_user_func_array($function,$pars);}
				else echo call_user_func($function);
	        	break;
	        case $pos[6]:

				if($pos[6]>1)
				{
					$first_str=substr($str,1,$pos[6]-1);
					echo substr($first_str,0,strrpos($first_str,'<img '));
					$numero=substr($str,strpos($str,'title="')+7);
					$numero=substr($numero,0,strpos($numero,'"'));
					$str=substr($str,$pos[6]);//resto da string
					$str=substr($str,strpos($str,'>')+1);
				}
				if($admin)
				{
						echo '<div id="edit_mask" class="edit_mask">'.modulos($numero);
						echo'<a class="edit" href="admin/modulos/pagina.php?ID='.$numero.'" target="_blank">EDITAR ELEMENTO</a>';
						echo'<a class="edit" href="#" onclick="window.open(\'admin/modulos/pagina_on_fly.php?ID='.$numero.'\',\'_new\',\'height=600,width=1000,toolbar=0,location=0,menubar=0,scrollbars=1\')" >EDITAR RÁPIDO</a>';
						echo'<a class="edit" href="index.php?oe=-1" target="_blank">EXIT</a></div>';
				}
				else echo modulos($numero);
				break;
			case $pos[7]:

				if($pos[7]>1) echo substr($str,1,$pos[7]-1);
				$str=substr($str,$pos[7]+10);
				$numero=substr($str,0,strpos($str,')'));
				$str=substr($str,strpos($str,')')+2);

				if($admin)
				{
						echo '<div id="edit_mask" class="edit_mask">'.modulos($numero);
						echo'<a class="edit" href="admin/modulos/pagina.php?ID='.$numero.'" target="_blank">EDITAR ELEMENTO</a>';
						echo'<a class="edit" href="#" onclick="window.open(\'admin/modulos/pagina_on_fly.php?ID='.$numero.'\',\'_new\',\'height=600,width=1000,toolbar=0,location=0,menubar=0,scrollbars=1\')" >EDITAR RÁPIDO</a>';
						echo'<a class="edit" href="index.php?oe=-1" target="_blank">EXIT</a></div>';
				}
				else echo modulos($numero);
				break;
			case $pos[8]:
			
				if($pos[8]>1) echo substr($str,1,$pos[8]-1);
				$str=substr($str,$pos[8]+6);
				if(isset($_GET['tipo_catalogo'])) echo catalogo_produtos($_GET['tipo_catalogo']);
				else echo catalogo_produtos();
				break;
			case $pos[9]:

				if($pos[9]>1) echo substr($str,1,$pos[9]-1);
				$str=substr($str,$pos[9]+9);
				if($admin)echo'<a class="edit" href="admin/products/products.php?ID='.$_GET['prod_id'].'" target="_blank">Edit product</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
				echo produto();
		    	break;
		    case $pos[10]:
		    
				if($pos[10]>1) echo substr($str,1,$pos[10]-1);
				$str=substr($str,$pos[10]+17);
				show_registo_cliente();
				break;
			case $pos[11]:
		    
				if($pos[11]>1) echo substr($str,1,$pos[11]-1);
				$str=substr($str,$pos[11]+16);
				carrinho_purchase();
				break;
			case $pos[12]:
				if($pos[12]>1) echo substr($str,1,$pos[12]-1);
				$str=substr($str,$pos[12]+10);
				show_pesquisa_resultados();
				break;
		
		}
		$str=".".$str;
		$str2=strtolower($str);
		$pos[0]=strpos($str2,'[form(');
		$pos[1]=strpos($str2,'[media(');
		$pos[2]=strpos($str2,'[show_page(');
		$pos[3]=strpos($str2,'pages.php?page_id=');
		$pos[4]=strpos($str2,'[menu(');
		$pos[5]=strpos($str2,'[callfunction(');
		$pos[6]=strpos($str2,'html5.png');
		$pos[7]=strpos($str2,'[elemento(');
		$pos[8]=strpos($str2,'[shop]');
		$pos[9]=strpos($str2,'[product]');
		$pos[10]=strpos($str2,'[registo_cliente]');
		$pos[11]=strpos($str2,'[shopping_cart3]');
		$pos[12]=strpos($str2,'[pesquisa]');
		
		$i=0;
		while(isset($pos[$i]) && intval($pos[$i])==0 && $i<count($pos)) $i++;
		if(isset($pos[$i])) $lower_value=$pos[$i];
		for($k=$i;$k<count($pos);$k++)
		{
			if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
		}
		$flag=array_sum($pos);
	}
	return substr($str,1);
}
function trata_tags($str,$admin=0)
{
	global $m_http_url_images;
	global $online_edit;
	$admin=$online_edit;
	$str=".".$str;
	$str2=strtolower($str);
	$pos[0]=strpos($str2,'[form(');
	$pos[1]=strpos($str2,'[media(');
	$pos[2]=strpos($str2,'[show_page(');
	$pos[3]=strpos($str2,'pages.php?page_id=');
	$pos[4]=strpos($str2,'[menu(');
	$pos[5]=strpos($str2,'[linguas(');
	$pos[6]=strpos($str2,'[search]');
	$pos[7]=strpos($str2,'[sugira]');
	$pos[8]=strpos($str2,'[registo_news]');
	$pos[9]=strpos($str2,'[registo_cliente]');
	$pos[10]=strpos($str2,'[pesquisa]');
	$pos[11]=strpos($str2,'<a href="'.$m_http_url_images.'zoom/');
	$pos[12]=strpos($str2,'[lista_noticias(');
	$pos[13]=strpos($str2,'[menu2(');
	$pos[14]=strpos($str2,'[novidades]');
	$pos[15]=strpos($str2,'[form_orcamentos]');
	$pos[16]=strpos($str2,'[menu3(');
	$pos[17]=strpos($str2,'[menu_news(');
	$pos[18]=strpos($str2,'[last_news(');
	$pos[19]=strpos($str2,'[most_popular]');
	$pos[20]=strpos($str2,'[text_news]');
	$pos[21]=strpos($str2,'[list_events]');
	$pos[22]=strpos($str2,'[follow_us]');
	$pos[23]=strpos($str2,'[footer]');
	$pos[24]=strpos($str2,'[shop_menu]');
	$pos[25]=strpos($str2,'[shop]');
	$pos[26]=strpos($str2,'[shopping_cart]');
	$pos[27]=strpos($str2,'[linguas]');
	$pos[28]=strpos($str2,'[pesquisa_top]');
	$pos[29]=strpos($str2,'[product]');
	$pos[30]=strpos($str2,'[path]');
	$pos[31]=strpos($str2,'[shopping_cart2]');
	$pos[32]=strpos($str2,'[shopping_cart3]');
	$pos[33]=strpos($str2,'[blog_home]');
	$pos[34]=strpos($str2,'[blog_all]');
	$pos[35]=strpos($str2,'[shopping_cart4]');
	$pos[36]=strpos($str2,'[all_news]');
	$pos[37]=strpos($str2,'[all_vids]');
	$pos[38]=strpos($str2,'[opinion]');
	$pos[39]=strpos($str2,'[shopfams]');
	$pos[40]=strpos($str2,'[gallery]');
	$pos[41]=strpos($str2,'[club]');
	$pos[42]=strpos($str2,'[menu_club(');
	$pos[43]=strpos($str2,'[clubfooter]');
	$pos[44]=strpos($str2,'[club_home]');
	$pos[45]=strpos($str2,'[opinion2]');
	$pos[46]=strpos($str2,'[documentaries]');
	$pos[47]=strpos($str2,'[pictures]');
	$pos[48]=strpos($str2,'[prod_novidades]');
	$pos[49]=strpos($str2,'[prod_oferta]');
	$pos[50]=strpos($str2,'[cart]');
	$pos[51]=strpos($str2,'[cloud]');
	$pos[52]=strpos($str2,'[footer2]');
	$pos[53]=strpos($str2,'[seo(');
	$pos[54]=strpos($str2,'[destaques]');
	$pos[55]=strpos($str2,'[page_title]');
	$pos[56]=strpos($str2,'[page_title_shop]');
	$pos[57]=strpos($str2,'[recomendar_produto]');
	$pos[58]=strpos($str2,'[menu_categorias]');
	$pos[59]=strpos($str2,'[marcas]');
	$pos[60]=strpos($str2,'[rand_novidade]');
	$pos[61]=strpos($str2,'[menu_simples(');
	$pos[62]=strpos($str2,'[breadcrums]');
	$pos[63]=strpos($str2,'[welcome]');
	$pos[64]=strpos($str2,'[carrinho_visualiza]');
	$pos[65]=strpos($str2,'[logo]');
	$pos[66]=strpos($str2,'[pedidocotacao]');
	$pos[67]=strpos($str2,'[pedidorecolha]');
	$pos[68]=strpos($str2,'[contactoscotacao]');
	$pos[69]=strpos($str2,'[filtros]');
	$pos[70]=strpos($str2,'[loginpopup]');
	$pos[71]=strpos($str2,'[show_last_news(');
	$pos[72]=strpos($str2,'[show_destaques]');
	$pos[73]=strpos($str2,'[chat]');
	$pos[74]=strpos($str2,'[show_promocoes]');
	$pos[75]=strpos($str2,'[show_marcas]');
	$pos[76]=strpos($str2,'[shopping_cartchange]');
	$pos[77]=strpos($str2,'[currency]');
	$pos[78]=strpos($str2,'[produtospromo]');
	$pos[79]=strpos($str2,'[sliderproducts]');
	$pos[80]=strpos($str2,'[catfam]');
	$pos[81]=strpos($str2,'[caixageral]');
	$pos[82]=strpos($str2,'[callfunction(');
	$pos[83]=strpos($str2,'[projeto]');
	$pos[84]=strpos($str2,'html5.png');
	$pos[85]=strpos($str2,'[elemento(');

	$i=0;
	while(isset($pos[$i]) && intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
	if(isset($pos[$i])) $lower_value=$pos[$i];
	for($k=$i;$k<sizeof($pos);$k++)
	{
		if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
	}
	$flag=array_sum($pos);
	while($flag){
		if($pos[0]==$lower_value)
		{
			if($pos[0]>1) echo substr($str,1,$pos[0]-1);
			$str=substr($str,$pos[0]+6);
			$numero=substr($str,0,strpos($str,")"));
			$str=substr($str,strpos($str,")")+2);
			if($admin)echo '<a class="edit" href="admin/forms/pagina.php?ID='.$numero.'" target="_blank">Edit form</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
			show_forms($numero);
		}
		elseif($pos[1]==$lower_value)
		{
			if($pos[1]>1) echo substr($str,1,$pos[1]-1);
			$str=substr($str,$pos[1]+7);
			$numero=substr($str,0,strpos($str,")"));
			$str=substr($str,strpos($str,")")+2);
			if($admin)echo '<a class="edit" href="admin/media_type/pagina.php?ID='.$numero.'" target="_blank">Edit media</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
			echo show_media($numero);
		}
		elseif($pos[2]==$lower_value)
		{
			if($pos[2]>1) echo substr($str,1,$pos[2]-1);
			$str=substr($str,$pos[2]+11);
			$numero=substr($str,0,strpos($str,")"));
			$str=substr($str,strpos($str,")")+2);
			show_content($numero);
		} elseif($pos[3]==$lower_value){
			if($pos[3]>1) echo substr($str,1,$pos[3]-1);
			$str=substr($str,$pos[3]+18);
			$numero=substr($str,0,strpos($str,'"'));

			$str=substr($str,strpos($str,'"'));
			if(substr_count($numero, '#')){
				$hastag=substr($numero, strpos($numero, "#"),strlen($numero));
				$numero=substr($numero, 0,strpos($numero, "#"));
			}else{
				$hastag='';
			}
			echo seo('pages.php?page_id='.$numero).$hastag;
		}
		elseif($pos[4]==$lower_value){
			if($pos[4]>1) echo substr($str,1,$pos[4]-1);
			$str=substr($str,$pos[4]+6);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			if($admin)echo '<a class="edit" href="admin/menus/pagina.php?ID='.$numero.'" target="_blank">Edit menu</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
			echo show_menu($numero);
		}
		elseif($pos[5]==$lower_value){
			if($pos[5]>1) echo substr($str,1,$pos[5]-1);
			$str=substr($str,$pos[5]+9);
			$str=substr($str,strpos($str,')')+2);
			show_linguas();
		}
		elseif($pos[6]==$lower_value){
			if($pos[6]>1) echo substr($str,1,$pos[6]-1);
			$str=substr($str,$pos[6]+8);
			show_search();
		}
		elseif($pos[7]==$lower_value){
			if($pos[7]>1) echo substr($str,1,$pos[7]-1);
			$str=substr($str,$pos[7]+8);
			show_sugira();
		}
		elseif($pos[8]==$lower_value){
			if($pos[8]>1) echo substr($str,1,$pos[8]-1);
			$str=substr($str,$pos[8]+14);
			show_registo_news();
		}
		elseif($pos[9]==$lower_value){
			if($pos[9]>1) echo substr($str,1,$pos[9]-1);
			$str=substr($str,$pos[9]+17);
			show_registo_cliente();
		}
		elseif($pos[10]==$lower_value){
			if($pos[10]>1) echo substr($str,1,$pos[10]-1);
			$str=substr($str,$pos[10]+10);
			show_pesquisa_resultados();
		}
		elseif($pos[11]==$lower_value) {
			if($pos[11]>1) echo substr($str,1,$pos[11]-1);
			$str=substr($str,$pos[11]+strlen('<a href="'.$m_http_url_images.'zoom/'));
			$img=substr($str,0,strpos($str,"\""));
			$str=substr($str,strpos($str,"\"")+1);
			echo '<a href="'.$m_http_url_images.'zoom/'.$img.'" rel="lightbox[foto]"';
		}
		elseif($pos[12]==$lower_value){
			if($pos[12]>1) echo substr($str,1,$pos[12]-1);
			$str=substr($str,$pos[12]+16);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			echo show_fam_lista_noticias($numero);
		}
		elseif($pos[13]==$lower_value){
			if($pos[13]>1) echo substr($str,1,$pos[13]-1);
			$str=substr($str,$pos[13]+7);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			if($admin)echo'<a class="edit" href="admin/menus/pagina.php?ID='.$numero.'" target="_blank">Edit menu</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
			echo show_menu2($numero);
		}
		elseif($pos[14]==$lower_value){
			if($pos[14]>1) echo substr($str,1,$pos[14]-1);
			$str=substr($str,$pos[14]+11);
			echo show_novidades();
		}
		elseif($pos[15]==$lower_value){
			if($pos[15]>1) echo substr($str,1,$pos[15]-1);
			$str=substr($str,$pos[15]+17);
			echo formulario_orcamentos();
		}elseif($pos[16]==$lower_value){
			if($pos[16]>1) echo substr($str,1,$pos[16]-1);
			$str=substr($str,$pos[16]+7);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			if($admin)echo'<a class="edit" href="admin/menus/pagina.php?ID='.$numero.'" target="_blank">Edit menu</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
			echo show_menu3($numero);
		}
		elseif($pos[17]==$lower_value)
		{
			if($pos[17]>1) echo substr($str,1,$pos[17]-1);
			$str=substr($str,$pos[17]+11);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			echo show_menu_noticias($numero);
		}
		elseif($pos[18]==$lower_value)
		{
			if($pos[18]>1) echo substr($str,1,$pos[18]-1);
			$str=substr($str,$pos[18]+11);
			$numero=substr($str,0,strpos($str,')'));
			$numero2=explode(',',$numero);
			$str=substr($str,strpos($str,')')+2);
			// echo show_lista_noticias_last($numero);
			echo show_lista_noticias($numero2[0], $numero2[1]);
		}/*
		elseif($pos[19]==$lower_value)
		{
			if($pos[19]>1) echo substr($str,1,$pos[19]-1);
			$str=substr($str,$pos[19]+14);
			echo pnews_popular();
		}
		elseif($pos[20]==$lower_value)
		{
			if($pos[20]>1) echo substr($str,1,$pos[20]-1);
			$str=substr($str,$pos[20]+11);
			echo show_lista_noticias_last_text();
		}
		elseif($pos[21]==$lower_value)
		{
			if($pos[21]>1) echo substr($str,1,$pos[21]-1);
			$str=substr($str,$pos[21]+13);
			echo show_calendar();
		}*/
		elseif($pos[22]==$lower_value)
		{
			if($pos[22]>1) echo substr($str,1,$pos[22]-1);
			$str=substr($str,$pos[22]+11);
			echo show_follow_us();
		}
		elseif($pos[23]==$lower_value)
		{
			if($pos[23]>1) echo substr($str,1,$pos[23]-1);
			$str=substr($str,$pos[23]+8);
			echo show_footer();
		}
		elseif($pos[24]==$lower_value)
		{
			if($pos[24]>1) echo substr($str,1,$pos[24]-1);
			$str=substr($str,$pos[24]+11);
			echo show_menu_shop(108);
		}
		elseif($pos[25]==$lower_value)
		{
			if($pos[25]>1) echo substr($str,1,$pos[25]-1);
			$str=substr($str,$pos[25]+6);
			if(isset($_GET['tipo_catalogo'])) echo catalogo_produtos($_GET['tipo_catalogo']);
			else echo catalogo_produtos();
		}
		elseif($pos[26]==$lower_value)
		{
			if($pos[26]>1) echo substr($str,1,$pos[26]-1);
			$str=substr($str,$pos[26]+15);
			echo carrinho_small();
		}
		elseif($pos[27]==$lower_value)
		{
			if($pos[27]>1) echo substr($str,1,$pos[27]-1);
			$str=substr($str,$pos[27]+9);
			echo linguas_show();
		}
		elseif($pos[28]==$lower_value)
		{
			if($pos[28]>1) echo substr($str,1,$pos[28]-1);
			$str=substr($str,$pos[28]+14);
			echo get_pesquisa();
		}
		elseif($pos[29]==$lower_value)
		{
			if($pos[29]>1) echo substr($str,1,$pos[29]-1);
			$str=substr($str,$pos[29]+9);
			if($admin)echo'<a class="edit" href="admin/products/products.php?ID='.$_GET['prod_id'].'" target="_blank">Edit product</a> <a class="edit" href="index.php?oe=-1" target="_blank">Exit</a>';
			echo produto();
	   }
		elseif($pos[30]==$lower_value)
		{
			if($pos[30]>1) echo substr($str,1,$pos[30]-1);
			$str=substr($str,$pos[30]+6);
			echo $GLOBALS['site_http_url'];
		}
		elseif($pos[31]==$lower_value)
		{
			if($pos[31]>1) echo substr($str,1,$pos[31]-1);
			$str=substr($str,$pos[31]+16);
			echo carrinho_small_modify();
		}
		elseif($pos[32]==$lower_value)
		{
			if($pos[32]>1) echo substr($str,1,$pos[32]-1);
			$str=substr($str,$pos[32]+16);
			echo carrinho_purchase();
		}/*
		elseif($pos[33]==$lower_value)
		{
			if($pos[33]>1) echo substr($str,1,$pos[33]-1);
			$str=substr($str,$pos[33]+11);
			echo show_lista_blog();
		}
		elseif($pos[34]==$lower_value)
		{
			if($pos[34]>1) echo substr($str,1,$pos[34]-1);
			$str=substr($str,$pos[34]+10);
			echo show_lista_blog_all();
		}*/
		elseif($pos[35]==$lower_value)
		{
			if($pos[35]>1) echo substr($str,1,$pos[35]-1);
			$str=substr($str,$pos[35]+16);
			echo carrinho_small2();
		}
		elseif($pos[36]==$lower_value)
		{
			if($pos[36]>1) echo substr($str,1,$pos[36]-1);
			$str=substr($str,$pos[36]+10);
			echo show_all_noticias();
		}
		elseif($pos[37]==$lower_value)
		{
			if($pos[37]>1) echo substr($str,1,$pos[37]-1);
			$str=substr($str,$pos[37]+10);
			echo show_all_noticias('videos');
		}
		elseif($pos[38]==$lower_value)
		{
			if($pos[38]>1) echo substr($str,1,$pos[38]-1);
			$str=substr($str,$pos[38]+9);
			echo opinion();
		}
		elseif($pos[39]==$lower_value)
		{
			if($pos[39]>1) echo substr($str,1,$pos[39]-1);
			$str=substr($str,$pos[39]+10);
			echo shopfams();
		}
		elseif($pos[40]==$lower_value)
		{
			if($pos[40]>1) echo substr($str,1,$pos[40]-1);
			$str=substr($str,$pos[40]+9);
			echo gallery();
		}
		elseif($pos[45]==$lower_value)
		{
			if($pos[45]>1) echo substr($str,1,$pos[45]-1);
			$str=substr($str,$pos[45]+10);
			echo show_opinion();
		}
		elseif($pos[47]==$lower_value)
		{
			if($pos[47]>1) echo substr($str,1,$pos[47]-1);
			$str=substr($str,$pos[47]+10);
			echo pictures();
		}
		elseif($pos[48]==$lower_value)
		{
			if($pos[48]>1) echo substr($str,1,$pos[48]-1);
			$str=substr($str,$pos[48]+16);
			echo produto_novidade();
		}
		elseif($pos[49]==$lower_value)
		{
			if($pos[49]>1) echo substr($str,1,$pos[49]-1);
			$str=substr($str,$pos[49]+13);
			echo produto_oferta();
		}
		elseif($pos[50]==$lower_value)
		{
			if($pos[50]>1) echo substr($str,1,$pos[50]-1);
			$str=substr($str,$pos[50]+6);
			if($GLOBALS['n_enc'] > 0) echo '<div class="pesq_cart" onclick="window.open(\''.$GLOBALS['site_http_url'].'pg161-shop-cart-modify-en\',\'_self\')"></div>';
		}
		elseif($pos[51]==$lower_value)
		{
			if($pos[51]>1) echo substr($str,1,$pos[51]-1);
			$str=substr($str,$pos[51]+7);
			echo getCloud();
		}
		elseif($pos[52]==$lower_value)
		{
			if($pos[52]>1) echo substr($str,1,$pos[52]-1);
			$str=substr($str,$pos[52]+9);
			echo show_footer2();
		}
		elseif($pos[53]==$lower_value)
		{
			if($pos[53]>1) echo substr($str,1,$pos[53]-1);
			$str=substr($str,$pos[53]+5);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			echo seo($numero);
		}
		elseif($pos[54]==$lower_value)
		{
			if($pos[54]>1) echo substr($str,1,$pos[54]-1);
			$str=substr($str,$pos[54]+11);
			echo catalogo_produtos('destaque');
		}
		elseif($pos[55]==$lower_value)
		{
			if($pos[55]>1) echo substr($str,1,$pos[55]-1);
			$str=substr($str,$pos[55]+12);
			echo get_noticias_title($_GET['page_id']);
		}
		elseif($pos[56]==$lower_value)
		{
			if($pos[56]>1) echo substr($str,1,$pos[56]-1);
			$str=substr($str,$pos[56]+17);
			if(isset($_GET['cat']) && $_GET['cat'] > 0)
			{
				$fam_info=get_family_info($_GET['cat']);
				echo $fam_info['fam_nome'];
			}
			elseif(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
			{
				$prods=get_produtc_info($_GET['prod_id']);
				echo $prods['prod_nome'];
			}
		}
		elseif($pos[57]==$lower_value)
		{
			if($pos[57]>1) echo substr($str,1,$pos[57]-1);
			$str=substr($str,$pos[57]+20);
			echo produto_recomendacao();
		}
		elseif($pos[58]==$lower_value)
		{
			if($pos[58]>1) echo substr($str,1,$pos[58]-1);
			$str=substr($str,$pos[58]+17);
			echo menu_categorias();
		}
		elseif($pos[59]==$lower_value)
		{
			if($pos[59]>1) echo substr($str,1,$pos[59]-1);
			$str=substr($str,$pos[59]+8);
			echo show_marcas();
		}
		elseif($pos[60]==$lower_value)
		{
			if($pos[60]>1) echo substr($str,1,$pos[60]-1);
			$str=substr($str,$pos[60]+15);
			echo show_random_novidade();
		}
		elseif($pos[61]==$lower_value){
			if($pos[61]>1) echo substr($str,1,$pos[61]-1);
			$str=substr($str,$pos[61]+14);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			echo show_menu_simples($numero);
		}
		elseif($pos[62]==$lower_value){
			/*[breadcrums]*/
			if($pos[62]>1) echo substr($str,1,$pos[62]-1);
			$str=substr($str,$pos[62]+12);
			echo show_breadcrums();
		}
		elseif($pos[63]==$lower_value)
		{
			/*[welcome]*/
			if($pos[63]>1) echo substr($str,1,$pos[63]-1);
			$str=substr($str,$pos[63]+9);
			echo show_menu_login();
		}
		elseif($pos[64]==$lower_value)
		{
			/*[welcome]*/
			if($pos[64]>1) echo substr($str,1,$pos[64]-1);
			$str=substr($str,$pos[64]+20);
			echo carrinho_small_visualiza();
		}
		elseif($pos[65]==$lower_value)
		{
			/*[logo]*/
			if($pos[65]>1) echo substr($str,1,$pos[65]-1);
			$str=substr($str,$pos[65]+6);
			echo showLogo();
		}
		elseif($pos[66]==$lower_value)
		{
			/*[pedidocotacao]*/
			if($pos[66]>1) echo substr($str,1,$pos[66]-1);
			$str=substr($str,$pos[66]+15);
			echo form_pedido_cotacao();
		}
		elseif($pos[67]==$lower_value)
		{
			/*[pedidorecolha]*/
			if($pos[67]>1) echo substr($str,1,$pos[67]-1);
			$str=substr($str,$pos[67]+15);
			echo form_pedido_recolha();
		}elseif($pos[68]==$lower_value)
		{
			/*[pedidocotacaocontactos]*/
			if($pos[68]>1) echo substr($str,1,$pos[68]-1);
			$str=substr($str,$pos[68]+24);
			echo form_pedido_cotacao_contactos();
		}
		elseif($pos[69]==$lower_value)
		{
			/*[pedidocotacaocontactos]*/
			if($pos[69]>1) echo substr($str,1,$pos[69]-1);
			$str=substr($str,$pos[69]+9);
			echo filtros_left();
		}
		elseif($pos[70]==$lower_value)
		{
			/*[loginpopup]*/
			if($pos[70]>1) echo substr($str,1,$pos[70]-1);
			$str=substr($str,$pos[70]+12);
			echo show_menu_login2();
		}
		elseif($pos[71]==$lower_value)
		{
			/*[show_last_news()]*/
			if($pos[71]>1) echo substr($str,1,$pos[71]-1);
			$str=substr($str,$pos[71]+16);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			echo show_last_news($numero);
		}
		 elseif($pos[72]==$lower_value)
		{
			/*[show_destaques]*/
			if($pos[72]>1) echo substr($str,1,$pos[72]-1);
			$str=substr($str,$pos[72]+16);
			echo getProdutosDestaques();
		}
		elseif($pos[73]==$lower_value)
		{
			/*[chat]*/
			if($pos[73]>1) echo substr($str,1,$pos[73]-1);
			$str=substr($str,$pos[73]+6);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			echo show_chat();
		}
		elseif($pos[74]==$lower_value)
		{
			/*[show_promocoes]*/
			if($pos[74]>1) echo substr($str,1,$pos[74]-1);
			$str=substr($str,$pos[74]+16);
			echo getProdutosPromocoes();
		}
		elseif($pos[75]==$lower_value)
		{
			/*[show_marcas]*/
			if($pos[75]>1) echo substr($str,1,$pos[75]-1);
			$str=substr($str,$pos[75]+13);
			echo getListaMarcas();
		}
		elseif($pos[76]==$lower_value)
		{
			if($pos[76]>1) echo substr($str,1,$pos[76]-1);
			$str=substr($str,$pos[76]+21);
			//echo carrinho_change();
		}
		elseif($pos[77]==$lower_value)
		{
			if($pos[77]>1) echo substr($str,1,$pos[77]-1);
			$str=substr($str,$pos[77]+10);
			echo showCurrency();
		}
		elseif($pos[78]==$lower_value)
		{
			if($pos[78]>1) echo substr($str,1,$pos[78]-1);
			$str=substr($str,$pos[78]+15);
			echo GERA_PRODUTO_PROMOCAO();
		}
		elseif($pos[79]==$lower_value)
		{
			if($pos[79]>1) echo substr($str,1,$pos[79]-1);
			$str=substr($str,$pos[79]+16);
			echo '<iframe src="'.$GLOBALS['site_http_url'].'core/media/slideproducts/index.php" width="755" height="180" style="overflow:hidden;" frameborder=0 SCROLLING="no"></iframe>';
		}
		elseif($pos[80]==$lower_value)
		{
			if($pos[80]>1) echo substr($str,1,$pos[80]-1);
			$str=substr($str,$pos[80]+8);
			echo MOSTRA_FAMILIAS();
		}
		elseif($pos[81]==$lower_value)
		{
			if($pos[81]>1) echo substr($str,1,$pos[81]-1);
			$str=substr($str,$pos[81]+12);
			echo '<a style="text-decoration:none" href="https://www.netcaixa.pt/" target="_blank">
				<img alt="netcaixa" src="'.$GLOBALS['site_http_url'].'layout/default/images/netcaixa-online.png">
			</a>
			<a style="text-decoration:none" href="#" onclick="window.open(\'http://www.visaeurope.com/en/cardholders/verified_by_visa.aspx\',\'Verified by Visa\',\'width=580, height=580,scrollbars=yes\'); return false;" target="_blank">
				<img style="position:relative; top:5px;" alt="Verified By Visa" src="'.$GLOBALS['site_http_url'].'layout/default/images/VerifiedByVisa.png">
			</a>
			<a style="text-decoration:none" href="#" onclick="window.open(\'http://www.MasterCardbusiness.com/mcbiz/index.jsp?template=/orphans&content=securecodepopup\',\'MasterCard SecureCode\',\'width=580, height=370\'); return false;" target="_blank">
				<img style="position:relative; top:5px;" src="'.$GLOBALS['site_http_url'].'layout/default/images/mcsc.png">
			</a>';
		}
		elseif($pos[82]==$lower_value)
		{
            if($pos[82]>1) echo substr($str,1,$pos[82]-1);
            $str=substr($str,$pos[82]+14);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);
			$pars=explode(',',$numero);
			if(count($pars) > 1) $use=1; else $use=0;
			$function=$pars[0];
			if($use==1 && $function != ''){ @array_shift($pars); echo call_user_func_array($function,$pars);}
			else echo call_user_func($function);
        }
		elseif($pos[83]==$lower_value)
		{
           if($pos[83] > 1) echo substr($str,1,$pos[83]-1);
			$str=substr($str,$pos[83]+9);
			echo PROJETO;
        }
        elseif($pos[84]==$lower_value)
		{
			if($pos[84]>1)
			{
				$first_str=substr($str,1,$pos[84]-1);
				echo substr($first_str,0,strrpos($first_str,'<img '));
				$numero=substr($str,strpos($str,'title="')+7);
				$numero=substr($numero,0,strpos($numero,'"'));
				$str=substr($str,$pos[84]);//resto da string
				$str=substr($str,strpos($str,'>')+1);
			}
			if($admin)
			{
					echo '<div id="edit_mask" class="edit_mask">'.modulos($numero);
					echo'<a class="edit" href="admin/modulos/pagina.php?ID='.$numero.'" target="_blank">EDITAR ELEMENTO</a>';
					echo'<a class="edit" href="#" onclick="window.open(\'admin/modulos/pagina_on_fly.php?ID='.$numero.'\',\'_new\',\'height=600,width=1000,toolbar=0,location=0,menubar=0,scrollbars=1\')" >EDITAR RÁPIDO</a>';
					echo'<a class="edit" href="index.php?oe=-1" target="_blank">EXIT</a></div>';
			}
			else echo modulos($numero);
		}
		elseif($pos[85]==$lower_value)
		{
			if($pos[85]>1) echo substr($str,1,$pos[85]-1);
			$str=substr($str,$pos[85]+10);
			$numero=substr($str,0,strpos($str,')'));
			$str=substr($str,strpos($str,')')+2);

			if($admin)
			{
					echo '<div id="edit_mask" class="edit_mask">'.modulos($numero);
					echo'<a class="edit" href="admin/modulos/pagina.php?ID='.$numero.'" target="_blank">EDITAR ELEMENTO</a>';
					echo'<a class="edit" href="#" onclick="window.open(\'admin/modulos/pagina_on_fly.php?ID='.$numero.'\',\'_new\',\'height=600,width=1000,toolbar=0,location=0,menubar=0,scrollbars=1\')" >EDITAR RÁPIDO</a>';
					echo'<a class="edit" href="index.php?oe=-1" target="_blank">EXIT</a></div>';
			}
			else echo modulos($numero);
		}
		$str=".".$str;
		$str2=strtolower($str);
		$pos[0]=strpos($str2,'[form(');
		$pos[1]=strpos($str2,'[media(');
		$pos[2]=strpos($str2,'[show_page(');
		$pos[3]=strpos($str2,'pages.php?page_id=');
		$pos[4]=strpos($str2,'[menu(');
		$pos[5]=strpos($str2,'[linguas(');
		$pos[6]=strpos($str2,'[search]');
		$pos[7]=strpos($str2,'[sugira]');
		$pos[8]=strpos($str2,'[registo_news]');
		$pos[9]=strpos($str2,'[registo_cliente]');
		$pos[10]=strpos($str2,'[pesquisa]');
		$pos[11]=strpos($str2,'<a href="'.$m_http_url_images.'zoom/');
		$pos[12]=strpos($str2,'[lista_noticias(');
		$pos[13]=strpos($str2,'[menu2(');
		$pos[14]=strpos($str2,'[novidades]');
		$pos[15]=strpos($str2,'[form_orcamentos]');
		$pos[16]=strpos($str2,'[menu3(');
		$pos[17]=strpos($str2,'[menu_news(');
		$pos[18]=strpos($str2,'[last_news(');
		$pos[19]=strpos($str2,'[most_popular]');
		$pos[20]=strpos($str2,'[text_news]');
		$pos[21]=strpos($str2,'[list_events]');
		$pos[22]=strpos($str2,'[follow_us]');
		$pos[23]=strpos($str2,'[footer]');
		$pos[24]=strpos($str2,'[shop_menu]');
		$pos[25]=strpos($str2,'[shop]');
		$pos[26]=strpos($str2,'[shopping_cart]');
		$pos[27]=strpos($str2,'[linguas]');
		$pos[28]=strpos($str2,'[pesquisa_top]');
		$pos[29]=strpos($str2,'[product]');
		$pos[30]=strpos($str2,'[path]');
		$pos[31]=strpos($str2,'[shopping_cart2]');
		$pos[32]=strpos($str2,'[shopping_cart3]');
		$pos[33]=strpos($str2,'[blog_home]');
		$pos[34]=strpos($str2,'[blog_all]');
		$pos[35]=strpos($str2,'[shopping_cart4]');
		$pos[36]=strpos($str2,'[all_news]');
		$pos[37]=strpos($str2,'[all_vids]');
		$pos[38]=strpos($str2,'[opinion]');
		$pos[39]=strpos($str2,'[shopfams]');
		$pos[40]=strpos($str2,'[gallery]');
		$pos[41]=strpos($str2,'[club]');
		$pos[42]=strpos($str2,'[menu_club(');
		$pos[43]=strpos($str2,'[clubfooter]');
		$pos[44]=strpos($str2,'[club_home]');
		$pos[45]=strpos($str2,'[opinion2]');
		$pos[46]=strpos($str2,'[documentaries]');
		$pos[47]=strpos($str2,'[pictures]');
		$pos[48]=strpos($str2,'[prod_novidades]');
		$pos[49]=strpos($str2,'[prod_oferta]');
		$pos[50]=strpos($str2,'[cart]');
		$pos[51]=strpos($str2,'[cloud]');
		$pos[52]=strpos($str2,'[footer2]');
		$pos[53]=strpos($str2,'[seo(');
		$pos[54]=strpos($str2,'[destaques]');
		$pos[55]=strpos($str2,'[page_title]');
		$pos[56]=strpos($str2,'[page_title_shop]');
		$pos[57]=strpos($str2,'[recomendar_produto]');
		$pos[58]=strpos($str2,'[menu_categorias]');
		$pos[59]=strpos($str2,'[marcas]');
		$pos[60]=strpos($str2,'[rand_novidade]');
		$pos[61]=strpos($str2,'[menu_simples(');
		$pos[62]=strpos($str2,'[breadcrums]');
		$pos[63]=strpos($str2,'[welcome]');
		$pos[64]=strpos($str2,'[carrinho_visualiza]');
		$pos[65]=strpos($str2,'[logo]');
		$pos[66]=strpos($str2,'[pedidocotacao]');
		$pos[67]=strpos($str2,'[pedidorecolha]');
		$pos[68]=strpos($str2,'[contactoscotacao]');
		$pos[69]=strpos($str2,'[filtros]');
		$pos[70]=strpos($str2,'[loginpopup]');
		$pos[71]=strpos($str2,'[show_last_news(');
		$pos[72]=strpos($str2,'[show_destaques]');
		$pos[73]=strpos($str2,'[chat]');
		$pos[74]=strpos($str2,'[show_promocoes]');
		$pos[75]=strpos($str2,'[show_marcas]');
		$pos[76]=strpos($str2,'[shopping_cartchange]');
		$pos[77]=strpos($str2,'[currency]');
		$pos[78]=strpos($str2,'[produtospromo]');
		$pos[79]=strpos($str2,'[sliderproducts]');
		$pos[80]=strpos($str2,'[catfam]');
		$pos[81]=strpos($str2,'[caixageral]');
		$pos[82]=strpos($str2,'[callfunction(');
		$pos[83]=strpos($str2,'[projeto]');
		$pos[84]=strpos($str2,'html5.png');
		$pos[85]=strpos($str2,'[elemento(');
		$i=0;
		while(isset($pos[$i]) && intval($pos[$i])==0 && $i<count($pos)) $i++;
		if(isset($pos[$i])) $lower_value=$pos[$i];
		for($k=$i;$k<count($pos);$k++)
		{
			if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
		}
		$flag=array_sum($pos);
	}
	return substr($str,1);
}
function str2SQLstr($str,$pelicas=1)
{
	if(strstr($str,"'") || strstr($str,"\\")){$tam=strlen($str);$str1="";for($i=0;$i < $tam;$i++){$char=substr($str,$i,1);if($char=="'" || $char=="\\")	$char="\\".$char;$str1.=$char;}}
	else $str1=$str;
	if($pelicas)$str1="'".$str1."'";
	return ($str1);
}
function str2SQLLikestr($str)
{
	$str=str2SQLstr($str,0);
	if(strstr($str,"%")){while(strstr($str,"%")){$str1.=substr($str,0,strstr($str,"%"))."\%";$str=substr(strstr($str,"%") + 1);}}
	else $str1=$str;
	if(strstr($str1,"_")){while(strstr($str1,"_")){$str.=substr($str1,0,strstr($str1,"_"))."\_";$str1=substr(strstr($str1,"_") + 1);}}
	else $str=$str1;
	return $str;
}
function html_cut($text,$max_length)
{
	return $text;
	/*$tags=array();$result=''; $is_open=false;$grab_open=false;$is_close=false;$in_double_quotes=false;$in_single_quotes=false;$tag='';$i=0;$stripped=0;
	$stripped_text=strip_tags($text);
	while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
	{
		$symbol =$text[$i];  $result .= $symbol;
		switch ($symbol)
		{
			case '<':
				$is_open  =true; $grab_open=true;
			break;
			case '"':
			   if ($in_double_quotes) $in_double_quotes=false;
			   else $in_double_quotes=true;
			break;
			case "'":
			  if ($in_single_quotes) $in_single_quotes=false;
			  else $in_single_quotes=true;
			break;
			case '/':
				if ($is_open && !$in_double_quotes && !$in_single_quotes){$is_close =true; $is_open  =false; $grab_open=false;}
			break;
			case ' ':
				if ($is_open) $grab_open=false;
				else $stripped++;
			break;
			case '>':
				if ($is_open){ $is_open  =false; $grab_open=false;  array_push($tags,$tag);  $tag="";  }
				else if ($is_close){ $is_close=false; array_pop($tags); $tag="";}
			break;
			default:
				if ($grab_open || $is_close) $tag .= $symbol;
				if (!$is_open && !$is_close) $stripped++;
		}
		$i++;
	}
	while ($tags) $result .= "</".array_pop($tags).">";
	return $result;*/
}

function file_get_contents2($URL)
{
	global $site_shell_url;
	$html=file_get_contents($URL);
	return $html;
}
function gerar_sitemap()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_shell_url,$site_http_url;
	$path=$site_shell_url."files/";

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj; $rs2=new DataObj;


	$myFile=dirname( __FILE__ )."/../files/importar/atualizar_googlemaps.txt";
	$fh=fopen($myFile, 'a+');
	fwrite($fh, 'a atualiza...'."\n");
	chmod($myFile, 0755);


	$cabecalho="<?xml version='1.0' encoding='UTF-8'?>
	<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
	$rodape="</urlset>";

	$i=1;
	$n=0;

	$filepointer=fopen($path."sitemap.xml", "w");
	fwrite($filepointer,$cabecalho);
	/* //Paginas
	$sql="SELECT pages.* FROM pages WHERE Visible=1 order by Data1 Desc";
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$page_id=$rs->db_data["ID"];
		$link=$site_http_url.seo('pages.php?page_id='.$page_id);
		$data=time($rs->db_data["Data1"]);
		$data=date('Y-m-d', $data);
		$res ='
			<url>
				<loc>'.$link.'</loc>
				<lastmod>'.$data.'</lastmod>
				<changefreq>weekly</changefreq>
				<priority>0.5</priority>
			</url>';

		fwrite ($filepointer, $res);

		$rs->db_next();
	}
	$rs->db_close(); */
	$sql='
				SELECT
					p.ID, p.URL_Link, pl.URL_Nome2 as Cabecalho, pl.Lang
				FROM
					menus_links p
					INNER JOIN menus_links_lang pl ON pl.ID_PAI=p.ID
				WHERE
					p.Visivel=1
					AND p.Logado=0
					AND p.ID_ORG > 0
					AND p.URL_Link <> ""
					AND pl.Lang="pt"
				GROUP BY
					p.URL_Link
				ORDER BY
					pl.Lang DESC
				';

		$rs->db_open($db_link,$DB_name,$sql);
		while(!$rs->EOF())
		{
			$page_id=$rs->db_data["ID"];
			$URL_Link=$rs->db_data["URL_Link"];
			if(substr_count($URL_Link, 'pages.php')) $link=$site_http_url.seo($URL_Link, '', '', $Sigla);
			else $link=$URL_Link;

			if(substr_count($site_http_url,$link) > 0 )
			{
				$data=time($rs->db_data["Data1"]);
				$data=date('Y-m-d', $data);
				$res ='
					<url>
						<loc>'.$link.'</loc>
						<lastmod>'.$data.'</lastmod>
						<changefreq>daily</changefreq>
						<priority>0.5</priority>
					</url>';

				fwrite ($filepointer, $res);
				fwrite($fh, 'page_id:'.$page_id."\n");
				if(!stream_resolve_include_path($myFile)) die();

				$n++;
				if($n>40000)
				{
					$n=0;
					$i++;
					fclose($filepointer);
					$filepointer=fopen($path."sitemap".$i.".xml", "w");
				}
			}

			$rs->db_next();
		}
		$rs->db_close();


	// *******************************************PRODUTOS
	$sql = "SELECT
				products.ID
			FROM
				products
				INNER JOIN products_lang ON products_lang.ID_PAI=products.ID
				INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
				INNER JOIN products_families_obj pfo on pfo.ID_PROD=products.ID
				INNER JOIN products_families pf on pf.ID=pfo.ID_ORG
			WHERE
				(products.Aproved=1 and apagado=0)
				AND products_lang.Lang='".$GLOBALS['site_lang']."'
				AND products_objs.obj_pvp > 0
				AND pf.visivel=1
			GROUP BY
				products.ID
			ORDER BY
				products.ID
			";	
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$prod_id=$rs->db_data["ID"];
		$link=$site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$prod_id, $prod_id, 2);


			$res ='
				<url>
					<loc>'.$link.'</loc>
					<changefreq>hourly</changefreq>
					<priority>1.0</priority>
				</url>';

			fwrite ($filepointer, $res);
			fwrite($fh, 'product:'.$prod_id."\n");
			if(!stream_resolve_include_path($myFile)) die();

			$n++;
			if($n>40000)
			{
				$n=0;
				$i++;
				fclose($filepointer);
				$filepointer=fopen($path."sitemap".$i.".xml", "w");
			}

		$rs->db_next();
	}
	$rs->db_close();


	// *********************************FAMILIAS
	$sql=' SELECT pf.ID, pf.ID_ORG, pfl.Cabecalho FROM products_families pf INNER JOIN products_families_lang pfl ON (pf.ID=pfl.ID_PAI) WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'" AND pf.ID_ORG=0';
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$fam_id=$rs->db_data["ID"];
		$link=$site_http_url.$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$fam_id,$fam_id, 1);

			$res ='
				<url>
					<loc>'.$link.'</loc>
					<changefreq>daily</changefreq>
					<priority>1.0</priority>
				</url>';

			fwrite ($filepointer, $res);
			fwrite($fh, 'fam_id:'.$fam_id."\n");
			// SUB FAMILIAS
			$sql=' SELECT pf.ID, pf.ID_ORG, pfl.Cabecalho FROM products_families pf INNER JOIN products_families_lang pfl ON (pf.ID=pfl.ID_PAI) WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'" AND pf.ID_ORG='.$fam_id;
			$rs2->db_open($db_link,$DB_name,$sql);
			while(!$rs2->EOF())
			{
				$fam_id=$rs2->db_data["ID"];
				$link=$site_http_url.$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$fam_id, $fam_id, 1);

					$res ='
						<url>
							<loc>'.$link.'</loc>
							<changefreq>daily</changefreq>
							<priority>1.0</priority>
						</url>';

					fwrite ($filepointer, $res);
					fwrite($fh, 'subfam_id:'.$fam_id."\n");

					if(!stream_resolve_include_path($myFile)) die();

					$n++;
					if($n>40000)
					{
						$n=0;
						$i++;
						fclose($filepointer);
						$filepointer=fopen($path."sitemap".$i.".xml", "w");
					}

				$rs2->db_next();
			}
			$rs2->db_close();

		$rs->db_next();
	}
	$rs->db_close();

	fwrite ($filepointer, $rodape);
	fclose($filepointer);
	fclose($fh);

	@unlink($myFile);
}

function gerar_rss()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_shell_url;
	$path=$site_shell_url."files/";
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	global $site_title;
	global $rss_site_url;
	global $rss_site_description;
	global $rss_email_editor;
	global $rss_email_autor;
	global $rss_email_webmaster;
	global $site_copyright;

	$cabecalho="<?xml version='1.0' encoding='UTF-8'?>
		<rss version=\"2.0\">
		<channel>
			<title>".$site_title."</title>
			<link>".$rss_site_url."</link>
			<description>".$rss_site_description."</description>
			<language>en</language>
			<copyright>".$site_copyright."</copyright>
			<managingEditor>".$rss_email_editor."</managingEditor>
			<webMaster>".$rss_email_webmaster."</webMaster>
		  <image>
			<title>".$site_title."</title>
			<url>".$rss_site_url."/layout/default/images/rss_logo.gif</url>
			<link>".$rss_site_url."</link>
			<width>180</width>
			<height>95</height>
			<description>".$rss_site_description."</description>
		  </image>";
	$rodape="
	</channel>
	</rss>";

	$filepointer=fopen($path."rss.xml", "w");
	fwrite($filepointer,$cabecalho);

	$sql="SELECT pages.*,Cabecalho,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Destaque=1 and (curdate() between Data1 and Data2) and Lang='$admin_lang' order by Data1 Desc";
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$C=stripslashes($rs->db_data["Cabecalho"]);
		$T2=limpa_editor_lixo(stripslashes($rs->db_data["Texto2"]));
		$page_id=$rs->db_data["ID"];
		$I1=$rs->db_data["img_ext"];
		$data1=$rs->db_data["Data1"];
		$img_file="files/pages/".$page_id."_1.".$I1;
		$img_file_peq="files/pages/".$page_id."_2.".$I1;

		$TimeStamp=mktime(0, 0, 0, (int) substr($data1, 5, 2), (int) substr($data1, 8, 2), (int) substr($data1, 0, 4));
		$m_data=date("y.m.d",$TimeStamp);
		$m_data2=date("d M Y",$TimeStamp);
		$hora=date("H:i:s");
		$m_data2.=" $hora";

		$link=$rss_site_url."/pages.php?page_id=".$page_id;
		$Resumo=limpaHTML($T2);
		$Resumo=strlen($Resumo) > 160 ? substr($Resumo,0,157)."..." : $Resumo;

		$not="<item>
				<title>$C</title>
				<pubDate>$m_data2 GMT</pubDate>
				<link>$link</link>
				<author>".$rss_email_autor."</author>
				<description>
				  <![CDATA[
				  <p align=justify><font face=\"verdana\" size=2><font color=#004080><font>
				  $Resumo </font></p>
				  ]]>
				</description>
			  </item>";
		fwrite($filepointer,$not);

		$rs->db_next();
	}
	$rs->db_close();

	fwrite ($filepointer,$rodape);
	fclose($filepointer);

}
function registarLogCron($mensagem,$query,$data)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql='INSERT INTO logs_crons(Mensagem,Query,Data) VALUES ("'.addslashes($mensagem).'","'.addslashes($query).'","'.$data.'")';
	$res=Runquery($db_link,$DB_name,$sql);
}
function printr($vec)
{
	echo '<pre>';
	print_r($vec);
	echo '</pre>';
}
function gerarCSFRToken($len,$nome)
{
	$token='';
	if(function_exists('openssl_random_pseudo_bytes'))
	{
		$byteLen=intval(($len / 2) + 1);
		$token=substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
	}
	elseif(@is_readable('/dev/urandom'))
	{
		$f=fopen('/dev/urandom', 'r');
		$urandom=fread($f, $len);
		fclose($f);
		$token='';
	}
	if(empty($token))
	{
		for ($i=0;$i<$len;++$i)
		{
			if(!isset($urandom))
			{
				if ($i%2==0)
				{
					mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
				}
				$rand=48+mt_rand()%64;
			}
			else
			{
				$rand=48+ord($urandom[$i])%64;
			}

			if ($rand>57) $rand+=7;
			if ($rand>90) $rand+=6;
			if ($rand==123) $rand=52;
			if ($rand==124) $rand=53;
			$token.=chr($rand);
		}
	}
	$_SESSION[$nome]=$token;
	return $token;
}
function validarCSFRToken($nome,$form_value)
{
	if($_SESSION[$nome]==$form_value) return true;
	return false;
}