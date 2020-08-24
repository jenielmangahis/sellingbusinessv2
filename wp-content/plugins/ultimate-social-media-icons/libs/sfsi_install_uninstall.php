<?php
function sfsi_update_plugin()
{
    if($feed_id = get_option('sfsi_feed_id'))
    {
        if(is_numeric($feed_id))
        {
            $sfsiId = SFSI_updateFeedUrl();
            update_option('sfsi_feed_id', sanitize_text_field($sfsiId->feed_id));
            update_option('sfsi_redirect_url', esc_url($sfsiId->redirect_url));
        }
    }
    
    //Install version
    update_option("sfsi_pluginVersion", "2.20");

    if(!get_option('sfsi_serverphpVersionnotification'))
    {
        add_option("sfsi_serverphpVersionnotification", "yes");
    }
    if(!get_option('sfsi_footer_sec'))
    {
        add_option('sfsi_footer_sec','no');
    }
    /* show notification premium plugin */
    if(!get_option('show_premium_notification'))
    {
        add_option("show_premium_notification", "yes");
    }

    if(!get_option('show_premium_cumulative_count_notification'))
    {
        add_option("show_premium_cumulative_count_notification", "yes");
    }    

    /*show notification*/
    if(!get_option('show_notification'))
    {
        add_option("show_notification", "yes");
    }
    /*show notification*/
    if(!get_option('show_new_notification'))
    {
        add_option("show_new_notification", "no");
    }
    
    /* show mobile notification */
    if(!get_option('show_mobile_notification'))
    {
        add_option("show_mobile_notification", "yes");
    }
    
    if(!get_option('sfsi_languageNotice'))
    {
        add_option("sfsi_languageNotice", "yes");
    }
    
    /*Extra important options*/
    if(!get_option('sfsi_instagram_sf_count')){

        $sfsi_instagram_sf_count = array(
            "date" => strtotime(date("Y-m-d")),
            "sfsi_sf_count" => "",
            "sfsi_instagram_count" => ""
        );
        add_option('sfsi_instagram_sf_count',  serialize($sfsi_instagram_sf_count));
    }else{
        $sfsi_instagram_sf_count = unserialize(get_option('sfsi_instagram_sf_count',false));
        $sfsi_instagram_sf_count["date_sf"] = $sfsi_instagram_sf_count["date"];
        $sfsi_instagram_sf_count["date_instagram"] = $sfsi_instagram_sf_count["date"];
        update_option('sfsi_instagram_sf_count',$sfsi_instagram_sf_count);
    }

    $option4 = unserialize(get_option('sfsi_section4_options',false));

    if(isset($option4) && !empty($option4))
    {
        if(!isset($option4['sfsi_instagram_clientid'])){
            $option4['sfsi_instagram_clientid'] = '';
            $option4['sfsi_instagram_appurl']   = '';
            $option4['sfsi_instagram_token']    = '';            
        }

        /*Youtube Channelid settings*/
        if(!isset($option4['sfsi_youtube_channelId'])){
            $option4['sfsi_youtube_channelId'] = '';            
        }
    }

    $option3 = unserialize(get_option('sfsi_section3_options',false));
    
    if(isset($option3) && !empty($option3))
    {
        if(!isset($option3['sfsi_mouseOver_effect_type'])){
            $option3['sfsi_mouseOver_effect_type'] = 'same_icons';
        }

        if(!isset($option3['mouseover_other_icons_transition_effect'])){
            $option3['mouseover_other_icons_transition_effect'] = 'flip';
        }
    }

    $option2 = unserialize(get_option('sfsi_section2_options',false));
    
    if(isset($option2) && !empty($option2))
    {
        if(!isset($option2['sfsi_youtubeusernameorid'])){
            
            $option2['sfsi_youtubeusernameorid']    = '';

            if(isset($option4['sfsi_youtubeusernameorid']) && !empty($option4['sfsi_youtubeusernameorid'])){
                $option2['sfsi_youtubeusernameorid'] = $option4['sfsi_youtubeusernameorid'];
            }
        }
        
        if(!isset($option2['sfsi_ytube_chnlid'])){
            
            $option2['sfsi_ytube_chnlid']     = '';
            
            if(isset($option4['sfsi_ytube_chnlid']) && !empty($option4['sfsi_ytube_chnlid'])){
                $option2['sfsi_ytube_chnlid'] = $option4['sfsi_ytube_chnlid'];
            }            
        }        
    }

    update_option('sfsi_section4_options', serialize($option4));
    update_option('sfsi_section2_options', serialize($option2));
    

    $option7 = unserialize(get_option('sfsi_section7_options',false));
    $option7 = isset($option7) && !empty($option7) ? $option7 : array();

    if(!isset($option7['sfsi_show_popup']))                  { $option7['sfsi_show_popup']                  = 'no'; }
    if(!isset($option7['sfsi_popup_text']))                  { $option7['sfsi_popup_text']                  = 'Enjoy this blog? Please spread the word :)'; }
    if(!isset($option7['sfsi_popup_background_color']))      { $option7['sfsi_popup_background_color']      = '#eff7f7'; }
    if(!isset($option7['sfsi_popup_border_color']))          { $option7['sfsi_popup_border_color']          = '#f3faf2'; }
    if(!isset($option7['sfsi_popup_border_thickness']))      { $option7['sfsi_popup_border_thickness']      = '1'; }
    if(!isset($option7['sfsi_popup_border_shadow']))         { $option7['sfsi_popup_border_shadow']         = 'yes'; } 
    if(!isset($option7['sfsi_popup_font']))                  { $option7['sfsi_popup_font']                  = 'Helvetica,Arial,sans-serif';} 
    if(!isset($option7['sfsi_popup_fontSize']))              { $option7['sfsi_popup_fontSize']              = '30';}
    if(!isset($option7['sfsi_popup_fontStyle']))             { $option7['sfsi_popup_fontStyle']             = 'normal';} 
    if(!isset($option7['sfsi_popup_fontColor']))             { $option7['sfsi_popup_fontColor']             = '#000000';}
    if(!isset($option7['sfsi_Show_popupOn']))                { $option7['sfsi_Show_popupOn']                = 'none';} 
    if(!isset($option7['sfsi_Show_popupOn_PageIDs']))        { $option7['sfsi_Show_popupOn_PageIDs']        = '';}
    if(!isset($option7['sfsi_Shown_popupOnceTime']))         { $option7['sfsi_Shown_popupOnceTime']         = '';}
    if(!isset($option7['sfsi_Shown_popuplimitPerUserTime'])) { $option7['sfsi_Shown_popuplimitPerUserTime'] = '';}                 

    update_option('sfsi_section7_options', serialize($option7)); 

    /* subscription form */
    $option8 = unserialize(get_option('sfsi_section8_options',false));
    $option8 = isset($option8) && !empty($option8) ? $option8 : array();

    if(!isset($option8['sfsi_form_adjustment']))            { $option8['sfsi_form_adjustment']          = 'yes';}
    if(!isset($option8['sfsi_form_height']))                { $option8['sfsi_form_height']              = '180';}
    if(!isset($option8['sfsi_form_width']))                 { $option8['sfsi_form_width']               = '230';}
    if(!isset($option8['sfsi_form_border']))                { $option8['sfsi_form_border']              = 'yes';}
    if(!isset($option8['sfsi_form_border_thickness']))      { $option8['sfsi_form_border_thickness']    = '1';}
    if(!isset($option8['sfsi_form_border_color']))          { $option8['sfsi_form_border_color']        = '#b5b5b5';} 
    if(!isset($option8['sfsi_form_background']))            { $option8['sfsi_form_background']          = '#ffffff';} //
    if(!isset($option8['sfsi_form_heading_text']))          { $option8['sfsi_form_heading_text']        = 'Get new posts by email';}
    if(!isset($option8['sfsi_form_heading_font']))          { $option8['sfsi_form_heading_font']        = 'Helvetica,Arial,sans-serif';} 
    if(!isset($option8['sfsi_form_heading_fontstyle']))     { $option8['sfsi_form_heading_fontstyle']   = 'bold';}
    if(!isset($option8['sfsi_form_heading_fontcolor']))     { $option8['sfsi_form_heading_fontcolor']   = '#000000';} 
    if(!isset($option8['sfsi_form_heading_fontsize']))      { $option8['sfsi_form_heading_fontsize']    = '16';}
    if(!isset($option8['sfsi_form_heading_fontalign']))     { $option8['sfsi_form_heading_fontalign']   = 'center';}
    if(!isset($option8['sfsi_form_field_text']))            { $option8['sfsi_form_field_text']          = 'Enter your email';}
    if(!isset($option8['sfsi_form_field_font']))            { $option8['sfsi_form_field_font']          = 'Helvetica,Arial,sans-serif';}
    if(!isset($option8['sfsi_form_field_fontstyle']))       { $option8['sfsi_form_field_fontstyle']     = 'normal';}
    if(!isset($option8['sfsi_form_field_fontcolor']))       { $option8['sfsi_form_field_fontcolor']     = '#000000';} 
    if(!isset($option8['sfsi_form_field_fontsize']))        { $option8['sfsi_form_field_fontsize']      = '14';}
    if(!isset($option8['sfsi_form_field_fontalign']))       { $option8['sfsi_form_field_fontalign']     = 'center';}
    if(!isset($option8['sfsi_form_button_text']))           { $option8['sfsi_form_button_text']         = 'Subscribe';}
    if(!isset($option8['sfsi_form_button_font']))           { $option8['sfsi_form_button_font']         = 'Helvetica,Arial,sans-serif';}
    if(!isset($option8['sfsi_form_button_fontstyle']))      { $option8['sfsi_form_button_fontstyle']    = 'bold';}
    if(!isset($option8['sfsi_form_button_fontcolor']))      { $option8['sfsi_form_button_fontcolor']    = '#000000';} 
    if(!isset($option8['sfsi_form_button_fontsize']))       { $option8['sfsi_form_button_fontsize']     = '16';}
    if(!isset($option8['sfsi_form_button_fontalign']))      { $option8['sfsi_form_button_fontalign']    = 'center';}
    if(!isset($option8['sfsi_form_button_background']))     { $option8['sfsi_form_button_background'] =  '#dedede';}                

    update_option('sfsi_section8_options', serialize($option8)); 

    
    /*Float Icon setting*/
    $option5 = unserialize(get_option('sfsi_section5_options',false));

    $sfsi_show_via_widget           = 'no';

    $sfsi_icons_float               = 'no';
    $sfsi_icons_floatPosition       = 'center-right';
    $sfsi_icons_floatMargin_top     = '';
    $sfsi_icons_floatMargin_bottom  = '';
    $sfsi_icons_floatMargin_left    = '';
    $sfsi_icons_floatMargin_right   = '';
    $sfsi_disable_floaticons        = 'no';

    $sfsi_show_via_shortcode        = 'no';
    $sfsi_show_via_afterposts       = 'no';


    if(isset($option5) && !empty($option5))
    { 
        if(isset($option5['sfsi_icons_float'])){
            $sfsi_icons_float               = $option5['sfsi_icons_float'];
            unset($option5['sfsi_icons_float']);
        }

        if(isset($option5['sfsi_icons_floatPosition'])){
            $sfsi_icons_floatPosition       = $option5['sfsi_icons_floatPosition'];
            unset($option5['sfsi_icons_floatPosition']);                            
        }

        if(isset($option5['sfsi_icons_floatMargin_top'])){
            $sfsi_icons_floatMargin_top     = $option5['sfsi_icons_floatMargin_top'];
            unset($option5['sfsi_icons_floatMargin_top']);                                        
        }

        if(isset($option5['sfsi_icons_floatMargin_bottom'])){
            $sfsi_icons_floatMargin_bottom  = $option5['sfsi_icons_floatMargin_bottom'];
            unset($option5['sfsi_icons_floatMargin_bottom']);
        }

        if(isset($option5['sfsi_icons_floatMargin_left'])){
            $sfsi_icons_floatMargin_left    = $option5['sfsi_icons_floatMargin_left'];
            unset($option5['sfsi_icons_floatMargin_left']);                       
        }

        if(isset($option5['sfsi_icons_floatMargin_right'])){
            $sfsi_icons_floatMargin_right   = $option5['sfsi_icons_floatMargin_right'];
            unset($option5['sfsi_icons_floatMargin_right']);          
        }

        if(isset($option5['sfsi_disable_floaticons'])){
            $sfsi_disable_floaticons        = $option5['sfsi_disable_floaticons'];
            unset($option5['sfsi_disable_floaticons']); 
        }

        if(isset($option5['sfsi_custom_social_hide'])){        	        	
            $option5['sfsi_custom_social_hide']    = 'no';
        }


        if(!isset($option5['sfsi_pplus_icons_suppress_errors'])){
        	
        	$sup_errors = "no";
        	$sup_errors_banner_dismissed = true;

        	if(defined('WP_DEBUG') && false != WP_DEBUG){
            	$sup_errors = 'yes';
            	$sup_errors_banner_dismissed = false;
        	}

            $option5['sfsi_pplus_icons_suppress_errors'] = $sup_errors;
            update_option('sfsi_pplus_error_reporting_notice_dismissed',$sup_errors_banner_dismissed);            
        }		        
    }

    update_option('sfsi_section5_options', serialize($option5));  

    $option6=  unserialize(get_option('sfsi_section6_options',false));

    if(isset($option6) && !empty($option6))
    {
        if(!isset($option6['sfsi_rectpinit']))
        {
            $option6['sfsi_rectpinit'] = 'no';
        }
        if(!isset($option6['sfsi_rectfbshare']))
        {
            $option6['sfsi_rectfbshare'] = 'no';
        }

        update_option('sfsi_section6_options', serialize($option6));
    }


    // Setting default values for Question 3
    $option9 = unserialize(get_option('sfsi_section9_options',false));
    $option9 = isset($option9) && !empty($option9) ? $option9 : array();

    if(!isset($option9['sfsi_show_via_widget'])){
        
        if(class_exists('Sfsi_Widget')){            
            $widegtObj            = new Sfsi_Widget();          
            $sfsi_show_via_widget = is_active_widget(false,false,$widegtObj->id_base,true) ? "yes" : "no";
        }        
        $option9['sfsi_show_via_widget'] = $sfsi_show_via_widget;
    }

    if(!isset($option9['sfsi_show_via_shortcode']))      { $option9['sfsi_show_via_shortcode']       = $sfsi_show_via_shortcode;}
    if(!isset($option9['sfsi_show_via_afterposts']))     { $option9['sfsi_show_via_afterposts']      = $sfsi_show_via_afterposts;}
    if(!isset($option9['sfsi_icons_float']))             { $option9['sfsi_icons_float']              = $sfsi_icons_float;}
    if(!isset($option9['sfsi_icons_floatPosition']))     { $option9['sfsi_icons_floatPosition']      = $sfsi_icons_floatPosition;}
    if(!isset($option9['sfsi_icons_floatMargin_top']))   { $option9['sfsi_icons_floatMargin_top']    = $sfsi_icons_floatMargin_top;}
    if(!isset($option9['sfsi_icons_floatMargin_bottom'])){ $option9['sfsi_icons_floatMargin_bottom'] = $sfsi_icons_floatMargin_bottom;}
    if(!isset($option9['sfsi_icons_floatMargin_left']))  { $option9['sfsi_icons_floatMargin_left']   = $sfsi_icons_floatMargin_left;}
    if(!isset($option9['sfsi_icons_floatMargin_right'])) { $option9['sfsi_icons_floatMargin_right']  = $sfsi_icons_floatMargin_right;}
    if(!isset($option9['sfsi_disable_floaticons']))      { $option9['sfsi_disable_floaticons']       = $sfsi_disable_floaticons;}

    update_option('sfsi_section9_options', serialize($option9));

    // Add this removed in version 2.0.2, removing values from section 1 & section 6 & setting notice display value
    sfsi_was_displaying_addthis();    
}

function sfsi_activate_plugin()
{
    add_option('sfsi_plugin_do_activation_redirect', true);
    
    /* check for CURL enable at server */
    curl_enable_notice();
    
    if(!get_option('show_new_notification'))
    {
        add_option("show_new_notification", "yes");
    }

    if(!get_option('show_premium_cumulative_count_notification'))
    {
        add_option("show_premium_cumulative_count_notification", "yes");
    }

	$option1 = unserialize(get_option('sfsi_section1_options',false));

    if(!isset($option1) || empty($option1)){

        $options1=array('sfsi_rss_display'=>'yes',
                'sfsi_email_display'=>'yes',
                'sfsi_facebook_display'=>'yes',
                'sfsi_twitter_display'=>'yes',
                'sfsi_google_display'=>'no',
                'sfsi_pinterest_display'=>'no',
                'sfsi_instagram_display'=>'no',
                'sfsi_linkedin_display'=>'no',
                'sfsi_youtube_display'=>'no',  
                'sfsi_custom_display'=>'',
                'sfsi_custom_files'=>''  
        );
        add_option('sfsi_section1_options',  serialize($options1));
    }
    
    if(get_option('sfsi_feed_id') && get_option('sfsi_redirect_url'))
    {
        $sffeeds["feed_id"] = sanitize_text_field(get_option('sfsi_feed_id'));
        $sffeeds["redirect_url"] = esc_url(get_option('sfsi_redirect_url'));
        $sffeeds = (object)$sffeeds;
    }
    else
    {
        $sffeeds = SFSI_getFeedUrl();
    }
    
    $addThisDismissed = get_option('sfsi_addThis_icon_removal_notice_dismissed',false);

    if(!isset($addThisDismissed)){
        update_option('sfsi_addThis_icon_removal_notice_dismissed',true);
    }

	$option2 = unserialize(get_option('sfsi_section2_options',false));

    if(!isset($option2) || empty($option2)){

        /* Links and icons  options */   
        $options2=array('sfsi_rss_url'=>sfsi_get_bloginfo('rss2_url'),
            'sfsi_rss_icons'             =>'email', 
            'sfsi_email_url'             =>$sffeeds->redirect_url,
            'sfsi_facebookPage_option'   =>'no',
            'sfsi_facebookPage_url'      =>'',
            'sfsi_facebookLike_option'   =>'yes',
            'sfsi_facebookShare_option'  =>'yes',
            'sfsi_twitter_followme'      =>'no',
            'sfsi_twitter_followUserName'=>'',
            'sfsi_twitter_aboutPage'     =>'yes',
            'sfsi_twitter_page'          =>'no',
            'sfsi_twitter_pageURL'       =>'',
            'sfsi_twitter_aboutPageText' =>'Hey, check out this cool site I found: www.yourname.com #Topic via@my_twitter_name',
            'sfsi_google_page'           =>'no',
            'sfsi_google_pageURL'        =>'',
            'sfsi_googleLike_option'     =>'yes',
            'sfsi_googleShare_option'    =>'yes',
            'sfsi_youtube_pageUrl'       =>'',
            'sfsi_youtube_page'          =>'no',
            'sfsi_youtubeusernameorid'   => '',
            'sfsi_ytube_chnlid'          => '',
            'sfsi_youtube_follow'        =>'no',
            'sfsi_pinterest_page'        =>'no',
            'sfsi_pinterest_pageUrl'     =>'',
            'sfsi_pinterest_pingBlog'    =>'',
            'sfsi_instagram_page'        =>'no',
            'sfsi_instagram_pageUrl'     =>'',
            'sfsi_linkedin_page'         =>'no',
            'sfsi_linkedin_pageURL'      =>'',
            'sfsi_linkedin_follow'       =>'no',
            'sfsi_linkedin_followCompany'=>'',
            'sfsi_linkedin_SharePage'         =>'yes',
            'sfsi_linkedin_recommendBusines'  =>'no',
            'sfsi_linkedin_recommendCompany'  =>'',
            'sfsi_linkedin_recommendProductId'=>'',
            'sfsi_CustomIcon_links'           =>''
            );
        add_option('sfsi_section2_options',  serialize($options2));
    }
    
	$option3 = unserialize(get_option('sfsi_section3_options',false));

	if(!isset($option3) || empty($option3)){

        /* Design and animation option  */
        $options3 = array(

            'sfsi_mouseOver'             =>'no',
            'sfsi_mouseOver_effect'      =>'fade_in',
            'sfsi_mouseOver_effect_type' => 'same_icons',
            'mouseover_other_icons_transition_effect' => 'flip',
            'sfsi_shuffle_icons'         =>'no',
            'sfsi_shuffle_Firstload'     =>'no',
            'sfsi_shuffle_interval'      =>'no',
            'sfsi_shuffle_intervalTime'  =>'',                              
            'sfsi_actvite_theme'         =>'default' 
        );
        add_option('sfsi_section3_options',  serialize($options3));
    }
    
	$option4 = unserialize(get_option('sfsi_section4_options',false));

    if(!isset($option4) || empty($option4)){

        /* display counts options */         
        $options4=array('sfsi_display_counts'=>'no',
            'sfsi_email_countsDisplay'=>'no',
            'sfsi_email_countsFrom'=>'source',
            'sfsi_email_manualCounts'=>'20',
            'sfsi_rss_countsDisplay'=>'no',
            'sfsi_rss_manualCounts'=>'20',
            'sfsi_facebook_PageLink'=>'',
            'sfsi_facebook_countsDisplay'=>'no',
            'sfsi_facebook_countsFrom'=>'manual',
            'sfsi_facebook_manualCounts'=>'20',
            'sfsi_twitter_countsDisplay'=>'no',
            'sfsi_twitter_countsFrom'=>'manual',
            'sfsi_twitter_manualCounts'=>'20',
            'sfsi_google_api_key'=>'',   
            'sfsi_google_countsDisplay'=>'no',
            'sfsi_google_countsFrom'=>'manual',
            'sfsi_google_manualCounts'=>'20',
            'sfsi_linkedIn_countsDisplay'=>'no',
            'sfsi_linkedIn_countsFrom'=>'manual',
            'sfsi_linkedIn_manualCounts'=>'20',
            'ln_api_key'=>'',
            'ln_secret_key'=>'',
            'ln_oAuth_user_token'=>'',
            'ln_company'=>'',
            'sfsi_youtubeusernameorid'=>'name',
            'sfsi_youtube_user'=>'',
            'sfsi_youtube_channelId' =>'',
            'sfsi_ytube_chnlid'=>'',
            'sfsi_youtube_countsDisplay'=>'no',
            'sfsi_youtube_countsFrom'=>'manual',
            'sfsi_youtube_manualCounts'=>'20',
            'sfsi_pinterest_countsDisplay'=>'no',
            'sfsi_pinterest_countsFrom'=>'manual',
            'sfsi_pinterest_manualCounts'=>'20',
            'sfsi_pinterest_user'=>'',
            'sfsi_pinterest_board'=>'',
        
            'sfsi_instagram_countsFrom'=>'manual',
            'sfsi_instagram_countsDisplay'=>'no',
            'sfsi_instagram_manualCounts'=>'20',

            'sfsi_instagram_User'=>'',
            
        );
        add_option('sfsi_section4_options',  serialize($options4));
    }
    
	$option5 = unserialize(get_option('sfsi_section5_options',false));

    if(!isset($option5) || empty($option5)){

        $options5=array(
            'sfsi_icons_size'			=>'40',
            'sfsi_icons_spacing'		=>'5',
            'sfsi_icons_Alignment'		=>'left',
            'sfsi_icons_perRow'			=>'5',
            'sfsi_icons_ClickPageOpen'	=>'yes',
            'sfsi_icons_suppress_errors'=>'no',
            'sfsi_icons_float'			=>'no',
            'sfsi_disable_floaticons'	=>'no',
            'sfsi_icons_floatPosition'	=>'center-right',
            'sfsi_icons_floatMargin_top'=>'',
            'sfsi_icons_floatMargin_bottom'=>'',
            'sfsi_icons_floatMargin_left'=>'',
            'sfsi_icons_floatMargin_right'=>'',
            'sfsi_icons_stick'			=>'no',
            'sfsi_rssIcon_order'		=>'1',
            'sfsi_emailIcon_order'		=>'2',	
            'sfsi_facebookIcon_order'	=>'3',
            'sfsi_twitterIcon_order'	=>'4',
            'sfsi_youtubeIcon_order'	=>'5',
            'sfsi_pinterestIcon_order'	=>'7',
            'sfsi_linkedinIcon_order'	=>'8',
            'sfsi_instagramIcon_order'	=>'9',
            'sfsi_googleIcon_order'		=>'10',
            'sfsi_CustomIcons_order'	=>'',
            'sfsi_rss_MouseOverText'	=>'RSS',
            'sfsi_email_MouseOverText'	=>'Follow by Email',
            'sfsi_twitter_MouseOverText'=>'Twitter',
            'sfsi_facebook_MouseOverText' =>'Facebook',
            'sfsi_google_MouseOverText'	  =>'Google+',
            'sfsi_linkedIn_MouseOverText' =>'LinkedIn',
            'sfsi_pinterest_MouseOverText'=>'Pinterest',
            'sfsi_instagram_MouseOverText'=>'Instagram',
            'sfsi_youtube_MouseOverText'  =>'YouTube',
            'sfsi_custom_MouseOverTexts'  =>'',
            'sfsi_custom_social_hide' 	  =>'no'
            );
            update_option('sfsi_section5_options',  serialize($options5));
    }
    
 	$option6 = unserialize(get_option('sfsi_section6_options',false));
    
    if(!isset($option6) || empty($option6)){

        /* post options */                  
        $options6=array('sfsi_show_Onposts'=>'no',
            'sfsi_show_Onbottom'=>'no',
            'sfsi_icons_postPositon'=>'source',
            'sfsi_icons_alignment'=>'center-right',
            'sfsi_rss_countsDisplay'=>'no',
            'sfsi_textBefor_icons'=>'Please follow and like us:',
            'sfsi_icons_DisplayCounts'=>'no',
            'sfsi_rectsub'=>'yes',
            'sfsi_rectfb'=>'yes',
            'sfsi_rectgp'=>'yes',
            'sfsi_rectshr'=>'no',
            'sfsi_recttwtr'=>'yes',
            'sfsi_rectpinit'=>'yes',
            'sfsi_rectfbshare'=>'yes'
        );
        add_option('sfsi_section6_options',  serialize($options6));
    }       
    
 	$option7 = unserialize(get_option('sfsi_section7_options',false));
    
    if(!isset($option7) || empty($option7)){

        /* icons pop options */
        $options7=array('sfsi_show_popup'=>'no',
            'sfsi_popup_text'=>'Enjoy this blog? Please spread the word :)',
            'sfsi_popup_background_color'=>'#eff7f7',
            'sfsi_popup_border_color'=>'#f3faf2',
            'sfsi_popup_border_thickness'=>'1',
            'sfsi_popup_border_shadow'=>'yes',
            'sfsi_popup_font'=>'Helvetica,Arial,sans-serif',
            'sfsi_popup_fontSize'=>'30',
            'sfsi_popup_fontStyle'=>'normal',
            'sfsi_popup_fontColor'=>'#000000',
            'sfsi_Show_popupOn'=>'none',
            'sfsi_Show_popupOn_PageIDs'=>'',
            'sfsi_Shown_pop'=>'ETscroll',
            'sfsi_Shown_popupOnceTime'=>'',
            'sfsi_Shown_popuplimitPerUserTime'=>'',
        );
        add_option('sfsi_section7_options',  serialize($options7));
    }
       
 	$option8 = unserialize(get_option('sfsi_section8_options',false));
    
    if(!isset($option8) || empty($option8)){

        /* Question 8 */
        $options8=array(
            'sfsi_form_adjustment'      =>  'yes',
            'sfsi_form_height'          =>  '180',
            'sfsi_form_width'           =>  '230',
            'sfsi_form_border'          =>  'no',
            'sfsi_form_border_thickness'=>  '1',
            'sfsi_form_border_color'    =>  '#b5b5b5',
            'sfsi_form_background'      =>  '#ffffff',
            
            'sfsi_form_heading_text'    =>  'Get new posts by email',
            'sfsi_form_heading_font'    =>  'Helvetica,Arial,sans-serif',
            'sfsi_form_heading_fontstyle'=> 'bold',
            'sfsi_form_heading_fontcolor'=> '#000000',
            'sfsi_form_heading_fontsize'=>  '16',
            'sfsi_form_heading_fontalign'=> 'center',
            
            'sfsi_form_field_text'      =>  'Subscribe',
            'sfsi_form_field_font'      =>  'Helvetica,Arial,sans-serif',
            'sfsi_form_field_fontstyle' =>  'normal',
            'sfsi_form_field_fontcolor' =>  '#000000',
            'sfsi_form_field_fontsize'  =>  '14',
            'sfsi_form_field_fontalign' =>  'center',
            
            'sfsi_form_button_text'     =>  'Subscribe',
            'sfsi_form_button_font'     =>  'Helvetica,Arial,sans-serif',
            'sfsi_form_button_fontstyle'=>  'bold',
            'sfsi_form_button_fontcolor'=>  '#000000',
            'sfsi_form_button_fontsize' =>  '16',
            'sfsi_form_button_fontalign'=>  'center',
            'sfsi_form_button_background'=> '#dedede',
        );
        add_option('sfsi_section8_options',  serialize($options8));
    }

 	$option9 = unserialize(get_option('sfsi_section9_options',false));
    
    if(!isset($option9) || empty($option9)){

        /* Question 3->Where shall they be displayed?    */
        $options9 =array(
            
            'sfsi_show_via_widget'          => 'no',

            'sfsi_icons_float'              => 'no',
            'sfsi_icons_floatPosition'      => 'center-right',
            'sfsi_icons_floatMargin_top'    => '',
            'sfsi_icons_floatMargin_bottom' => '',
            'sfsi_icons_floatMargin_left'   => '',
            'sfsi_icons_floatMargin_right'  => '',                              
            'sfsi_disable_floaticons'       => 'no',

            'sfsi_show_via_shortcode'       => 'no',
            'sfsi_show_via_afterposts'      => 'no'

        );
        add_option('sfsi_section9_options',  serialize($options9));
    }

    /*Some additional option added*/
    update_option('sfsi_feed_id',sanitize_text_field($sffeeds->feed_id));
    update_option('sfsi_redirect_url',esc_url($sffeeds->redirect_url));
    add_option('sfsi_installDate',date('Y-m-d h:i:s'));
    add_option('sfsi_RatingDiv','no');
    add_option('sfsi_footer_sec','no');
    update_option('sfsi_activate', 1);
    
    /*Changes in option 2*/
    $get_option2 = unserialize(get_option('sfsi_section2_options',false));

    $get_option2['sfsi_email_url'] = $sffeeds->redirect_url;
    update_option('sfsi_section2_options', serialize($get_option2));
    
    /*Activation Setup for (specificfeed)*/
    sfsi_setUpfeeds($sffeeds->feed_id);
    sfsi_updateFeedPing('N',$sffeeds->feed_id);
    
    /*Extra important options*/
    $sfsi_instagram_sf_count = array(
        "date_sf" => strtotime(date("Y-m-d")),
        "date_instagram" => strtotime(date("Y-m-d")),
        "sfsi_sf_count" => "",
        "sfsi_instagram_count" => ""
    );
    add_option('sfsi_instagram_sf_count',  serialize($sfsi_instagram_sf_count));

    $error_banner = get_option('sfsi_error_reporting_notice_dismissed',false);

    if(!$error_banner){
    	update_option('sfsi_error_reporting_notice_dismissed',true);
    }
}
/* end function  */



/* deactivate plugin */
function sfsi_deactivate_plugin()
{
     global $wpdb;
     sfsi_updateFeedPing('Y',sanitize_text_field(get_option('sfsi_feed_id')));
}
/* end function  */

function sfsi_updateFeedPing($status,$feed_id)
{
    $curl = curl_init();  
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://www.specificfeeds.com/wordpress/pingfeed',
        CURLOPT_USERAGENT => 'sf rss request',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
            'feed_id' => $feed_id,
            'status' => $status
        )
    ));
     // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $resp=json_decode($resp);
    curl_close($curl);
}
/* unistall plugin function */
function sfsi_Unistall_plugin()
{   
    global $wpdb;
    /* Delete option for which icons to display */
    delete_option('sfsi_section1_options');
    delete_option('sfsi_section2_options');
    delete_option('sfsi_section3_options');
    delete_option('sfsi_section4_options');
    delete_option('sfsi_section5_options');
    delete_option('sfsi_section6_options');
    delete_option('sfsi_section7_options');
    delete_option('sfsi_section8_options');
    delete_option('sfsi_section9_options');    
    delete_option('sfsi_feed_id');
    delete_option('sfsi_redirect_url');
    delete_option('sfsi_footer_sec');
    delete_option('sfsi_activate');
    delete_option("sfsi_pluginVersion");
    delete_option('sfsi_verificatiom_code');
    delete_option("sfsi_curlErrorNotices");
    delete_option("sfsi_curlErrorMessage");
	delete_option("sfsi_RatingDiv");
	delete_option("sfsi_languageNotice");
	delete_option("sfsi_instagram_sf_count");
	delete_option("sfsi_installDate");
	
	delete_option("adding_tags");
	delete_option("show_notification_plugin");
	delete_option("show_premium_notification");
	delete_option("show_mobile_notification");
	delete_option("show_notification");
	delete_option("show_new_notification");
    delete_option('sfsi_serverphpVersionnotification');
    delete_option("show_premium_cumulative_count_notification");

    delete_option("sfsi_addThis_icon_removal_notice_dismissed");
    delete_option("sfsi_error_reporting_notice_dismissed");
    delete_option('widget_sfsi_widget');
    delete_option('widget_subscriber_widget');
}
/* end function */

/* check CUrl */
function curl_enable_notice()
{
    if(!function_exists('curl_init')) {
    echo '<div class="error"><p> Error: It seems that CURL is disabled on your server. Please contact your server administrator to install / enable CURL.</p></div>'; die;
    }
}
    
/* add admin menus */
function sfsi_admin_menu()
{
    add_menu_page('Ultimate Social Media Icons', 'Ultimate Social Media Icons', 'administrator','sfsi-options','sfsi_options_page',plugins_url( 'images/logo.png' , dirname(__FILE__) ));
    //add_submenu_page('sfsi-options', 'Subscription Options', 'Settings','administrator', 'sfsi-options', 'sfsi_options_page');
    //add_submenu_page('sfsi-options', 'Specific About Us', 'About','administrator', 'sfsi-about', 'sfsi_about_page');
}
function sfsi_options_page(){ include SFSI_DOCROOT . '/views/sfsi_options_view.php';    } /* end function  */
function sfsi_about_page(){ include SFSI_DOCROOT . '/views/sfsi_aboutus.php';   } /* end function  */
if ( is_admin() ){
    add_action('admin_menu', 'sfsi_admin_menu');
}
/* fetch rss url from specificfeeds */ 
function SFSI_getFeedUrl()
{
    $curl = curl_init();  
     
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://www.specificfeeds.com/wordpress/plugin_setup',
        CURLOPT_USERAGENT => 'sf rss request',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
            'web_url'   => get_bloginfo('url'),
            'feed_url'  => sfsi_get_bloginfo('rss2_url'),
            'email'     => '',
            'subscriber_type' => 'OWP'
        )
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    if(curl_errno($curl))
    {
        update_option("sfsi_curlErrorNotices", "yes");
        update_option("sfsi_curlErrorMessage", curl_errno($curl));
    }
    $resp = json_decode($resp);
    curl_close($curl);
    
    $feed_url = stripslashes_deep($resp->redirect_url);
    return $resp;exit;
}
/* fetch rss url from specificfeeds on */ 
function SFSI_updateFeedUrl()
{
    $curl = curl_init();  
     
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://www.specificfeeds.com/wordpress/updateFeedPlugin',
        CURLOPT_USERAGENT => 'sf rss request',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
            'feed_id'   => sanitize_text_field(get_option('sfsi_feed_id')),
            'web_url'   => get_bloginfo('url'),
            'feed_url'  => sfsi_get_bloginfo('rss2_url'),
            'email'     => ''
        )
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $resp = json_decode($resp);
    curl_close($curl);
    
    $feed_url = stripslashes_deep($resp->redirect_url);
    return $resp;exit;
}
/* add sf tags */
function sfsi_setUpfeeds($feed_id)
{
    $curl = curl_init();  
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://www.specificfeeds.com/rssegtcrons/download_rssmorefeed_data_single/'.$feed_id."/Y",
        CURLOPT_USERAGENT => 'sf rss request',
        CURLOPT_POST => 0      
    ));
    $resp = curl_exec($curl);
    curl_close($curl);  
}
/* admin notice if wp_head is missing in active theme */
function sfsi_check_wp_head() {
    
    $template_directory = get_template_directory();
    $header = $template_directory . '/header.php';
    
    if (is_file($header)) {
        
        $search_header = "wp_head";
        $file_lines = @file($header);
        $foind_header=0;
        foreach ($file_lines as $line)
        {
            $searchCount = substr_count($line, $search_header);
            if ($searchCount > 0)
            {
                return true;
            }
        }
        
        $path=pathinfo($_SERVER['REQUEST_URI']);
        if($path['basename']=="themes.php" || $path['basename']=="theme-editor.php" || $path['basename']=="admin.php?page=sfsi-options")
        {
            $currentTheme = wp_get_theme();
                        
            if(isset($currentTheme) && !empty($currentTheme) && $currentTheme->get( 'Name' ) != "Customizr"){
                echo "<div class=\"error\" >" . "<p> Error : Please fix your theme to make plugins work correctly: Go to the <a href=\"theme-editor.php\">Theme Editor</a> and insert <code>&lt;?php wp_head(); ?&gt;</code> just before the <code>&lt;/head&gt;</code> line of your theme's <code>header.php</code> file." . "</p></div>";
            }
        }  
    }
}
/* admin notice if wp_footer is missing in active theme */
function sfsi_check_wp_footer() {
    $template_directory = get_template_directory();
    $footer = $template_directory . '/footer.php';
 
    if (is_file($footer)) {
        $search_string = "wp_footer";
        $file_lines = @file($footer);
        
        foreach ($file_lines as $line) {
            $searchCount = substr_count($line, $search_string);
            if ($searchCount > 0) {
                return true;
            }
        }
        $path=pathinfo($_SERVER['REQUEST_URI']);
        
        if($path['basename']=="themes.php" || $path['basename']=="theme-editor.php" || $path['basename']=="admin.php?page=sfsi-options")
        {
        echo "<div class=\"error\" >" . "<p>Error : Please fix your theme to make plugins work correctly: Go to the <a href=\"theme-editor.php\">Theme Editor</a> and insert <code>&lt;?php wp_footer(); ?&gt;</code> as the first line of your theme's <code>footer.php</code> file. " . "</p></div>";
        }       
    }
}
/* admin notice for first time installation */
function sfsi_activation_msg(){

    global $wp_version;
     
    if(get_option('sfsi_activate',false)==1)
     {
        echo "<div class=\"updated\" >" . "<p>Thank you for installing the <b>Ultimate Social Media Icons</b> Plugin. Please go to the <a href=\"admin.php?page=sfsi-options\">plugin's settings page </a> to configure it. </p></div>"; update_option('sfsi_activate',0);
     }
     
     $path=pathinfo($_SERVER['REQUEST_URI']);
     
     update_option('sfsi_activate',0);      
     
     if($wp_version<3.5 &&  $path['basename']=="admin.php?page=sfsi-options")
     {
        echo "<div class=\"update-nag\" >" . "<p ><b>You're using an old Wordpress version, which may cause several of your plugins to not work correctly. Please upgrade</b></p></div>"; 
     }
}
/* admin notice for first time installation */
function sfsi_rating_msg()
{
    global $wp_version;
    $install_date = get_option('sfsi_installDate');
    $display_date = date('Y-m-d h:i:s');
    $datetime1 = new DateTime($install_date);
    $datetime2 = new DateTime($display_date);
    $diff_inrval = round(($datetime2->format('U') - $datetime1->format('U')) / (60*60*24));
    
    if($diff_inrval >= 30 && "no" == get_option('sfsi_RatingDiv'))
    {
      ?>
      <style type="text/css">
        .plg-rating-dismiss:before {
            background: none;
            color: #72777c;
            content: "\f153";
            display: block;
            font: normal 16px/20px dashicons;
            speak: none;
            height: 20px;
            text-align: center;
            width: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }      
        .plg-rating-dismiss{
            position: absolute;
            top: 0;
            right: 15px;
            border: none;
            margin: 0;
            padding: 9px;
            background: none;
            color: #72777c;
            cursor: pointer;
        }
      </style>
      <div class="sfwp_fivestar notice notice-success">
                <p>You've been using the Ultimate Social Media Plugin for more than 30 days. Great! If you're happy, could you please do us a BIG favor and let us know ONE thing we can improve in it?</p>
                <ul>
                    <li><a href="https://wordpress.org/support/plugin/ultimate-social-media-icons#new-topic-0" target="_new" title="Yes, that's fair, let me give feedback!">Yes, let me give feedback!</a></li>
                    <li><a target="_new" href="https://wordpress.org/support/plugin/ultimate-social-media-icons/reviews/?filter=5">No clue, let me give a 5-star rating instead</a></li>
                    <li><a href="javascript:void(0);" class="sfsiHideRating" title="I already did">I already did (don't show this again)</a></li>
                </ul>
                <button type="button" class="plg-rating-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>                
            </div>
    <script>

    jQuery( document ).ready(function( $ ) {

        var sel1 = jQuery('.sfsiHideRating');
        var sel2 = jQuery('.plg-rating-dismiss');

        function sfsi_hide_rating(element){

            element.on("click",function(event){

                event.stopImmediatePropagation();

                var data = {'action':'sfsi_hideRating' , 'nonce':'<?php echo wp_create_nonce('sfsi_hideRating'); ?>'};

                jQuery.ajax({
                    url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                    type: "post",
                    data: data,
                    dataType: "json",
                    async: !0,
                    success: function(e)
                    {
                        if (e=="success") {
                           jQuery('.sfwp_fivestar').slideUp('slow');
                        }
                    }
                 });
            });            

        }

        sfsi_hide_rating(sel1);
        sfsi_hide_rating(sel2);

    });
    </script>
    
    <?php 
   }
}

add_action('wp_ajax_sfsi_hideRating','sfsi_HideRatingDiv', 0);
function sfsi_HideRatingDiv()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "sfsi_hideRating")) {
        echo  json_encode(array('res'=>"error")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }


    
    update_option('sfsi_RatingDiv','yes');
    echo  json_encode(array("success")); exit;
}
/* add all admin message */
add_action('admin_notices', 'sfsi_activation_msg');
add_action('admin_notices', 'sfsi_rating_msg');
add_action('admin_notices', 'sfsi_check_wp_head');
add_action('admin_notices', 'sfsi_check_wp_footer');

function sfsi_pingVendor( $post_id )
{
    global $wp,$wpdb;
    // If this is just a revision, don't send the email.
    if ( wp_is_post_revision( $post_id ) )
        return;
    $post_data=get_post($post_id,ARRAY_A);
    if($post_data['post_status']=='publish' && $post_data['post_type']=='post') : 
     $categories = wp_get_post_categories($post_data['ID']);
     $cats='';
     $total=count($categories);
     $count=1;
     foreach($categories as $c)
     {  
        $cat_data = get_category( $c );
        if($count==$total)
        {
            $cats.=$cat_data->name;
        }
        else
        {
          $cats.=$cat_data->name.',';   
        }
        $count++;   
     }
    $postto_array = array(
        'feed_id'   => sanitize_text_field(get_option('sfsi_feed_id')),
        'title'     => $post_data['post_title'],
        'description'=> $post_data['post_content'],
        'link'      => $post_data['guid'],
        'author'    => get_the_author_meta('user_login', $post_data['post_author']),
        'category'  => $cats,
        'pubDate'   => $post_data['post_modified'],
        'rssurl'    => sfsi_get_bloginfo('rss2_url')
    );
    $curl = curl_init();  
     
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://www.specificfeeds.com/wordpress/addpostdata ',
        CURLOPT_USERAGENT => 'sf rss request',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $postto_array
    ));
    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    $resp=json_decode($resp);
    curl_close($curl);
      
       return true;
    endif;
}
add_action( 'save_post', 'sfsi_pingVendor' );

function sfsi_was_displaying_addthis(){

    $isDismissed   =  true;

    $sfsi_section1 =  unserialize(get_option('sfsi_section1_options',false));
    $sfsi_section6 =  unserialize(get_option('sfsi_section6_options',false));

    $sfsi_addThiswasDisplayed_section1 = isset($sfsi_section1['sfsi_share_display']) && 'yes'== sanitize_text_field($sfsi_section1['sfsi_share_display']);

    $sfsi_addThiswasDisplayed_section6 = isset($sfsi_section6['sfsi_rectshr']) && 'yes'== sanitize_text_field($sfsi_section6['sfsi_rectshr']);

    $isDisplayed = $sfsi_addThiswasDisplayed_section1 || $sfsi_addThiswasDisplayed_section6;

    // If icon was displayed 
    $isDismissed = false != $isDisplayed ? false : true;

    update_option('sfsi_addThis_icon_removal_notice_dismissed',$isDismissed);

    if($sfsi_addThiswasDisplayed_section1){
        unset($sfsi_section1['sfsi_share_display']);
        update_option('sfsi_section1_options', serialize($sfsi_section1) );
    }

    if($sfsi_addThiswasDisplayed_section6){
        unset($sfsi_section6['sfsi_rectshr']);
        update_option('sfsi_section6_options', serialize($sfsi_section6) );     
    }
}  
?>
