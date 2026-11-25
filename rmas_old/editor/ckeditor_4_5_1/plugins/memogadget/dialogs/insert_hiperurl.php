<?
   	$path="../../../../../../core/"; 
	include $path."core_files.php";
	function show_links($pai=0,$contador=-5)
	{
		$path="../../../../../../core/";
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;	
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$contador+=5;
		$rs=new DataObj;
		$sql="Select * from pages where ID_ORG='".$pai."' order by Ordem Asc";
		$hRes= $rs->db_open($db_link,$DB_name,$sql);
		$i=0;
		while(!$rs->EOF())
		{
			$IDb=$rs->db_data["ID"];
			$rec= new DataObj;
			$rec->db_open($db_link,$DB_name, "Select * from pages_lang where ID_PAI=$IDb and lang='$admin_lang'");
			$nomeb=$rec->db_data["Cabecalho"];
			$rec->db_close();
			$i=0;
			while($i<$contador){echo "&nbsp;";$i++;}
			echo '<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;<a style="cursor:pointer" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(dialog,"pages.php?page_id='.$rs->db_data["ID"].'")\\\'>'.htmlentities_b($nomeb).'</a><br>';
			show_links($IDb,$contador);
			$rs->db_next();	
			$i++;
		}
		$rs->db_close();
	}
?>
function addCode(e,t)
{
    var instance = e.getParentEditor();
    var selection = instance.getSelection();
    if (selection.getType() == 3)
    {
    	var selectedContent = selection.getSelectedElement().$.outerHTML;
    }
    else if (selection.getType() == 2)
    {
	    var selectedContent = instance.getSelection().getSelectedText(); 
	}

    
    selected_text=selectedContent;
    
    link = instance.document.createElement( 'a' );
    link.setAttribute( 'href', t );
    link.setHtml( selected_text );
    instance.insertElement( link );
    CKEDITOR.dialog.getCurrent().hide();
 };
 
CKEDITOR.dialog.add("hiperurl",function(e){
 return {
  title:'LINKS INTERNOS',
  resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
  minWidth:300,
  minHeight:100,
  onShow:function(){},
  onLoad:function(){dialog = this;},
  onOk:function(){},
  contents:[
  {
  	id:"info",
    name:'info',
    label:'Tab',
    elements:[
    {  
      type:'html',
      html:'<span style="">'+'<? show_links()?></span>'
     }
    ]
  }
  ],
  buttons:[CKEDITOR.dialog.cancelButton],
};



});