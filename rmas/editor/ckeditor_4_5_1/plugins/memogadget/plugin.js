CKEDITOR.plugins.add('memogadget',   //name of our plugin
{    
    requires: ['dialog'], //requires a dialog window
    init:function(a) {
	var b1="hiperurl";
	var b2="media";
	var b3="modulos";
	var b4="menus";
	var b5="forms";
	var b6="content";
	var c1=a.addCommand(b1,new CKEDITOR.dialogCommand(b1));
	var c2=a.addCommand(b2,new CKEDITOR.dialogCommand(b2));
	var c3=a.addCommand(b3,new CKEDITOR.dialogCommand(b3));
	var c4=a.addCommand(b4,new CKEDITOR.dialogCommand(b4));
	var c5=a.addCommand(b5,new CKEDITOR.dialogCommand(b5));
	var c6=a.addCommand(b6,new CKEDITOR.dialogCommand(b6));
	c1.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
	c2.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
	c3.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
	c4.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
	c5.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
	c6.modes={wysiwyg:1,source:1}; //Enable our plugin in both modes
	c1.canUndo=true;
	c2.canUndo=true;
	c3.canUndo=true;
	c4.canUndo=true;
	c5.canUndo=true;
	c6.canUndo=true;
	//add new button to the editor
	a.ui.addButton("hiperurl",	{label:'Links internos',	command:b1,icon:this.path+"internal.png"});
	a.ui.addButton("media",		{label:'Media',				command:b2,icon:this.path+"media.png"});
	a.ui.addButton("modulos",	{label:'Elementos',			command:b3,icon:this.path+"html5.png"});
	a.ui.addButton("menus",		{label:'Menus',				command:b4,icon:this.path+"menus.png"});
	a.ui.addButton("forms",		{label:'Formulários',		command:b5,icon:this.path+"forms.png"});
	a.ui.addButton("content",	{label:'Páginas',			command:b6,icon:this.path+"pages.png"});
	CKEDITOR.dialog.add(b1,this.path+"dialogs/insert_hiperurl.php") //path of our dialog file
	CKEDITOR.dialog.add(b2,this.path+"dialogs/insert_media.php") //path of our dialog file
	CKEDITOR.dialog.add(b3,this.path+"dialogs/insert_modulo.php") //path of our dialog file
	CKEDITOR.dialog.add(b4,this.path+"dialogs/insert_menu.php") //path of our dialog file
	CKEDITOR.dialog.add(b5,this.path+"dialogs/insert_form.php") //path of our dialog file
	CKEDITOR.dialog.add(b6,this.path+"dialogs/insert_content.php") //path of our dialog file
	}
});