<?
   	$path="../../../../../../core/"; 
	include $path."core_files.php";
	
	
	
	
	
	
	function show_links($pai=0,$contador=-5)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$hRes=$rs->db_open($db_link,$DB_name, "Select menus_links.*,URL_Nome2 from menus_links inner join menus_links_lang on menus_links_lang.ID_PAI=menus_links.ID where ID_ORG=0 and Lang='$admin_lang'");
		while(!$rs->EOF())
		{
			echo '<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;<a style="cursor:pointer" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(dialog,"[menu('.$rs->db_data["ID"].')]")\\\'>'.htmlentities_b($rs->db_data["URL_Nome2"]).'</a><br>';
			$rs->db_next();
		}
		$rs->db_close();
	}
?>
function addCode(e,t)
{
    var instance = e.getParentEditor();
    instance.insertText( t );
    CKEDITOR.dialog.getCurrent().hide();
 }; 
CKEDITOR.dialog.add("menus",function(e){
 return {
  title:'MENU',
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