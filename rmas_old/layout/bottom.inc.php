<script src="<?php echo $GLOBALS['site_http_url']?>rmas/layout/choosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$("#navigation_horiz a.navlink").each(function() {
    var label = this.getAttribute("title") || "";
    this.setAttribute("data-label", label);
});
$(".chzn-select").chosen();
$(".chzn-select-deselect").chosen({allow_single_deselect:true});
/* $('.dropdate').mask("0000/00/00", {placeholder: "aaaa/mm/gg"}); */
</script>
</html>
