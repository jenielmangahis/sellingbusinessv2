/* ----------------- Start Document ----------------- */
(function($){
"use strict";

  $(document).ready(function(){

  /*--------------------------------------------------*/
  /*  Mobile Menu - mmenu.js
  /*--------------------------------------------------*/
  $(function() {
    function mmenuInit() {
      var wi = $(window).width();
      if(wi <= '992') {

        $('#footer').removeClass("sticky-footer");

        $(".mmenu-init" ).remove();
        $("#navigation").clone().addClass("mmenu-init").insertBefore("#navigation").removeAttr('id').removeClass('style-1 style-2').find('ul').removeAttr('id');
        $(".mmenu-init").find(".container").removeClass("container");

        $(".mmenu-init").mmenu({
          "counters": true
        }, {
         // configuration
         offCanvas: {
            pageNodetype: "#wrapper"
         }
        });

        var mmenuAPI = $(".mmenu-init").data( "mmenu" );
        var $icon = $(".hamburger");

        $(".mmenu-trigger").click(function() {
          mmenuAPI.open();
        });

        mmenuAPI.bind( "open:finish", function() {
           setTimeout(function() {
              $icon.addClass( "is-active" );
           });
        });
        mmenuAPI.bind( "close:finish", function() {
           setTimeout(function() {
              $icon.removeClass( "is-active" );
           });
        });


      }
      $(".mm-next").addClass("mm-fullsubopen");
    }
    mmenuInit();
    $(window).resize(function() { mmenuInit(); });
  });

    /*  User Menu */
    $('.user-menu').on('click', function(){
    $(this).toggleClass('active');
  });


/*----------------------------------------------------*/
/*  Counters
/*----------------------------------------------------*/

    $('.counter').counterUp({
        delay: 10,
        time: 800
    });

  


    /*----------------------------------------------------*/
    /*  Sticky Header 
    /*----------------------------------------------------*/
    $( ".sticky-header #header" ).not( "#header-container.header-style-2 #header" ).clone(true).addClass('cloned unsticky').insertAfter( "#header" );
    $( "#navigation.style-2" ).clone(true).addClass('cloned unsticky').insertAfter( "#navigation.style-2" );

    // Logo for header style 2
    $( "#logo .sticky-logo" ).clone(true).prependTo("#navigation.style-2.cloned ul#responsive");


    // sticky header script
    var headerOffset = $("#header-container").height() * 2; // height on which the sticky header will shows

    $(window).scroll(function(){
        if($(window).scrollTop() >= headerOffset){
            $("#header.cloned").addClass('sticky').removeClass("unsticky");
            $("#navigation.style-2.cloned").addClass('sticky').removeClass("unsticky");
        } else {
            $("#header.cloned").addClass('unsticky').removeClass("sticky");
            $("#navigation.style-2.cloned").addClass('unsticky').removeClass("sticky");
        }
    });



    /*----------------------------------------------------*/
    /*  Parallax
    /*----------------------------------------------------*/

    /* detect touch */
    if("ontouchstart" in window){
        document.documentElement.className = document.documentElement.className + " touch";
    }
    if(!$("html").hasClass("touch")){
        /* background fix */
        $(".parallax").css("background-attachment", "fixed");
    }

    /* fix vertical when not overflow
    call fullscreenFix() if .fullscreen content changes */
    function fullscreenFix(){
        var h = $('body').height();
        // set .fullscreen height
        $(".content-b").each(function(i){
            if($(this).innerHeight() > h){ $(this).closest(".fullscreen").addClass("overflow");
            }
        });
    }
    $(window).resize(fullscreenFix);
    fullscreenFix();

    /* resize background images */
    function backgroundResize(){
        var windowH = $(window).height();
        $(".parallax").each(function(i){
            var path = $(this);
            // variables
            var contW = path.width();
            var contH = path.height();
            var imgW = path.attr("data-img-width");
            var imgH = path.attr("data-img-height");
            var ratio = imgW / imgH;
            // overflowing difference
            var diff = 100;
            diff = diff ? diff : 0;
            // remaining height to have fullscreen image only on parallax
            var remainingH = 0;
            if(path.hasClass("parallax") && !$("html").hasClass("touch")){
                //var maxH = contH > windowH ? contH : windowH;
                remainingH = windowH - contH;
            }
            // set img values depending on cont
            imgH = contH + remainingH + diff;
            imgW = imgH * ratio;
            // fix when too large
            if(contW > imgW){
                imgW = contW;
                imgH = imgW / ratio;
            }
            //
            path.data("resized-imgW", imgW);
            path.data("resized-imgH", imgH);
            path.css("background-size", imgW + "px " + imgH + "px");
        });
    }


    $(window).resize(backgroundResize);
    $(window).focus(backgroundResize);
    backgroundResize();

    /* set parallax background-position */
    function parallaxPosition(e){
        var heightWindow = $(window).height();
        var topWindow = $(window).scrollTop();
        var bottomWindow = topWindow + heightWindow;
        var currentWindow = (topWindow + bottomWindow) / 2;
        $(".parallax").each(function(i){
            var path = $(this);
            var height = path.height();
            var top = path.offset().top;
            var bottom = top + height;
            // only when in range
            if(bottomWindow > top && topWindow < bottom){
                //var imgW = path.data("resized-imgW");
                var imgH = path.data("resized-imgH");
                // min when image touch top of window
                var min = 0;
                // max when image touch bottom of window
                var max = - imgH + heightWindow;
                // overflow changes parallax
                var overflowH = height < heightWindow ? imgH - height : imgH - heightWindow; // fix height on overflow
                top = top - overflowH;
                bottom = bottom + overflowH;


                // value with linear interpolation
                // var value = min + (max - min) * (currentWindow - top) / (bottom - top);
                var value = 0;
          if ( $('.parallax').is(".titlebar") ) {
              value = min + (max - min) * (currentWindow - top) / (bottom - top) *2;
          } else {
            value = min + (max - min) * (currentWindow - top) / (bottom - top);
          }

                // set background-position
                var orizontalPosition = path.attr("data-oriz-pos");
                orizontalPosition = orizontalPosition ? orizontalPosition : "50%";
                $(this).css("background-position", orizontalPosition + " " + value + "px");
            }
        });
    }

    if(!$("html").hasClass("touch")){
        $(window).resize(parallaxPosition);
        //$(window).focus(parallaxPosition);
        $(window).scroll(parallaxPosition);
        parallaxPosition();
    }
    

    // Jumping background fix for IE
    if(navigator.userAgent.match(/Trident\/7\./)) { // if IE
        $('body').on("mousewheel", function () {
            event.preventDefault(); 

            var wheelDelta = event.wheelDelta;
            var currentScrollPosition = window.pageYOffset;
            window.scrollTo(0, currentScrollPosition - wheelDelta);
        });
    }
    

    /*----------------------------------------------------*/
    /*  Search Type Buttons
    /*----------------------------------------------------*/
    function searchTypeButtons() {

      // Radio attr reset
      $('.search-type label.active input[type="radio"]').prop('checked',true);

      // Positioning indicator arrow
      var buttonWidth = $('.search-type label.active').width();
      var arrowDist = $('.search-type label.active').position().left;
      $('.search-type-arrow').css('left', arrowDist + (buttonWidth/2) );

      $('.search-type label').on('change', function() {
          $('.search-type input[type="radio"]').parent('label').removeClass('active');
          $('.search-type input[type="radio"]:checked').parent('label').addClass('active');

        // Positioning indicator arrow
        var buttonWidth = $('.search-type label.active').width();
        var arrowDist = $('.search-type label.active').position().left;

        $('.search-type-arrow').css({
          'left': arrowDist + (buttonWidth/2),
          'transition':'left 0.4s cubic-bezier(.87,-.41,.19,1.44)'
        });
      });

    }

    // Init
    if ($(".main-search-form .search-type").length){ searchTypeButtons(); }


    function parallaxBG() {

      $('.parallax,.vc_parallax').prepend('<div class="parallax-overlay"></div>');

      $( ".parallax,.vc_parallax").each(function() {
        var attrImage = $(this).attr('data-background');
        var attrColor = $(this).attr('data-color');
        var attrOpacity = $(this).attr('data-color-opacity');

        $(this).css('background-image', 'url('+attrImage+')');
        $(this).find(".parallax-overlay").css('background-color', ''+attrColor+'');
        $(this).find(".parallax-overlay").css('opacity', ''+attrOpacity+'');
      });
    }

    parallaxBG();


    /*----------------------------------------------------*/
    /*  Tabs
    /*----------------------------------------------------*/ 

    var $tabsNav    = $('.tabs-nav'),
    $tabsNavLis = $tabsNav.children('li');

    $tabsNav.each(function() {
         var $this = $(this);

         $this.next().children('.tab-content').stop(true,true).hide()
         .first().show();

         $this.children('li').first().addClass('active').stop(true,true).show();
    });

    $tabsNavLis.on('click', function(e) {
         var $this = $(this);

         $this.siblings().removeClass('active').end()
         .addClass('active');

         $this.parent().next().children('.tab-content').stop(true,true).hide()
         .siblings( $this.find('a').attr('href') ).fadeIn();

         e.preventDefault();
    });
    var hash = window.location.hash;
    var anchor = $('.tabs-nav a[href="' + hash + '"]');
    
    if (anchor.length === 0) {
         $(".tabs-nav li:first").addClass("active").show(); //Activate first tab
         $(".tab-content:first").show(); //Show first tab content
    } else {
         console.log(anchor);
         anchor.parent('li').click();
    }

    /*----------------------------------------------------*/
    /*  Toggle
    /*----------------------------------------------------*/

    $(".toggle-container").hide();

    $('.trigger, .trigger.opened').on('click', function(a){
        $(this).toggleClass('active');
        a.preventDefault();
    });

    $(".trigger").on('click', function(){
        $(this).next(".toggle-container").slideToggle(300);
    });

    $(".trigger.opened").addClass("active").next(".toggle-container").show();


    /*----------------------------------------------------*/
    /* Top Bar Dropdown Menu
    /*----------------------------------------------------*/

    $('.top-bar-dropdown').on('click', function(event){
        $('.top-bar-dropdown').not(this).removeClass('active');
        if ($(event.target).parent().parent().attr('class') == 'options' ) {
            hideDD();
        } else {
            if($(this).hasClass('active') &&  $(event.target).is( "span" )) {
                hideDD();
            } else {
                $(this).toggleClass('active');
            }
        }
        event.stopPropagation();
    });

    $(document).on('click', function(e){ hideDD(); });

    function hideDD(){
        $('.top-bar-dropdown').removeClass('active');
    }

    
    /*----------------------------------------------------*/
    /*  Inline CSS replacement for backgrounds etc.
    /*----------------------------------------------------*/
    function inlineCSS() {

        // Common Inline CSS
        $("section.fullwidth, .img-box-background, .flip-banner, .fullwidth-property-slider .item, .fullwidth-home-slider .item").each(function() {
            var attrImageBG = $(this).attr('data-background-image');
            var attrColorBG = $(this).attr('data-background-color');
            if(attrImageBG) {
              $(this).css('background-image', 'url('+attrImageBG+')');
            }
            if(attrColorBG){
              $(this).css('background', ''+attrColorBG+'');
            }
        });

    }

    // Init
    inlineCSS();

    /*----------------------------------------------------*/
    /*  Image Box 
    /*----------------------------------------------------*/
    $('.img-box').each(function(){

        // add a photo container
        $(this).append('<div class="img-box-background"></div>');

        // set up a background image for each tile based on data-background-image attribute
        $(this).children('.img-box-background').css({'background-image': 'url('+ $(this).attr('data-background-image') +')'});

        // background animation on mousemove
        // $(this).on('mousemove', function(e){
        //   $(this).children('.img-box-background').css({'transform-origin': ((e.pageX - $(this).offset().left) / $(this).width()) * 100 + '% ' + ((e.pageY - $(this).offset().top) / $(this).height()) * 100 +'%'});
        // })
    });

    /*----------------------------------------------------*/
    /* Advanced Search Button
    /*----------------------------------------------------*/
    $('form .adv-search-btn,.adv-search-btn.with-map').on('click', function(e){

      if ( $(this).is(".active") ) {

        $(this).removeClass("active");
        $(".main-search-container").removeClass("active");
        setTimeout( function() { 
          $("#map-container.homepage-map").removeClass("overflow")
        }, 0);

      } else {

        $(this).addClass("active");
        $(".main-search-container").addClass("active");
        setTimeout( function() { 
          $("#map-container.homepage-map").addClass("overflow")
        }, 400);

      }

      e.preventDefault();
  });


   /*----------------------------------------------------*/
    /*  Show More Button
    /*----------------------------------------------------*/
    $('a.show-more-button').on('click', function(e){
      console.log('klik');
      e.preventDefault();
    $('.show-more').toggleClass('visible');
  });


    /*----------------------------------------------------*/
    /*  Back to Top
    /*----------------------------------------------------*/
      var pxShow = 600; // height on which the button will show
      var fadeInTime = 300; // how slow / fast you want the button to show
      var fadeOutTime = 300; // how slow / fast you want the button to hide
      var scrollSpeed = 500; // how slow / fast you want the button to scroll to top.

      $(window).scroll(function(){
         if($(window).scrollTop() >= pxShow){
            $("#backtotop").fadeIn(fadeInTime);
         } else {
            $("#backtotop").fadeOut(fadeOutTime);
         }
      });

      $('#backtotop a').on('click', function(){
         $('html, body').animate({scrollTop:0}, scrollSpeed);
         return false;
      });



    /*----------------------------------------------------*/
    /*  Magnific Popup
    /*----------------------------------------------------*/
    $('body').magnificPopup({
       type: 'image',
       delegate: 'a.mfp-gallery',

       fixedContentPos: true,
       fixedBgPos: true,

       overflowY: 'auto',

       closeBtnInside: false,
       preloader: true,

       removalDelay: 0,
       mainClass: 'mfp-fade',

       gallery:{enabled:true}
    });


    $('.popup-with-zoom-anim').magnificPopup({
       type: 'inline',

       fixedContentPos: false,
       fixedBgPos: true,

       overflowY: 'auto',

       closeBtnInside: true,
       preloader: false,

       midClick: true,
       removalDelay: 300,
       mainClass: 'my-mfp-zoom-in'
    });


    $('.mfp-image').magnificPopup({
       type: 'image',
       closeOnContentClick: true,
       mainClass: 'mfp-fade',
       image: {
          verticalFit: true
       }
    });

    $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
       disableOn: 700,
       type: 'iframe',
       mainClass: 'mfp-fade',
       removalDelay: 160,
       preloader: false,

       fixedContentPos: false
    });


    /*----------------------------------------------------*/
    /*  Tooltips
    /*----------------------------------------------------*/

    $(".tooltip.top").tipTip({
      defaultPosition: "top"
    });

    $(".tooltip.bottom").tipTip({
      defaultPosition: "bottom"
    });

    $(".tooltip.left").tipTip({
      defaultPosition: "left"
    });

    $(".tooltip.right").tipTip({
      defaultPosition: "right"
    });

    /*----------------------------------------------------*/
    /*  Accordions
    /*----------------------------------------------------*/
    var $accor = $('.accordion');

     $accor.each(function() {
       $(this).toggleClass('ui-accordion ui-widget ui-helper-reset');
       $(this).find('h3').addClass('ui-accordion-header ui-helper-reset ui-state-default ui-accordion-icons ui-corner-all');
       $(this).find('div').addClass('ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom');
       $(this).find("div").hide();
    });

    var $trigger = $accor.find('h3');

    $trigger.on('click', function(e) {
       var location = $(this).parent();

       if( $(this).next().is(':hidden') ) {
          var $triggerloc = $('h3',location);
          $triggerloc.removeClass('ui-accordion-header-active ui-state-active ui-corner-top').next().slideUp(300);
          $triggerloc.find('span').removeClass('ui-accordion-icon-active');
          $(this).find('span').addClass('ui-accordion-icon-active');
          $(this).addClass('ui-accordion-header-active ui-state-active ui-corner-top').next().slideDown(300);
       }
       else if( $(this).is(':visible') ) {
          var $triggerloc = $('h3',location);
          $triggerloc.removeClass('ui-accordion-header-active ui-state-active ui-corner-top').next().slideUp(300);
          $triggerloc.find('span').removeClass('ui-accordion-icon-active');
       }
        e.preventDefault();
    });


    /*----------------------------------------------------*/
    /*  Sticky Footer (footer-reveal.js)
    /*----------------------------------------------------*/

    // disable if IE
    if(navigator.userAgent.match(/Trident\/7\./)) { // if IE
        $('#footer').removeClass("sticky-footer");
    }

    $('#footer.sticky-footer').footerReveal();


    if($('.listings-container').data('counter')){
      $('.count_properties').html($('.listings-container').data('counter'));
    }

  // ------------------ End Document ------------------ //
  });

})(this.jQuery);

(function($) {

  $.fn.footerReveal = function(options) {

	$('#footer.sticky-footer').before('<div class="footer-shadow"></div>');

    var $this = $(this),
        $prev = $this.prev(),
        $win = $(window),

        defaults = $.extend ({
          shadow : true,
          shadowOpacity: 0.12,
          zIndex : -10
        }, options ),

        settings = $.extend(true, {}, defaults, options);

		$this.before('<div class="footer-reveal-offset"></div>');

    if ($this.outerHeight() <= $win.outerHeight()) {
      $this.css({
        'z-index' : defaults.zIndex,
        position : 'fixed',
        bottom : 0
      });

      $win.on('load resize', function() {
        $this.css({
          'width' : $prev.outerWidth()
        });
        $prev.css({
          'margin-bottom' : $this.outerHeight()
        });
      });
    }

    return this;

  };

}) (this.jQuery);

 /*
 * TipTip
 * Copyright 2010 Drew Wilson
 * www.drewwilson.com
 * code.drewwilson.com/entry/tiptip-jquery-plugin
 *
 * Version 1.3   -   Updated: Mar. 23, 2010
 *
 * This Plug-In will create a custom tooltip to replace the default
 * browser tooltip. It is extremely lightweight and very smart in
 * that it detects the edges of the browser window and will make sure
 * the tooltip stays within the current window size. As a result the
 * tooltip will adjust itself to be displayed above, below, to the left 
 * or to the right depending on what is necessary to stay within the
 * browser window. It is completely customizable as well via CSS.
 *
 * This TipTip jQuery plug-in is dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function($){$.fn.tipTip=function(options){var defaults={activation:"hover",keepAlive:false,maxWidth:"200px",edgeOffset:3,defaultPosition:"top",delay:80,fadeIn:200,fadeOut:50,attribute:"title",content:false,enter:function(){},exit:function(){}};var opts=$.extend(defaults,options);if($("#tiptip_holder").length<=0){var tiptip_holder=$('<div id="tiptip_holder" style="max-width:'+opts.maxWidth+';"></div>');var tiptip_content=$('<div id="tiptip_content"></div>');var tiptip_arrow=$('<div id="tiptip_arrow"></div>');$("html").append(tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>')))}else{var tiptip_holder=$("#tiptip_holder");var tiptip_content=$("#tiptip_content");var tiptip_arrow=$("#tiptip_arrow")}return this.each(function(){var org_elem=$(this);if(opts.content){var org_title=opts.content}else{var org_title=org_elem.attr(opts.attribute)}if(org_title!=""){if(!opts.content){org_elem.removeAttr(opts.attribute)}var timeout=false;if(opts.activation=="hover"){org_elem.hover(function(){active_tiptip()},function(){if(!opts.keepAlive){deactive_tiptip()}});if(opts.keepAlive){tiptip_holder.hover(function(){},function(){deactive_tiptip()})}}else if(opts.activation=="focus"){org_elem.focus(function(){active_tiptip()}).blur(function(){deactive_tiptip()})}else if(opts.activation=="click"){org_elem.click(function(){active_tiptip();return false}).hover(function(){},function(){if(!opts.keepAlive){deactive_tiptip()}});if(opts.keepAlive){tiptip_holder.hover(function(){},function(){deactive_tiptip()})}}function active_tiptip(){opts.enter.call(this);tiptip_content.html(org_title);tiptip_holder.hide().removeAttr("class").css("margin","0");tiptip_arrow.removeAttr("style");var top=parseInt(org_elem.offset()['top']);var left=parseInt(org_elem.offset()['left']);var org_width=parseInt(org_elem.outerWidth());var org_height=parseInt(org_elem.outerHeight());var tip_w=tiptip_holder.outerWidth();var tip_h=tiptip_holder.outerHeight();var w_compare=Math.round((org_width-tip_w)/2);var h_compare=Math.round((org_height-tip_h)/2);var marg_left=Math.round(left+w_compare);var marg_top=Math.round(top+org_height+opts.edgeOffset);var t_class="";var arrow_top="";var arrow_left=Math.round(tip_w-12)/2;if(opts.defaultPosition=="bottom"){t_class="_bottom"}else if(opts.defaultPosition=="top"){t_class="_top"}else if(opts.defaultPosition=="left"){t_class="_left"}else if(opts.defaultPosition=="right"){t_class="_right"}var right_compare=(w_compare+left)<parseInt($(window).scrollLeft());var left_compare=(tip_w+left)>parseInt($(window).width());if((right_compare&&w_compare<0)||(t_class=="_right"&&!left_compare)||(t_class=="_left"&&left<(tip_w+opts.edgeOffset+5))){t_class="_right";arrow_top=Math.round(tip_h-13)/2;arrow_left=-12;marg_left=Math.round(left+org_width+opts.edgeOffset);marg_top=Math.round(top+h_compare)}else if((left_compare&&w_compare<0)||(t_class=="_left"&&!right_compare)){t_class="_left";arrow_top=Math.round(tip_h-13)/2;arrow_left=Math.round(tip_w);marg_left=Math.round(left-(tip_w+opts.edgeOffset+5));marg_top=Math.round(top+h_compare)}var top_compare=(top+org_height+opts.edgeOffset+tip_h+8)>parseInt($(window).height()+$(window).scrollTop());var bottom_compare=((top+org_height)-(opts.edgeOffset+tip_h+8))<0;if(top_compare||(t_class=="_bottom"&&top_compare)||(t_class=="_top"&&!bottom_compare)){if(t_class=="_top"||t_class=="_bottom"){t_class="_top"}else{t_class=t_class+"_top"}arrow_top=tip_h;marg_top=Math.round(top-(tip_h+5+opts.edgeOffset))}else if(bottom_compare|(t_class=="_top"&&bottom_compare)||(t_class=="_bottom"&&!top_compare)){if(t_class=="_top"||t_class=="_bottom"){t_class="_bottom"}else{t_class=t_class+"_bottom"}arrow_top=-12;marg_top=Math.round(top+org_height+opts.edgeOffset)}if(t_class=="_right_top"||t_class=="_left_top"){marg_top=marg_top+5}else if(t_class=="_right_bottom"||t_class=="_left_bottom"){marg_top=marg_top-5}if(t_class=="_left_top"||t_class=="_left_bottom"){marg_left=marg_left+5}tiptip_arrow.css({"margin-left":arrow_left+"px","margin-top":arrow_top+"px"});tiptip_holder.css({"margin-left":marg_left+"px","margin-top":marg_top+"px"}).attr("class","tip"+t_class);if(timeout){clearTimeout(timeout)}timeout=setTimeout(function(){tiptip_holder.stop(true,true).fadeIn(opts.fadeIn)},opts.delay)}function deactive_tiptip(){opts.exit.call(this);if(timeout){clearTimeout(timeout)}tiptip_holder.fadeOut(opts.fadeOut)}}})}})(jQuery);