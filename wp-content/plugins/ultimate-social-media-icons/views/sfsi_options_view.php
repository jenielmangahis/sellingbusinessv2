<!-- Loader Image section  -->
<div id="sfpageLoad" >
    
</div>
<!-- END Loader Image section  -->

<!-- javascript error loader  -->
<div class="error" id="sfsi_onload_errors" style="margin-left: 60px;display: none;">
    <p>We found errors in your javascript which may cause the plugin to not work properly. Please fix the error:</p><p id="sfsi_jerrors"></p>
</div> <!-- END javascript error loader  -->

<!-- START Admin view for plugin-->
<div class="wapper sfsi_mainContainer">
    
    <!-- Get notification bar-->
    <?php if(get_option("show_new_notification") == "yes") { ?>
    <script type="text/javascript">
        jQuery(document).ready(function(e) {
            jQuery(".sfsi_show_notification").click(function(){
                SFSI.ajax({
                    url:sfsi_icon_ajax_object.ajax_url,
                    type:"post",
                    data: {action: "notification_read",nonce: "<?php echo wp_create_nonce('notification_read'); ?>"},
                    success:function(msg){
                        if(jQuery.trim(msg) == 'success')
                        {
                            jQuery(".sfsi_show_notification").hide("fast");
                        }
                    }
                });
            });
        });
    </script>
    <style type="text/css">
    .sfsi_show_notification {
        float: left;
        margin-bottom: 45px;
        padding: 12px 13px;
        width: 98%;
        background-image: url(<?php echo SFSI_PLUGURL ?>images/notification-close.png);
        background-position: right 20px center;
        background-repeat: no-repeat;
        cursor: pointer;
        text-align:center;
    }
    </style>
    <!-- <div class="sfsi_show_notification" style="background-color: #38B54A; color: #fff; font-size: 18px;">
        New: You can now also show a subscription form on your site, increasing sign-ups! (Question 8)
        <p>
        (If question 8 gets displayed in a funny way then please reload the page by pressing Control+F5(PC) or Command+R(Mac))
        </p>
    </div> -->
    <?php } ?>
    <!-- Get notification bar-->
    
    <div class="sfsi_notificationBannner"></div>
    <!-- Get new_notification bar-->


    <script type="text/javascript">

        jQuery(document).ready(function(){

            jQuery("#floating").click(function(){
                jQuery("#ui-id-9").trigger("click");
                jQuery('html, body').animate({scrollTop: jQuery("#ui-id-9").offset().top - jQuery("#ui-id-9").height()}, 2000);
            });

            jQuery("#afterposts").click(function(){
                if("none" == jQuery("#ui-id-12").css('display')){
                    jQuery("#ui-id-11").trigger("click");                    
                } 
                jQuery('html, body').animate({scrollTop: jQuery("#ui-id-11").offset().top - jQuery("#ui-id-11").height()}, 2000);
            });

        });
    </script>

    <!-- Top content area of plugin -->
    <div class="main_contant">

        <h1>Welcome to the Ultimate Social Icons and Share Plugin!</h1>
        
        <div class="welcometext">

            <p>Simply answer the questions below (at least the first 3) by clicking on them - that`s it!</p>
           
            <p><a style="text-decoration: none;" href="javascript:void(0)"><?php _e( 'New:' ); ?> </a><?php _e('Our new Premium Plugin allows many more placement options, better sharing features (e.g. define which text &amp; images will get shared), optimization for mobile, <a class="sfsi_unbold_link" target="_blank" href="https://www.ultimatelysocial.com/extra-icon-style/">more icon design styles</a>, <a class="sfsi_unbold_link" target="_blank" href="https://www.ultimatelysocial.com/animated-social-media-icons/">animated icons</a>, <a class="sfsi_unbold_link" target="_blank" href="https://www.ultimatelysocial.com/themed-icons-search/">themed icons</a>, and <a class="sfsi_unbold_link" href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmplus_settings_page&utm_campaign=notification_banner&utm_medium=banner" target="_blank">much more</a>.' ); ?> 
                <a class="pop-up" href="#" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" >
                    <?php _e('Go premium now'); ?>
                </a>
            </p>


            <p><?php  _e( 'Please' ); ?> <a target="_blank" style="text-decoration: underline;" href="https://goo.gl/auxJ9C#no-topic-0"><?php  _e( 'give us feedback,' ); ?></a><?php  _e( ' and tell us how we can make the plugin better. Thank you!.' ); ?></p>

        </div>
        <!-- <div class="welcometext">
           
           <p>Simple answer the questions below (at least the first 3) by clicking on them - that's it! </p>

           <p>If you want more features like <a target="_blank" href="https://www.ultimatelysocial.com/all-platforms/">more platform icons</a>, <a target="_blank" href="https://www.ultimatelysocial.com/themed-icons-search/">themed icons</a>, <a target="_blank" href="https://www.ultimatelysocial.com/animated-social-media-icons/">animated icons</a>, more design options, better sharing features, mobile optimization, faster loading, full support and <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/">much more</a>, then please check out our <a target="_blank" href="https://www.ultimatelysocial.com/checkout?edd_action=add_to_cart&download_id=150&edd_options[price_id]=2">Premium Plugin</a>. Pricing is really fair (24.98 USD).</p> 


           <p>For questions and suggestions please raise a ticket in the <a target="_blank" href="https://goo.gl/auxJ9C#no-topic-0">Support Forum.</a></p> 

        </div> -->
        
        <div class="supportforum">
            <div class="support-container">
                <div class="have-questions">
                    <img src="<?php echo SFSI_PLUGURL ?>images/question.png">
                    <p class="have-quest">Have questions?</p>
                    <!-- <p class="ask-question">Ask them in the...</p> -->
                </div>
                <div class="support-forum-green-div">
                    <a target="_blank" href="#" onclick="event.preventDefault();sfsi_open_chat(event)" class="support-forum-green-bg">
                        <p class="support-forum">Chat with us!</p>
                    </a>
                </div>
                <!-- <div class="respond-text">
                    <p>We'll try to respond ASAP!</p>
                </div> -->
            </div>
        </div>


    </div> <!-- END Top content area of plugin -->

<!-- step 1 end  here -->
<div id="accordion">

    <h3><span>1</span>Which icons do you want to show on your site? </h3>
    <!-- step 1 end  here -->
    <?php include(SFSI_DOCROOT.'/views/sfsi_option_view1.php'); ?>
    <!-- step 1 end here -->
    
    <!-- step 2 start here -->
    <h3><span>2</span>What do you want the icons to do? </h3>
    <?php include(SFSI_DOCROOT.'/views/sfsi_option_view2.php'); ?>
    <!-- step 2 END here -->
    
    <!-- step 3 start here -->
    <h3><span>3</span>Where shall they be displayed? </h3>
    <?php include(SFSI_DOCROOT.'/views/sfsi_question3.php'); ?>
    <!-- step 3 end here -->

    
</div>

    <h2 class="optional">Optional</h2>

<div id="accordion1">

    <!-- step 4 start here -->
    <h3><span>4</span>What design &amp; animation do you want to give your icons?</h3>
     <?php include(SFSI_DOCROOT.'/views/sfsi_option_view3.php'); ?>
    <!-- step 4 END here -->

    <!-- step 5 Start here -->
    <h3><span>5</span>Do you want to display "counts" next to your icons?</h3>
     <?php include(SFSI_DOCROOT.'/views/sfsi_option_view4.php'); ?>
    <!-- step 5 END here -->

    <!-- step 6 Start here -->
    <h3><span>6</span>Any other wishes for your main icons?</h3>
    <?php include(SFSI_DOCROOT.'/views/sfsi_option_view5.php'); ?>
    <!-- step 6 END here -->

    <!-- step 7 Start here -->
    <h3><span>7</span>Do you want to display icons at the end of every post?</h3>
     <?php include(SFSI_DOCROOT.'/views/sfsi_option_view6.php'); ?>
    <!-- step 7 END here -->


    <!-- step 8 Start here -->
    <h3><span>8</span>Do you want to display a pop-up, asking people to subscribe?</h3>
     <?php include(SFSI_DOCROOT.'/views/sfsi_option_view7.php'); ?>
    <!-- step 8 END here -->
    
    <!-- step 9 Start here -->
    <h3><span>9</span>Do you want to show a subscription form (<b>increases sign ups</b>)?</h3>
    <?php include(SFSI_DOCROOT.'/views/sfsi_option_view8.php'); ?>
    <!-- step 9 END here -->

    </div>

    <div class="tab10">
         <div class="save_button">
          <img src="<?php echo SFSI_PLUGURL; ?>images/ajax-loader.gif" class="loader-img" />
        <a href="javascript:;" id="save_all_settings" title="Save All Settings">Save All Settings</a>
     </div>
     <p class="red_txt errorMsg" style="display:none"> </p>
     <p class="green_txt sucMsg" style="display:none"> </p>

    <?php include(SFSI_DOCROOT.'/views/sfsi_affiliate_banner.php'); ?>

     <?php include(SFSI_DOCROOT.'/views/sfsi_section_for_premium.php'); ?>
             
     <!--<p class="bldtxtmsg">Need top-notch Wordpress development work at a competitive price? Visit us at <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=footer_credit&utm_medium=banner">ultimatelysocial.com</a></p>-->
    </div>
 <!-- all pops of plugin under sfsi_pop_content.php file -->
 <?php include(SFSI_DOCROOT.'/views/sfsi_pop_content.php'); ?>
</div> <!-- START Admin view for plugin-->
<?php if(in_array(get_site_url(),array('http://www.managingio.com','http://blog-latest.socialshare.com'))): ?>
    <div style="text-align:center">
        <input type="text" name="domain" id="sfsi_domain_input" style="width:40%;min-height: :40px;text-align:center;margin:0 auto" placeholder="Enter Domian to check its theme" />
        <input type="text" name="sfsi_domain_input_nonce"  value="<?php echo wp_create_nonce('bannerOption'); ?>">
        <div class="save_button">
          <img src="<?php echo SFSI_PLUGURL; ?>images/ajax-loader.gif" class="loader-img" />
        <a href="javascript:;" id="sfsi_check_theme_of_domain_btn" title="Check">Check the Theme</a>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#sfsi_check_theme_of_domain_btn').click(function(){
               jQuery.ajax({
                    url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                    type:"post",
                    data:{
                        'action':'bannerOption',
                        'domain':$('#sfsi_domain_input').val(),
                        'nonce': $('#sfsi_domain_input_nonce').val(),
                    },
                    success:function(s) {
        			     var sfsi_container = $("html,body");
        			     var sfsi_scrollTo = $('.sfsi_notificationBannner');
                         $('.sfsi_notificationBannner').attr('tabindex',$('.sfsi_notificationBannner').attr('tabindex')||-1);
                        jQuery(".sfsi_notificationBannner").html(s).focus();
                        sfsi_container.animate({scrollTop: (sfsi_scrollTo.offset().top - sfsi_container.offset().top + sfsi_container.scrollTop()), scrollLeft: 0},300);

                    }
                }); 
            });
        })
    </script>
<?php endif; ?>


<script type="text/javascript">
    var e = {
        action:"bannerOption",
        'nonce': '<?php echo wp_create_nonce('bannerOption'); ?>',

    };
    jQuery.ajax({
        url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
        type:"post",
        data:e,
        success:function(s) {
            jQuery(".sfsi_notificationBannner").html(s);
        }
    });

</script>
?>
<?php include(SFSI_DOCROOT.'/views/sfsi_chat_on_admin_pannel.php'); ?>
