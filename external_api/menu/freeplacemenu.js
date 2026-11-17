var FreePlaceMenu = (function () 
{	
	if ('ontouchstart' in document.documentElement) var evento = 'click';
	else var evento = 'mouseover';
	
	var b = $("#freeplace-menu > ul > li"), g = b.children("a"), c = $("html"), d = -1;
	var t_menu = Math.ceil((b.length / 2));
    function f() { g.on(evento, a);  b.on(evento, function (h) {h.stopPropagation(); }); }
    function a(j) 
	{
        if (d !== -1) b.eq(d).removeClass("cbp-hropen");
        var i = $(j.currentTarget).parent("li"), h = i.index();
		
        if (d === h){i.removeClass("cbp-hropen"); d = -1;} /* if(h) */
		else 
		{
			i.addClass("cbp-hropen"); 
			/*if(t_menu<(Number(h)+1)){ $('.cbp-hropen .cbp-hrsub').attr('style','right:0');} */
			d = h; c.off(evento).on(evento, e);
		}
        return false;
    }
    function e(h){b.eq(d).removeClass("cbp-hropen");  d = -1;}
    return {init: f}
})();