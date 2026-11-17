<?
   	$path="../../../../../../core/"; 
	include $path."core_files.php";
	
	function show_links($pai=0,$contador=-5)
	{
		$path="../../../../../../core/";
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;	
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$contador+=5;
		$dados=new DataObj;
		$sql="Select * from pages where ID_ORG='".$pai."' order by Ordem Asc";
		$hRes= $dados->db_open($db_link,$DB_name,$sql);
		$i=0;
		while(!$dados->EOF()){
			$IDb=$dados->db_data["ID"];
			$Tipo=$dados->db_data["Tipo"];
			
			$rec= new DataObj;
			$rec->db_open($db_link,$DB_name, "Select * from pages_lang where ID_PAI=$IDb and Lang='$admin_lang'");
			$nomeb=$rec->db_data["Cabecalho"];
			$nomeb2=$rec->db_data["Cabecalho2"];
			$rec->db_close();
	
			$i=0;
			echo '<tr align="left"><td>';
				while($i<$contador){echo "&nbsp;";$i++;}
				if($Tipo==2) echo '<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;'.($nomeb2 != '' ? htmlentities_b($nomeb).': '.htmlentities_b($nomeb2) : htmlentities_b($nomeb)).' - <span style="color:#303030"><a style="cursor:pointer;color:#303030;text-decoration:underline" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(this,"[last_news('.$IDb.',press)]")\\\'>Press</a> | <a style="cursor:pointer;color:#303030;text-decoration:underline" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(this,"[last_news('.$IDb.',videos)]")\\\'>Videos</a> | <a style="cursor:pointer;color:#303030;text-decoration:underline" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(this,"[last_news('.$IDb.',links)]")\\\'>Links</a> | <a style="cursor:pointer;color:#303030;text-decoration:underline" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(this,"[last_news('.$IDb.',faqs)]")\\\'>Faqs</a> </span>';
				else echo '<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;<a style="cursor:pointer" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(this,"[show_page('.$IDb.')]")\\\'>'.($nomeb2 != '' ? htmlentities_b($nomeb).': '.htmlentities_b($nomeb2) : htmlentities_b($nomeb)).'</a>';
			echo '</td></tr>';
			show_links($IDb,$contador);
			$dados->db_next();	
			$i++;
		}
		$dados->db_close();
	}
?>
function addCode(e,t)
{
    editor.insertText( t );
    CKEDITOR.dialog.getCurrent().hide();
 }; 
CKEDITOR.dialog.add("content",function(e){
 return {
  title:'PAGINAS',
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