<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)
{
	exit();
}
function formatdata($data = '')
{
	global $site_lingua;
	return strftime('%d %B, %Y',strtotime($data));
}
function show_portfolio2()
{
	show_portfolio(1);
}
function show_portfolio($all=0)
{
    global $site_lang,$site_http_url,$site_shell_url;
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;

	$page_id=intval($_GET['page_id']);
	$news1_id=intval($_GET['news1_id']);
	$id_fam=intval($_GET['id_fam']);

	if($news1_id)
	{
		$DESTAQUE='';
		$ID_ORG='';
	    if($id_fam != '') $ID_ORG = "AND ID_ORG=".$id_fam;
	    else $DESTAQUE= 'AND Destaque=1';

		if($_GET['prev']==1)
		{
			if($id_fam)
			{
				$sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1,news1_lang.Texto2,news1_lang.Texto3 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG=".$id_fam." order By Ordem DESC,news1.ID DESC";
			}
			else $sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1,news1_lang.Texto2,news1_lang.Texto3 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 order By Ordem DESC,news1.ID DESC";

			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$found=0;

			if(!$rs->EOF() && !$found)
			{
				$prod_id_novo=0;
				$prod_id_primeiro=$rs->db_data['ID'];


				while(!$rs->EOF() && !$found)
				{
					if($prod_id_novo==$news1_id)$found=1;//E vai buscar o proximo:
					$prod_id_novo=$rs->db_data['ID'];
					$rs->db_next();
				}
				$news1_id=$prod_id_novo;
			}
			$rs->db_close();
		}
		elseif($_GET['next']==1)
		{
			if($id_fam)
			{
				$sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1,news1_lang.Texto2,news1_lang.Texto3 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG=".$id_fam." order By Ordem ASC,news1.ID ASC";
			}
			else $sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1,news1_lang.Texto2,news1_lang.Texto3 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 order By Ordem ASC,news1.ID ASC";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$found=0;

			if(!$rs->EOF() && !$found)
			{
				$prod_id_novo=0;
				$prod_id_primeiro=$rs->db_data['ID'];


				while(!$rs->EOF() && !$found)
				{
					if($prod_id_novo==$news1_id)$found=1;//E vai buscar o proximo:
					$prod_id_novo=$rs->db_data['ID'];
					$rs->db_next();
				}
				$news1_id=$prod_id_novo;
			}
			$rs->db_close();
		}
		if($news1_id)
		{
			/* Página do "produto" */
			$sql="SELECT news1.ID,news1.Data_lance,news1.ID_ORG,news1.created_at,news1.img_ext,news1.img2_ext,news1.img3_ext,news1_lang.Cabecalho,news1_lang.Texto1,news1_lang.Texto2,news1_lang.Texto3 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 and news1.ID=$news1_id";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if(!$rs->EOF())$news1_id=$rs->db_data['ID'];
		}
		if(!$rs->EOF())
		{
		    $img='layout/no-image-port.jpg';
			$img2='';
			$titulo=$rs->db_data['Cabecalho'];
			$created_at=explode(" ",$rs->db_data['created_at']);
			$Data_lance=explode(" ",$rs->db_data['Data_lance']);
			$vc=explode("-",$Data_lance);
			$thisdata = $Data_lance[0];
			if(intval($vc[0]==0)){
				$thisdata = $created_at[0];
			}

			$texto2=str_replace('&trade;','<sup>TM</sup>',limpa_editor_lixo($rs->db_data['Texto2']));
			$texto3=str_replace('&trade;','<sup>TM</sup>',limpa_editor_lixo($rs->db_data['Texto3']));

			echo '<style>#portfolio_zoom{background-color:#76787D}#fundo img {top: -17px !important}</style><input type="hidden" id="news1_id" value="'.$news1_id.'">
			<div class="container" style="padding-top:12px;padding-bottom:50px">
			<div class="row">
				<div class="col-xs-12 col-md-6 col-sm-6" style="text-align:right">
					<div id="portfolio_img" class="page-slider-wrap page-slider-wrap modulocss" data-bullets="1" data-thumbs="1" data-arrows="1" data-height="150">
						<div class="page-slider page-slider modulo">
							<ul>';

                                $sql3="SELECT * FROM news1_images WHERE ID_ORG='$news1_id'";
                                $rs3->db_open($db_link, $GLOBALS['DB_name'], $sql3);
                                $thumbs='';
                                $c=0;
                                while(!$rs3->EOF())
                                {
									$c++;
                                    $img_ext = $rs3->db_data['img_ext'];
                                    $ordem = $rs3->db_data['Ordem'];

                                    //if($img_ext && stream_resolve_include_path($site_shell_url.'files/news1/'.$news1_id.'_'.$ordem.'.'.$img_ext)) $img=$site_http_url.'files/news1/'.$news1_id.'_'.$ordem.'.'.$img_ext;

									$img_file_med_SEO='files/news1/'.$news1_id.'_'.$ordem;
                                    if($img_ext && stream_resolve_include_path($site_shell_url.'files/news1/'.$news1_id.'_'.$ordem.'.'.$img_ext)) $img = 'i/'.$img_file_med_SEO.'/'.limpa_special(stripslashes($rs->db_data['Cabecalho'])).'.'.$img_ext.'?dp='.dumpcache;else $img_file_med = $GLOBALS['site_http_url'].'layout/no_photo2.png';

                                    ?>
                                    <li
                                    	data-transition="fade"
                                    	data-masterspeed="800">
                                        <img src="<?=$img?>" alt="<?=$titulo?>" data-bgposition="center center" data-bgrepeat="no-repeat" alt="<?=$titulo?>">
                                    </li>
                                    <?php
                                    $thumbs.='<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 image_small"><img src="'.$img.'" alt="" title="" border="0" onclick="event_click('.$c.')"></div>';
                                    $rs3->db_next();

                                }
                                $rs3->db_close();

								echo '
							</ul>
							<div class="tp-bannertimer tp-bottom"></div>

						</div>
					</div>';

					if($c>1)
					{

						echo '<div class="row">'.$thumbs.'</div>';
					}


				echo '
				</div>
				<div class="col-xs-12 col-md-6 col-sm-6 newsrightside">
					<div class="content" style="color:#000;font-size:16px">';

					/* Familia do "produto" */
					$sql="SELECT news1_families_lang.Cabecalho FROM news1_families_lang WHERE Lang='$site_lang' AND ID_PAI=".$rs->db_data['ID_ORG'];
					$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
					if(!$rs2->EOF())$Familia=$rs2->db_data['Cabecalho'];
					$rs2->db_close();

					echo '<b><h2 style="margin: 0;padding: 0;text-indent: 0;text-align: left;font: bold 15px/20px Open Sans;text-transform: none;text-decoration: none;color: #b3b3b3;letter-spacing: normal"><img src="' . $site_http_url . 'layout/' . PROJETO . '/images/composite-path-3.png" style="margin: -7px 3px 0px 0px;max-width: 15px;"> '.formatdata($created_at[0]).'</h2></b><br>';
					echo '<b><h1 style="margin: 0;padding: 0;text-indent: 0;text-align: left;font: bold 31px/36px Open Sans;text-transform: none;text-decoration: none;color: #fc7714;letter-spacing: normal">'.$rs->db_data['Cabecalho'].'</h1></b><br>';
					echo '<div id="caminho-12"></div>';
					echo hilight_keywords($texto2).'<br><br>';
					echo hilight_keywords($texto3);
					echo '</div>
				</div>
				<BR><BR>

			</div>';
			echo '</div>';

			echo '<script type="text/javascript">
				$(function () {
					var data= $(\'.page-slider-wrap\').data();
					if(data == undefined || data.height == undefined || data.height == 0 || data.height == \'\') height=550;
					else height=data.height;

					var arrows=\'solo\';
					if(data == undefined || data.arrows == undefined || data.arrows == 0 || data.arrows == \'\') arrows=\'none\';
					var bullets=\'bullet\';
					if(data == undefined || data.bullets == undefined || data.bullets == 0 || data.bullets == \'\') bullets=\'none\';

					$(\'.page-slider\').revolution({

						delay           :2000,
						startheight     :487,
						hideThumbs      :300,
						touchenabled    :\'on\',
						onHoverStop     :\'on\',
						startwidth      :$(\'body\').hasClass(\'w970\') ? 800 :800,
						navOffsetHorizontal :0,
						navOffsetVertical   :0,
						navigationVAlign	:\'bottom\',
						navigationVOffset	:	5,
						minFullScreenHeight :\'320\',
						navigationType:bullets,
						navigationArrows:\'none\',
						autoplay:1

					}).bind(\'revolution.slide.onloaded\',function (e,data) {
						$(this).parent().css({
							background  :\'white\',
							height      :\'auto\',
							overflow    :\'hidden\'
						}).children().animate({opacity :1},500);
					});
				});
			</script>';
		}
		$rs->db_close();
	}
	else
	{
		show_portfolio_elementos($all);
	}
}

function show_portfolio_elementos($all=0)
{
	global $site_lang,$site_http_url,$site_shell_url;
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$rs2=new DataObj;
	$page_id=11;
	if($_GET["tag"])$tag=intval($_GET["tag"]);
	$id_fam=intval($_GET['id_fam']);
	?>
	<div id="portfolio">
		<div id="portfolio_items" style="margin-top:40px;margin-bottom:0">
		<div class="title title-section"></div>
		<div id="caminho-11"></div>

		<div>
			<?php
			if($id_fam==1) show_content(331);
			if($id_fam==2) show_content(332);
			if($id_fam==3) show_content(333);
			if($id_fam==4) show_content(334);
			if(!$id_fam) show_content(293)?>
		</div>

		<section class="portfolio-projects change-badge" id="anchor">
			<div class="project-filter" data-grid="#projects">
				<div style="position:relative;top:0;text-align:center">
					<ul style="width:100%;" class="filter">
					<?php if(!$tag){?>

						<li class="branding<?php if(!$tag) echo'2'; ?>"><a style="display:inline-block;margin-left: 15px;margin-right: 15px" data-categories="d" class="titulo_familia_portfolio"><?= getLinguas('key_todas', $GLOBALS['site_lang'])?></a></li>

					<?php }else{ ?><li class="branding" onclick="window.open('<?=$site_http_url.seo('pages.php?page_id='.$page_id.'&id_fam=0')?>','_self')"><a href="<?=$site_http_url.seo('pages.php?page_id='.$page_id.'&id_fam=0')?>" style="display:inline-block;margin-left: 15px;margin-right: 15px" class="titulo_familia_portfolio"><?= getLinguas('key_todos', $GLOBALS['site_lang'])?></a></li>
						<?php }
						$sql="SELECT news1_families.ID,news1_families.ID_ORG,news1_families_lang.Cabecalho FROM news1_families INNER JOIN news1_families_lang ON news1_families_lang.ID_PAI=news1_families.ID WHERE Lang='".$GLOBALS['site_lang']."' AND ID_ORG=0 AND visivel=1 order By Ordem ASC ";

						$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
						while(!$rs->EOF())
						{
							?>
							<li class="branding<?php if($tag==$rs->db_data['ID']) echo '2'; ?>" onclick="window.open('<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?tag='.$rs->db_data['ID'].'&id_fam='.$rs->db_data['ID']; ?>','_self')">
								<a href="<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?tag='.$rs->db_data['ID'].'&id_fam='.$rs->db_data['ID']; ?>" class="titulo_familia_portfolio" style="display:inline-block"><?=$rs->db_data['Cabecalho']?></a>
							</li>
							<?php
							$rs->db_next();
						}
						$rs->db_close();
						?>
					</ul>
				</div>
			</div>
			<div class="clear" style="min-height:60px"></div>
			<div class="grid-wrap">
				<section id="projects" class="grid" data-columns="3" data-masonry='{ "gutterWidth":40 }'><?php
					$rs=new DataObj;
					$rs2=new DataObj;
					$produtos=array();
					$n_fam=0;
					$n_max_produtos=0;
					$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
					if($all==1)
					{
						if($tag)
							$sql="SELECT news1.ID, news1.created_at,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG=".$tag." AND  ((NOW() BETWEEN news1.Data_inicio AND news1.Data_fim) OR news1.permanente=1 ) order By Ordem ASC,news1.ID ASC";
						else
						{
							$sql="SELECT news1.ID_ORG FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 group by ID_ORG";
							$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
							while(!$rs2->EOF())
							{
								$ID_ORG=$rs2->db_data['ID_ORG'];
								$n_produtos=0;

								$sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE ID_ORG='$ID_ORG' AND Lang='$site_lang' AND Aproved=1 order By Ordem ASC,news1.ID ASC";
								$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
								while(!$rs->EOF())
								{
									$produtos[$n_fam][]=$rs->db_data['ID'];
									$n_produtos++;
									$rs->db_next();
								}
								$rs->db_close();

								if($n_produtos > $n_max_produtos)$n_max_produtos=$n_produtos;



								$rs2->db_next();
								$n_fam++;
							}
							$rs2->db_close();


							//Aqui queremos saltar registos
							$sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 order By Ordem ASC,news1.ID ASC";
						}
					}
					else
					{
						$sql="SELECT news1.ID_ORG FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 group by ID_ORG";
						$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
						while(!$rs2->EOF())
						{
							$ID_ORG=$rs2->db_data['ID_ORG'];
							$n_produtos=0;

							$sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE ID_ORG='$ID_ORG' AND Lang='$site_lang' AND Aproved=1  AND Destaque=1 ORDER BY RAND()";
							$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
							while(!$rs->EOF())
							{
								$produtos[$n_fam][]=$rs->db_data['ID'];
								$n_produtos++;
								$rs->db_next();
							}
							$rs->db_close();

							if($n_produtos > $n_max_produtos)$n_max_produtos=$n_produtos;



							$rs2->db_next();
							$n_fam++;
						}
						$rs2->db_close();

                        $sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1 FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND Aproved=1 AND Destaque=1";
                        $all=1;
                        $n_max_produtos=7;//Numero de linhas de produto ( 1 = 4 )
                        $n_max_produtos=4;//Numero de linhas de produto ( 1 = 4 )
                        $tag='';
                    }

					if($all==1 && !$tag)
					{

						$i=0;
						while($i<$n_max_produtos)
						{
							//$n_fam++;
							$j=0;
							while($j<$n_fam)
							{
								if($produtos[$j][$i]) //Se existir o produto
								{
									//echo $produtos[$j][$i].'<br>';

										$sql="SELECT news1.ID,news1.Destaque,news1.ID_ORG,news1_lang.Cabecalho,news1_lang.Texto1, news1.created_at, news1.Data_lance FROM news1 inner join news1_lang on news1_lang.ID_PAI=news1.ID WHERE Lang='$site_lang' AND news1.ID=".$produtos[$j][$i]." AND  ((NOW() BETWEEN news1.Data_inicio AND news1.Data_fim) OR news1.permanente=1 ) ";
										/*if($produtos[$j][$i]==11)
											echo $sql;*/
										$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);

										if(!$rs->EOF())
										{
											$d=$rs->db_data['Destaque'];
											$c_a = explode(" ",$rs->db_data['Data_lance']);
											$class='';
											if($d)$class='d';
											$imgs=createProductImg($rs->db_data['ID']);
											if($imgs[0]['med'])
											{
												$texto=str_replace('&trade;','<sup>TM</sup>',limpa_editor_lixo($rs->db_data['Texto1']));

											?>
												<article class="project project-animated <?=$class?> tag_<?=$rs->db_data['ID_ORG']?>">
													<div class="project-heading">
														<div class="thumbnail">
															<?php if(!$tag){
																$src="layout/blank.jpg";
																$onclick = 'onclick="window.open(\''.$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$id_fam.'\',\'_self\')"';

															}else{

																$onclick = 'onclick="window.open(\''.$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$tag.'\',\'_self\')"';

															}

															$src = $imgs[0]['med'];
															?>
															<div class="img-content" style="background: url('<?=$src?>');background-size: cover;background-position: center;cursor:pointer; " <?=$onclick?>>



														</div>
															<div class="info-news" style="margin:0 auto;color:#000;font-size:18px">
																<p style="margin-top:10px" class="text-style-3">

																	<div class="data-news">


																		<img src="<?=$site_http_url?>layout/<?=PROJETO?>/images/composite-path-3.png" style="margin: -7px 3px 0px 0px;max-width: 15px;">
																		<?php echo formatdata($c_a[0]);?>
																	</div>
																	<a class="cabecalho" href="<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$tag; ?>">
																		<?=$rs->db_data['Cabecalho']?>
																	</a>
																</p>
																<a href="<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$tag; ?>">
																<p class="text-style-1">

																	<?=hilight_keywords($texto)?>

																	</p>
																</a>
															</div>
														</div>
													</div>
												</article>
											<?php
											}
										}
										$rs->db_close();





								}
								$j++;
							}

							$i++;
						}
						//print_r($produtos);
					}
					else
					{

						$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
						while(!$rs->EOF())
						{
							$d=$rs->db_data['Destaque'];
							$c_a = explode(" ",$rs->db_data['Data_lance']);
							$class='';
							if($d)$class='d';
							$imgs=createProductImg($rs->db_data['ID']);
							if($imgs[0]['med'])
							{
								$texto=str_replace('&trade;','<sup>TM</sup>',limpa_editor_lixo($rs->db_data['Texto1']));

								?>
								<article class="project project-animated <?=$class?> tag_<?=$rs->db_data['ID_ORG']?>">
									<div class="project-heading">
										<div class="thumbnail">
											<?php if(!$tag){
																$src="layout/blank.jpg";
																$onclick = 'onclick="window.open(\''.$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$id_fam.'\',\'_self\')"';

															}else{

																$onclick = 'onclick="window.open(\''.$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$tag.'\',\'_self\')"';

															}

															$src = $imgs[0]['med'];
															?>
															<div class="img-content" style="background: url('<?=$src?>');background-size: cover;background-position: center;cursor:pointer; " <?=$onclick?>>



														</div>
															<div class="info-news" style="margin:0 auto;color:#000;font-size:18px">
																<p style="margin-top:10px" class="text-style-3">

																	<div class="data-news">
																		<img src="<?=$site_http_url?>layout/<?=PROJETO?>/images/composite-path-3.png" style="margin: -7px 3px 0px 0px;max-width: 15px;">
																		<?php echo formatdata($c_a[0]);?>
																	</div>
																	<a class="cabecalho" href="<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$tag; ?>">
																		<?=$rs->db_data['Cabecalho']?>
																	</a>
																</p>
																<a href="<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?news1_id='.$rs->db_data['ID'].'&id_fam='.$tag; ?>">
																<p class="text-style-1">

																	<?=hilight_keywords($texto)?>

																	</p>
																</a>
															</div>
										</div>
									</div>
								</article>
							<?php
							}
							$rs->db_next();
						}
						$rs->db_close();
					}
					?>
				</section>
			</div>
			<div class="clear"></div>
		</section>
		</div>
		<div id="portfolio_zoom" style="display:none">
		</div>
	</div>
	<script>
		$( document ).ready(function() {
			$(".portfolio-projects article .img-content").css("width","100%");
			var w = $(".portfolio-projects article .img-content").width();
			if(w>500) w =500;
			$(".portfolio-projects article .img-content").height((w*0.6)+"px");
			$(".portfolio-projects article .img-content").width(w+"px");
		});

		$( window ).resize(function() {
			$(".portfolio-projects article .img-content").css("width","100%");
			var w = $(".portfolio-projects article .img-content").width();
			if(w>500) w =500;
			$(".portfolio-projects article .img-content").height((w*0.6)+"px");
			$(".portfolio-projects article .img-content").width(w+"px");
		});
		//.portfolio-projects article .img-content
	/*$(function() {
    $("img.lazy").lazyload();
	});*/
	</script>
	<?php
}

function createProductImg($news1_id)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$return=array();

	$sql="SELECT * FROM news1 WHERE ID=$news1_id";
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		for($i=1; $i < 8; $i++)
		{
			$s=($i == 1) ? '' : $i;
			if($rs->db_data['img'.$s.'_ext'])
			{
				$I1=$rs->db_data['img'.$s.'_ext'];
				$img_file='files/news1/'.$news1_id.'_'.$i.'.'.$I1;
				if($i == 1)
				{
					$img_file_cart='files/news1/'.$news1_id.'_'.$i.'_cart.'.$I1;
					$img_file_med='files/news1/'.$news1_id.'_'.$i.'_med.'.$I1;
					if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_file)) $original=filemtime($GLOBALS['site_shell_url'].$img_file); // data/hora em que foi modificada/criada
					if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_file_cart)) $cart=filemtime($GLOBALS['site_shell_url'].$img_file_cart); //data/hora em que foi modificada/criada
					if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_file_med)) $med=filemtime($GLOBALS['site_shell_url'].$img_file_med); //data/hora em que foi modificada/criada


					if($cart<$original)	redimencionaImagem($GLOBALS['site_shell_url'],$img_file,320,189,$img_file_cart);



					//$img_file_med = str_replace('jpg','png',$img_file_med);
					unlink($GLOBALS['site_shell_url'].$img_file_med);
					redimencionaImagem($GLOBALS['site_shell_url'],$img_file,450,254,$img_file_med);

					//if($med<$original)	redimencionaImagem($GLOBALS['site_shell_url'],$img_file,450,254,$img_file_med);

					if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_file_cart) && $I1!='') $img_file_cart=$GLOBALS['site_http_url'].$img_file_cart.'?dp='.$cart;
					else $img_file_cart= '';
					if(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_file_med) && $I1!='') $img_file_med=$GLOBALS['site_http_url'].$img_file_med.'?dp='.$med;
					else $img_file_med='';
					$return[$i-1]['cart']=$img_file_cart;
					$return[$i-1]['med']=$img_file_med;
				}

				$return[$i-1]['orig']=(stream_resolve_include_path($GLOBALS['site_shell_url'].$img_file)) ? $img_file : '';

			}
		}
	}

	$rs->db_close();

	return $return;
}

function hilight_keywords($txt)
{
	return $txt;
}

function show_videos_lista($id_fam=0,$tipo=0)
{

    global $site_shell_url, $from;
    if (!$from)$from=0;
    $str='';
    $videos=0;
    $str .= '<div class="noticias_area row">';


    global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
    $qtd=3;
    $db_link=db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs=new DataObj;

    $and='';
    if (isset($_GET['tag']))$and .= ' Texto3 LIKE "%' . $_GET['tag'] . '%" AND';

    $sql="SELECT pages.*,Cabecalho,Texto,Texto2,Texto3,Lang,video_id FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE $and Tipo=2 and Visible=1 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' and ID_ORG=$id_fam order by Ordem Asc, Data1 Asc, ID Asc";
    $c=0;
    $rs->db_open($db_link, $DB_name, $sql);
    while (!$rs->EOF())
    {
        $C=stripslashes($rs->db_data['Cabecalho']);
        if ($C)
        {
            //$C=strlen($C) > 30 ? substr($C, 0, 27) . " ..." : $C;
        }
        $T=limpa_editor_lixo(stripslashes($rs->db_data['Texto']));
        $T2=limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));

        $T3=limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
        $page_id=$rs->db_data['ID'];
        $I1=$rs->db_data['img_ext'];
        $data1=$rs->db_data['Data1'];
        $video_id=$rs->db_data['video_id'];
        $existe=0;
        $obj2=new DataObj;
        $sql="SELECT * FROM video_menager_link WHERE video_id='" . $video_id . "'";
        $obj2->db_open($db_link, $DB_name, $sql);
        if (!$obj2->EOF())
        {
            $existe=1;
        }
        $obj2->db_close();
        if ($video_id > 0 && $existe == 1)
        {
            $img_file="files/videos/" . $video_id . "_1.png";
            $img_file_peq="files/videos/" . $video_id . "_2.png";
            if (stream_resolve_include_path($site_shell_url . $img_file))
            {
                $original=date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
            } // data/hora em que foi modificada/criada
            if (stream_resolve_include_path($site_shell_url . $img_file_peq))
            {
                $peq=date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
            } //data/hora em que foi modificada/criada
            if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
            {
                if (redimencionaImagem($site_shell_url, $img_file, 150, 110, $img_file_peq))
                {
                    cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 142, 103);
                }
            }
            if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
            {
                $img='';
            }
            else
            {
                $img=$m_http_url_images . 'videos/' . $video_id . '_2.png?d=' . uniqid();
            }
        }
        else
        {
            $img_file="files/pages/" . $page_id . "_1." . $I1;
            $img_file_peq="files/pages/" . $page_id . "_2." . $I1;
            if (stream_resolve_include_path($site_shell_url . $img_file))
            {
                $original=date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
            } // data/hora em que foi modificada/criada
            if (stream_resolve_include_path($site_shell_url . $img_file_peq))
            {
                $peq=date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
            } //data/hora em que foi modificada/criada
            if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
            {
                if (redimencionaImagem($site_shell_url, $img_file, 150, 110, $img_file_peq))
                {
                    cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 142, 103);
                }
            }
            if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
            {
                $img=$GLOBALS['site_http_url'] . 'layout/no_photo_news.png';
            }
            else
            {
                $img=$m_http_url_images . 'pages/' . $page_id . '_1.' . $I1 . '?d=' . uniqid();
            }
        }

        // premios
        if($tipo==1)
        {
            $str .= '

                    <div class="row">
                        <div class="noticia col-md-4 col-xs-12">
                            <a href="' . seo('index.php?page_id=' . $page_id) . '"><img src="layout/blank.jpg" class="lazy" data-original="' . $img . '" alt="" /></a>
                        </div>
                        <div class="noticia col-md-8 col-xs-12">
                            <p class="orange b" style="margin-bottom:15px">' . $C . '</p>
                            <p class="black">' . $T2 . '</p>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <br><br><br><br>

                ';

        }

        //noticias
        if(!$tipo)
        {
            $str .= '
                <div class="noticia">
                    <div class="noticia_img"><img src="' . $img . '" alt="" /></div>
                    <div class="noticia_conteudo">
                        <p class="black">' . $T2 . '</p>
                    </div>
                </div>
                <div class="clear"></div>
                <br><br><br><br>';
        }

        if($tipo==2)
        {
            $str .= '
                <div class="noticia">
                    <div class="noticia_img"><img src="' . $img . '" alt="" /></div>
                    <div class="noticia_conteudo">
                        <p class="orange b" style="font: bold 21px/26px Raleway;margin-bottom:15px">' . $C . '</p>
                        <p class="black">' . $T2 . '</p>
                        <p class="black2">' . $T . '</p>
                        <p class="orange2">' . $T3 . '</p>
                    </div>
                </div>
                <div class="clear"></div>
                <br><br><br><br>';
        }

        //testemunhos
        if($tipo==3)
        {
            $str .= '
                <div class="noticia col-md-4 col-xs-12">
                    <div class="row">
                        <a href="' . seo('index.php?page_id=' . $page_id) . '"><img src="' . $img . '" alt="" /></a>
                        <p class="orange b" style="margin-bottom:15px">' . $C . '</p>
                    </div>
                </div>
                ';
        }
        //noticias
        if(!$tipo)
        {
            $str .= '
                <div class="noticia">
                    <div class="noticia_img"><img src="' . $img . '" alt="" /></div>
                    <div class="noticia_conteudo">
                        <p class="black">' . $T2 . '</p>
                    </div>
                </div>
                <div class="clear"></div>
                <br><br><br><br>';
        }

        if($tipo==4) //videos de ajuda
        {
        	$a='';
        	if($T3 !='')
        	{
        		$href='';
        		if(substr_count($T3,'http'))
        		{
        			$href=$T3;
        			$a = '<img style="width:100%; max-height: 360px;" src="' . $img . '" alt="" />';
        			$href = '<a class="fancy_content" href="'.$href.'?autoplay=1" style="display:block">';
        			$titulo = $C;
        		}
        		else
        		{
        			$a = '<img style="width:100%;" src="' . $img . '" alt="" />';
        		}
        	}
        	else
    		{
    			$a = '<img style="width:100%;" src="' . $img . '" alt="" />';
    		}
           	$str .= '<div class="noticia col-xs-12 col-sm-6 col-md-6">
            			'.$href.'
                    	<div class="noticia_img">'.$a.'</div>
                    	<div class="noticia_titulo">'.$titulo.'</div>
                    	</a>
                	</div>';
        }

        $rs->db_next();
        $c++;
    }
    $rs->db_close();

    $str .= '</div>';
    echo $str;
}
?>