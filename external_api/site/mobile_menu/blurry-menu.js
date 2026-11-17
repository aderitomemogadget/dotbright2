jQuery(document).ready(function() {
    BlurryMenu.preloadMenu();
    $(window).resize(function() {});
    $(document).bind('click touchend', function(e) {
        if ($(e.target).closest('#menu').length === 0 && $(e.target).closest('#loginboxpop').length === 0 && $(e.target).closest('.apprise').length === 0 && $(e.target).closest('.apprise-overlay').length === 0) {
            BlurryMenu.closeMenu();
            jQuery(function($) {
                powerinstance = new Powerlistmenu({
                    menuid: 'powermenu1'
                })
            });
        }
    });
});

var BlurryMenu = function() {
    self.menuWidth = 280;
    self.animationDuration = 500;
    self.animationStyle = "easeOutQuart";
    self.blurStrength = 16;
    self.menuOpened = false;
    return {
        closeMenu: function() {
            if (self.menuOpened) {
                


                $("#menu").animate({
                    width: 0
                }, {
                    duration: self.animationDuration,
                    specialEasing: {width: self.animationStyle},
                    complete: function() {

                        $("#powermenu1_bg").css("display", 'none');
                        $(".columns-container").css("display",'block');
                        $(".footer-container").css("display",'block');
                        $("#banners").css("display",'block');                        
                    }
                });


                $("#menu-content").animate({
                    width: 0
                }, {
                    duration: self.animationDuration,
                    specialEasing: {width: self.animationStyle},
                    complete: function() {
                        $("#menu-content").css("position", "relative");
                        self.menuOpened = false;                        
                    }
                });
                self.menuOpened = false;




            }
        },
        preloadMenu: function() {},
        openMenu: function(t) {
            if (!self.menuOpened) {
                $("#menu-content").css("position", "fixed");
                $("#menu-content").css("width", 0);
                $("#menu").height(document.body.clientHeight);


                $("#powermenu1_bg").css("display", 'block');
                $(".columns-container").css("display",'none');
                $(".footer-container").css("display",'none');
                $("#banners").css("display",'none');
                
                //$(".submenu").css("width",'100%');


                $("#menu-content").animate({
                    width: self.menuWidth
                }, {
                    duration: self.animationDuration,
                    specialEasing: {width: self.animationStyle}
                });
                $("#menu").animate({
                    width: self.menuWidth
                }, {
                    duration: self.animationDuration,
                    specialEasing: {width: self.animationStyle},
                    complete: function() {
                        if (t == 1) $("#pesquisa-mobile").focus();
                        self.menuOpened = true;
                    }
                });
            }
        }
    };
}();