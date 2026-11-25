<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function show_portfolio3_destaques($idTrab=0)
{
	$page_id=140;
	global $site_lang, $site_http_url, $site_shell_url;
	$rs=new DataObj();
	$dbo=new DataObj();
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$and='';
	if($idTrab > 0)$and=" AND pf.ID='$idTrab'";

	$sql="SELECT pf.ID,pfl.Cabecalho FROM portfolio3_families pf inner join portfolio3_families_lang pfl on pfl.ID_PAI=pf.ID inner join portfolio3 p on p.ID_ORG=pf.ID WHERE pfl.Lang='$site_lang' AND pf.visivel=1 AND p.destaque=1 AND p.Aproved=1 $and GROUP BY pf.ID ORDER BY pf.Ordem,pfl.Cabecalho";
	$dbo->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$dbo->EOF())
	{
		?>
		<style>
			.ult-trabs-wrapper{padding: 200px 0}
			.ult-trabs-wrapper .filter .ult-filter-elem{cursor: pointer;color:#ADADAD}
			.ult-trabs-wrapper .filter .active .ult-filter-elem{color:#000}
			.ult-trabs a{width: 100%;text-align:center;display: block;color:#000 !important;border: solid 0px rgba(65,155,28,0.8)}
			.ult-trabs .grid_project_title{margin-top: 10px;
				font-family: raleway,sans-serif!important;
				text-transform: uppercase;
				font-weight: 700 !important;
				font-size: 13px;
				line-height: 1.2}
			.ult-trabs img{max-width: 100%}
			.ult-trabs a:focus, .ult-trabs a:hover {
				opacity: .6;
				cursor: pointer;
				color: #7a7a7a !important;
			}
		</style>
		<script type="text/javascript">
		$(document).ready(function(e){
			$('.ult-filter-elem').on('click',function(){
				var scroll=$(window).scrollTop();
				$('.ult-filter-elem').parent('li').removeClass('active');
				$(this).parent('li').addClass('active');
				var data=$(this).data();
				$('.ult-trabs-wrapper .carousel-wrap').hide();
				$('#ult-trabs-'+data.ultId).show();
				$('.carousel').trigger('resize');
				$(window).scrollTop(scroll);
				if (e.preventDefault) e.preventDefault();
			});});
		</script>
		<div class="ult-trabs-wrapper">
			<div class="container">
				<div class="title title-section"><h2 class="titulos_negro"><?=$GLOBALS['key_portfolio3_destaques_titulo'][$site_lang]?></h2></div>
				<div class="text-center">
					<ul class="filter" style="margin: 40px 0;padding:0">
						<?php
						$p=0;
						while(!$dbo->EOF())
						{
							if($p == 0) echo "<li class='active'><div class='ult-filter-elem' data-ult-id='{$dbo->db_data['ID']}'>{$dbo->db_data['Cabecalho']}</div></li>";
							else echo "<li><div class='ult-filter-elem' data-ult-id='{$dbo->db_data['ID']}'>{$dbo->db_data['Cabecalho']}</div></li>";
							$p++;
							$dbo->db_next();
						}?>
					</ul>
				</div>
		<?
		$sql="SELECT pf.ID,pfl.Cabecalho FROM portfolio3_families pf inner join portfolio3_families_lang pfl on pfl.ID_PAI=pf.ID inner join portfolio3 p on p.ID_ORG=pf.ID WHERE pfl.Lang='$site_lang' AND pf.visivel=1 AND p.destaque=1 AND p.Aproved=1 GROUP BY pf.ID ORDER BY pf.Ordem,pfl.Cabecalho";
		$dbo->db_open($db_link, $GLOBALS['DB_name'], $sql);
		$p=0;
		while(!$dbo->EOF())
		{
			$sql="SELECT p.ID,p.ID_ORG,pl.Cabecalho,pl.Texto1 FROM portfolio3 p inner join portfolio3_lang pl on pl.ID_PAI=p.ID WHERE pl.Lang='$site_lang' AND p.destaque=1 AND p.Aproved=1 AND p.ID_ORG='{$dbo->db_data['ID']}'";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$j=0;
			if(!$rs->EOF())
			{
				$total=4;
				?>
				<div class="carousel-wrap" id="ult-trabs-<?=$dbo->db_data['ID']?>" <?=($p > 0) ? 'style="display: none"':''?> >
					<div class="carousel" data-visible="<?=$total?>">
						<?php
						while(!$rs->EOF())
						{
							$imgs=createItemFolio3Img($rs->db_data['ID']);
							if($imgs[0]['med']){
							?>
							<article class="ult-trabs">
								<a href="<?=$site_http_url.seo('pages.php?page_id='.$page_id).'?prod_id='.$rs->db_data['ID']; ?>">
									<img src="<?=$imgs[0]['med']?>">
									<h4 class="text-center grid_project_title ellipsis"><?=$rs->db_data['Cabecalho']?></h4>
								</a>
							</article>
							<?php
							}
							$rs->db_next();
							$j++;
						}
						?>
					</div>
					<ul class="carousel-nav" style="text-align:center !important">
						<li><a href="#" class="btn btn-icon-prev prev"></a></li>
						<li><a href="#" class="btn btn-icon-next next"></a></li>
					</ul>
				</div>
				<?php
			}
			$rs->db_close();
			$p++;
			$dbo->db_next();
		}

		echo "</div>";
		echo "</div>";
	}
	$dbo->db_close();
}
function show_portfolio3()
{
	global $site_lang, $site_http_url, $site_shell_url;
	$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs=new DataObj; $rs2=new DataObj;

	$page_id=intval($_GET['page_id']);
	$prod_id=intval($_GET['prod_id']);
	$id_fam=intval($_GET['id_fam']);

	$page_id=140;

	if($prod_id)
	{
		$DESTAQUE='';
		$ID_ORG='';
	    if($id_fam != '') $ID_ORG = "AND ID_ORG=".$id_fam;
	    else $DESTAQUE= 'AND Destaque=1';

		if($_GET['prev']==1)
		{

			$sql="SELECT portfolio3.ID,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 $ID_ORG $DESTAQUE and portfolio3.ID<$prod_id order By portfolio3.ID DESC Limit 0,1";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if($rs->EOF())
			{
				$sql="SELECT portfolio3.ID, portfolio3.ID_ORG,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 $ID_ORG $DESTAQUE and portfolio3.ID>$prod_id Order by portfolio3.ID Desc Limit 0,1 ";
				$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
				if($rs->EOF())
				{
					$sql="SELECT portfolio3.ID,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 and portfolio3.ID=$prod_id";
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
				}
			}
			if(!$rs->EOF())$prod_id=$rs->db_data['ID'];
		}
		elseif($_GET['next']==1)
		{
			$sql="SELECT portfolio3.ID,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 $ID_ORG $DESTAQUE and portfolio3.ID>$prod_id order By portfolio3.ID ASC Limit 0,1";

			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if($rs->EOF())
			{
				$sql="SELECT portfolio3.ID,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 $ID_ORG $DESTAQUE and portfolio3.ID<$prod_id Order by portfolio3.ID Asc Limit 0,1 ";
				$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
				if($rs->EOF())
				{
					$sql="SELECT portfolio3.ID,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 and portfolio3.ID=$prod_id";
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
				}
			}

			if(!$rs->EOF())$prod_id=$rs->db_data['ID'];
		}
		else
		{
			$sql="SELECT portfolio3.ID,portfolio3.img_ext,portfolio3.img2_ext,portfolio3.img3_ext,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1,portfolio3_lang.Areas1,portfolio3_lang.Ano1,portfolio3_lang.Pais1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND Aproved=1 AND ID_ORG>0 and portfolio3.ID=$prod_id";
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if(!$rs->EOF())$prod_id=$rs->db_data['ID'];
		}
		if(!$rs->EOF())
		{
			$img='layout/no-image-port.jpg';
			$img2='';
			$titulo=$rs->db_data['Cabecalho'];

			if($rs->db_data['img_ext'] && dotbright_image_resolve($site_shell_url.'files/portfolio3/'.$prod_id.'_1.'.$rs->db_data['img_ext']))$img=$site_http_url.'files/portfolio3/'.$prod_id.'_1.'.$rs->db_data['img_ext'];
			if($rs->db_data['img2_ext'] && dotbright_image_resolve($site_shell_url.'files/portfolio3/'.$prod_id.'_2.'.$rs->db_data['img2_ext']))$img2=$site_http_url.'files/portfolio3/'.$prod_id.'_2.'.$rs->db_data['img2_ext'];
			if($rs->db_data['img3_ext'] && dotbright_image_resolve($site_shell_url.'files/portfolio3/'.$prod_id.'_3.'.$rs->db_data['img3_ext']))$img3=$site_http_url.'files/portfolio3/'.$prod_id.'_3.'.$rs->db_data['img3_ext'];


			echo '<style>#portfolio3_zoom{background-color:#76787D}</style>
			<div class="container">
			<div class="row"><input type="hidden" id="prod_id" value="'.$prod_id.'">

				<div style="text-align:right">
					<svg class="fancybox-portfolio3-previous" style="cursor:pointer" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                        <switch>
                            <path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M0,12L12.6,0L14,1.4L3.4,11.9L14,22.6L12.6,24L0,12"/>
                                <foreignObject width="24" height="24">
                                    <img class="previous" src="layout/icon-previous.png" alt="Previous" title="Previous" />
                                </foreignObject>
                        </switch>
                    </svg>
                    <svg class="fancybox-portfolio3-next" style="cursor:pointer" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 14 24" enable-background="new 0 0 14 24" xml:space="preserve">
                        <switch>
                        	<path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M14,12L1.4,24L0,22.6l10.6-10.7L0,1.4L1.4,0L14,12"/>
                            	<foreignObject width="24" height="24">
                                	<img class="next" src="layout/icon-next.png" alt="Next" title="Next" />
                                </foreignObject>
                        </switch>
                    </svg>
                    <svg class="fancybox-portfolio3-close" style="cursor:pointer" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="24px" viewBox="5 5 17 17" enable-background="new 0 0 27 27" xml:space="preserve">
						<switch>
                            <g>
								<path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M21.3,5L22,5.7L5.7,22L5,21.3L21.3,5z"/>
								<path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M5,5.7L5.7,5L22,21.3L21.3,22L5,5.7z"/>
                            </g>
                            <foreignObject width="22" height="22">
                                <img class="close" width="17" height="17" src="layout/icon-close.png" alt="Close" title="Close" />
                            </foreignObject>
                        </switch>
                    </svg>
                </div>
            </div>
			<div class="row">

				<div class="col-xs-12 col-md-6 col-sm-6" style="text-align:right">
				<div class="page-slider-wrap page-slider-wrap modulocss" data-bullets="1" data-arrows="1" data-height="150">
					<div class="page-slider page-slider modulo">
						<ul>';

							if($img && !$img2)echo '
							<li data-transition="fade" data-masterspeed="1300">
								<img src="'.$img.'" alt="bg" data-bgposition="center center" data-bgrepeat="no-repeat">
							</li>';

							if($img2)echo '
							<li data-transition="fade" data-masterspeed="1300">
								<img src="'.$img2.'" alt="bg" data-bgposition="center center" data-bgrepeat="no-repeat">
							</li>';

							if($img3)echo '
							<li data-transition="fade" data-masterspeed="1300">
								<img src="'.$img3.'" alt="bg" data-bgposition="center center" data-bgrepeat="no-repeat">
							</li>';

							echo '
						</ul>
						<div class="tp-bannertimer tp-bottom"></div>
					</div>
				</div><BR><BR></div>';

			echo '
			<div class="col-xs-12 col-md-6 col-sm-6">

				<div class="content" style="color:#000;font-size:16px">
					<b style="font-size:22px">'.$rs->db_data['Cabecalho'].'</b><br>
					'.$rs->db_data['Texto1'].'<br><br>
					<b>'.$GLOBALS['key_areas'][$GLOBALS['site_lang']].':</b><br>';
					$array_areas=explode(';',$rs->db_data['Areas1']);
					$i=0;
					$t=count($array_areas);
					while($i<$t)
					{
						echo $array_areas[$i].'<br>';
						$i++;
					}
					echo '
					<b>'.$GLOBALS['key_pais'][$GLOBALS['site_lang']].':</b><br>
					'.$rs->db_data['Pais1'].'<br><br>
					<b>'.$GLOBALS['key_year'][$GLOBALS['site_lang']].':</b><br>
					'.$rs->db_data['Ano1'].'
				</div>
			</div>
			</div>
			</div>';



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

						delay           :5000,
						startheight     :487,
						hideThumbs      :300,
						touchenabled    :\'on\',
						onHoverStop     :\'on\',
						startwidth      :$(\'body\').hasClass(\'w970\') ? 800 :800,
						navOffsetHorizontal :0,
						navOffsetVertical   :0,
						navigationVAlign	:\'bottom\',
						navigationVOffset	:	80,
						minFullScreenHeight :\'320\',
						navigationType:\'none\',
						navigationArrows:arrows,
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
		show_portfolio3_novo2();
	}
}

function show_portfolio3_novo2()
{
	global $site_lang, $site_http_url, $site_shell_url,$portfolio3_show_detalhe_popup;
	$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs=new DataObj; $rs2=new DataObj;
	$page_id=140;
	?>
	<style>
	.thumbnail {padding: 0px !important;margin-bottom: 20px;border: 0px !important;border-radius: 0px !important}
	.project ul, .project ol {margin: 0 !important;padding: 0 !important}
	.project-action:hover{background-color: rgba(65,155,28,0.8)}
	.project-action {display: flex;align-items: center;justify-content: center}
	.project-action li {width: 100%;top: 0;margin-top: 0;margin-left:0;left: 0;text-align: center;position: relative}
	.project.active .project-action li, .project:hover .project-action li {margin-left:0px !important}
	</style>
	<input type="hidden" name="fam_id" id="fam_id" value="0">
	<div id="portfolio3">
		<div id="portfolio3_items" style="margin-top:25px;margin-bottom:0">
		<!-- <div class="title title-section"><h2 class="caixa_sobrenos">portfolio3</h2></div> -->
		<section class="portfolio3-projects change-badge" id="anchor">

			<div class="project-filter" data-grid="#projects">
				<div style="position: relative;top:0px;text-align:center">
					<ul style="width:100%;" class="filter">
						<li><a onclick="document.getElementById('fam_id').value='0'" style="display: inline-block;" data-categories="d" class="d active"><?=getLinguas('key_todas', $GLOBALS['site_lang'])?></a></li>
						<?
							$sql="SELECT portfolio3_families.ID,portfolio3_families.ID_ORG,portfolio3_families_lang.Cabecalho FROM portfolio3_families INNER JOIN portfolio3_families_lang ON portfolio3_families_lang.ID_PAI=portfolio3_families.ID WHERE Lang='".$GLOBALS['site_lang']."' AND ID_ORG=0 AND visivel=1 order by portfolio3_families.Ordem";
							$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
							while(!$rs->EOF())
							{
								?>
								<li><a onclick="document.getElementById('fam_id').value='<?=$rs->db_data['ID']?>'" style="display: inline-block;font-size: 15px;" data-categories="tag_<?=$rs->db_data['ID']?>"><?=$rs->db_data['Cabecalho']?></a></li>
								<?php
								$rs->db_next();
							}
							$rs->db_close();

							?>
					</ul>
				</div>
			</div>
			<div class="clear" style="height: 30px"></div>


			<div class="grid-wrap">
				<section id="projects" class="grid" data-columns="6" data-masonry='{ "gutterWidth": 0  }' ><?php
					$rs=new DataObj;
					$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
					$sql="SELECT portfolio3.ID,portfolio3.Destaque,portfolio3.ID_ORG,portfolio3_lang.Cabecalho,portfolio3_lang.Texto1 FROM portfolio3 inner join portfolio3_lang on portfolio3_lang.ID_PAI=portfolio3.ID WHERE Lang='$site_lang' AND ID_ORG>0 AND Aproved=1 order By portfolio3.ID ASC";
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
					$j=0;

					$mobile=0;
					$useragent=$_SERVER['HTTP_USER_AGENT'];
					if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) $mobile=1;


					while(!$rs->EOF())
					{
						$d=$rs->db_data['Destaque'];
						$class="";
						if($d)$class='d';

						$imgs=createItemFolio3Img($rs->db_data['ID']);

						if($imgs[0]['med'])
						{
						?>
							<article class="project project-animated <?=$class?> tag_<?=$rs->db_data['ID_ORG']?>">
								<div class="project-heading">
									<div class="thumbnail"><img style="width:100%;max-width:700px;height:auto " src="<?php if($mobile)echo $imgs[0]['cart']; else echo $imgs[0]['med']?>" alt=""></div>
									<ul class="project-action">

										<?php
										if($portfolio3_show_detalhe_popup==1)
										{
										?>
										<li class="fancybox-portfolio3 portfolio3_grid_item">
											<a class="fancybox" rel="group<?=$rs->db_data['ID']?>" href="<?=$imgs[0]['orig']?>" >
												<div class="portfolio3_text">
													<div style="margin:0 auto;color:#fff;font-size:18px">
														<div style="margin:10px"><?=$rs->db_data['Cabecalho']?></div>
														<div class="clear" style="height:10px;width: 100%;clear: all"></div>
														<!-- <div class="btn-solution" data="parceiros"><?=$GLOBALS['key_maisdetalhes'][$GLOBALS['site_lang']]?></div> -->
													</div>
												</div>
											</a>
										</li><?
										}
										else
										{
										?>
										<li class="fancybox-portfolio3 portfolio3_grid_item" onclick='window.open("p149-portfolio3-<?=$GLOBALS['site_lang']?>?prod_id=<?=$rs->db_data['ID']?>","_top")'>
											<a>
												<div class="portfolio3_text">
													<div style="margin:0 auto;color:#fff;font-size:18px">
														<div style="margin:10px"><?=$rs->db_data['Cabecalho']?></div>
														<div class="clear" style="height:10px;width: 100%;clear: all"></div>
														<div class="btn-solution" data="parceiros"><?=$GLOBALS['key_maisdetalhes'][$GLOBALS['site_lang']]?></div>
													</div>
												</div>
											</a>
										</li><?
										}?>
									</ul>
								</div>
							</article>
						<?
						}
						$j = 1;
						while ($j < sizeof($imgs))
						{
							if($imgs[$j]['orig'] && $portfolio3_show_detalhe_popup==1)
							{
							?>
								<a class="fancybox" rel="group<?=$rs->db_data['ID']?>" href="<?=$imgs[$j]['orig']?>" ></a>
							<?
							}
							$j++;
						}

						$rs->db_next();

					}
					$rs->db_close();
					?>
				</section>
			</div>
			<div class="clear"></div>
			<script type="text/javascript">
				$('.grid').isotope({filter: '.d'});
			</script>
		</section>
		</div>
		<div id="portfolio3_zoom" style="display: none">
		</div>
	</div>
	<?php
}



function createItemFolio3Img($prod_id)
{

	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;

	$return=array();

	$sql="SELECT * FROM portfolio3 WHERE ID=$prod_id";
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		for($i=1; $i < 8; $i++)
		{
			$s=($i == 1) ? '' : $i;
			if($rs->db_data['img'.$s.'_ext'])
			{
				$I1=$rs->db_data['img'.$s.'_ext'];
				$img_file='files/portfolio3/'.$prod_id.'_'.$i.'.'.$I1;
				if($i == 1)
				{
					$img_file_cart='files/portfolio3/'.$prod_id.'_'.$i.'_cart.'.$I1;
					$img_file_med='files/portfolio3/'.$prod_id.'_'.$i.'_med.'.$I1;
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)) $original=dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file); // data/hora em que foi modificada/criada
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_cart)) $cart=dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_cart); //data/hora em que foi modificada/criada
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_med)) $med=dotbright_image_mtime($GLOBALS['site_shell_url'].$img_file_med); //data/hora em que foi modificada/criada

					if($cart<$original) redim_fill($GLOBALS['site_shell_url'],$img_file,220,220,$img_file_cart);
					if($med<$original)  redim_fill($GLOBALS['site_shell_url'],$img_file,900,800,$img_file_med);

					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_cart) && $I1!='') $img_file_cart=$GLOBALS['site_http_url'].$img_file_cart.'?dp='.dumpcache;
					else $img_file_cart= '';
					if(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file_med) && $I1!='') $img_file_med=$GLOBALS['site_http_url'].$img_file_med.'?dp='.dumpcache;
					else $img_file_med='';
					$return[$i-1]['cart']=$img_file_cart;
					$return[$i-1]['med']=$img_file_med;
				}

				$return[$i-1]['orig']=(dotbright_image_resolve($GLOBALS['site_shell_url'].$img_file)) ? $img_file : '';

			}
		}
	}

	$rs->db_close();

	return $return;
}
