var switchLayoutDirection = function(d) {
    var c = d(document.body);
    c
        .find(".dropdown-menu:not(.datepicker-dropdown,.colorpicker)")
        .toggleClass("pull-right").end().find(".pull-right:not(.dropdown-menu,blockquote,.profile-skills .pull-right)")
        .removeClass("pull-right").addClass("tmp-rtl-pull-right").end()
        .find(".pull-left:not(.dropdown-submenu,.profile-skills .pull-left)")
        .removeClass("pull-left").addClass("pull-right")
        .end().find(".tmp-rtl-pull-right").removeClass("tmp-rtl-pull-right")
        .addClass("pull-left").end().find(".chosen-container").toggleClass("chosen-rtl").end();

    function a(h, g) {
        c.find("." + h).removeClass(h).addClass("tmp-rtl-" + h).end().find("." + g).removeClass(g).addClass(h).end().find(".tmp-rtl-" + h).removeClass("tmp-rtl-" + h).addClass(g)
    }

    function b(h, g, i) {
        i.each(function() {
            var k = d(this);
            var j = k.css(g);
            k.css(g, k.css(h));
            k.css(h, j)
        })
    }
    a("align-left", "align-right");
    a("no-padding-left", "no-padding-right");
    a("arrowed", "arrowed-right");
    a("arrowed-in", "arrowed-in-right");
    a("messagebar-item-left", "messagebar-item-right");
    var e = d("#piechart-placeholder");
    if (e.size() > 0) {
        var f = d(document.body).hasClass("rtl") ? "nw" : "ne";
        e.data("draw").call(e.get(0), e, e.data("chart"), f)
    }
};

jQuery(function($) {
    if($(document.body).hasClass('rtl')) {
        switchLayoutDirection(jQuery);
    }
});