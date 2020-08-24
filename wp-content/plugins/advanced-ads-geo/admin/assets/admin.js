window.advads_geo_admin = window.advads_geo_admin || {};

advads_geo_admin.set_mode = function(index, mode){
	if (mode == "latlon"){
		jQuery('#advads_geo_classic_' + index).hide();
		jQuery('#advads_geo_latlon_' + index).show();
	}
	else{
		jQuery('#advads_geo_classic_' + index).show();
		jQuery('#advads_geo_latlon_' + index).hide();
		jQuery('#advads_geo_latlon_by_city_' + index).hide();
	}
}

advads_geo_admin.click_locname = function(index){
	jQuery('#advads_geo_latlon_by_city_' + index).show();
	jQuery('#advads_geo_latlon_' + index).hide();
}

advads_geo_admin.search_loc_close = function(index){
	jQuery('#advads_geo_latlon_by_city_' + index).hide();
	jQuery('#advads_geo_latlon_' + index).show();
}

advads_geo_admin.search_loc = function(index){
	var city = $('#advads_geo_input_search_city_' + index).val();
	
	jQuery.get({
	  url: 'https://nominatim.openstreetmap.org/search',
	  data: {format: 'json', q: city, linkedplaces: 0, hierarchy: 0},
	  dataType: 'json'
	})
	.done(function(data){
		advads_geo_admin.receive_search_results(index, data);
	})
	.fail(function(jqXHR, textStatus, errorThrown){
		$('#advads_geo_latlon_loading_' + index).hide();
		var container = $('#advads_geo_latlon_results_' + index);
		container[0].innerHTML = '';
		container.append($('<div class="error inline notice"/>').text(advads_geo_translation.could_not_retrieve_city));
	});
	$('#advads_geo_latlon_loading_' + index).show();
}

advads_geo_admin.receive_search_results = function(index, data){
	$('#advads_geo_latlon_results_' + index).show();
	$('#advads_geo_latlon_loading_' + index).hide();
	var container = $('#advads_geo_latlon_results_' + index);
	container[0].innerHTML = '';
	if (data.length > 1){
		var text = advads_geo_translation.found_results.replace('$1', data.length);
		container.append($('<div>' + text + '</div>'));
	}
	else if (data.length == 0)
		container.append($('<div>' + advads_geo_translation.no_results + '</div>'));
	
	for (var i in data){
		var itm = data[i];
		var elm = jQuery('<div style="margin-bottom:5pt;cursor:pointer;" class="inline notice"><strong>' + itm.display_name + '</strong><font class="description">(' + itm.lat + ' / ' + itm.lon + ')</font></div>')
			.mouseover(function(){
				$(this).addClass('updated');
			}).mouseout(function(){
				$(this).removeClass('updated');
			});
		
		elm[0].location = itm;
		elm.click(function(){
			var itm = jQuery(this)[0].location;
			$('#advads_geo_input_search_city_' + index).val(itm.display_name);
			$('#advads_geo_input_lat_' + index).val(itm.lat);
			$('#advads_geo_input_lon_' + index).val(itm.lon);
			container[0].innerHTML = '';
			advads_geo_admin.search_loc_close(index);
		});
		container.append(elm);
	}
}