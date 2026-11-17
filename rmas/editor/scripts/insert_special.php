<?
   	$path="../../../core/"; 
	include $path."core_files.php";
?>
<html>
<head>
	<title>COMPONENTES</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style/editor.css" rel="stylesheet" type="text/css">
</head>
<body>
<script language="javascript">
	function windowClose(){
	  parent.oUtil.onSelectionChanged=null;
	  self.close();
	};
    function ReturnUrl(string)
    {
		sURL=string;
		if(navigator.appName.indexOf('Microsoft')<0)
		{
			var oEditor=parent.oUtil.oEditor;
			var oSel=oEditor.getSelection();
			var range = oSel.getRangeAt(0);
			parent.oUtil.obj.saveForUndo();
			var emptySel = false;
			if(string!="")
			{
            	if(range.toString()==""){emptySel=true;}
				if(emptySel)
                {
					var node = oEditor.document.createTextNode(sURL);
					range.insertNode(node);
					oEditor.document.designMode = "on";
	
					range = oEditor.document.createRange();
					range.setStart(node, 0);
					range.setEnd(node, sURL.length);
	
					oSel = oEditor.getSelection();
					oSel.removeAllRanges();
					oSel.addRange(range);
                }
            }
	        var isSelInMidText = (range.startContainer.nodeType==Node.TEXT_NODE) && (range.startOffset>0)
			window.focus();
			windowClose();
		}
		else
		{
			parent.oUtil.obj.setFocus();
			if(!parent.oUtil.obj.checkFocus()){return;}//Focus stuff
			var oEditor=parent.oUtil.oEditor;
			var oSel=oEditor.document.selection.createRange();
			parent.oUtil.obj.saveForUndo();
		
		
			if(string!="")
			{
				if(oSel.parentElement)
				{
	
						if(oSel.text=="")//If no (text) selection, then build selection using the typed URL
						{
							var oSelTmp=oSel.duplicate();
							oSel.text=sURL;
							oSel.setEndPoint("StartToStart",oSelTmp);
							oSel.select();
						}
				}
			}
			else
			{
				oSel.execCommand("unlink");//unlink				
				dialogArguments.realTime(dialogArguments.oUtil.oName);
				dialogArguments.oUtil.activeElement=null;
			}	
        	parent.oUtil.onSelectionChanged=null;
			windowClose();
		}
    }
</script>
</head>
<body style="overflow:auto;">

<table width=100% height=100% align=center cellpadding=0 cellspacing=0>
<tr>
    <td valign=top style="padding:5;height:100%">
        <table cellpadding=2 cellspacing=0 width="100%" border=0>
        <tr>
            <td valign=top align="left"><a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("[scroll]some text here[/scroll]")'><img src="scroll.jpg" border=0 align="absmiddle"> Scroll</a></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 width="100%" border=0>
        <tr>
            <td valign=top align="left"><a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("[search]")'><img src="search.jpg" border=0 align="absmiddle"> pesquisa</a></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 width="100%" border=0>
        <tr>
            <td valign=top align="left"><a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("[linguas()]")'><img src="lang.jpg" border=0 align="absmiddle"> escolha de lingua</a></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 width="100%" border=0>
        <tr>
            <td valign=top align="left"><a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("[sugira]")'> formulário de recomendar pagina</a></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 width="100%" border=0>
        <tr>
            <td valign=top align="left"><a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("[registo_news]")'> formulário de registo de emails</a></td>
        </tr>
        </table>
        <table cellpadding=2 cellspacing=0 width="100%" border=0>
        <tr>
            <td valign=top align="left"><a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("[registo_cliente]")'> formulário de registo de cliente</a></td>
        </tr>
        </table>
    </td>
</tr>
<tr>
    <td class="dialogFooter" align="center">
      <table cellpadding=0 cellspacing=0>
      <tr>
          <td>
            <input type="button" name=btnCancel id=btnCancel value="cancel" onClick="windowClose()" class="inpBtn" onMouseOver="this.className='inpBtnOver';" onMouseOut="this.className='inpBtnOut'">
          </td>
      </tr>
      </table>
    </td>
</tr>
</table>

</body>
</html>