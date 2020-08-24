<?php

    $sfsi_show_via_afterposts = "no";

    if(isset($option9['sfsi_show_via_afterposts']) && !empty($option9['sfsi_show_via_afterposts'])){
        $sfsi_show_via_afterposts = $option9['sfsi_show_via_afterposts'];
    }

    $label_style = 'style="display:none;"';
    $checked     = "";

    if($sfsi_show_via_afterposts =='yes'){          
        $label_style = 'style="display:block;"';
        $checked     = 'checked="true"';
    }    
?>
		<li class="sfsi_show_via_afterposts">
			
			<div class="radio_section tb_4_ck" onclick="checkforinfoslction_checkbox(this);"><input name="sfsi_show_via_afterposts" <?php echo $checked; ?>  type="checkbox" value="<?php echo $sfsi_show_via_afterposts; ?>" class="styled"  /></div>
			
			<div class="sfsi_right_info">
				
                <p>

					<span class="sfsi_toglepstpgspn">Show icons after posts</span>
   
                    <div class="kckslctn" <?php echo $label_style;?>>
                        
                        <p>Please select this under <a href="#sfsi_dsplyafterposts" class="sfsi_navigate_to_question7">question 7</a>.</p>
                        
                        <p>If you want to place the <b>round icons</b> after/before posts (i.e. the ones you selected under question 1): This is possible in the <a href="https://www.ultimatelysocial.com/usm-premium/" target="_blank"><b>Premium Plugin.</b></a></p> 
                    </div>

				</p>
				
			</div>
		</li>