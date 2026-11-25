<?
  $path="../../../../../../core/"; 
	include $path."core_files.php";
	function GetRSRecordspages($id=0, $campos="*",$Start=1, $Nomeb="")
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		if($Nomeb) $hRes= $obj->db_open($db_link,$DB_name, "Select $campos from $Nomeb");
		else $hRes=$obj->db_open($db_link,$DB_name, "Select $campos from $Nome");
		return $obj;
	}
	function show_links($pai=0,$contador=-5)
	{
		$rs=GetRSRecordspages(0,'*',0,"media_type");
		while(!$rs->EOF())
		{
			echo '<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;<a style="cursor:pointer" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(dialog,"[media('.$rs->db_data["ID"].')]")\\\'>'.htmlentities_b($rs->db_data["Nome"]).'</a><br>';
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
CKEDITOR.dialog.add("media",function(e){
 return {
  title:'MEDIA',
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