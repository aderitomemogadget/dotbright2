				<script src=../../external_api/jquery.min.js></script>
                <style>
                #drop-area2{min-width: 90%; min-height: 100px;height:100px;padding: 10px; border: 1px dotted #ccc;text-align: center;	position: relative;background-color:#F0FAFF}
                #drop-area2:before{content: "Drop files here!";color:#fff;font-size:25px;font-weight:bold;opacity:0.5;text-shadow:1px 1px #000;position:absolute;width:100%;left:0;top:50%;margin:-25px 0 0;z-index:1;}
                #drop-area2.hover{border:1px dashed red}
                #drop-area2 div{width:150px;height:150px;border:1px solid #CCC;background-color:#F0FAFF;font-size:.5em;padding:5px;background-position:center;background-repeat:no-repeat;background-size:contain;float:left;margin:10px;word-wrap:break-word}
                #status2 {padding:5px 20px;font-family:Arial;font-size:11px;border-top:#000 1px solid;border-bottom:#000 1px solid;background-color:#000;color:#fff}
                #settings {padding:5px 20px;font-family:Arial;font-size:11px;border-top:#000 1px solid;border-bottom:#000 1px solid;background-color:#000;color:#fff}
                td.tm{color:#000}
                </style>
                <style type="text/css">
                <!--
                    ul.zzul {list-style-type:none; display:block;}
                    span.zzspace {left:11px;}
                    a.exp, a.exp:visited {color:#0000ff;}
                    a.exp:hover { color:#ff0000;}
                -->
                </style>
                </head>
                <body>
                <table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>
                    <table width="100%" border=0 cellspacing=0 cellpadding=0>
                    
                    <tr><td colspan=4>
                    <script>
					function insert_new_image2(image)
					{
						sURL=image;
						if(navigator.appName.indexOf('Microsoft')<0)
						{
							var oEditor=parent.oUtil.oEditor;
							var oSel=oEditor.getSelection();
							var range =oSel.getRangeAt(0);
							parent.oUtil.obj.saveForUndo();
							var emptySel =false;
							if(range.toString()=='')emptySel =true;
							if(emptySel)
							{
								parent.oUtil.obj.doCmd("InsertImage",image);
							}
							var isSelInMidText=(range.startContainer.nodeType==Node.TEXT_NODE) && (range.startOffset>0)
							window.focus();
							self.close();
						}
						else
						{
							parent.oUtil.obj.setFocus();
							if(!parent.oUtil.obj.checkFocus()){return;}//Focus stuff
							var oEditor=parent.oUtil.oEditor;
							var oSel=oEditor.document.selection.createRange();
							parent.oUtil.obj.saveForUndo();
							if(oSel.parentElement)
							{
									if(oSel.text=='')//If no (text) selection, then build selection using the typed URL
									{
										var oSelTmp=oSel.duplicate();
										oSel.text=sURL;
										oSel.setEndPoint("StartToStart",oSelTmp);
										oSel.select();
									}
							}
							parent.oUtil.onSelectionChanged=null;
							self.closeWin();						
						}
					}
					
                    var drop_settings =0;
                    var lista='';
                    function ajax_upload(url)
                    {
                        try{var x=new ActiveXObject("Microsoft.XMLHTTP");}
                        catch(e){
                            try{var x=new ActiveXObject("Msxml2.XMLHTTP");}
                                catch(ex){
                                    try{var x=new XMLHttpRequest();}
                                    catch(exc){alert("Browser not accept x");x=null;}
                                    }
                                }
                        if(x)
                        {
                            x.open("GET",url,true);
                            x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
                            x.onreadystatechange=function(){
                                if(x.readyState==4)
                                {
                                    var t=x.responseText;
                                    t=unescape(t);
                                    if(t=="done")alert("O ficheiro foi aceite com sucesso");
                                    else alert("ERRO!!!! o ficheiro é inválido");					
                                    return true;
                                }
                            };
                            x.send(null);
                        }
                    }
                    var up={	
                    $drop :			null,
                    queue :			[],
                    upload_end:     0,
                    processing :	null,
                    uploading :		false,
                    binaryReader :	null,
                    dataReader :	null,
                    xhr:			null,
                    init : function(){
                        up.$drop=$('#drop-area2');
                        up.$drop.bind('dragenter',up.enter);
                        up.$drop.bind('dragleave',up.leave);
                        up.$drop.bind('dragover',up.over);
                        up.$drop.bind('drop',up.drop);		
                        $('#status2').click(up.cancel);
                        up.xhr =new XMLHttpRequest();
                        if(useragent="msie"){}
                        else{
                        up.xhr.upload.addEventListener('progress', up.uploadProgress , false);
                        up.xhr.upload.addEventListener('load', up.uploadLoaded , false);
                        }
                    },	
                    enter : function(e){$(e.target).addClass('hover');return false;},	
                    leave : function(e){$(e.target).removeClass('hover');return false;},	
                    over : function(e){	return false;},	
                    drop : function(e){
                        $(e.target).removeClass('hover');	
                        e.preventDefault();		
                        var origem=e.originalEvent.dataTransfer.getData("URL");		
                        if(origem.substr(0,4)=="http")
                        {		
                            $('#status2').html('loading from remote URL... WAIT!!');
                            ajax_upload('easycreate_process.php?cod=<?=$ID?>&url='+origem);
                            return false;
                        }
                        
                        var files =e.originalEvent.dataTransfer.files;
                        for (var i =0; i<files.length; i++){
                            var file =files[i];
                            up.queue.push(file);
                        }
                        if(up.uploading==false){
                            up.uploading =true;
                            up.process();
                        }		
                        return false;
                    },
                    process : function(){
                        up.processing =up.queue.shift();		
                        up.$drop.append('<div>'+up.processing.name+'</div>');		
                        if(window.FileReader){ //firefox 3.6, Chrome 6, Webkit
                            if(up.processing.type.match(/image/gi) !=null){ //is an image - read it
                                up.dataReader =new FileReader();
                                if(up.dataReader.addEventListener){ //firefox
                                    up.dataReader.addEventListener('loadend', up.binaryLoad, false);
                                    up.dataReader.addEventListener('error', up.loadError, false);
                                    up.dataReader.addEventListener('progress', up.loadProgress, false);
                                } else { //chrome / webkit
                                    up.dataReader.onloadend =up.binaryLoad;
                                    up.dataReader.onerror =up.loadError;
                                    up.dataReader.onprogress =up.loadProgress;
                                }
                                up.dataReader.readAsDataURL(up.processing);
                            }			
                            up.binaryReader =new FileReader();
                            if(up.binaryReader.addEventListener){ //firefox
                                up.binaryReader.addEventListener('loadend', up.binaryLoad, false);
                                up.binaryReader.addEventListener('error', up.loadError, false);
                                up.binaryReader.addEventListener('progress', up.loadProgress, false);
                            } else { //chrome / webkit
                                up.binaryReader.onloadend =up.binaryLoad;
                                up.binaryReader.onerror =up.loadError;
                                up.binaryReader.onprogress =up.loadProgress;
                            }
                            up.binaryReader.readAsBinaryString(up.processing);			
                        } else { // safari 5 + others?		
                            up.xhr.abort(); //make sure xhr is a new request
                            up.xhr.open('POST', 'easycreate_process.php?cod=<?=$ID?>&up=true', true);		
                            up.xhr.setRequestHeader('UP-FILENAME', up.processing.name);
                            up.xhr.setRequestHeader('UP-SIZE', up.processing.size);
                            up.xhr.setRequestHeader('UP-TYPE', up.processing.type);			
                            up.xhr.send(up.processing); 
                            up.xhr.onload =up.onload;
                        }		
                    },	
                    loadError : function(e){
                        switch(e.target.error.code){
                            case e.target.error.NOT_FOUND_ERR:
                                alert('File Not Found!');
                            break;
                            case e.target.error.NOT_READABLE_ERR:
                                alert('File is not readable');
                            break;
                            case e.target.error.ABORT_ERR:
                            break; 
                            default:
                                alert('An error occurred reading this file.');
                        };
                    },	
                    loadProgress : function(e){	if(e.lengthComputable){	var percentage =Math.round((e.loaded * 100) / e.total);		$('#status2').html('loaded: '+percentage+'%');	}	},		
                    binaryLoad : function(e){		
                        var isimage=(up.processing.type.match(/image/gi)!=null);
                        if( isimage && up.dataReader.readyState==2 && up.$drop.find('div:last').css('background-image')=='none'){
                            if(up.queue.length <=10) up.$drop.find('div:last').css('background-image','url(' + up.dataReader.result + ')');	
                        }
                        /*else 
                        {		
                            up.$drop.find('div:last').css('background-image','url(important.png)');	
                        }*/
                        if(isimage && up.dataReader.readyState==2 && up.binaryReader.readyState==2 || !isimage && up.binaryReader.readyState==2 )
                        {			
                            up.xhr.abort(); //make sure xhr is a new request			
                            var binary =e.target.result;			
                            if(up.xhr.sendAsBinary !=null)
                            { //firefox
                                up.xhr.open('POST', 'easycreate_process.php?cod=<?=$ID?>&up=true&size='+e.total+'&drop_settings='+drop_settings+'&lista='+lista+'&fn='+up.processing.name, true);
								//window.open('easycreate_process.php?cod=<?=$ID?>&up=true&size='+e.total+'&drop_settings='+drop_settings+'&lista='+lista, '_blank');
                                if(drop_settings==2)drop_settings=1; // reset
                                if(drop_settings==4)drop_settings=3; // reset
                                
                                
                                var boundary='xxxxxxxxx';			    
                                var body='--' + boundary + "\r\n";  
                                body +="Content-Disposition: form-data; name='upload'; filename='" + up.processing.name + "'\r\n";  
                                body +="Content-Type: application/octet-stream\r\n\r\n";  
                                body +=binary + "\r\n";  
                                body +='--' + boundary + '--';			    
                                up.xhr.setRequestHeader('content-type', 'multipart/form-data; boundary=' + boundary);
                                up.xhr.sendAsBinary(body);
                                
                                up.xhr.onreadystatechange =function(){
                                    if(up.xhr.readyState==4){
                                        up.dataReader.result='';
                                        if(up.xhr.responseText=="done")
                                        {
											if(up.queue.length <=0)
                                            {													
                                                up.upload_end=1;
                                                alert("Todos os ficheiro foram inseridos/actualizados com sucesso");
												window.open('products_families.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&total_prods=<?=$total_prods?>&checkshowimages=<?=$checkshowimages?>','_self');
                                            }
                                        }
                                        else alert(up.xhr.responseText+" - ERRO A Enviar "+up.processing.name);
                                    }
                                }
                            }
                            else
                            { //nao foi possivel enviar em modo binario
                                up.xhr.open('POST','easycreate_process.php?cod=<?=$ID?>&up=true&base64=true', true);			 
                                up.xhr.setRequestHeader('UP-FILENAME', up.processing.name);
                                up.xhr.setRequestHeader('UP-SIZE', up.processing.size);
                                up.xhr.setRequestHeader('UP-TYPE', up.processing.type);			 	
                                up.xhr.send(window.btoa(binary)); 
                            }			 
                            up.xhr.onload =up.onload;			
                        }
                    },	
                    uploadProgress : function(e){	if(e.lengthComputable){	var percentage =Math.round((e.loaded * 100) / e.total);		$('#status2').html('uploaded: '+percentage+'%');	}	},	
                    uploadLoaded : function(e){	$('#status2').html('File Uploaded: 99%');},	
                    onload : function (e){	if(up.queue.length > 0 || up.upload_end==0){		up.process();	} else {		up.uploading =false;		$('#status2').html('Queue Uploaded');
                    
                    }	},	
                    cancel : function(e){	
                        if(up.dataReader){		up.dataReader.abort();	}
                        if(up.dataReader){		up.binaryReader.abort();}
                        if(up.xhr){			up.xhr.abort();	}
                        up.uploading =false;
                        up.queue=[];
                        up.processing =null;
                        $('#status2').html('O Upload foi Cancelado!!!');
                        return false;
                    }
                }
                $(up.init);
                
	        			</script>
                    <div id="status2">Arraste foto para criar produto</div>
                    <div id="drop-area2" name="drop-area2"></div>
					</td></tr>
					</table>
				</td></tr></table>