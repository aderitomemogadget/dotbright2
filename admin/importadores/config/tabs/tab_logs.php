<?php
	/* clear logs if button pressed */
	if($_GET["clear_logs"]==1)
	{
		$myarray = glob($GLOBALS['site_shell_url']."files/importado/analisar_csv".$Supplier_ID."*.txt", GLOB_NOSORT);
		array_map('unlink', $myarray);
		$myarray = glob($GLOBALS['site_shell_url']."files/importar/analisar_csv".$Supplier_ID."*.txt", GLOB_NOSORT);
		array_map('unlink', $myarray);
	}
?>
<div style="display:block;padding:5px;vertical-align:top;background:#FFF">
	<div style="width:145px;display:inline-block;padding:5px 0"><h2>GRAVAR LOG</h2></div>
	<div style="width:150px;display:inline-block;padding:5px 0">
		<input type="checkbox" name="log_activation" id="log_activation" value="1" <?php if($log_activation==1){echo 'checked="checked"';}?>> SIM
	</div>
	<div style="clear:both"></div>
</div>
<div style="display:block;padding:5px;vertical-align:top;background:#FFF">
<h2>LOGS DE IMPORTAÇÕES A DECORRER:</h2>
<?php
	$myarray = glob($GLOBALS['site_shell_url']."files/importar/analisar_csv".$Supplier_ID."*.txt", GLOB_NOSORT);
	if(empty($myarray))
	{?>
		<div style="width:150px;padding:5px 0">Não existem Logs!</div>
		<div style="clear:both"></div>
	<?
	}
	else
	{
		array_multisort(array_map('filemtime', $myarray), SORT_NUMERIC, SORT_DESC, $myarray);
		$a=1;
		foreach ($myarray as $filename)
		{
			if($a<15)
			{
			$data_ficheiro = date ("Y-m-d H:i:s", filemtime($filename));
			$nome_ficheiro = basename($filename);
			$link_ficheiro = $filename;
			?>
			<div style="width:145px;display:inline-block;padding:5px 0"><?php echo $data_ficheiro;?></div>
			<div style="width:150px;display:inline-block;padding:5px 0">
				<img src="img/loading.gif" border="0" style="vertical-align:middle">
				<? if($log_activation==1){?><a href="see_log.php?tipo=0&file=<?php echo $link_ficheiro;?>" class="fancybox_url preto" style="text-decoration:underline">Ver LOG</a><? }?>
			</div>
			<br>
			<div style="clear:both"></div>
			<?php
			}
			$a++;
		}
	}
?>
</div><br>
<div style="display:block;padding:5px;vertical-align:top;background:#FFF">
<h2>LOGS DE IMPORTAÇÕES TERMINADAS:</h2>
<?php
	$myarray = glob($GLOBALS['site_shell_url']."files/importado/analisar_csv".$Supplier_ID."*.txt", GLOB_NOSORT);
	if(empty($myarray) )
	{?>
		<div style="width:150px;padding:5px 0">Não existem Logs!</div>
		<div style="clear:both"></div>
	<?
	}
	else
	{
			array_multisort(array_map('filemtime', $myarray), SORT_NUMERIC, SORT_DESC, $myarray);
			$a=1;
			foreach ($myarray as $filename)
			{
				if($a<15)
				{
					$data_ficheiro = date ("Y-m-d H:i:s", filemtime($filename));
					$nome_ficheiro = basename($filename);
					$link_ficheiro = $filename;
					?>
					<div style="width:145px;display:inline-block;padding:5px 0"><?php echo $data_ficheiro;?></div><div style="width:150px;display:inline-block;padding:5px 0"><a href="see_log.php?tipo=1&file=<?php echo $link_ficheiro;?>" class="fancybox_url preto" style="text-decoration:underline">Ver LOG</a></div><br>
					<div style="clear:both"></div>
					<?php
				}
				$a++;
			}
		?>
		<div style="width:150px;display:inline-block;padding:5px 0">
			<button class="btn btn-primary btn-link"><a href="import.php?Supplier_ID=<?=$Supplier_ID?>&tab_sel=tab-6&clear_logs=1" style="color:#FFFFFF;">Limpar Logs</a></button>
		</div>
	<br>
	<div style="clear:both"></div>
	<?
	}
	if($Supplier_ID == 5){
		
?>
</div><br>
<div style="display:block;padding:5px;vertical-align:top;background:#FFF">
<h2>LOGS DO WEBSERVICE CPCDI:</h2>
<?php
	$myarray = glob($GLOBALS['site_shell_url']."files/importado/ws_cpcdi_*.txt", GLOB_NOSORT);
	if(empty($myarray) )
	{?>
		<div style="width:150px;padding:5px 0">Não existem Logs!</div>
		<div style="clear:both"></div>
	<?
	}
	else
	{
			array_multisort(array_map('filemtime', $myarray), SORT_NUMERIC, SORT_DESC, $myarray);
			$a=1;
			foreach ($myarray as $filename)
			{
				if($a<15)
				{
					$data_ficheiro = date ("Y-m-d H:i:s", filemtime($filename));
					$nome_ficheiro = basename($filename);
					$link_ficheiro = $filename;
					?>
					<div style="width:145px;display:inline-block;padding:5px 0"><?php echo $data_ficheiro;?></div><div style="width:150px;display:inline-block;padding:5px 0"><a href="see_log.php?tipo=1&file=<?php echo $link_ficheiro;?>" class="fancybox_url preto" style="text-decoration:underline">Ver LOG</a></div><br>
					<div style="clear:both"></div>
					<?php
				}
				$a++;
			}
		?>
		<!-- <div style="width:150px;display:inline-block;padding:5px 0">
			<button class="btn btn-primary btn-link"><a href="import.php?Supplier_ID=<?=$Supplier_ID?>&tab_sel=tab-6&clear_logs=1" style="color:#FFFFFF;">Limpar Logs</a></button>
		</div> -->
	<br>
	<div style="clear:both"></div>
	<?
	}
	}
?>
</div><br>
<div style="display:block;padding:5px;vertical-align:top;background:#FFF">
	<h2>Avisar Por Email</h2>
	<div style="width:145px;display:inline-block;padding:5px 0">
		endereço:
	</div>
	<div style="width:150px;display:inline-block;padding:5px 0">
		<input class="form-control" type="text" value="<?echo $email_address;?>" name="email_address" id="email_address" style="width:240px">
	</div>
	<div style="clear:both"></div>
</div>