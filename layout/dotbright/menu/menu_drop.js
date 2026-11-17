$(document).ready(function () {
    $(".drop a").on('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var link=$(this);

        if (link.attr("href") != '#') {
            window.open(link.attr("href"), '_self');
        }
        else {
            var closest_ul=link.closest("ul");
            var parallel_active_links=closest_ul.find(".active")
            var closest_li=link.closest("li");
            var link_status=closest_li.hasClass("active");
            var count=0;

            closest_ul.find("ul").slideUp(function () {
                if (++count == closest_ul.find("ul").length)
                    parallel_active_links.removeClass("active");
            });

            if (!link_status) {
                closest_li.children("ul").slideDown();
                closest_li.addClass("active");
            }
        }
    });

    $(".drop .expand").click(function (e) {
        if ($(".drop .hidden2").length > 0) {
            $(".drop .hidden2").each(function () {


                $(this).show(500);
                $(this).attr("class", "show2");
                $(this).removeClass("hidden2");
            });

            $(this).html('ESCONDER [-]<div class="c"></div>');
        }
        else {
            $(".drop .show2").each(function () {
                $(this).hide(500);
                $(this).attr("class", "hidden2");
                $(this).removeClass("show2");
            });

            $(this).html('EXPANDIR [+]<div class="c"></div>');
        }
    });






	  $(".drop2 a").on('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var link=$(this);

        if (link.attr("href") != '#') {
            window.open(link.attr("href"), '_self');
        }
        else {
            var closest_ul=link.closest("ul");
            var parallel_active_links=closest_ul.find(".active")
            var closest_li=link.closest("li");
            var link_status=closest_li.hasClass("active");
            var count=0;

            closest_ul.find("ul").slideUp(function () {
                if (++count == closest_ul.find("ul").length)
                    parallel_active_links.removeClass("active");
            });

            if (!link_status) {
                closest_li.children("ul").slideDown();
                closest_li.addClass("active");
            }
        }
    });

    $(".drop2 .expand").click(function (e) {
        if ($(".drop2 .hidden2").length > 0) {
            $(".drop2 .hidden2").each(function () {


                $(this).show(500);
                $(this).attr("class", "show2");
                $(this).removeClass("hidden2");
            });

            $(this).html('ESCONDER [-]<div class="c"></div>');
        }
        else {
            $(".drop2 .show2").each(function () {
                $(this).hide(500);
                $(this).attr("class", "hidden2");
                $(this).removeClass("show2");
            });

            $(this).html('EXPANDIR [+]<div class="c"></div>');
        }
    });

});