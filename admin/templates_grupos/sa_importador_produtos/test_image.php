<?php
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(-1);
		
		$filename = 'http://services.sorefoz.pt/redes/imgredes2.aspx?IMG=10400034';
		
		
		// $image = imagecreatefromstring(file_get_contents($filename));
		$exif = exif_read_data(file_get_contents($filename));
		
		print_r($exif);
		
		
		/* if(!empty($exif['Orientation'])) {
			switch($exif['Orientation']) {
				case 8:
					$image = imagerotate($image,90,0);
					break;
				case 3:
					$image = imagerotate($image,180,0);
					break;
				case 6:
					$image = imagerotate($image,-90,0);
					break;
			}
		} */