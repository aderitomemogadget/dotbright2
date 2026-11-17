/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ){
	config.allowedContent=true;
	config.skin = 'moonocolor';
	config.extraPlugins = 'memogadget,googledocs,wenzgmap,qrc,texttransform,youtube,templates,smiley,wysiwygarea,showblocks';


	config.enterMode = CKEDITOR.ENTER_BR;
	config.toolbar = 'Full';
	config.toolbar_Full = [
	{ name: 'tools', items: [ 'Maximize' ] },
	{ name: 'document', items: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
	{ name: 'clipboard', items: [ 'clipboard', 'undo' ], items: [ 'Find','SelectAll', 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', items: [ 'find', 'selection']},
	{ name: 'basicstyles', items: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', items: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Table', 'SpecialChar' ] },
	{ name: 'styles', items: [ 'Styles', 'Format' ] },
	{ name: 'alinhamentos', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
	{ name: 'others', items: [ 'Font','FontSize','Image','Flash','TextColor','BGColor'] },
	{ name: 'texttransform', items: ['TransformTextToUppercase', 'TransformTextToLowercase', 'TransformTextCapitalize', 'TransformTextSwitcher' ] },
	{ name: 'Memogadget', items: ['hiperurl','media','modulos','menus','forms','content','wenzgmap','googledocs','youtube','qrc','ShowBlocks','Templates','Smiley','Smiley'] }
	];
	config.toolbar = 'Small';
	config.toolbar_Small = [
	{ name: 'tools', items: [ 'Maximize' ] },
	{ name: 'basicstyles', items: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript' ] },
	{ name: 'styles', items: [ 'Styles'] },
	{ name: 'links', items: [ 'Link', 'Unlink'] },
	{ name: 'document', items: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
	{ name: 'alinhamentos', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
	{ name: 'others', items: [ 'Font','FontSize','Image','Flash','TextColor','BGColor'] },
	{ name: 'Memogadget', items: ['hiperurl','media','modulos','forms','content'] }
	];

};
