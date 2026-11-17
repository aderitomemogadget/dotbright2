<div style="display:block;padding:5px;vertical-align:top;background:#FFF;">
	
	<div style="width:145px;display:inline-block;padding:5px 0"><h2>Activar Cron</h2></div>
	<div style="width:150px;display:inline-block;padding:5px 0">
		<input type="checkbox" name="cron_activation" id="cron_activation" value="1" <?php if($cron_activation==1){echo 'checked="checked"';}?>>
	</div>
	<div style="clear:both"></div>
</div>

<div style="display:block;padding:5px;vertical-align:top;background:#FFF;">
	<div style="width:145px;display:inline-block;padding:5px 0">
		Crons Activos
	</div>
	<div style="width:150px;display:inline-block;padding:5px 0">
		<input class="form-control" type="text" value="<?echo $cron;?>" name="cron" id="cron" style="width:240px">
	</div>
	<div style="clear:both"></div>
</div>