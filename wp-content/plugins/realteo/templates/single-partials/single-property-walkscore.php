<div class="walkscore">
	<h3 class="desc-headline no-border print-no" id="location"><?php esc_html_e('Walk Score','realteo'); ?></h3>
	<style>
    	.walkscore-container {
        	background-color: #fff;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
    	}
    </style>
	<div class="walkscore-container">
        <script type='text/javascript'>
        var ws_wsid = '<?php echo get_option('realteo_single_property_walkscore_id'); ?>';
        var ws_address = '<?php echo get_post_meta( $post->ID, '_address', true );  ?>';
        var ws_format = 'wide';
        var ws_width = '100%';
        var ws_height = '500';
        </script><style type='text/css'>#ws-walkscore-tile{position:relative;text-align:left;}#ws-walkscore-tile *{float:none;}</style><div id='ws-walkscore-tile'></div><script type='text/javascript' src='https://www.walkscore.com/tile/show-walkscore-tile.php'></script>
    </div>
</div>