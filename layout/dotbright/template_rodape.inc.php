<?php
	if($GLOBALS['rodape'] && !isset($_GET['popup'])) put_content($rodape);
	echo google_analytics();
	
    /*

	if ($_SESSION['ss_ismobile'] == 1)
    {
        echo '<script>if (pagewidth > 1024) {
                var url = window.location.pathname + window.location.search;
                var separator = url.includes(\'?\') ? \'&\' : \'?\';
                window.open(url + separator + \'ism=-1\', \'_top\');
            }
            </script>';
    }
    else
    {
        echo '<script>if (pagewidth <= 1024) {
                var url = window.location.pathname + window.location.search;
                var separator = url.includes(\'?\') ? \'&\' : \'?\';
                window.open(url + separator + \'ism=1\', \'_top\');
            }
            </script>';
    }
    */

 include $path.'../cookies/memogadgetcookiebot.php'; ?>
 <script src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/assets/vendor/swiper/swiper-bundle.min_v_<?=dumpcache?>.js"></script>
 <script type="text/javascript">
		$(document).ready(function(){
		  $('.body').css('display','block');
		});
	</script>
</body>
</html>
