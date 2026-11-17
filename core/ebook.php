<?php
function ebook()
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$ebook_id = 0;
	if(isset($_GET['ebook_id']) && intval($_GET['ebook_id']) > 0) $ebook_id = intval($_GET['ebook_id']);
	else
	{
		//redirect para homepage
	}
	$sql = "SELECT * FROM modulos_imgs WHERE bloco_img_id = $ebook_id";
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$ebook = $rs->db_data['bloco_pdf_ext'];

			// echo 'ola';
		if($ebook != '' && stream_resolve_include_path('files/documentos/'.$ebook))
		{
			$r.='<div id="book_container"><div id="book"></div><div id="book-controls"></div></div>';
			$r.='

        <script>
            $(function () {
                var bookOptions = {
                    width: 1200
                    , flipSound: false
                    , maxWidth: 1200
                    , centeredWhenClosed: true
                    , hardcovers: true
                    , numberedPages: []
                    , toolbar: "first, back, next, last, zoomin, zoomout, slideshow, download"
                    , thumbnailsPosition: \'bottom\'
                    , responsiveHandleWidth: 30
                    , scaleToFit: "#book_container"
                    , pdf: "'.$GLOBALS['site_http_url'].'files/documentos/'.$ebook.'"
                };

                $("#book").wowBook(bookOptions); // create the book
                var book = $.wowBook("#book"); // get book object instance
                $(".wowbook-lightbox > .wowbook-close").remove(); // remove lightbox\'s close button

                $(".wowbook-lightbox > .wowbook-close").click(function () {
                    window.open("#", "_self");
                });

                $("#cover").click(function () {
                    book.advance();
                });

                function rebuildThumbnails() {
                    book.destroyThumbnails()
                    book.showThumbnails()
                    $("#thumbs_holder").css("marginTop", -$("#thumbs_holder").height() / 2)
                }

                $("#thumbs_position button").on("click", function () {
                    var position = $(this).text().toLowerCase()
                    if ($(this).data("customized")) {
                        position = "top"
                        book.opts.thumbnailsParent = "#thumbs_holder";
                    } else {
                        book.opts.thumbnailsParent = "body";
                    }
                    book.opts.thumbnailsPosition = position
                    rebuildThumbnails();
                })
                $("#thumbs_size button").click(function () {
                    var factor = 0.02 * ( $(this).index() ? -1 : 1 );
                    book.opts.thumbnailScale = book.opts.thumbnailScale + factor;
                    rebuildThumbnails();
                })
            })
        </script>';
		}
	}
	$rs->db_close();
	echo $r;
}
?>