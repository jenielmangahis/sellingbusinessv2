/* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){

	/*----------------------------------------------------*/
	/*  Inline CSS replacement for backgrounds etc.
	/*----------------------------------------------------*/
	function inlineCSS() {

		// Common Inline CSS
		$(".property-slider .item, .fullwidth-property-slider .item, .address-container").each(function() {
			var attrImageBG = $(this).attr('data-background-image');
			var attrColorBG = $(this).attr('data-background-color');

			$(this).css('background-image', 'url('+attrImageBG+')');
			$(this).css('background', ''+attrColorBG+'');
		});

	}

	// Init
	inlineCSS();


	$( '.realteo-ordering' ).on( 'change', 'select.orderby', function() {
		var order = $(this).val();
		console.log(order);
		$('#realteo-search-form #realteo_order').val(order);
		$( 'form#realteo-search-form' ).submit();
	});


	$('label.selectit input[type="checkbox"]').click(function(){
        $(this).parent().toggleClass('active');
    });
	
    /*----------------------------------------------------*/
    /*  Slick Carousel
    /*----------------------------------------------------*/
	 $('.property-slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		fade: true,
		asNavFor: '.property-slider-nav',
		centerMode: true,
		slide: ".item"
	});

	$('.property-slider-nav').slick({
		slidesToShow: 6,
		slidesToScroll: 1,
		asNavFor: '.property-slider',
		dots: false,
		arrows: false,
		centerMode: false,
		focusOnSelect: true,
		responsive: [
			{
			  breakpoint: 993,
			  settings: {
			   		slidesToShow: 4,
			  }
			},
			{
			  breakpoint: 767,
			  settings: {
			   		slidesToShow: 3,
			  }
			}
		]
	});


	 $('.fullwidth-property-slider').slick({
		centerMode: true,
		centerPadding: '20%',
		slidesToShow: 1, 
		responsive: [
			{
			  breakpoint: 1367,
			  settings: {
			    centerPadding: '15%'
			  }
			},
			{
			  breakpoint: 993,
			  settings: {
			    centerPadding: '0'
			  }
			}
		]
	});


	 $('.fullwidth-home-slider').slick({
		centerMode: true,
		centerPadding: '0',
		slidesToShow: 1, 
		responsive: [
			{
			  breakpoint: 1367,
			  settings: {
			    centerPadding: '0'
			  }
			},
			{
			  breakpoint: 993,
			  settings: {
			    centerPadding: '0'
			  }
			}
		]
	});

 	/*----------------------------------------------------*/
	/*  Mortgage Calculator
	/*----------------------------------------------------*/

	// Gets property price
	var propertyPricing = parseFloat($('.property-price').text().replace(/[^0-9\.]+/g,""));
	if (propertyPricing > 0) {
		$('.pick-price').on('click', function(){
			$('#amount').val(parseInt(propertyPricing));
		});
	}

	// replacing comma with dot
	
	$('.mortgageCalc').on( 'change', '#interest', function() {
		$("#interest").val($("#interest").val().replace(/,/g, '.'));
	});

	// Calculator
	function mortgageCalc() {

		var amount = parseFloat($("#amount").val().replace(/[^0-9\.]+/g,"")),
			months =parseFloat($("#years").val().replace(/[^0-9\.]+/g,"")*12),
			down = parseFloat($("#downpayment").val().replace(/[^0-9\.]+/g,"")),
			annInterest = parseFloat($("#interest").val().replace(/[^0-9\.]+/g,"")),
			monInt = annInterest / 1200,
			calculation = ((monInt + monInt / (Math.pow(1 + monInt, months) - 1)) * (amount - (down || 0))).toFixed(2);

			if (calculation > 0 ){
				$(".calc-output-container").css({'opacity' : '1', 'max-height' : '200px' });
				$(".calc-output").hide().html(calculation + ' ' + $('.mortgageCalc').attr("data-calc-currency")).fadeIn(300);
			}
	}

	// Calculate
	$('.calc-button').on('click', function(){
		mortgageCalc();
	});


	/*----------------------------------------------------*/
	/*  Compare Menu
	/*----------------------------------------------------*/
    $('.csm-trigger').on('click', function(){
		$('.compare-slide-menu').toggleClass('active');
	});
	
    $('.csm-mobile-trigger').on('click', function(){
		$('.compare-slide-menu').removeClass('active');
	});

    // Tooltips
	$(".compare-button.with-tip, .like-icon.with-tip, .widget-button.with-tip").each(function() {
		$(this).on('click', function(e){
	    	e.preventDefault();
		});
		var tipContent = $(this).attr('data-tip-content');
		if($(this).hasClass('already-added')){
			$(this).append('<div class="tip-content">'+ $(this).attr('data-tip-added-content') + '</div>');
		} else {
			$(this).append('<div class="tip-content">'+ tipContent + '</div>');
		}
		
	});

	$('.compare-button, .compare-widget-button').on('click', function(e){
            e.preventDefault();
            /*if it's already added show the sidebar and stop*/
            if($(this).hasClass('already-added')){
            	$('.compare-slide-menu').fadeIn().addClass('active');
            	return;
            }
			var tipAddingContent = $(this).attr('data-tip-adding-content');
			var tipAddedContent = $(this).attr('data-tip-added-content');

			/* if there are 1-3 properties, proceed to add on emore*/
            if($(".csm-properties .listing-item").length < 4) {
            	$(this).find(".tip-content").html(tipAddingContent);
            	
            	$('.compare-button, .compare-widget-button').fadeIn();
				$(this).addClass('clicked');
	            var post_id 	= $(this).data("post_id"),
	            handler 		= $(this),
	            nonce 			= $(this).data("nonce"),
	            addedtolist 	= $(this).data("saved-title")

	            $.ajax({
	               type 	: "post",
	               dataType : "json",
	               url 		: realteo.ajax_url,
	               data 	: {action: "realteo_compare_this", post_id : post_id, nonce: nonce},
	               success	: function(response) {
	                  if(response.type == "success") {
	                     handler.removeClass('clicked').addClass('compared').addClass('already-added');
	                		$('.csm-properties').append(response.html);
	                		$('.compare-slide-menu').fadeIn().addClass('active');
	                		handler.find(".tip-content").html(tipAddedContent);
	                		if($(".csm-properties .listing-item").length > 4) {
					            	$('.compare-slide-menu').fadeIn().addClass('active');
					            	$('.compare-slide-menu .notification').show();
					            	setTimeout( function(){ $('.compare-slide-menu .notification').fadeOut() }, 2500 );
					            }
	                  } else {
	                     handler.removeClass('clicked')
	                     handler.find(".tip-content").html(response.message);
	                  }
	               }
	            });
            } else {
            	$('.compare-slide-menu').fadeIn().addClass('active');
            	$('.compare-slide-menu .notification').show();
            	setTimeout( function(){ $('.compare-slide-menu .notification').fadeOut() }, 2500 );
            }
        });



        $(".compare-list-container .remove-from-compare").on('click', function(e){

            e.preventDefault();
            var handler = $(this);
            var post_id = $(this).data("post_id");
            var nonce = $(this).data("nonce");
            var index = handler.parent().parent().parent().index();
			
            $( "#compare-list li.compare-list-properties > div:eq("+index+")").addClass('opacity-05');
            $.ajax({
               type 	: "post",
               dataType : "json",
               url 		: realteo.ajax_url,
               data 	: {action: "realteo_uncompare_this", post_id : post_id, nonce: nonce},
               success	: function(response) {
                  
                  if(response.type == "success") {
                  		console.log($('#compare-list li.compare-list-properties > div').length);
                  		var number_left = $('#compare-list li.compare-list-properties > div').length;
                  		if(number_left == 2) {
                  			$('.compare-list-container').fadeOut(300, function() { $(this).remove(); });
                  			$('.nothing-compares-2u').show();
                  		}
						$( "#compare-list li.compare-list-properties > div:eq("+index+")").fadeOut(300, function() { $(this).remove(); });
						$( "#compare-list li:not(.compare-list-properties)" ).each(function() {
						  $( this ).find('div:eq('+index+' )').fadeOut(300, function() { $(this).remove(); });
						});
                  }
                  else {
                     
                     $( "#compare-list li.compare-list-properties > div:eq("+index+")").removeClass('opacity-05');
                  }
               }
            })   
        }); 

        
		$('.compare-slide-menu').on( 'click', '.reset-compare', function(e) {
			e.preventDefault();
            var handler = $(this);
            var nonce = $(this).data("nonce");
            
	            $(".csm-properties").addClass('opacity-05');
	            $.ajax({
	               type 	: "post",
	               dataType : "json",
	               url 		: realteo.ajax_url,
	               data 	: {action: "realteo_uncompare_all", nonce: nonce},
	               success	: function(response) {
	                  if(response.type == "success") {
							$(".csm-properties").empty(); 
							$('.compare-slide-menu').removeClass('active');
							setTimeout( function(){ $('.compare-slide-menu').fadeOut().removeClass('opacity-05') }, 500 );
	                  } else {
	                    alert(response.message);
	                    $(".csm-properties").removeClass('opacity-05');
	                  }
	               }
	            })  
		});

		$('.compare-slide-menu').on( 'click', '.remove-from-compare', function(e) {
            e.preventDefault();
            var handler = $(this);
            var post_id = $(this).data("post_id");
            var nonce = $(this).data("nonce");
            
	            $(this).parent().parent().addClass('opacity-05');
	            $.ajax({
	               type 	: "post",
	               dataType : "json",
	               url 		: realteo.ajax_url,
	               data 	: {action: "realteo_uncompare_this", post_id : post_id, nonce: nonce},
	               success	: function(response) {
	                  if(response.type == "success") {
							handler.parent().parent().fadeOut(300, function() { 
								$(this).remove(); 
								$('.compare-widget-button.with-tip').removeClass('already-added').find('.tip-content').html($('.compare-widget-button.with-tip').data('tip-content'));

								if ($(".csm-properties .listing-item").length == 0){ 
									$('.compare-slide-menu').removeClass('active')
									setTimeout( function(){ $('.compare-slide-menu').fadeOut() }, 500 );
								}
								
							});
							
	                  } else {
	                    alert(response.message);
	                    handler.parent().parent().removeClass('opacity-05');
	                  }
	               }
	            })   
            
        });
	


    /*----------------------------------------------------*/
    /*  Owl Carousel
    /*----------------------------------------------------*/

	$('.carousel').owlCarousel({
		autoPlay: false,
		navigation: true,
		slideSpeed: 600,
		items : 3,
		itemsDesktop : [1239,3],
		itemsTablet : [991,2],
		itemsMobile : [767,1]
	});


	$('.logo-carousel').owlCarousel({
		autoPlay: false,
		navigation: true,
		slideSpeed: 600,
		items : 5,
		itemsDesktop : [1239,4],
		itemsTablet : [991,3],
		itemsMobile : [767,1]
	});


	$('.listing-carousel').owlCarousel({
		autoPlay: false,
		navigation: true,
		slideSpeed: 800,
		items : 1,
		itemsDesktop : [1239,1],
		itemsTablet : [991,1],
		itemsMobile : [767,1]
	});

    $('.owl-next, .owl-prev').on("click", function (e) {
        e.preventDefault(); 
     });


     $('#realteo_reset_filters').on("click", function (e) {
     	e.preventDefault();
        $('#realteo-search-form').get(0).reset();
        $('#realteo-search-form input').val('');
        $("#realteo-search-form .chosen-select").val('').trigger("chosen:updated");
        $('#realteo-search-form').find('input[type=checkbox]:checked').removeAttr('checked');
        
       	$(".first-slider-value").each(function() {
       		var name = $(this).attr('name');
       		var val = realteo[name];
       		var $slider = $(this).parent();
			$slider.slider("values", 0, val);
       		$(this).val(val);
       	});       	
       	$(".second-slider-value").each(function() {
       		var name = $(this).attr('name');
       		var val = realteo[name];
       		var $slider = $(this).parent();
			$slider.slider("values", 1, val);
       		$(this).val(val);
       	});
  	});
    /*----------------------------------------------------*/
    /*  Chosen Plugin
    /*----------------------------------------------------*/

    var config = {
      '.chosen-select'           : {disable_search_threshold: 10, width:"100%"},
      '.chosen-select-deselect'  : {allow_single_deselect:true, width:"100%"},
      '.chosen-select-no-single' : {disable_search_threshold:100, width:"100%"},
      '.chosen-select-no-single.no-search' : {disable_search_threshold:10, width:"100%"},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    };

    for (var selector in config) {
	   	if (config.hasOwnProperty(selector)) {
	      $(selector).chosen(config[selector]);
	  	}
    }


    /*  Custom Input With Select
    /*----------------------------------------------------*/
	$('.select-input').each(function(){

		var thisContainer = $(this);
	    var $this = $(this).children('select'), numberOfOptions = $this.children('option').length;
	  
	    $this.addClass('select-hidden'); 
	    $this.wrap('<div class="select"></div>');
	    $this.after('<div class="select-styled"></div>');
	    var $styledSelect = $this.next('div.select-styled');
	    $styledSelect.text($this.children('option').eq(0).text());
	  
	    var $list = $('<ul />', {
	        'class': 'select-options'
	    }).insertAfter($styledSelect);
	  
	    for (var i = 0; i < numberOfOptions; i++) {
	        $('<li />', {
	            text: $this.children('option').eq(i).text(),
	            rel: $this.children('option').eq(i).val()
	        }).appendTo($list);
	    }
	  
	    var $listItems = $list.children('li');
	 
	 	$list.wrapInner('<div class="select-list-container"></div>');


	    $(this).children('input').on('click', function(e){
	    	$('.select-options').hide();
	        e.stopPropagation();
	        $styledSelect.toggleClass('active').next('ul.select-options').toggle();
	     });

	    $(this).children('input').keypress(function() {
	        $styledSelect.removeClass('active');
	        $list.hide();
	    });

 
	    $listItems.on('click', function(e){
	        e.stopPropagation();
	        // $styledSelect.text($(this).text()).removeClass('active');
	        $(thisContainer).children('input').val( $(this).text() ).removeClass('active');
	        $this.val($(this).attr('rel'));
	        $list.hide();
	        //console.log($this.val());
	    });
	  
	    $(document).on('click', function(e){
	        $styledSelect.removeClass('active');
	        $list.hide();
	    });


	    // Unit character
	    var fieldUnit = $(this).children('input').attr('data-unit');
	    if(fieldUnit) {
	   		$(this).children('input').before('<i class="data-unit">'+ fieldUnit + '</i>'); 	
	    }
	   


	});


	
    /*----------------------------------------------------*/
    /*  Search Handler
    /*----------------------------------------------------*/

    $('.main-search-input #keyword_search').change(function() {
      
        $('.sidebar #search_keywords').val($(this).val());
    });

    if($('#realteo-search-form').length>0){
    	$("#myButton").click(function() {
           $("#myForm").submit();
       });
    }  

    /*----------------------------------------------------*/
    /*  Searh Form More Options
    /*----------------------------------------------------*/
    $('.more-search-options-trigger').on('click', function(e){
    	e.preventDefault();
		$('.more-search-options, .more-search-options-trigger').toggleClass('active');
		$('.more-search-options.relative').animate({height: 'toggle', opacity: 'toggle'}, 300);
	});
	if ($("#realteo-search-form .more-search-options input:checkbox:checked").length > 0) {
		$('.more-search-options, .more-search-options-trigger').toggleClass('active');
		$('.more-search-options.relative').animate({height: 'toggle', opacity: 'toggle'}, 300);
	}


	/*----------------------------------------------------*/
	/*  Range Sliders
	/*----------------------------------------------------*/


	// Range
	$(".range-slider-element").each(function() {
		var id = $(this).attr('id');
		var offer_type = $('#_offer_type').val();

		if( id == '_price' && offer_type ){
			var	dataMin = realteo[offer_type + '_' + id + '_min'],
				dataMax = realteo[offer_type + '_' + id + '_max'];
		} else {
			var	dataMin = realteo[id+'_min'],
				dataMax = realteo[id+'_max'];
		}

		if(typeof dataMin == typeof undefined) {
			dataMin = $(this).attr('data-min');
		}	
		if(typeof dataMax == typeof undefined) {
			dataMax = $(this).attr('data-max');
		}
		var 
		dataValueMax = $(this).attr('data-value-max'),
		dataValueMin = $(this).attr('data-value-min'),
		dataUnit = $(this).attr('data-unit'),
		name = $(this).attr('id');

		$(this).append( "<input type='text' name='"+name+"_min' class='first-slider-value' /><input type='text' name='"+name+"_max' class='second-slider-value' />" );

		console.log(dataMax);
		$(this).slider({
		  range: true,
		  min: parseInt(dataMin),
		  max: parseInt(dataMax),
		  values: [ parseInt(dataValueMin), parseInt(dataValueMax) ],

		  slide: function( event, ui ) {
			 event = event;
			 $(this).children( ".first-slider-value" ).val( dataUnit  + ui.values[ 0 ].toString() );
			 $(this).children( ".second-slider-value" ).val( dataUnit +  ui.values[ 1 ].toString() );
		  }
		});
		 $(this).children( ".first-slider-value" ).val( dataUnit + $( this ).slider( "values", 0 ).toString() );
		 $(this).children( ".second-slider-value" ).val( dataUnit  +  $( this ).slider( "values", 1 ).toString() );


	});


	 $(".realteo-bookmark-it").click( function(e) {
            e.preventDefault();
     		var tipAddingContent = $(this).attr('data-tip-content-bookmarking');
     		var tipAddedContent = $(this).attr('data-tip-content-bookmarked');
			
            if($(this).is('.clicked,.liked')){
            	return;
            }
			$(this).addClass('clicked');
			$(this).find(".tip-content").html(tipAddingContent);
    
            var post_id 	= $(this).data("post_id"),
            handler 		= $(this),
            nonce 			= $(this).data("nonce"),
            addedtolist 	= $(this).data("saved-title")

            $.ajax({
               type 	: "post",
               dataType : "json",
               url 		: realteo.ajax_url,
               data 	: {action: "realteo_bookmark_this", post_id : post_id, nonce: nonce},
               success	: function(response) {
                  if(response.type == "success") {
                     handler.removeClass('clicked').addClass('liked');
                	handler.find(".tip-content").html(tipAddedContent);
                  }
                  else {
                     alert(response.message);
                     handler.removeClass('clicked')
                  }
               }
            })   
        });



        $(".realteo-unbookmark-it").click( function(e) {
            e.preventDefault();
            var handler = $(this);
            var post_id = $(this).data("post_id");
            var nonce = $(this).data("nonce");
            handler.closest('tr').addClass('opacity-05');
            $.ajax({
               type 	: "post",
               dataType : "json",
               url 		: realteo.ajax_url,
               data 	: {action: "realteo_unbookmark_this", post_id : post_id, nonce: nonce},
               success	: function(response) {
                  console.log(response);
                  if(response.type == "success") {
                      handler.closest('tr').fadeOut();
                  }
                  else {
                     
                     handler.closest('tr').removeClass('opacity-05');
                  }
               }
            })   
        });


	  // Tooltip
	$(".tip").each(function() {
		var tipContent = $(this).attr('data-tip-content');
		$(this).append('<div class="tip-content">'+ tipContent + '</div>');
	});

    /*----------------------------------------------------*/
    /*  Listing Layout Switcher
    /*----------------------------------------------------*/
	function gridLayoutSwitcher() {

		var listingsContainer = $('.listings-container');

		// switcher buttons / anchors
		if ( $(listingsContainer).is(".list-layout") ) {
			owlReload();
			$('.layout-switcher a.grid, .layout-switcher a.grid-three').removeClass("active");
			$('.layout-switcher a.list').addClass("active");
		}

		if ( $(listingsContainer).is(".grid-layout") ) {
			owlReload();
			$('.layout-switcher a.grid').addClass("active");
			$('.layout-switcher a.grid-three, .layout-switcher a.list').removeClass("active");
			gridClear(2);
		}

		if ( $(listingsContainer).is(".grid-layout-three") ) {
			owlReload();
			$('.layout-switcher a.grid, .layout-switcher a.list').removeClass("active");
			$('.layout-switcher a.grid-three').addClass("active");
			gridClear(3);
		}


		// grid cleaning
		function gridClear(gridColumns) {
			$(listingsContainer).find(".clearfix").remove();
			$(".listings-container > .listing-item:nth-child("+gridColumns+"n)").after("<div class='clearfix'></div>");
		}


		// objects that need to resized
		var resizeObjects =  $('.listings-container .listing-img-container img, .listings-container .listing-img-container').not('.listings-container.compact .listing-img-container img, .listings-container.compact .listing-img-container');

		// if list layout is active
		function listLayout() {
			if ( $('.layout-switcher a').is(".list.active") ) {

				$(listingsContainer).each(function(){
					$(this).removeClass("grid-layout grid-layout-three");
					$(this).addClass("list-layout");
				});

				$('.listing-item').each(function(){
					var listingContent = $(this).find('.listing-content').height();
					$(this).find(resizeObjects).css('height', ''+listingContent+'');
				});
			}
		} listLayout();
		
		$(window).on('load resize', function() {
			listLayout();
		});


		// if grid layout is active
		$('.layout-switcher a.grid').on('click', function(e) { gridClear(2); });

		function gridLayout() {
			if ( $('.layout-switcher a').is(".grid.active") ) {

				$(listingsContainer).each(function(){
					$(this).removeClass("list-layout grid-layout-three");
					$(this).addClass("grid-layout");
				});

				$('.listing-item').each(function(){
					$(this).find(resizeObjects).css('height', 'auto');
				});

			}
		} gridLayout();


		// if grid layout is active
		$('.layout-switcher a.grid-three').on('click', function(e) { gridClear(3); });

		function gridThreeLayout() {
			if ( $('.layout-switcher a').is(".grid-three.active") ) {

				$(listingsContainer).each(function(){
					$(this).removeClass("list-layout grid-layout");
					$(this).addClass("grid-layout-three");
				});

				$('.listing-item').each(function(){
					$(this).find(resizeObjects).css('height', 'auto');
				});

			}
		} gridThreeLayout();


		// Mobile fixes
		$(window).on('resize', function() {
			$(resizeObjects).css('height', '0');
			listLayout();
			gridLayout();
			gridThreeLayout();
		});

		$(window).on('load resize', function() {
			var winWidth = $(window).width();

			if(winWidth < 992) {
				owlReload();

				// reset to two columns grid
				gridClear(2);
			}

			if(winWidth > 992) {
				if ( $(listingsContainer).is(".grid-layout-three") ) {
					gridClear(3);
				}
				if ( $(listingsContainer).is(".grid-layout") ) {
					gridClear(2);
				}
			}

			if(winWidth < 768) {
				if ( $(listingsContainer).is(".list-layout") ) {
					$('.listing-item').each(function(){
						$(this).find(resizeObjects).css('height', 'auto');
					});
				}
			}

			if(winWidth < 1366) {
				if ( $(".fs-listings").is(".list-layout") ) {
					$('.listing-item').each(function(){
						$(this).find(resizeObjects).css('height', 'auto');
					});
				}
			}
		});


		// owlCarousel reload
		function owlReload() {
			$('.listing-carousel').each(function(){
				$(this).data('owlCarousel').reload();
			});
		}


	    // switcher buttons
		$('.layout-switcher a').on('click', function(e) {
		    e.preventDefault();

		    var switcherButton = $(this);
		    switcherButton.addClass("active").siblings().removeClass('active');

		    // reset images height
			$(resizeObjects).css('height', '0');

		    // carousel reload
			owlReload();

		    // if grid layout is active
			gridLayout();

		    // if three columns grid layout is active
			gridThreeLayout();

			// if list layout is active
			listLayout();

		});

	} gridLayoutSwitcher();
	

    /*  Adjusting Similar Listings List Layout */
    function similarPropertiesResize() {
	    $('.listings-container.list-layout .listing-item').each(function(){
		    var listingContentS = $(this).find('.listing-content').height();
	    	$(this).find('.listing-img-container img, .listing-img-container').css('height', ''+listingContentS+'');
    	});
    } 
    similarPropertiesResize();
		
    $(window).on('load resize', function() {
        similarPropertiesResize();
    });




	/*----------------------------------------------------*/
	/*  Masonry
	/*----------------------------------------------------*/

	// Agent Profile Alignment
    $(window).on('load resize', function() {

		$('.agents-grid-container').masonry({
			itemSelector: '.grid-item', // use a separate class for itemSelector, other than .col-
			columnWidth: '.grid-item',
			percentPosition: true,
			horizontalOrder: true
		});

		var agentAvatarHeight = $(".agent-avatar img").height();
		var agentContentHeight = $(".agent-content").innerHeight();

		if ( agentAvatarHeight < agentContentHeight ) {
			$('.agent-page').addClass('long-content');
		} else  {
			$('.agent-page').removeClass('long-content');
		}
    });

if($("#avatar-uploader").length>0) {
	 /* Upload using dropzone */
    Dropzone.autoDiscover = false;

   	var avatarDropzone = new Dropzone ("#avatar-uploader", {
    	url: realteo.upload,
    	maxFiles:1,
	    acceptedFiles: 'image/*',
		accept: function(file, done) {
		    console.log("uploaded");
		    done();
		  },
		init: function() {
		      this.on("addedfile", function() {
			      if (this.files[1]!=null){
			        this.removeFile(this.files[0]);
			      }
			    });
		},   

	    success: function (file, response) {
	        file.previewElement.classList.add("dz-success");
	        file['attachment_id'] = response; // push the id for future reference
	        $("#avatar-uploader-id").val(file['attachment_id']);

	    },
	    error: function (file, response) {
	        file.previewElement.classList.add("dz-error");
	    },
	    // update the following section is for removing image from library
	    addRemoveLinks: true,
	    removedfile: function(file) {
	    	var attachment_id = file['attachment_id'];
	        $("#avatar-uploader-id").val('');
	        $.ajax({
	            type: 'POST',
	            url: realteo.delete,
	            data: {
	                media_id : attachment_id
	            }, 
	            success: function (result) {
                   console.log(result);
                },
                error: function () {
                    console.log("delete error");
                }
	        });
	        var _ref;
	        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
	    }
	});

	avatarDropzone.on("maxfilesexceeded", function(file)
	{
	    this.removeFile(file);
	});
	if($('.edit-profile-photo').attr('data-photo')){
		var mockFile = { name: $('.edit-profile-photo').attr('data-name'), size: $('.edit-profile-photo').attr('data-size') };
	    avatarDropzone.emit("addedfile", mockFile);
	    avatarDropzone.emit("thumbnail", mockFile, $('.edit-profile-photo').attr('data-photo'));
	    avatarDropzone.emit("complete", mockFile);
	    avatarDropzone.files.push(mockFile);
		// If you use the maxFiles option, make sure you adjust it to the
		// correct amount:
		
		avatarDropzone.options.maxFiles = 1;
	}


}


if($("#media-uploader").length>0) {
	 /* Upload using dropzone */
    Dropzone.autoDiscover = false;

   	var myDropzone = new Dropzone ("#media-uploader", {
    	url: realteo.upload,
    	maxFiles:99,
	    acceptedFiles: 'image/*',
	    init: function() {

	   		this.on("addedfile", function(file){
	   			/* Set active thumb class to preview that is used as thumbnail*/
	   			
    			if(file['attachment_id'] === parseInt($('#thumbnail').val())) {
    				file.previewElement.className += ' active-thumb gallery'+file['attachment_id'];
    			} else {
    				file.previewElement.className += ' gallery'+ parseInt(file['attachment_id']);
    			}
	             file.previewElement.addEventListener("click", function() {
	             	$('.dz-preview').removeClass('active-thumb');
				   $(this).addClass('active-thumb'); 
				   console.log($(this));
				   console.log(file);
				   var id = file['attachment_id'];  
				   $('#thumbnail').val(id); 
				});
	        })
	        ,
	        this.on("complete", function(file){
   				file.previewElement.className += ' gallery'+file.attachment_id;
	        });
	    },
	    success: function (file, response) {
	        file.previewElement.classList.add("dz-success");
	        file['attachment_id'] = response; // push the id for future reference
			
	        $("#media-uploader-ids").append('<input id="gallery' + file['attachment_id'] +'" type="hidden" name="_gallery[' +file['attachment_id']+ ']"  value="'+file['name']+'">');

	    },
	    error: function (file, response) {
	        file.previewElement.classList.add("dz-error");
	    },
	    // update the following section is for removing image from library
	    addRemoveLinks: true,
	    removedfile: function(file) {
	        var attachment_id = file['attachment_id'];   
	        $('input#_gallery'+attachment_id).remove();
	        /*remove thumbnail if the image was set as it*/
	        if($('#thumbnail').val() == attachment_id){
				$('#thumbnail').val('');
	        }
	        $.ajax({
	            type: 'POST',
	            url: realteo.delete,
	            data: {
	                media_id : attachment_id
	            }, 
	            success: function (result) {

                   console.log(result);
                },
                error: function () {
                    console.log("delete error");
                }
	        });
	        var _ref;
	        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
	    }
	});
	if (typeof images !== typeof undefined && images !== false) {
		var uploaded_media = jQuery.parseJSON(images);
		for (var i = 0; i < uploaded_media.length; ++i) {
		 	
		 		var mockFile = { name: uploaded_media[i].name, size: uploaded_media[i].size, attachment_id: uploaded_media[i].attachment_id };
		        myDropzone.emit("addedfile", mockFile);
		        myDropzone.emit("thumbnail", mockFile, uploaded_media[i].thumb);
		        myDropzone.emit("complete", mockFile);
		        myDropzone.files.push(mockFile);
				// If you use the maxFiles option, make sure you adjust it to the
				// correct amount:
				var existingFileCount = 1; // The number of files already uploaded
				myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
		}
	}
	  $(".dropzone").sortable({
        items:'.dz-preview',
        cursor: 'move',
        opacity: 0.5,
        containment: '.dropzone',
        distance: 20,
        tolerance: 'pointer',
/*        stop: function(event, ui) {
	        var data = "";

	        $(".dropzone .dz-preview").each(function(i, el){

	            var p = $(el).attr('class').match(/\d+/g);
	           console.log(p);
	        });
	    }*/
	    update: sortinputs
    }).disableSelection();

  	function sortinputs(){

	    $('.dropzone .dz-preview').each(function(i, el){
	    	var p = $(el).attr('class').match(/\d+/g);
	    	console.log( $('input#_gallery' + p ));
	        $('#media-uploader-ids input#_gallery' + p )
	            .remove()
	            .appendTo($('#media-uploader-ids'));
	                
	    });
	}
}
   
	$(document).on("click", ".realteo-submit-image-preview", function(){

		$('.realteo-submit-image-preview').removeClass('active-thumb');
		$(this).addClass('active-thumb');
		var id = $(this).data('thumb');
	
		$('#thumbnail').val(id);
	});

	var type = $('#_offer_type,#offer_type').val();
	if(type === 'rent') {
		$('#rental_period').prop('disabled', false).trigger("chosen:updated");
		$('#_rental_period').prop('disabled', false).trigger("chosen:updated");	
	} else {
		$('#rental_period').prop('disabled', true).trigger("chosen:updated");
    	$('#_rental_period').prop('disabled', true).trigger("chosen:updated");
	}
	
	$(document.body).on('change', '#_offer_type,#offer_type', function () {
        var type = $(this).val();
        if(type === 'rent') {
        	$('.range-slider-element#_price').slider("option", "min", parseInt(realteo.rent_price_min)); 
			$('.range-slider-element#_price').slider("option", "max", parseInt(realteo.rent_price_max)); 
			$('.range-slider-element#_price input[name=_price_min]').val( parseInt(realteo.rent_price_min));
			$('.range-slider-element#_price input[name=_price_max]').val( parseInt(realteo.rent_price_max));
        	$('#rental_period').prop('disabled', false).trigger("chosen:updated");
        	$('#_rental_period').prop('disabled', false).trigger("chosen:updated");
        } else {
        	$('#rental_period').prop('disabled', true).trigger("chosen:updated");
        	$('#_rental_period').prop('disabled', true).trigger("chosen:updated");
			$('.range-slider-element#_price').slider("option", "min", parseInt(realteo.sale_price_min)); 
			$('.range-slider-element#_price').slider("option", "max", parseInt(realteo.sale_price_max)); 
			$('.range-slider-element#_price input[name=_price_min]').val( parseInt(realteo.sale_price_min));
			$('.range-slider-element#_price input[name=_price_max]').val( parseInt(realteo.sale_price_max));
        }
    });

	$('.print-simple').on( "click", function() {
        window.print();
        return false;
    });


// ------------------ End Document ------------------ //
});

})(this.jQuery);
/**/

  var pfHeaderImgUrl = '';
  var pfHeaderTagline = '';
  var pfdisableClickToDel = '0';
  var pfHideImages = '0';
  var pfImageDisplayStyle = 'right';
  var pfDisableEmail = '0';
  var pfDisablePDF = '0';
  var pfDisablePrint = '0';
  var pfCustomCSS = '.rsImg {display:block;}';
(function() {
    var e = document.createElement('script'); e.type="text/javascript";
if('https:' == document.location.protocol) {
js='https://pf-cdn.printfriendly.com/ssl/main.js';
}
else{
js='http://cdn.printfriendly.com/printfriendly.js';
}
    e.src = js;
    document.getElementsByTagName('head')[0].appendChild(e);
})();

/*! jQuery UI - v1.12.1 - 2017-09-21
* http://jqueryui.com
* Includes: widget.js, widgets/mouse.js
* Copyright jQuery Foundation and other contributors; Licensed MIT
*
* jQuery UI Touch Punch 0.2.3
*
* Copyright 2011–2014, Dave Furfero
* Dual licensed under the MIT or GPL Version 2 licenses.
*
* Depends:
*  jquery.ui.widget.js
*  jquery.ui.mouse.js
*/

 !function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t(jQuery)}(function(t){t.ui=t.ui||{};t.ui.version="1.12.1";var e=0,i=Array.prototype.slice;t.cleanData=function(e){return function(i){var s,n,o;for(o=0;null!=(n=i[o]);o++)try{(s=t._data(n,"events"))&&s.remove&&t(n).triggerHandler("remove")}catch(t){}e(i)}}(t.cleanData),t.widget=function(e,i,s){var n,o,a,u={},r=e.split(".")[0],h=r+"-"+(e=e.split(".")[1]);return s||(s=i,i=t.Widget),t.isArray(s)&&(s=t.extend.apply(null,[{}].concat(s))),t.expr[":"][h.toLowerCase()]=function(e){return!!t.data(e,h)},t[r]=t[r]||{},n=t[r][e],o=t[r][e]=function(t,e){if(!this._createWidget)return new o(t,e);arguments.length&&this._createWidget(t,e)},t.extend(o,n,{version:s.version,_proto:t.extend({},s),_childConstructors:[]}),a=new i,a.options=t.widget.extend({},a.options),t.each(s,function(e,s){t.isFunction(s)?u[e]=function(){function t(){return i.prototype[e].apply(this,arguments)}function n(t){return i.prototype[e].apply(this,t)}return function(){var e,i=this._super,o=this._superApply;return this._super=t,this._superApply=n,e=s.apply(this,arguments),this._super=i,this._superApply=o,e}}():u[e]=s}),o.prototype=t.widget.extend(a,{widgetEventPrefix:n?a.widgetEventPrefix||e:e},u,{constructor:o,namespace:r,widgetName:e,widgetFullName:h}),n?(t.each(n._childConstructors,function(e,i){var s=i.prototype;t.widget(s.namespace+"."+s.widgetName,o,i._proto)}),delete n._childConstructors):i._childConstructors.push(o),t.widget.bridge(e,o),o},t.widget.extend=function(e){for(var s,n,o=i.call(arguments,1),a=0,u=o.length;a<u;a++)for(s in o[a])n=o[a][s],o[a].hasOwnProperty(s)&&void 0!==n&&(t.isPlainObject(n)?e[s]=t.isPlainObject(e[s])?t.widget.extend({},e[s],n):t.widget.extend({},n):e[s]=n);return e},t.widget.bridge=function(e,s){var n=s.prototype.widgetFullName||e;t.fn[e]=function(o){var a="string"==typeof o,u=i.call(arguments,1),r=this;return a?this.length||"instance"!==o?this.each(function(){var i,s=t.data(this,n);return"instance"===o?(r=s,!1):s?t.isFunction(s[o])&&"_"!==o.charAt(0)?(i=s[o].apply(s,u),i!==s&&void 0!==i?(r=i&&i.jquery?r.pushStack(i.get()):i,!1):void 0):t.error("no such method '"+o+"' for "+e+" widget instance"):t.error("cannot call methods on "+e+" prior to initialization; attempted to call method '"+o+"'")}):r=void 0:(u.length&&(o=t.widget.extend.apply(null,[o].concat(u))),this.each(function(){var e=t.data(this,n);e?(e.option(o||{}),e._init&&e._init()):t.data(this,n,new s(o,this))})),r}},t.Widget=function(){},t.Widget._childConstructors=[],t.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{classes:{},disabled:!1,create:null},_createWidget:function(i,s){s=t(s||this.defaultElement||this)[0],this.element=t(s),this.uuid=e++,this.eventNamespace="."+this.widgetName+this.uuid,this.bindings=t(),this.hoverable=t(),this.focusable=t(),this.classesElementLookup={},s!==this&&(t.data(s,this.widgetFullName,this),this._on(!0,this.element,{remove:function(t){t.target===s&&this.destroy()}}),this.document=t(s.style?s.ownerDocument:s.document||s),this.window=t(this.document[0].defaultView||this.document[0].parentWindow)),this.options=t.widget.extend({},this.options,this._getCreateOptions(),i),this._create(),this.options.disabled&&this._setOptionDisabled(this.options.disabled),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:function(){return{}},_getCreateEventData:t.noop,_create:t.noop,_init:t.noop,destroy:function(){var e=this;this._destroy(),t.each(this.classesElementLookup,function(t,i){e._removeClass(i,t)}),this.element.off(this.eventNamespace).removeData(this.widgetFullName),this.widget().off(this.eventNamespace).removeAttr("aria-disabled"),this.bindings.off(this.eventNamespace)},_destroy:t.noop,widget:function(){return this.element},option:function(e,i){var s,n,o,a=e;if(0===arguments.length)return t.widget.extend({},this.options);if("string"==typeof e)if(a={},s=e.split("."),e=s.shift(),s.length){for(n=a[e]=t.widget.extend({},this.options[e]),o=0;o<s.length-1;o++)n[s[o]]=n[s[o]]||{},n=n[s[o]];if(e=s.pop(),1===arguments.length)return void 0===n[e]?null:n[e];n[e]=i}else{if(1===arguments.length)return void 0===this.options[e]?null:this.options[e];a[e]=i}return this._setOptions(a),this},_setOptions:function(t){var e;for(e in t)this._setOption(e,t[e]);return this},_setOption:function(t,e){return"classes"===t&&this._setOptionClasses(e),this.options[t]=e,"disabled"===t&&this._setOptionDisabled(e),this},_setOptionClasses:function(e){var i,s,n;for(i in e)n=this.classesElementLookup[i],e[i]!==this.options.classes[i]&&n&&n.length&&(s=t(n.get()),this._removeClass(n,i),s.addClass(this._classes({element:s,keys:i,classes:e,add:!0})))},_setOptionDisabled:function(t){this._toggleClass(this.widget(),this.widgetFullName+"-disabled",null,!!t),t&&(this._removeClass(this.hoverable,null,"ui-state-hover"),this._removeClass(this.focusable,null,"ui-state-focus"))},enable:function(){return this._setOptions({disabled:!1})},disable:function(){return this._setOptions({disabled:!0})},_classes:function(e){function i(i,o){var a,u;for(u=0;u<i.length;u++)a=n.classesElementLookup[i[u]]||t(),a=t(e.add?t.unique(a.get().concat(e.element.get())):a.not(e.element).get()),n.classesElementLookup[i[u]]=a,s.push(i[u]),o&&e.classes[i[u]]&&s.push(e.classes[i[u]])}var s=[],n=this;return e=t.extend({element:this.element,classes:this.options.classes||{}},e),this._on(e.element,{remove:"_untrackClassesElement"}),e.keys&&i(e.keys.match(/\S+/g)||[],!0),e.extra&&i(e.extra.match(/\S+/g)||[]),s.join(" ")},_untrackClassesElement:function(e){var i=this;t.each(i.classesElementLookup,function(s,n){-1!==t.inArray(e.target,n)&&(i.classesElementLookup[s]=t(n.not(e.target).get()))})},_removeClass:function(t,e,i){return this._toggleClass(t,e,i,!1)},_addClass:function(t,e,i){return this._toggleClass(t,e,i,!0)},_toggleClass:function(t,e,i,s){s="boolean"==typeof s?s:i;var n="string"==typeof t||null===t,o={extra:n?e:i,keys:n?t:e,element:n?this.element:t,add:s};return o.element.toggleClass(this._classes(o),s),this},_on:function(e,i,s){var n,o=this;"boolean"!=typeof e&&(s=i,i=e,e=!1),s?(i=n=t(i),this.bindings=this.bindings.add(i)):(s=i,i=this.element,n=this.widget()),t.each(s,function(s,a){function u(){if(e||!0!==o.options.disabled&&!t(this).hasClass("ui-state-disabled"))return("string"==typeof a?o[a]:a).apply(o,arguments)}"string"!=typeof a&&(u.guid=a.guid=a.guid||u.guid||t.guid++);var r=s.match(/^([\w:-]*)\s*(.*)$/),h=r[1]+o.eventNamespace,c=r[2];c?n.on(h,c,u):i.on(h,u)})},_off:function(e,i){i=(i||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,e.off(i).off(i),this.bindings=t(this.bindings.not(e).get()),this.focusable=t(this.focusable.not(e).get()),this.hoverable=t(this.hoverable.not(e).get())},_delay:function(t,e){var i=this;return setTimeout(function(){return("string"==typeof t?i[t]:t).apply(i,arguments)},e||0)},_hoverable:function(e){this.hoverable=this.hoverable.add(e),this._on(e,{mouseenter:function(e){this._addClass(t(e.currentTarget),null,"ui-state-hover")},mouseleave:function(e){this._removeClass(t(e.currentTarget),null,"ui-state-hover")}})},_focusable:function(e){this.focusable=this.focusable.add(e),this._on(e,{focusin:function(e){this._addClass(t(e.currentTarget),null,"ui-state-focus")},focusout:function(e){this._removeClass(t(e.currentTarget),null,"ui-state-focus")}})},_trigger:function(e,i,s){var n,o,a=this.options[e];if(s=s||{},i=t.Event(i),i.type=(e===this.widgetEventPrefix?e:this.widgetEventPrefix+e).toLowerCase(),i.target=this.element[0],o=i.originalEvent)for(n in o)n in i||(i[n]=o[n]);return this.element.trigger(i,s),!(t.isFunction(a)&&!1===a.apply(this.element[0],[i].concat(s))||i.isDefaultPrevented())}},t.each({show:"fadeIn",hide:"fadeOut"},function(e,i){t.Widget.prototype["_"+e]=function(s,n,o){"string"==typeof n&&(n={effect:n});var a,u=n?!0===n||"number"==typeof n?i:n.effect||i:e;"number"==typeof(n=n||{})&&(n={duration:n}),a=!t.isEmptyObject(n),n.complete=o,n.delay&&s.delay(n.delay),a&&t.effects&&t.effects.effect[u]?s[e](n):u!==e&&s[u]?s[u](n.duration,n.easing,o):s.queue(function(i){t(this)[e](),o&&o.call(s[0]),i()})}});t.widget,t.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());var s=!1;t(document).on("mouseup",function(){s=!1});t.widget("ui.mouse",{version:"1.12.1",options:{cancel:"input, textarea, button, select, option",distance:1,delay:0},_mouseInit:function(){var e=this;this.element.on("mousedown."+this.widgetName,function(t){return e._mouseDown(t)}).on("click."+this.widgetName,function(i){if(!0===t.data(i.target,e.widgetName+".preventClickEvent"))return t.removeData(i.target,e.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1}),this.started=!1},_mouseDestroy:function(){this.element.off("."+this.widgetName),this._mouseMoveDelegate&&this.document.off("mousemove."+this.widgetName,this._mouseMoveDelegate).off("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(e){if(!s){this._mouseMoved=!1,this._mouseStarted&&this._mouseUp(e),this._mouseDownEvent=e;var i=this,n=1===e.which,o=!("string"!=typeof this.options.cancel||!e.target.nodeName)&&t(e.target).closest(this.options.cancel).length;return!(n&&!o&&this._mouseCapture(e))||(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){i.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(e)&&this._mouseDelayMet(e)&&(this._mouseStarted=!1!==this._mouseStart(e),!this._mouseStarted)?(e.preventDefault(),!0):(!0===t.data(e.target,this.widgetName+".preventClickEvent")&&t.removeData(e.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(t){return i._mouseMove(t)},this._mouseUpDelegate=function(t){return i._mouseUp(t)},this.document.on("mousemove."+this.widgetName,this._mouseMoveDelegate).on("mouseup."+this.widgetName,this._mouseUpDelegate),e.preventDefault(),s=!0,!0))}},_mouseMove:function(e){if(this._mouseMoved){if(t.ui.ie&&(!document.documentMode||document.documentMode<9)&&!e.button)return this._mouseUp(e);if(!e.which)if(e.originalEvent.altKey||e.originalEvent.ctrlKey||e.originalEvent.metaKey||e.originalEvent.shiftKey)this.ignoreMissingWhich=!0;else if(!this.ignoreMissingWhich)return this._mouseUp(e)}return(e.which||e.button)&&(this._mouseMoved=!0),this._mouseStarted?(this._mouseDrag(e),e.preventDefault()):(this._mouseDistanceMet(e)&&this._mouseDelayMet(e)&&(this._mouseStarted=!1!==this._mouseStart(this._mouseDownEvent,e),this._mouseStarted?this._mouseDrag(e):this._mouseUp(e)),!this._mouseStarted)},_mouseUp:function(e){this.document.off("mousemove."+this.widgetName,this._mouseMoveDelegate).off("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,e.target===this._mouseDownEvent.target&&t.data(e.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(e)),this._mouseDelayTimer&&(clearTimeout(this._mouseDelayTimer),delete this._mouseDelayTimer),this.ignoreMissingWhich=!1,s=!1,e.preventDefault()},_mouseDistanceMet:function(t){return Math.max(Math.abs(this._mouseDownEvent.pageX-t.pageX),Math.abs(this._mouseDownEvent.pageY-t.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}})}),function(t){function e(t,e){if(!(t.originalEvent.touches.length>1)){t.preventDefault();var i=t.originalEvent.changedTouches[0],s=document.createEvent("MouseEvents");s.initMouseEvent(e,!0,!0,window,1,i.screenX,i.screenY,i.clientX,i.clientY,!1,!1,!1,!1,0,null),t.target.dispatchEvent(s)}}if(t.support.touch="ontouchend"in document,t.support.touch){var i,s=t.ui.mouse.prototype,n=s._mouseInit,o=s._mouseDestroy;s._touchStart=function(t){var s=this;!i&&s._mouseCapture(t.originalEvent.changedTouches[0])&&(i=!0,s._touchMoved=!1,e(t,"mouseover"),e(t,"mousemove"),e(t,"mousedown"))},s._touchMove=function(t){i&&(this._touchMoved=!0,e(t,"mousemove"))},s._touchEnd=function(t){i&&(e(t,"mouseup"),e(t,"mouseout"),this._touchMoved||e(t,"click"),i=!1)},s._mouseInit=function(){var e=this;e.element.bind({touchstart:t.proxy(e,"_touchStart"),touchmove:t.proxy(e,"_touchMove"),touchend:t.proxy(e,"_touchEnd")}),n.call(e)},s._mouseDestroy=function(){var e=this;e.element.unbind({touchstart:t.proxy(e,"_touchStart"),touchmove:t.proxy(e,"_touchMove"),touchend:t.proxy(e,"_touchEnd")}),o.call(e)}}}(jQuery);