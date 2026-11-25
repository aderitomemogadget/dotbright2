<?
   	$path="../../../core/"; 
	include $path."core_files.php";
	
	function GetRSRecordspages($id=0, $campos="*",$Start=1, $Tabela="")
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		if($Nomeb) $hRes=$rs->db_open($db_link,$DB_name, "Select $campos from $Tabela");
		else $hRes=$rs->db_open($db_link,$DB_name, "Select $campos from $Tabela");
		return $rs;
	}
?>
<html>
<head>
	<title>Formulários</title>
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
		sURL="[form("+string+")]";
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
            <td valign=top align="center">
                <table width="100%" border=0 cellpadding=0 cellspacing="1"><? $rs=GetRSRecordspages(0,'*',0,"forms");
                    while(!$rs->EOF())
                    {	
                        ?><tr align="left"><td><img alt="" src="../../images/arrow.png" align="absmiddle">&nbsp;<a class="preto" href="javascript:;" onclick='javascript:ReturnUrl("<?=$rs->db_data["ID"]?>")'><?=htmlentities_b(($rs->db_data["Name"]));?></a></td></tr><? 
                        $rs->db_next();
                    }
                    $rs->db_close(0);?>
                </table>
             </td>
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