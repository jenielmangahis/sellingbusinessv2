

/**
 * jQuery Geocoding and Places Autocomplete Plugin - V 1.7.0
 *
 * @author Martin Kleppe <kleppe@ubilabs.net>, 2016
 * @author Ubilabs http://ubilabs.net, 2016
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
(function($,window,document,undefined){var defaults={bounds:true,strictBounds:false,country:null,map:false,details:false,detailsAttribute:"name",detailsScope:null,autoselect:true,location:false,mapOptions:{zoom:14,scrollwheel:false,mapTypeId:"roadmap"},markerOptions:{draggable:false},maxZoom:16,types:["geocode"],blur:false,geocodeAfterResult:false,restoreValueAfterBlur:false};var componentTypes=("street_address route intersection political "+"country administrative_area_level_1 administrative_area_level_2 "+"administrative_area_level_3 colloquial_area locality sublocality "+"neighborhood premise subpremise postal_code natural_feature airport "+"park point_of_interest post_box street_number floor room "+"lat lng viewport location "+"formatted_address location_type bounds").split(" ");var placesDetails=("id place_id url website vicinity reference name rating "+"international_phone_number icon formatted_phone_number").split(" ");function GeoComplete(input,options){this.options=$.extend(true,{},defaults,options);if(options&&options.types){this.options.types=options.types}this.input=input;this.$input=$(input);this._defaults=defaults;this._name="geocomplete";this.init()}$.extend(GeoComplete.prototype,{init:function(){this.initMap();this.initMarker();this.initGeocoder();this.initDetails();this.initLocation()},initMap:function(){if(!this.options.map){return}if(typeof this.options.map.setCenter=="function"){this.map=this.options.map;return}this.map=new google.maps.Map($(this.options.map)[0],this.options.mapOptions);google.maps.event.addListener(this.map,"click",$.proxy(this.mapClicked,this));google.maps.event.addListener(this.map,"dragend",$.proxy(this.mapDragged,this));google.maps.event.addListener(this.map,"idle",$.proxy(this.mapIdle,this));google.maps.event.addListener(this.map,"zoom_changed",$.proxy(this.mapZoomed,this))},initMarker:function(){if(!this.map){return}var options=$.extend(this.options.markerOptions,{map:this.map});if(options.disabled){return}this.marker=new google.maps.Marker(options);google.maps.event.addListener(this.marker,"dragend",$.proxy(this.markerDragged,this))},initGeocoder:function(){var selected=false;var options={types:this.options.types,bounds:this.options.bounds===true?null:this.options.bounds,componentRestrictions:this.options.componentRestrictions,strictBounds:this.options.strictBounds};if(this.options.country){options.componentRestrictions={country:this.options.country}}this.autocomplete=new google.maps.places.Autocomplete(this.input,options);this.geocoder=new google.maps.Geocoder;if(this.map&&this.options.bounds===true){this.autocomplete.bindTo("bounds",this.map)}google.maps.event.addListener(this.autocomplete,"place_changed",$.proxy(this.placeChanged,this));this.$input.on("keypress."+this._name,function(event){if(event.keyCode===13){return false}});if(this.options.geocodeAfterResult===true){this.$input.bind("keypress."+this._name,$.proxy(function(){if(event.keyCode!=9&&this.selected===true){this.selected=false}},this))}this.$input.bind("geocode."+this._name,$.proxy(function(){this.find()},this));this.$input.bind("geocode:result."+this._name,$.proxy(function(){this.lastInputVal=this.$input.val()},this));if(this.options.blur===true){this.$input.on("blur."+this._name,$.proxy(function(){if(this.options.geocodeAfterResult===true&&this.selected===true){return}if(this.options.restoreValueAfterBlur===true&&this.selected===true){setTimeout($.proxy(this.restoreLastValue,this),0)}else{this.find()}},this))}},initDetails:function(){if(!this.options.details){return}if(this.options.detailsScope){var $details=$(this.input).parents(this.options.detailsScope).find(this.options.details)}else{var $details=$(this.options.details)}var attribute=this.options.detailsAttribute,details={};function setDetail(value){details[value]=$details.find("["+attribute+"="+value+"]")}$.each(componentTypes,function(index,key){setDetail(key);setDetail(key+"_short")});$.each(placesDetails,function(index,key){setDetail(key)});this.$details=$details;this.details=details},initLocation:function(){var location=this.options.location,latLng;if(!location){return}if(typeof location=="string"){this.find(location);return}if(location instanceof Array){latLng=new google.maps.LatLng(location[0],location[1])}if(location instanceof google.maps.LatLng){latLng=location}if(latLng){if(this.map){this.map.setCenter(latLng)}if(this.marker){this.marker.setPosition(latLng)}}},destroy:function(){if(this.map){google.maps.event.clearInstanceListeners(this.map);google.maps.event.clearInstanceListeners(this.marker)}this.autocomplete.unbindAll();google.maps.event.clearInstanceListeners(this.autocomplete);google.maps.event.clearInstanceListeners(this.input);this.$input.removeData();this.$input.off(this._name);this.$input.unbind("."+this._name)},find:function(address){this.geocode({address:address||this.$input.val()})},geocode:function(request){if(!request.address){return}if(this.options.bounds&&!request.bounds){if(this.options.bounds===true){request.bounds=this.map&&this.map.getBounds()}else{request.bounds=this.options.bounds}}if(this.options.country){request.region=this.options.country}this.geocoder.geocode(request,$.proxy(this.handleGeocode,this))},selectFirstResult:function(){var selected="";if($(".pac-item-selected")[0]){selected="-selected"}var $span1=$(".pac-container:visible .pac-item"+selected+":first span:nth-child(2)").text();var $span2=$(".pac-container:visible .pac-item"+selected+":first span:nth-child(3)").text();var firstResult=$span1;if($span2){firstResult+=" - "+$span2}this.$input.val(firstResult);return firstResult},restoreLastValue:function(){if(this.lastInputVal){this.$input.val(this.lastInputVal)}},handleGeocode:function(results,status){if(status===google.maps.GeocoderStatus.OK){var result=results[0];this.$input.val(result.formatted_address);this.update(result);if(results.length>1){this.trigger("geocode:multiple",results)}}else{this.trigger("geocode:error",status)}},trigger:function(event,argument){this.$input.trigger(event,[argument])},center:function(geometry){if(geometry.viewport){this.map.fitBounds(geometry.viewport);if(this.map.getZoom()>this.options.maxZoom){this.map.setZoom(this.options.maxZoom)}}else{this.map.setZoom(this.options.maxZoom);this.map.setCenter(geometry.location)}if(this.marker){this.marker.setPosition(geometry.location);this.marker.setAnimation(this.options.markerOptions.animation)}},update:function(result){if(this.map){this.center(result.geometry)}if(this.$details){this.fillDetails(result)}this.trigger("geocode:result",result)},fillDetails:function(result){var data={},geometry=result.geometry,viewport=geometry.viewport,bounds=geometry.bounds;$.each(result.address_components,function(index,object){var name=object.types[0];$.each(object.types,function(index,name){data[name]=object.long_name;data[name+"_short"]=object.short_name})});$.each(placesDetails,function(index,key){data[key]=result[key]});$.extend(data,{formatted_address:result.formatted_address,location_type:geometry.location_type||"PLACES",viewport:viewport,bounds:bounds,location:geometry.location,lat:geometry.location.lat(),lng:geometry.location.lng()});$.each(this.details,$.proxy(function(key,$detail){var value=data[key];this.setDetail($detail,value)},this));this.data=data},setDetail:function($element,value){if(value===undefined){value=""}else if(typeof value.toUrlValue=="function"){value=value.toUrlValue()}if($element.is(":input")){$element.val(value)}else{$element.text(value)}},markerDragged:function(event){this.trigger("geocode:dragged",event.latLng)},mapClicked:function(event){this.trigger("geocode:click",event.latLng)},mapDragged:function(event){this.trigger("geocode:mapdragged",this.map.getCenter())},mapIdle:function(event){this.trigger("geocode:idle",this.map.getCenter())},mapZoomed:function(event){this.trigger("geocode:zoom",this.map.getZoom())},resetMarker:function(){this.marker.setPosition(this.data.location);this.setDetail(this.details.lat,this.data.location.lat());this.setDetail(this.details.lng,this.data.location.lng())},placeChanged:function(){var place=this.autocomplete.getPlace();this.selected=true;if(!place.geometry){if(this.options.autoselect){var autoSelection=this.selectFirstResult();this.find(autoSelection)}}else{this.update(place)}}});$.fn.geocomplete=function(options){var attribute="plugin_geocomplete";if(typeof options=="string"){var instance=$(this).data(attribute)||$(this).geocomplete().data(attribute),prop=instance[options];if(typeof prop=="function"){prop.apply(instance,Array.prototype.slice.call(arguments,1));return $(this)}else{if(arguments.length==2){prop=arguments[1]}return prop}}else{return this.each(function(){var instance=$.data(this,attribute);if(!instance){instance=new GeoComplete(this,options);$.data(this,attribute,instance)}})}}})(jQuery,window,document);

(function($){
    "use strict";


    // Marker
    // ----------------------------------------------- //
    var markerIcon = {
        path: 'M19.9,0c-0.2,0-1.6,0-1.8,0C8.8,0.6,1.4,8.2,1.4,17.8c0,1.4,0.2,3.1,0.5,4.2c-0.1-0.1,0.5,1.9,0.8,2.6c0.4,1,0.7,2.1,1.2,3 c2,3.6,6.2,9.7,14.6,18.5c0.2,0.2,0.4,0.5,0.6,0.7c0,0,0,0,0,0c0,0,0,0,0,0c0.2-0.2,0.4-0.5,0.6-0.7c8.4-8.7,12.5-14.8,14.6-18.5 c0.5-0.9,0.9-2,1.3-3c0.3-0.7,0.9-2.6,0.8-2.5c0.3-1.1,0.5-2.7,0.5-4.1C36.7,8.4,29.3,0.6,19.9,0z M2.2,22.9 C2.2,22.9,2.2,22.9,2.2,22.9C2.2,22.9,2.2,22.9,2.2,22.9C2.2,22.9,3,25.2,2.2,22.9z M19.1,26.8c-5.2,0-9.4-4.2-9.4-9.4 s4.2-9.4,9.4-9.4c5.2,0,9.4,4.2,9.4,9.4S24.3,26.8,19.1,26.8z M36,22.9C35.2,25.2,36,22.9,36,22.9C36,22.9,36,22.9,36,22.9 C36,22.9,36,22.9,36,22.9z M13.8,17.3a5.3,5.3 0 1,0 10.6,0a5.3,5.3 0 1,0 -10.6,0',
        strokeOpacity: 0,
        strokeWeight: 1,
        fillColor: '#274abb',
        fillOpacity: 1,
        rotation: 0,
        scale: 1,
        anchor: new google.maps.Point(19,50)
    }

    if(realteo.centerPoint) {
      var latlngStr = realteo.centerPoint.split(",",2);
      var lat = parseFloat(latlngStr[0]);
      var lng = parseFloat(latlngStr[1]);

      var center = new google.maps.LatLng(lat, lng);
    } else {
      var center = new google.maps.LatLng(-33.92, 151.25);
    }


    function locationData(locationURL,locationPrice,locationImg,locationTitle,locationAddress) {
        return('<a href="'+ locationURL +'" class="listing-img-container"><div class="infoBox-close"><i class="fa fa-times"></i></div><div class="listing-img-content"><span class="listing-price">'+ locationPrice +'</i></span></div><img src="'+locationImg+'" alt=""></a><div class="listing-content"><div class="listing-title"><h4><a href="#">'+locationTitle+'</a></h4><p>'+locationAddress+'</p></div></div>')
    }

    function getMarkers() {
      var arrMarkers = [];
      $('div.listing-item').each(function(index) {
        if( $( this ).data('friendly-address') ){
          var point_address = $( this ).data('friendly-address');
        } else {
          var point_address = $( this ).data('address');
        }
        if( $( this ).data('longitude') ) {
          arrMarkers.push([ 
            locationData(
              $(this).find('a').attr('href'),
              $(this).data('price'),
              $(this).data('image'),
              $(this).data('title'),
              point_address,
            ),
            $( this ).data('longitude'), $( this ).data('latitude'), 1, markerIcon
          ])
        }
      });
      return arrMarkers;
    };

    /* Half Map Adjustments */
    $(window).on('load resize', function() {

      var topbarHeight = $("#top-bar").height();
      var headerHeight = $("#header").innerHeight() + topbarHeight;

      $(".fs-container").css('height', '' + $(window).height() - headerHeight +'px');
    });


    // Main Main
    // ----------------------------------------------- //
    function mainMap() {
     

      var locations = getMarkers();
 
      var mapZoomAttr = $('#map').attr('data-map-zoom');
      var mapScrollAttr = $('#map').attr('data-map-scroll');

      if (typeof mapZoomAttr !== typeof undefined && mapZoomAttr !== false) {
          var zoomLevel = parseInt(mapZoomAttr);
      } else {
          var zoomLevel = 5;
      }

      if (typeof mapScrollAttr !== typeof undefined && mapScrollAttr !== false) {
         var scrollEnabled = mapScrollAttr;
      } else {
        var scrollEnabled = false;
      }
      
      var bounds;
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: zoomLevel,
        scrollwheel: scrollEnabled,
        center: center,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoomControl: false,
        fullscreenControl: false,
        mapTypeControl: false,
        scaleControl: false,
        panControl: false,
        navigationControl: false,
        streetViewControl: false,

        // Google Map Style
        styles: [{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#747474"},{"lightness":"23"}]},{"featureType":"poi.attraction","elementType":"geometry.fill","stylers":[{"color":"#f38eb0"}]},{"featureType":"poi.government","elementType":"geometry.fill","stylers":[{"color":"#ced7db"}]},{"featureType":"poi.medical","elementType":"geometry.fill","stylers":[{"color":"#ffa5a8"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#c7e5c8"}]},{"featureType":"poi.place_of_worship","elementType":"geometry.fill","stylers":[{"color":"#d6cbc7"}]},{"featureType":"poi.school","elementType":"geometry.fill","stylers":[{"color":"#c4c9e8"}]},{"featureType":"poi.sports_complex","elementType":"geometry.fill","stylers":[{"color":"#b1eaf1"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":"100"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"},{"lightness":"100"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffd4a5"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffe9d2"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"weight":"3.00"}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"weight":"0.30"}]},{"featureType":"road.local","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#747474"},{"lightness":"36"}]},{"featureType":"road.local","elementType":"labels.text.stroke","stylers":[{"color":"#e9e5dc"},{"lightness":"30"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":"100"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#d2e7f7"}]}]

      });


      var boxText = document.createElement("div");
      boxText.className = 'map-box'

      var currentInfobox;
       
      var boxOptions = {
              content: boxText,
              disableAutoPan: true,
              alignBottom : true,
              maxWidth: 0,
              pixelOffset: new google.maps.Size(-60, -55),
              zIndex: null,
              boxStyle: { 
                width: "260px"
              },
              closeBoxMargin: "0",
              closeBoxURL: "",
              infoBoxClearance: new google.maps.Size(1, 1),
              isHidden: false,
              pane: "floatPane",
              enableEventPropagation: false,
      };


      var markerCluster, marker, i;
      var allMarkers = [];
      
      var clusterStyles = [
      {
        textColor: 'white',
        url: findeo.theme_url+'/images/m1.png',
        height: 50,
        width: 50
      }
      ];



        // Custom zoom buttons
        var zoomControlDiv = document.createElement('div');
        var zoomControl = new ZoomControl(zoomControlDiv, map);

        function ZoomControl(controlDiv, map) {

          zoomControlDiv.index = 1;
          map.controls[google.maps.ControlPosition.LEFT_CENTER].push(zoomControlDiv);
          // Creating divs & styles for custom zoom control
          controlDiv.style.padding = '5px';

          // Set CSS for the control wrapper
          var controlWrapper = document.createElement('div');
          controlDiv.appendChild(controlWrapper);
          
          // Set CSS for the zoomIn
          var zoomInButton = document.createElement('div');
          zoomInButton.className = "custom-zoom-in";
          controlWrapper.appendChild(zoomInButton);
            
          // Set CSS for the zoomOut
          var zoomOutButton = document.createElement('div');
          zoomOutButton.className = "custom-zoom-out";
          controlWrapper.appendChild(zoomOutButton);

          // Setup the click event listener - zoomIn
          google.maps.event.addDomListener(zoomInButton, 'click', function() {
            map.setZoom(map.getZoom() + 1);
          });
            
          // Setup the click event listener - zoomOut
          google.maps.event.addDomListener(zoomOutButton, 'click', function() {
            map.setZoom(map.getZoom() - 1);
          });  
          
      }
      
      bounds = new google.maps.LatLngBounds();

      for (i = 0; i < locations.length; i++) { 

        marker = new google.maps.Marker({
          position: new google.maps.LatLng(locations[i][1], locations[i][2]),
         
          icon: locations[i][4],
          id : i
        });
        allMarkers.push(marker);
        bounds.extend(marker.position);
        var ib = new InfoBox();

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
          return function() {
             ib.setOptions(boxOptions);
             boxText.innerHTML = locations[i][0];
             ib.open(map, marker);
             currentInfobox = marker.id;
             var latLng = new google.maps.LatLng(locations[i][1], locations[i][2]);
             map.panTo(latLng);
             map.panBy(0,-180);


            google.maps.event.addListener(ib,'domready',function(){
              $('.infoBox-close').on("click", function (e) {
            e.preventDefault();
                  ib.close();
              });
            });

          }
        })(marker, i));

        map.fitBounds(bounds); 

      } //eof for

      var options = {
          imagePath: 'images/',
          styles : clusterStyles,
          minClusterSize : 2

      };

      markerCluster = new MarkerClusterer(map, allMarkers, options); 

      google.maps.event.addDomListener(window, "resize", function() {
          var center = map.getCenter();
          google.maps.event.trigger(map, "resize");
          map.setCenter(center); 
      });


      // Scroll enabling button
      var scrollEnabling = $('#scrollEnabling');

      $(scrollEnabling).on("click", function (e) {
          e.preventDefault();
          $(this).toggleClass("enabled");

          if ( $(this).is(".enabled") ) {
             map.setOptions({'scrollwheel': true});
          } else {
             map.setOptions({'scrollwheel': false});
          }
      })

   
      // Geo location button
      $(".geoLocation").on("click", function (e) {
          e.preventDefault();
          geolocate();
      });

      function geolocate() {

          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(function (position) {
                  var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                  var latitude = position.coords.latitude;
                  var longitude = position.coords.longitude;
                  map.setCenter(pos);
                  map.setZoom(12);
                  var geocoder = new google.maps.Geocoder();
                  geocoder.geocode( { 'latLng': pos}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                      if (results[1]) {
                        $('#location_search').val(results[1].formatted_address);
                        $('#keyword_search').val(results[1].formatted_address);
                      }
                    }else{
                      console.log("Geocode was not successful for the following reason: " + status);
                    }
                  });
              });
          }
      }

      
      // Next / Prev buttons
        $('#nextpoint').on("click", function (e) {
            e.preventDefault();
            
             map.setZoom(15);

            var index = currentInfobox;
            if (index+1 < allMarkers.length ) {
                google.maps.event.trigger(allMarkers[index+1],'click');
                
            } else {
                google.maps.event.trigger(allMarkers[0],'click');
            }
        });


        $('#prevpoint').on("click", function (e) {
            e.preventDefault();

             map.setZoom(15);

            if ( typeof(currentInfobox) == "undefined" ) {
                 google.maps.event.trigger(allMarkers[allMarkers.length-1],'click');
            } else {
                 var index = currentInfobox;
                 if(index-1 < 0) {
                    //if index is less than zero than open last marker from array
                   google.maps.event.trigger(allMarkers[allMarkers.length-1],'click');
                 } else {
                    google.maps.event.trigger(allMarkers[index-1],'click');
                 }
            }
      });


    }


    // Map Init
    var map =  document.getElementById('map');
    if (typeof(map) != 'undefined' && map != null) {
      google.maps.event.addDomListener(window, 'load',  mainMap);
      google.maps.event.addDomListener(window, 'resize',  mainMap);
    }
      
    // Single Property Map 
    // ----------------------------------------------- //

    function singlePropertyMap() {

        var myLatLng = {lng: $( '#propertyMap' ).data('longitude'),lat: $( '#propertyMap' ).data('latitude'), };

        var single_map = new google.maps.Map(document.getElementById('propertyMap'), {
          zoom: 13,
          center: myLatLng,
          scrollwheel: false,
          zoomControl: false,
          mapTypeControl: false,
          scaleControl: false,
          panControl: false,
          navigationControl: false,  
          streetViewControl: false,
          styles:  [{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#747474"},{"lightness":"23"}]},{"featureType":"poi.attraction","elementType":"geometry.fill","stylers":[{"color":"#f38eb0"}]},{"featureType":"poi.government","elementType":"geometry.fill","stylers":[{"color":"#ced7db"}]},{"featureType":"poi.medical","elementType":"geometry.fill","stylers":[{"color":"#ffa5a8"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#c7e5c8"}]},{"featureType":"poi.place_of_worship","elementType":"geometry.fill","stylers":[{"color":"#d6cbc7"}]},{"featureType":"poi.school","elementType":"geometry.fill","stylers":[{"color":"#c4c9e8"}]},{"featureType":"poi.sports_complex","elementType":"geometry.fill","stylers":[{"color":"#b1eaf1"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":"100"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"},{"lightness":"100"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffd4a5"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffe9d2"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"weight":"3.00"}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"weight":"0.30"}]},{"featureType":"road.local","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#747474"},{"lightness":"36"}]},{"featureType":"road.local","elementType":"labels.text.stroke","stylers":[{"color":"#e9e5dc"},{"lightness":"30"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":"100"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#d2e7f7"}]}]

        });

        var marker = new google.maps.Marker({
            position: myLatLng,
            map: single_map,
            icon: markerIcon
          });


        // Custom zoom buttons
        var zoomControlDiv = document.createElement('div');
        var zoomControl = new ZoomControl(zoomControlDiv, single_map);

        function ZoomControl(controlDiv, single_map) {

        zoomControlDiv.index = 1;
        single_map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(zoomControlDiv);
        // Creating divs & styles for custom zoom control
        controlDiv.style.padding = '5px';

        // Set CSS for the control wrapper
        var controlWrapper = document.createElement('div');
        controlDiv.appendChild(controlWrapper);

        // Set CSS for the zoomIn
        var zoomInButton = document.createElement('div');
        zoomInButton.className = "custom-zoom-in";
        controlWrapper.appendChild(zoomInButton);
          
        // Set CSS for the zoomOut
        var zoomOutButton = document.createElement('div');
        zoomOutButton.className = "custom-zoom-out";
        controlWrapper.appendChild(zoomOutButton);

        // Setup the click event listener - zoomIn
        google.maps.event.addDomListener(zoomInButton, 'click', function() {
          single_map.setZoom(single_map.getZoom() + 1);
        });
          
        // Setup the click event listener - zoomOut
        google.maps.event.addDomListener(zoomOutButton, 'click', function() {
          single_map.setZoom(single_map.getZoom() - 1);
        });  
          
        }

        $('#streetView').click(function(e){
           e.preventDefault();
           single_map.getStreetView().setOptions({visible:true,position:myLatLng});
           $(this).css('display', 'none')
        });

        
        

    }


    // Single Property Map Init
    var single_map_el = $('#propertyMap').length;
    if(single_map_el){
      google.maps.event.addDomListener(window, 'load',  singlePropertyMap);
      google.maps.event.addDomListener(window, 'resize',  singlePropertyMap);
    }

    
    var geocoder;
    function submitPropertyMap() {

        geocoder = new google.maps.Geocoder();
        var submit_map = new google.maps.Map(document.getElementById('submit_map'), {
          zoom: 10,
          center:center,
          scrollwheel: false,
          zoomControl: true,
          zoomControlOptions: {
              position: google.maps.ControlPosition.LEFT_CENTER
          },
          mapTypeControl: false,
          scaleControl: false,
          panControl: false,
          navigationControl: false,  
          streetViewControl: false,
          styles:  [{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#747474"},{"lightness":"23"}]},{"featureType":"poi.attraction","elementType":"geometry.fill","stylers":[{"color":"#f38eb0"}]},{"featureType":"poi.government","elementType":"geometry.fill","stylers":[{"color":"#ced7db"}]},{"featureType":"poi.medical","elementType":"geometry.fill","stylers":[{"color":"#ffa5a8"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#c7e5c8"}]},{"featureType":"poi.place_of_worship","elementType":"geometry.fill","stylers":[{"color":"#d6cbc7"}]},{"featureType":"poi.school","elementType":"geometry.fill","stylers":[{"color":"#c4c9e8"}]},{"featureType":"poi.sports_complex","elementType":"geometry.fill","stylers":[{"color":"#b1eaf1"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":"100"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"},{"lightness":"100"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffd4a5"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffe9d2"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"weight":"3.00"}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"weight":"0.30"}]},{"featureType":"road.local","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#747474"},{"lightness":"36"}]},{"featureType":"road.local","elementType":"labels.text.stroke","stylers":[{"color":"#e9e5dc"},{"lightness":"30"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":"100"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#d2e7f7"}]}]

        });

        var marker = new google.maps.Marker({
            position: center,
            map: submit_map,
            draggable:true,
            animation   : google.maps.Animation.DROP,       
        });

        var mainmarker = marker;

        google.maps.event.addListener(marker, 'dragend', function(evt){
          $("#_geolocation_lat").val(evt.latLng.lat());
          $("#_geolocation_long").val(evt.latLng.lng());
        
        geocoder.geocode({
            latLng: this.getPosition()
          }, function(responses) {
            if (responses && responses.length > 0) {
              marker.formatted_address = responses[0].formatted_address;
            } else {
              marker.formatted_address = 'Cannot determine address at this location.';
            }
            $("#_address").val(marker.formatted_address);
          });
        });


        $("#_address").geocomplete().bind("geocode:result", function(event, result){
          var loc = result.geometry.location,
              lat = loc.lat(),
              lng = loc.lng();
              $("#_geolocation_lat").val(lat);
              $("#_geolocation_long").val(lng);
          moveMarker(mainmarker,loc);
          submit_map.panTo(loc);
        }); 



    }
    function moveMarker( marker, position ) {
        marker.setPosition( position );
    };

    $("#location_search,#keyword_search:not(.title-autocomplete)").geocomplete();

 // Geo location button
      $(".geoLocation").on("click", function (e) {
          e.preventDefault();
         
          geolocate_nomap();
      });

      function geolocate_nomap() {

          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(function (position) {
                  var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                  var latitude = position.coords.latitude;
                  var longitude = position.coords.longitude;
                 
                  var geocoder = new google.maps.Geocoder();
                  geocoder.geocode( { 'latLng': pos}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                      if (results[1]) {
                        $('#location_search').val(results[1].formatted_address);
                        $('#keyword_search').val(results[1].formatted_address);
                      }
                    }else{
                      console.log("Geocode was not successful for the following reason: " + status);
                    }
                  });
              });
          }
      }

        // Map Init
    var submit_map_cont =  document.getElementById('submit_map');
    if (typeof(submit_map_cont) != 'undefined' && submit_map_cont != null) {
        google.maps.event.addDomListener(window, 'load',  submitPropertyMap);
        google.maps.event.addDomListener(window, 'resize',  submitPropertyMap);
    }

    // -------------- Single Property Map / End -------------- //


})(this.jQuery);
