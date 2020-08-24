<div class="col-md-8">
	<div class="row">
		<div class="col-md-12 my-account">
		<?php 
			$errors = array();
			if(isset($data)) :
				$errors	 	= (isset($data->errors)) ? $data->errors : '' ;
				$attributes	 	= (isset($data->attributes)) ? $data->attributes : '' ;
			endif;
		?>
			<div id="alertForm">
            <div id="log"></div>
			<?php 			
				$current_user = wp_get_current_user();
				$settings = array(
					'post_id' => 'user_'.$current_user->ID,
					'field_groups' => array('group_5c8c941a66101'),
					'updated_message' => __("Business Alert Updated", 'acf'),
					'return' => add_query_arg( 'updated', 'true', get_permalink() ),
							
				);
				acf_form($settings);
			 ?>
            </div>
		</div>
	</div>
</div>
<script  type="text/javascript" src="<?=get_stylesheet_directory_uri()?>/assets/js/autoAUsearch.js"></script>
<script type="text/javascript">
(function($) {
	
<?php if(!is_user_logged_in()){
		$url = '/my-account/';
?>
	window.location.replace("<?=$url?>");
<?php } ?>
/*
    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }
	
	setTimeout(function(){
		console.log('Gooo');
	$.ajax({
	  type: 'GET',
	  url: 'https://digitalapi.auspost.com.au/postcode/search.json',
	  crossDomain: true,
	  jsonpCallback: 'jsonpCallback',
	  dataType: 'jsonp',
	  async: false,
	  data: {
            'q': '1900',
          },
	  //credentials: 'same-origin',
		//mode:'no-cors',
		headers: { "Auth-Key": "4f881700-465c-4357-9389-23bb84dff16a",
					"Access-Control-Allow-Origin": "*",
					"Access-Control-Allow-Methods": "GET, POST, PUT, DELETE",
					"Access-Control-Allow-Headers": "Authorization"
					},
	 
	  success: function( data ) {
            //response( data );
			alert('tset');
			console.log(data);
          }
	});
	}, 2000);
	var ajaxUrl = '<?=admin_url('admin-ajax.php?action=get_postage&nonce='.wp_create_nonce("get_postage_nonce"))?>';
    $( "#acf-field_5c8c98bb47101" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: ajaxUrl,
		  type: 'POST',
		  dataType: "json",
          data: {
            search: request.term
          },
          success: function( data ) {
            //response( data );
			if(data.type){
				
				response($.map(data.result, function(obj) {
                    return {
                        label: obj.location + ", " + obj.postcode,
                        value: obj.location + ", " + obj.postcode
                    };
                }));
				
			}
          }
        });
      },
      minLength: 2,
      select: function (event, ui) {
	   // Set selection
	   $('#acf-field_5c8c98bb47101').val(ui.item.label); // display the selected text
	   $('#log').text(ui.item.value); // save selected id to input
	   return false;
	  },
    });*/
	
})(jQuery);
</script>