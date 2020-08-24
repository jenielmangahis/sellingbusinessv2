if (typeof window.n2SSIframeLoader !== "function") {
    (function ($) {
        var frames = [],
            eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
        window[eventMethod](eventMethod === "attachEvent" ? "onmessage" : "message", function (e) {
            for (var i = 0; i < frames.length; i++) {
                if (frames[i] && frames[i].match(e.source)) {
                    frames[i].message(e[e.message ? "message" : "data"]);
                }
            }
        });

        function S(frame, i) {
            this.i = i;
            this.frame = frame;
            this.$frame = $(frame);
        }

        S.prototype.match = function (w) {
            if (w === (this.frame.contentWindow || this.frame.contentDocument)) {
                this.frameContent = this.frame.contentWindow || this.frame.contentDocument;
                return true;
            }

            return false;
        };

        S.prototype.message = function (data) {
            switch (data["key"]) {
                case "setLocation":
                    if (typeof window.zajax_goto === 'function') {
                        /**
                         * @url https://wordpress.org/plugins/zajax-ajax-navigation/
                         */
                        window.zajax_goto(data.location);
                    } else {
                        window.location = data.location;
                    }
                    break;
                case "ready":
                    var clientHeight = this.getClientHeight();
                    this.frameContent.postMessage({
                        key: "ackReady",
                        clientHeight: clientHeight
                    }, "*");
                    break;
                case "resize":
                    if (data.fullPage) {
                        if (this.fullpage !== data.fullPage) {
                            this.fullpage = data.fullPage;
                            this.verticalOffsetSelectors = $(data.verticalOffsetSelectors);
                            this.resizeFullPage();
                            $(window).on("resize.n2-ss-iframe-" + this.i, $.proxy(this.resizeFullPage, this));
                            $(window).on("orientationchange.n2-ss-iframe-" + this.i, $.proxy(this.resizeFullPage, this));
                        }
                    } else {
                        this.fullpage = 0;
                    }
                    this.$frame.css({
                        height: data.height
                    });

                    if (data.forceFull && this.forcefull !== data.forceFull) {

                        this.forcefull = data.forceFull;

                        var $container = $('body');
                        $container.css("overflow-x", "hidden");

                        this.resizeFullWidth();
                        $(window).on("resize.n2-ss-iframe-" + this.i, $.proxy(this.resizeFullWidth, this));
                    }
                    break;
            }
        };

        S.prototype.exists = function () {
            if ($.contains(document.body, this.frame)) {
                return true;
            }

            frames[this.i] = false;
            $(window).off(".n2-ss-iframe-" + this.i);

            return false;
        };

        S.prototype.resizeFullWidth = function (e) {
            if (this.exists()) {
                var customWidth = 0,
                    adjustLeftOffset = 0,
                    $fullWidthTo = $('.editor-writing-flow,.fl-responsive-preview .fl-builder-content');
                if ($fullWidthTo.length) {
                    customWidth = $fullWidthTo.width();
                    adjustLeftOffset = $fullWidthTo.offset().left;
                }

                var windowWidth = customWidth > 0 ? customWidth : (document.body.clientWidth || document.documentElement.clientWidth),
                    outerEl = this.$frame.parent(),
                    outerElBoundingRect = outerEl[0].getBoundingClientRect(),
                    outerElOffset,
                    isRTL = $("html").attr("dir") === "rtl";
                if (isRTL) {
                    outerElOffset = windowWidth - (outerElBoundingRect.left + outerEl.outerWidth());
                } else {
                    outerElOffset = outerElBoundingRect.left;
                }
                this.$frame.css(isRTL ? 'marginRight' : 'marginLeft', -outerElOffset - parseInt(outerEl.css('paddingLeft')) - parseInt(outerEl.css('borderLeftWidth')) + adjustLeftOffset)
                    .css("maxWidth", "none")
                    .width(windowWidth);
            }
        };

        S.prototype.resizeFullPage = function (e) {
            if (this.exists()) {
                var clientHeight = this.getClientHeight(e);
                for (var i = 0; i < this.verticalOffsetSelectors.length; i++) {
                    clientHeight -= this.verticalOffsetSelectors.eq(i).outerHeight();
                }
                this.frameContent.postMessage({
                    key: "update",
                    clientHeight: clientHeight
                }, "*");
                this.$frame.height(clientHeight);
            }
        };

        S.prototype.getClientHeight = function (e) {
            var clientHeight = 0;
            if (window.matchMedia && (/Android|iPhone|iPad|iPod|BlackBerry/i).test(navigator.userAgent || navigator.vendor || window.opera)) {
                var innerHeight,
                    isOrientationChanged = false,
                    lastOrientation = this.lastOrientation;

                if (e && e.type === 'orientationchange') {
                    isOrientationChanged = true;
                }

                if (/iPad|iPhone|iPod/.test(navigator.platform)) {
                    innerHeight = document.documentElement.clientHeight;
                } else {
                    innerHeight = window.innerHeight;
                }

                if (window.matchMedia("(orientation: landscape)").matches) {
                    clientHeight = Math.min(screen.width, innerHeight);
                    if (lastOrientation !== 90) {
                        isOrientationChanged = true;
                        this.lastOrientation = 90;
                    }
                } else {
                    clientHeight = Math.min(screen.height, innerHeight);
                    if (lastOrientation !== 0) {
                        isOrientationChanged = true;
                        this.lastOrientation = 0;
                    }
                }

                if (!isOrientationChanged && Math.abs(clientHeight - this.lastClientHeight) < 100) {
                    clientHeight = this.lastClientHeight;
                } else {
                    this.lastClientHeight = clientHeight;
                }
            } else {
                clientHeight = document.documentElement.clientHeight || document.body.clientHeight;
            }

            return clientHeight;
        };

        window.n2SSIframeLoader = function (iframe) {
            frames.push(new S(iframe, frames.length));
        }
    })(jQuery);
}