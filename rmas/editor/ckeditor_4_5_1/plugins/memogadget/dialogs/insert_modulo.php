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
    $sql="Select * from modulos where ID_ORG='".$pai."' order by ordem Asc";
    $hRes= $dados->db_open($db_link,$DB_name,$sql);
    $i=0;
    while(!$dados->EOF())
    {
      $IDb=$dados->db_data["ID"];
      $rec= new DataObj;
      $rec->db_open($db_link,$DB_name, "Select * from modulos_lang where ID_PAI=$IDb and Lang='$admin_lang'");
      $nomeb=$rec->db_data["titulo"];
      $rec->db_close();
      $i=0;
      echo '<tr align="left"><td>';
      while($i<$contador){echo "&nbsp;";$i++;}
      if(!$nomeb) $nomeb = '-------';
      echo $IDb.'<img alt="" src="../images/arrow.png" align="absmiddle">&nbsp;<a style="cursor:pointer" class="preto" href="javascript:;" onclick=\\\'javascript:addCode(dialog,"'.$IDb.'","'.htmlentities_b($nomeb).'")\\\'>'.htmlentities_b($nomeb).'</a>';
      echo '</td></tr>';
      show_links($IDb,$contador);
      $dados->db_next();  
      $i++;
    }
    $dados->db_close();
  }
?>
function addCode(e,t,d)
{
  var instance = e.getParentEditor();
  instance.insertHtml('<img class="html5" width="100%" height="100" alt="'+t+'-'+d+'" title="'+t+'" name="html5.png">');    
  /*    editor.insertHtml('<img class="html5" width="100%" height="100" alt="'+t+'-'+d+'" title="'+t+'" name="html5.png">'); */  
    CKEDITOR.dialog.getCurrent().hide();
 };




CKEDITOR.dialog.add("modulos",function(e){
 return {
  title:'ELEMENTOS',
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