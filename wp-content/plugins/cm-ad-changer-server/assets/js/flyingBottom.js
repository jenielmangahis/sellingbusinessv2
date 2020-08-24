function flyingBottomAd(e) {
    function safex(e, t) {
        return typeof e === "undefined" ? t : e
    }
    function getFlyingTimeT(e) {
        var t = e * 24 * 60 * 60 * 1e3;
        var n = new Date;
        n.setTime(n.getTime() + t);
        return "; expires=" + n.toGMTString() + "; path=/"
    }
    function showFlyingBottom() {
        jQuery("body").append(o);
        jQuery(".flyingBottomAdClose").on("click", function() {
            jQuery("#flyingBottomAd").hide();
            //jQuery("#flyin").addClass("minimizedFlyingAd");
        });
        /*jQuery("body").on("click", function() {
             * @todo get ad back
            jQuery("#flyin").removeClass("minimize");
        })*/
    }
    var e = e || {},
        t = safex(e.sensitivity, 20),
        n = safex(e.timer, 0),
        r = getFlyingTimeT(e.cookieExpire) || "",
        i = getFlyingTimeT(e.longExpire) || "",
        s = safex(e.auto, "false"),
        o = safex(e.htmlContent, ""),
        f = e.delay || 3e3;
    //show the add
    setTimeout(function() {
        showFlyingBottom()
    }, f)
}