<?php 
/* save all option to options table in database using ajax */
/* save settings for section 1 */        
add_action('wp_ajax_updateSrcn1','sfsi_options_updater1');        
function sfsi_options_updater1()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step1")) {
      echo  json_encode(array("wrong_nonce")); exit;
    } 
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $option1=  unserialize(get_option('sfsi_section1_options',false));
    $sfsi_rss_display           = isset($_POST["sfsi_rss_display"]) ? sanitize_text_field( $_POST["sfsi_rss_display"] ): 'no'; 
    $sfsi_email_display         = isset($_POST["sfsi_email_display"]) ? sanitize_text_field( $_POST["sfsi_email_display"] ): 'no'; 
    $sfsi_facebook_display      = isset($_POST["sfsi_facebook_display"]) ? sanitize_text_field( $_POST["sfsi_facebook_display"] ): 'no'; 
    $sfsi_twitter_display       = isset($_POST["sfsi_twitter_display"]) ? sanitize_text_field( $_POST["sfsi_twitter_display"] ): 'no'; 
    $sfsi_google_display        = isset($_POST["sfsi_google_display"]) ? sanitize_text_field( $_POST["sfsi_google_display"] ): 'no'; 
    $sfsi_youtube_display       = isset($_POST["sfsi_youtube_display"]) ? sanitize_text_field( $_POST["sfsi_youtube_display"] ): 'no'; 
    $sfsi_pinterest_display     = isset($_POST["sfsi_pinterest_display"]) ? sanitize_text_field( $_POST["sfsi_pinterest_display"] ): 'no';
    $sfsi_instagram_display     = isset($_POST["sfsi_instagram_display"]) ? sanitize_text_field( $_POST["sfsi_instagram_display"] ): 'no';
    $sfsi_linkedin_display      = isset($_POST["sfsi_linkedin_display"]) ? sanitize_text_field( $_POST["sfsi_linkedin_display"] ): 'no';
    $sfsi_custom_icons          = isset($option1['sfsi_custom_files']) ? $option1['sfsi_custom_files'] : '';
    $up_option1=array(
        'sfsi_rss_display'      => sanitize_text_field($sfsi_rss_display),
        'sfsi_email_display'    => sanitize_text_field($sfsi_email_display),
        'sfsi_facebook_display' => sanitize_text_field($sfsi_facebook_display),
        'sfsi_twitter_display'  => sanitize_text_field($sfsi_twitter_display),
        'sfsi_google_display'   => sanitize_text_field($sfsi_google_display),
        'sfsi_youtube_display'  => sanitize_text_field($sfsi_youtube_display),
        'sfsi_pinterest_display'=> sanitize_text_field($sfsi_pinterest_display),
        'sfsi_linkedin_display' => sanitize_text_field($sfsi_linkedin_display),
        'sfsi_instagram_display'=> sanitize_text_field($sfsi_instagram_display),
        'sfsi_custom_files'     => sanitize_text_field($sfsi_custom_icons)
    );
    update_option('sfsi_section1_options',  serialize($up_option1));
    header('Content-Type: application/json');
    echo  json_encode(array("success")); exit;
}
/* save settings for section 2 */  
add_action('wp_ajax_updateSrcn2','sfsi_options_updater2');        
function sfsi_options_updater2()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step2")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_rss_url                   = isset($_POST["sfsi_rss_url"]) ? esc_url( trim($_POST["sfsi_rss_url"])) : ''; 
    $sfsi_rss_icons                 = isset($_POST["sfsi_rss_icons"]) ? sanitize_text_field( $_POST["sfsi_rss_icons"] ): 'email'; 
    
    $sfsi_facebookPage_option       = isset($_POST["sfsi_facebookPage_option"]) ? sanitize_text_field( $_POST["sfsi_facebookPage_option"] ): 'no'; 
    $sfsi_facebookPage_url          = isset($_POST["sfsi_facebookPage_url"]) ? esc_url( trim($_POST["sfsi_facebookPage_url"])) : ''; 
    $sfsi_facebookLike_option       = isset($_POST["sfsi_facebookLike_option"]) ? sanitize_text_field( $_POST["sfsi_facebookLike_option"] ): 'no'; 
    $sfsi_facebookShare_option      = isset($_POST["sfsi_facebookShare_option"]) ? sanitize_text_field( $_POST["sfsi_facebookShare_option"] ): 'no'; 
    
    $sfsi_twitter_followme          = isset($_POST["sfsi_twitter_followme"]) ? sanitize_text_field( $_POST["sfsi_twitter_followme"] ): 'no'; 
    $sfsi_twitter_followUserName    = isset($_POST["sfsi_twitter_followUserName"]) ? sanitize_text_field( trim($_POST["sfsi_twitter_followUserName"])) : '';
    $sfsi_twitter_aboutPage         = isset($_POST["sfsi_twitter_aboutPage"]) ? sanitize_text_field( $_POST["sfsi_twitter_aboutPage"] ): 'no';
    $sfsi_twitter_page              = isset($_POST["sfsi_twitter_page"]) ? sanitize_text_field( $_POST["sfsi_twitter_page"] ): 'no';
    $sfsi_twitter_pageURL           = isset($_POST["sfsi_twitter_pageURL"]) ? esc_url( trim($_POST["sfsi_twitter_pageURL"])) : '';
    $sfsi_twitter_aboutPageText     = isset($_POST["sfsi_twitter_aboutPageText"]) ? sanitize_text_field( $_POST["sfsi_twitter_aboutPageText"] ): 'Hey check out this cool site I found';
    
    $sfsi_google_page               = isset($_POST["sfsi_google_page"]) ? sanitize_text_field( $_POST["sfsi_google_page"] ): 'no';
    $sfsi_google_pageURL            = isset($_POST["sfsi_google_pageURL"]) ? esc_url( trim($_POST["sfsi_google_pageURL"])) : '';
    $sfsi_googleLike_option         = isset($_POST["sfsi_googleLike_option"]) ? sanitize_text_field( $_POST["sfsi_googleLike_option"] ): 'no';
    $sfsi_googleShare_option        = isset($_POST["sfsi_googleShare_option"]) ? sanitize_text_field( $_POST["sfsi_googleShare_option"] ): 'no';
    
    $sfsi_youtube_pageUrl           = isset($_POST["sfsi_youtube_pageUrl"]) ? esc_url( trim($_POST["sfsi_youtube_pageUrl"])) : '';
    $sfsi_youtube_page              = isset($_POST["sfsi_youtube_page"]) ? sanitize_text_field( $_POST["sfsi_youtube_page"] ): 'no';
    $sfsi_youtube_follow            = isset($_POST["sfsi_youtube_follow"]) ? sanitize_text_field( $_POST["sfsi_youtube_follow"] ): 'no';
    
    $sfsi_pinterest_page            = isset($_POST["sfsi_pinterest_page"]) ? sanitize_text_field( $_POST["sfsi_pinterest_page"] ): 'no';
    $sfsi_pinterest_pageUrl         = isset($_POST["sfsi_pinterest_pageUrl"]) ? esc_url( trim($_POST["sfsi_pinterest_pageUrl"])) : '';
    $sfsi_pinterest_pingBlog        = isset($_POST["sfsi_pinterest_pingBlog"]) ? sanitize_text_field( $_POST["sfsi_pinterest_pingBlog"] ): 'no';
    
    $sfsi_instagram_pageUrl         = isset($_POST["sfsi_instagram_pageUrl"]) ? esc_url( trim($_POST["sfsi_instagram_pageUrl"])) : '';  
    
    $sfsi_linkedin_page             = isset($_POST["sfsi_linkedin_page"]) ? sanitize_text_field( $_POST["sfsi_linkedin_page"] ): 'no';
    $sfsi_linkedin_pageURL          = isset($_POST["sfsi_linkedin_pageURL"]) ? esc_url( trim($_POST["sfsi_linkedin_pageURL"])) : '';
    $sfsi_linkedin_follow           = isset($_POST["sfsi_linkedin_follow"]) ? sanitize_text_field( $_POST["sfsi_linkedin_follow"] ): 'no';
    $sfsi_linkedin_followCompany    = isset($_POST["sfsi_linkedin_followCompany"]) ? sanitize_text_field( trim($_POST["sfsi_linkedin_followCompany"])) : '';
    $sfsi_linkedin_SharePage        = isset($_POST["sfsi_linkedin_SharePage"]) ? sanitize_text_field( $_POST["sfsi_linkedin_SharePage"] ): 'no';
    $sfsi_linkedin_recommendBusines = isset($_POST["sfsi_linkedin_recommendBusines"]) ? sanitize_text_field( $_POST["sfsi_linkedin_recommendBusines"] ): 'no';
    $sfsi_linkedin_recommendCompany = isset($_POST["sfsi_linkedin_recommendCompany"]) ? sanitize_text_field( trim($_POST["sfsi_linkedin_recommendCompany"])) : '';
    $sfsi_linkedin_recommendProductId= isset($_POST["sfsi_linkedin_recommendProductId"]) ? intval( trim($_POST["sfsi_linkedin_recommendProductId"])) : '';
    
    $sfsi_youtubeusernameorid = isset($_POST["sfsi_youtubeusernameorid"]) ? sanitize_text_field( trim($_POST["sfsi_youtubeusernameorid"])) : '';
    $sfsi_ytube_user          = ($_POST["sfsi_ytube_user"]) ? $_POST["sfsi_ytube_user"] : '';
    $sfsi_ytube_chnlid        = isset($_POST["sfsi_ytube_chnlid"]) ? sanitize_text_field( $_POST["sfsi_ytube_chnlid"] ): '';
    
    /*
     * Escape custom icons url
     */
    if(
        isset($_POST["sfsi_custom_links"]) &&
        !empty($_POST["sfsi_custom_links"])
    )
    {
        $esacpedUrls = array();
        $sfsi_customIconsUrl = $_POST["sfsi_custom_links"];

        foreach($sfsi_customIconsUrl as $key => $sfsi_customIconUrl)
        {
            $esacpedUrls[$key] = esc_url($sfsi_customIconUrl);
        }        
    }
    else
    {
        $esacpedUrls = '';
    }
    $sfsi_CustomIcon_links    = isset($_POST["sfsi_custom_links"]) ?serialize($esacpedUrls) : '';
          
    $option2 = unserialize(get_option('sfsi_section2_options',false)); 
    $up_option2=array(
        'sfsi_rss_url'              => esc_url($sfsi_rss_url),
        'sfsi_rss_blogName'         => '',
        'sfsi_rss_blogEmail'        => '',
        'sfsi_rss_icons'            => sanitize_text_field($sfsi_rss_icons),
        'sfsi_email_url'            => esc_url($option2['sfsi_email_url']),   
        /* facebook buttons options */
        'sfsi_facebookPage_option'  => sanitize_text_field($sfsi_facebookPage_option),
        'sfsi_facebookPage_url'     => esc_url($sfsi_facebookPage_url),
        'sfsi_facebookLike_option'  => sanitize_text_field($sfsi_facebookLike_option),
        'sfsi_facebookShare_option' => sanitize_text_field($sfsi_facebookShare_option),
         /* Twitter buttons options */
        'sfsi_twitter_followme'     => sanitize_text_field($sfsi_twitter_followme),
        'sfsi_twitter_followUserName'=> sanitize_text_field($sfsi_twitter_followUserName),
        'sfsi_twitter_aboutPage'    => sanitize_text_field($sfsi_twitter_aboutPage),
        'sfsi_twitter_page'         => sanitize_text_field($sfsi_twitter_page),
        'sfsi_twitter_pageURL'      => esc_url($sfsi_twitter_pageURL),
        'sfsi_twitter_aboutPageText'=> sanitize_text_field($sfsi_twitter_aboutPageText),
         /* google + options */
        'sfsi_google_page'          => sanitize_text_field($sfsi_google_page),
        'sfsi_google_pageURL'       => esc_url($sfsi_google_pageURL),
        'sfsi_googleLike_option'    => sanitize_text_field($sfsi_googleLike_option),
        'sfsi_googleShare_option'   => sanitize_text_field($sfsi_googleShare_option),
         /* youtube options */
        'sfsi_youtube_pageUrl'      => esc_url($sfsi_youtube_pageUrl),
        'sfsi_youtube_page'         => sanitize_text_field($sfsi_youtube_page),
        'sfsi_youtube_follow'       => sanitize_text_field($sfsi_youtube_follow),
        'sfsi_youtubeusernameorid'  => sanitize_text_field($sfsi_youtubeusernameorid),
        'sfsi_ytube_user'           => sanitize_text_field($sfsi_ytube_user),
        'sfsi_ytube_chnlid'         => sanitize_text_field($sfsi_ytube_chnlid),
        
         /* pinterest options */
        'sfsi_pinterest_page'       => sanitize_text_field($sfsi_pinterest_page),
        'sfsi_pinterest_pageUrl'    => esc_url($sfsi_pinterest_pageUrl),
        'sfsi_pinterest_pingBlog'   => sanitize_text_field($sfsi_pinterest_pingBlog),
        /* instagram options */
        'sfsi_instagram_pageUrl'    => esc_url($sfsi_instagram_pageUrl),
         /* linkedIn options */
        'sfsi_linkedin_page'            => sanitize_text_field($sfsi_linkedin_page),
        'sfsi_linkedin_pageURL'         => esc_url($sfsi_linkedin_pageURL),
        'sfsi_linkedin_follow'          => sanitize_text_field($sfsi_linkedin_follow),
        'sfsi_linkedin_followCompany'   => intval($sfsi_linkedin_followCompany),
        'sfsi_linkedin_SharePage'       => sanitize_text_field($sfsi_linkedin_SharePage),
        'sfsi_linkedin_recommendBusines'=> sanitize_text_field($sfsi_linkedin_recommendBusines),
        'sfsi_linkedin_recommendCompany'=> sanitize_text_field($sfsi_linkedin_recommendCompany),
        'sfsi_linkedin_recommendProductId'=> intval($sfsi_linkedin_recommendProductId),
        'sfsi_CustomIcon_links'         => $sfsi_CustomIcon_links
    );

    $option4 = unserialize(get_option('sfsi_section4_options',false));     
    
    
    update_option('sfsi_section4_options',  serialize($option4));
    update_option('sfsi_section2_options',  serialize($up_option2));
    
    header('Content-Type: application/json');
    echo json_encode(array("success")); exit;
}
/* save settings for section 3 */ 
add_action('wp_ajax_updateSrcn3','sfsi_options_updater3');        
function sfsi_options_updater3()
{   
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step3")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_actvite_theme             = isset($_POST["sfsi_actvite_theme"]) ? sanitize_text_field( $_POST["sfsi_actvite_theme"] ): 'no'; 
    $sfsi_mouseOver                 = isset($_POST["sfsi_mouseOver"]) ? sanitize_text_field( $_POST["sfsi_mouseOver"] ): 'no'; 
    $sfsi_mouseOver_effect          = isset($_POST["sfsi_mouseOver_effect"]) ? sanitize_text_field( $_POST["sfsi_mouseOver_effect"] ): 'fade_in'; 
    $sfsi_mouseover_effect_type     = isset($_POST["sfsi_mouseover_effect_type"]) ? sanitize_text_field( $_POST["sfsi_mouseover_effect_type"] ): 'same_icons'; 
    $sfsi_shuffle_icons             = isset($_POST["sfsi_shuffle_icons"]) ? sanitize_text_field( $_POST["sfsi_shuffle_icons"] ): 'no'; 
    $sfsi_shuffle_Firstload         = isset($_POST["sfsi_shuffle_Firstload"]) ? sanitize_text_field( $_POST["sfsi_shuffle_Firstload"] ): 'no'; 
    $sfsi_shuffle_interval          = isset($_POST["sfsi_shuffle_interval"]) ? intval( $_POST["sfsi_shuffle_interval"] ): 'no'; 
    $sfsi_shuffle_intervalTime      = isset($_POST["sfsi_shuffle_intervalTime"]) ? sanitize_text_field( $_POST["sfsi_shuffle_intervalTime"] ): ''; 
    $sfsi_specialIcon_animation     = isset($_POST["sfsi_specialIcon_animation"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_animation"] ): '';
    $sfsi_specialIcon_MouseOver     = isset($_POST["sfsi_specialIcon_MouseOver"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_MouseOver"] ): 'no';
    $sfsi_specialIcon_Firstload     = isset($_POST["sfsi_specialIcon_Firstload"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_Firstload"] ): 'no';
    $sfsi_specialIcon_Firstload_Icons = isset($_POST["sfsi_specialIcon_Firstload_Icons"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_Firstload_Icons"] ): 'all';
    $sfsi_specialIcon_interval      = isset($_POST["sfsi_specialIcon_interval"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_interval"] ): 'no';
    $sfsi_specialIcon_intervalTime  = isset($_POST["sfsi_specialIcon_intervalTime"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_intervalTime"] ): '';
    $sfsi_specialIcon_intervalIcons = isset($_POST["sfsi_specialIcon_intervalIcons"]) ? sanitize_text_field( $_POST["sfsi_specialIcon_intervalIcons"] ): 'all';
    
    /* Design and animation option  */
    $up_option3 =array(     
        'sfsi_actvite_theme'                => sanitize_text_field($sfsi_actvite_theme),
        /* animations options */
        'sfsi_mouseOver'                    => sanitize_text_field($sfsi_mouseOver),
        'sfsi_mouseOver_effect'             => sanitize_text_field($sfsi_mouseOver_effect),
        'sfsi_mouseover_effect_type'        => sanitize_text_field($sfsi_mouseover_effect_type),
        'sfsi_shuffle_icons'                => sanitize_text_field($sfsi_shuffle_icons),
        'sfsi_shuffle_Firstload'            => sanitize_text_field($sfsi_shuffle_Firstload),
        'sfsi_shuffle_interval'             => sanitize_text_field($sfsi_shuffle_interval),
        'sfsi_shuffle_intervalTime'         => intval($sfsi_shuffle_intervalTime),
        'sfsi_specialIcon_animation'        => sanitize_text_field($sfsi_specialIcon_animation),
        'sfsi_specialIcon_MouseOver'        => sanitize_text_field($sfsi_specialIcon_MouseOver),
        'sfsi_specialIcon_Firstload'        => sanitize_text_field($sfsi_specialIcon_Firstload),
        'sfsi_specialIcon_Firstload_Icons'  => sanitize_text_field($sfsi_specialIcon_Firstload_Icons),
        'sfsi_specialIcon_interval'         => sanitize_text_field($sfsi_specialIcon_interval),
        'sfsi_specialIcon_intervalTime'     => sanitize_text_field($sfsi_specialIcon_intervalTime),
        'sfsi_specialIcon_intervalIcons'    => sanitize_text_field($sfsi_specialIcon_intervalIcons),
    );
    update_option('sfsi_section3_options', serialize($up_option3));
    header('Content-Type: application/json');
    echo json_encode(array("success")); exit;
}
/* save settings for section 4 */ 
add_action('wp_ajax_updateSrcn4','sfsi_options_updater4');        
function sfsi_options_updater4()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step4")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_display_counts             = isset($_POST["sfsi_display_counts"]) ? sanitize_text_field( $_POST["sfsi_display_counts"] ): 'no'; 
   
    $sfsi_email_countsDisplay        = isset($_POST["sfsi_email_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_email_countsDisplay"] ): 'no'; 
    $sfsi_email_countsFrom           = isset($_POST["sfsi_email_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_email_countsFrom"] ): 'manual'; 
    $sfsi_email_manualCounts         = isset($_POST["sfsi_email_manualCounts"]) ? intval( trim($_POST["sfsi_email_manualCounts"])) : ''; 
    
    $sfsi_rss_countsDisplay          = isset($_POST["sfsi_rss_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_rss_countsDisplay"] ): 'no'; 
    $sfsi_rss_manualCounts           = isset($_POST["sfsi_rss_manualCounts"]) ? intval( trim($_POST["sfsi_rss_manualCounts"])) : ''; 
    
    $sfsi_facebook_countsDisplay     = isset($_POST["sfsi_facebook_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_facebook_countsDisplay"] ): 'no'; 
    $sfsi_facebook_countsFrom        = isset($_POST["sfsi_facebook_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_facebook_countsFrom"] ): 'manual';
    $sfsi_facebook_mypageCounts      = isset($_POST["sfsi_facebook_mypageCounts"]) ? sanitize_text_field( trim($_POST["sfsi_facebook_mypageCounts"])) : '';
    $sfsi_facebook_manualCounts      = isset($_POST["sfsi_facebook_manualCounts"]) ? intval( trim($_POST["sfsi_facebook_manualCounts"])) : '';
    $sfsi_facebook_PageLink          = isset($_POST["sfsi_facebook_PageLink"]) ? sanitize_text_field( trim($_POST["sfsi_facebook_PageLink"])) : '';
    
    $sfsi_twitter_countsDisplay      = isset($_POST["sfsi_twitter_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_twitter_countsDisplay"] ): 'no';
    $sfsi_twitter_countsFrom         = isset($_POST["sfsi_twitter_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_twitter_countsFrom"] ): 'manual';
    $sfsi_twitter_manualCounts       = isset($_POST["sfsi_twitter_manualCounts"]) ? intval( trim($_POST["sfsi_twitter_manualCounts"])) : '';
    $tw_consumer_key                 = isset($_POST["tw_consumer_key"]) ? sanitize_text_field( trim($_POST["tw_consumer_key"]) ): '';
    $tw_consumer_secret              = isset($_POST["tw_consumer_secret"]) ? sanitize_text_field( trim($_POST["tw_consumer_secret"]) ): '';
    $tw_oauth_access_token           = isset($_POST["tw_oauth_access_token"]) ? sanitize_text_field( trim($_POST["tw_oauth_access_token"]) ): '';
    $tw_oauth_access_token_secret    = isset($_POST["tw_oauth_access_token_secret"]) ? sanitize_text_field( trim($_POST["tw_oauth_access_token_secret"]) ): '';
    
    $sfsi_google_countsDisplay       = isset($_POST["sfsi_google_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_google_countsDisplay"] ): 'no';
    $sfsi_google_countsFrom          = isset($_POST["sfsi_google_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_google_countsFrom"] ): 'manual';
    $sfsi_google_manualCounts        = isset($_POST["sfsi_google_manualCounts"]) ? intval( trim($_POST["sfsi_google_manualCounts"])) : '';
    $sfsi_google_api_key             = isset($_POST["sfsi_google_api_key"]) ? sanitize_text_field( trim($_POST["sfsi_google_api_key"]) ): '';  
    
    $sfsi_linkedIn_countsDisplay     = isset($_POST["sfsi_linkedIn_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_linkedIn_countsDisplay"] ): 'no';
    $sfsi_linkedIn_countsFrom        = isset($_POST["sfsi_linkedIn_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_linkedIn_countsFrom"] ): 'manual';
    $sfsi_linkedIn_manualCounts      = isset($_POST["sfsi_linkedIn_manualCounts"]) ? intval( trim($_POST["sfsi_linkedIn_manualCounts"])) : '';
    $ln_company                      = isset($_POST["ln_company"]) ? sanitize_text_field( trim($_POST["ln_company"])) : '';
    $ln_api_key                      = isset($_POST["ln_api_key"]) ? sanitize_text_field( trim($_POST["ln_api_key"])) : '';
    $ln_secret_key                   = isset($_POST["ln_secret_key"]) ? sanitize_text_field( trim($_POST["ln_secret_key"])) : '';
    $ln_oAuth_user_token             = isset($_POST["ln_oAuth_user_token"]) ? sanitize_text_field( trim($_POST["ln_oAuth_user_token"])) : '';
   
    $sfsi_youtube_countsDisplay      = isset($_POST["sfsi_youtube_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_youtube_countsDisplay"] ): 'no';
    $sfsi_youtube_countsFrom         = isset($_POST["sfsi_youtube_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_youtube_countsFrom"] ): 'manual';
    $sfsi_youtube_manualCounts       = isset($_POST["sfsi_youtube_manualCounts"]) ? intval( $_POST["sfsi_youtube_manualCounts"] ): '';
    $sfsi_youtube_user               = isset($_POST["sfsi_youtube_user"]) ? sanitize_text_field( trim($_POST["sfsi_youtube_user"])) : '';
    $sfsi_youtube_channelId          = isset($_POST["sfsi_youtube_channelId"]) ? sanitize_text_field( trim($_POST["sfsi_youtube_channelId"])) : '';
    
    $sfsi_pinterest_countsDisplay    = isset($_POST["sfsi_pinterest_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_pinterest_countsDisplay"] ): 'no';
    $sfsi_pinterest_countsFrom       = isset($_POST["sfsi_pinterest_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_pinterest_countsFrom"] ): 'manual';
    $sfsi_pinterest_manualCounts     = isset($_POST["sfsi_pinterest_manualCounts"]) ? intval( trim($_POST["sfsi_pinterest_manualCounts"])) : '';
    $sfsi_pinterest_user             = isset($_POST["sfsi_pinterest_user"]) ? sanitize_text_field( trim($_POST["sfsi_pinterest_user"]) ): '';
    $sfsi_pinterest_board            = isset($_POST["sfsi_pinterest_board"]) ? sanitize_text_field( trim($_POST["sfsi_pinterest_board"])) : '';
    
    $sfsi_instagram_countsDisplay    = isset($_POST["sfsi_instagram_countsDisplay"]) ? sanitize_text_field( $_POST["sfsi_instagram_countsDisplay"] ): 'no';
    $sfsi_instagram_countsFrom       = isset($_POST["sfsi_instagram_countsFrom"]) ? sanitize_text_field( $_POST["sfsi_instagram_countsFrom"] ): 'manual';
    $sfsi_instagram_manualCounts     = isset($_POST["sfsi_instagram_manualCounts"]) ? intval( trim($_POST["sfsi_instagram_manualCounts"])) : '';
    $sfsi_instagram_User             = isset($_POST["sfsi_instagram_User"]) ? sanitize_text_field( $_POST["sfsi_instagram_User"] ): '';
    $sfsi_instagram_clientid         = isset($_POST["sfsi_instagram_clientid"]) ? sanitize_text_field( $_POST["sfsi_instagram_clientid"] ): '';
    $sfsi_instagram_appurl           = isset($_POST["sfsi_instagram_appurl"]) ? sanitize_text_field( $_POST["sfsi_instagram_appurl"] ): '';
    $sfsi_instagram_token             = isset($_POST["sfsi_instagram_token"]) ? sanitize_text_field( $_POST["sfsi_instagram_token"] ): '';
    
    $sfsi_facebookPage_url           = isset($_POST["sfsi_facebookPage_url"]) ? sanitize_text_field( trim($_POST["sfsi_facebookPage_url"])) : ''; 
    
    $up_option4=array(
        'sfsi_display_counts'       => sanitize_text_field($sfsi_display_counts),
            
        'sfsi_email_countsDisplay'  => sanitize_text_field($sfsi_email_countsDisplay),
        'sfsi_email_countsFrom'     => sanitize_text_field($sfsi_email_countsFrom),
        'sfsi_email_manualCounts'   => intval($sfsi_email_manualCounts),
        
        'sfsi_rss_countsDisplay'    => sanitize_text_field($sfsi_rss_countsDisplay),
        'sfsi_rss_manualCounts'     => intval($sfsi_rss_manualCounts),
        
        'sfsi_facebook_countsDisplay'=> sanitize_text_field($sfsi_facebook_countsDisplay),
        'sfsi_facebook_countsFrom'  => sanitize_text_field($sfsi_facebook_countsFrom),
        'sfsi_facebook_mypageCounts'=> sfsi_sanitize_field($sfsi_facebook_mypageCounts),
        'sfsi_facebook_manualCounts'=> intval($sfsi_facebook_manualCounts),
        //'sfsi_facebook_PageLink'  => $sfsi_facebook_PageLink,
        
        'sfsi_twitter_countsDisplay'=> sanitize_text_field($sfsi_twitter_countsDisplay),
        'sfsi_twitter_countsFrom'   => sanitize_text_field($sfsi_twitter_countsFrom),
        'sfsi_twitter_manualCounts' => intval($sfsi_twitter_manualCounts),
        'tw_consumer_key'           => sfsi_sanitize_field($tw_consumer_key),     
        'tw_consumer_secret'        => sfsi_sanitize_field($tw_consumer_secret),
        'tw_oauth_access_token'     => sfsi_sanitize_field($tw_oauth_access_token),
        'tw_oauth_access_token_secret'=>sfsi_sanitize_field($tw_oauth_access_token_secret),
            
        'sfsi_google_countsDisplay' => sanitize_text_field($sfsi_google_countsDisplay),
        'sfsi_google_countsFrom'    => sanitize_text_field($sfsi_google_countsFrom),
        'sfsi_google_manualCounts'  => intval($sfsi_google_manualCounts),
        'sfsi_google_api_key'       => sfsi_sanitize_field($sfsi_google_api_key),
        
        //'ln_company'              => $ln_company,
        //'ln_api_key'              => $ln_api_key,
        //'ln_secret_key'           => $ln_secret_key,
        //'ln_oAuth_user_token'     => $ln_oAuth_user_token,     
        'sfsi_linkedIn_countsDisplay'=> sanitize_text_field($sfsi_linkedIn_countsDisplay),
        'sfsi_linkedIn_countsFrom'  => sanitize_text_field($sfsi_linkedIn_countsFrom),
        'sfsi_linkedIn_manualCounts'=> intval($sfsi_linkedIn_manualCounts),
        
        'sfsi_youtube_countsDisplay'=> sanitize_text_field($sfsi_youtube_countsDisplay),
        'sfsi_youtube_countsFrom'   => sanitize_text_field($sfsi_youtube_countsFrom),
        'sfsi_youtube_manualCounts' => intval($sfsi_youtube_manualCounts),
        'sfsi_youtube_user'         => sfsi_sanitize_field($sfsi_youtube_user),
        'sfsi_youtube_channelId'    => sfsi_sanitize_field($sfsi_youtube_channelId),
        
        'sfsi_pinterest_countsDisplay'=> sanitize_text_field($sfsi_pinterest_countsDisplay),
        'sfsi_pinterest_countsFrom' => sanitize_text_field($sfsi_pinterest_countsFrom),
        'sfsi_pinterest_manualCounts'=> intval($sfsi_pinterest_manualCounts),
        //'sfsi_pinterest_user'     => $sfsi_pinterest_user,     
        //'sfsi_pinterest_board'    => $sfsi_pinterest_board,
        
        'sfsi_instagram_countsFrom' => sanitize_text_field($sfsi_instagram_countsFrom),
        'sfsi_instagram_countsDisplay'=> sanitize_text_field($sfsi_instagram_countsDisplay),
        'sfsi_instagram_manualCounts'=> intval($sfsi_instagram_manualCounts),
        'sfsi_instagram_User'       => sanitize_text_field($sfsi_instagram_User),
        'sfsi_instagram_clientid'    => sanitize_text_field($sfsi_instagram_clientid),
        'sfsi_instagram_appurl'      => sanitize_text_field($sfsi_instagram_appurl),
        'sfsi_instagram_token'       => sanitize_text_field($sfsi_instagram_token),
   );
   update_option('sfsi_section4_options',   serialize($up_option4));
   
   $new_counts  = sfsi_getCounts();
   header('Content-Type: application/json');
   echo json_encode(array("res"=>"success",'counts'=>$new_counts)); exit;
}
/* save settings for section 5 */ 
add_action('wp_ajax_updateSrcn5','sfsi_options_updater5');        
function sfsi_options_updater5()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step5")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_icons_size                = isset($_POST["sfsi_icons_size"]) ? sanitize_text_field( $_POST["sfsi_icons_size"] ): '51'; 
    $sfsi_icons_spacing             = isset($_POST["sfsi_icons_spacing"]) ? sanitize_text_field( $_POST["sfsi_icons_spacing"] ): '2'; 
    $sfsi_icons_Alignment           = isset($_POST["sfsi_icons_Alignment"]) ? sanitize_text_field( $_POST["sfsi_icons_Alignment"] ): 'center'; 
    
    $sfsi_icons_perRow              = isset($_POST["sfsi_icons_perRow"]) ? sanitize_text_field( $_POST["sfsi_icons_perRow"] ): '5'; 
    $sfsi_icons_ClickPageOpen       = isset($_POST["sfsi_icons_ClickPageOpen"]) ? sanitize_text_field( $_POST["sfsi_icons_ClickPageOpen"] ): 'no'; 
    $sfsi_icons_suppress_errors     = isset($_POST["sfsi_icons_suppress_errors"]) ? sanitize_text_field( $_POST["sfsi_icons_suppress_errors"] ): 'no';     
    $sfsi_icons_stick               = isset($_POST["sfsi_icons_stick"]) ? sanitize_text_field( $_POST["sfsi_icons_stick"] ): 'no';

    $sfsi_rss_MouseOverText         = isset($_POST["sfsi_rss_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_rss_MouseOverText"] ): '';
    $sfsi_email_MouseOverText       = isset($_POST["sfsi_email_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_email_MouseOverText"] ): '';
    $sfsi_twitter_MouseOverText     = isset($_POST["sfsi_twitter_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_twitter_MouseOverText"] ): '';
    $sfsi_facebook_MouseOverText    = isset($_POST["sfsi_facebook_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_facebook_MouseOverText"] ): '';
    $sfsi_google_MouseOverText      = isset($_POST["sfsi_google_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_google_MouseOverText"] ): '';
    $sfsi_linkedIn_MouseOverText    = isset($_POST["sfsi_linkedIn_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_linkedIn_MouseOverText"] ): '';
    $sfsi_pinterest_MouseOverText   = isset($_POST["sfsi_pinterest_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_pinterest_MouseOverText"] ): '';
    $sfsi_instagram_MouseOverText   = isset($_POST["sfsi_instagram_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_instagram_MouseOverText"] ): '';
    $sfsi_youtube_MouseOverText     = isset($_POST["sfsi_youtube_MouseOverText"]) ? sanitize_text_field( $_POST["sfsi_youtube_MouseOverText"] ): '';
    if(isset($_POST["sfsi_custom_orders"])){
        $sfsi_custom_orders = array();
        foreach($_POST["sfsi_custom_orders"] as $index=>$custom_order){
            $sfsi_custom_orders[$index] = array();
            $sfsi_custom_orders[$index]["order"] = intval($_POST["sfsi_custom_orders"][$index]["order"] );
            $sfsi_custom_orders[$index]["ele"] = intval($_POST["sfsi_custom_orders"][$index]["order"] );
        }
    }

    $sfsi_custom_orders             = isset($_POST["sfsi_custom_orders"]) ? serialize($sfsi_custom_orders) : '';
    
    $sfsi_rssIcon_order             = isset($_POST["sfsi_rssIcon_order"]) ? sanitize_text_field( $_POST["sfsi_rssIcon_order"] ): '1';
    $sfsi_emailIcon_order           = isset($_POST["sfsi_emailIcon_order"]) ? sanitize_text_field( $_POST["sfsi_emailIcon_order"] ): '2';
    $sfsi_facebookIcon_order        = isset($_POST["sfsi_facebookIcon_order"]) ? sanitize_text_field( $_POST["sfsi_facebookIcon_order"] ): '3';
    $sfsi_googleIcon_order          = isset($_POST["sfsi_googleIcon_order"]) ? sanitize_text_field( $_POST["sfsi_googleIcon_order"] ): '4';
    $sfsi_twitterIcon_order         = isset($_POST["sfsi_twitterIcon_order"]) ? sanitize_text_field( $_POST["sfsi_twitterIcon_order"] ): '5';
    $sfsi_youtubeIcon_order         = isset($_POST["sfsi_youtubeIcon_order"]) ? sanitize_text_field( $_POST["sfsi_youtubeIcon_order"] ): '7';
    $sfsi_pinterestIcon_order       = isset($_POST["sfsi_pinterestIcon_order"]) ? sanitize_text_field( $_POST["sfsi_pinterestIcon_order"] ): '8';
    $sfsi_instagramIcon_order       = isset($_POST["sfsi_instagramIcon_order"]) ? sanitize_text_field( $_POST["sfsi_instagramIcon_order"] ): '10';
    $sfsi_linkedinIcon_order        = isset($_POST["sfsi_linkedinIcon_order"]) ? sanitize_text_field( $_POST["sfsi_linkedinIcon_order"] ): '9';
    if(isset($_POST["sfsi_custom_MouseOverTexts"])){
        $sfsi_custom_MouseOverTexts = array();
        foreach($_POST['sfsi_custom_MouseOverTexts'] as $index=>$sfsi_custom_MouseOverText){
            $sfsi_custom_MouseOverTexts[$index] = sanitize_text_field($_POST["sfsi_custom_MouseOverTexts"][$index]);
        }
    }
    $sfsi_custom_MouseOverTexts     = isset($sfsi_custom_MouseOverTexts) ? serialize($sfsi_custom_MouseOverTexts) : '';
    
    $sfsi_custom_social_hide        = isset($_POST["sfsi_custom_social_hide"]) ? sanitize_text_field( $_POST["sfsi_custom_social_hide"] ): 'no';    
    
    /* size and spacing of icons */
    $up_option5=array(
        'sfsi_icons_size'               => intval($sfsi_icons_size),
        'sfsi_icons_spacing'            => intval($sfsi_icons_spacing),
        'sfsi_icons_Alignment'          => sanitize_text_field($sfsi_icons_Alignment),
        'sfsi_icons_perRow'             => intval($sfsi_icons_perRow),
        'sfsi_icons_ClickPageOpen'      => sanitize_text_field($sfsi_icons_ClickPageOpen),
        'sfsi_icons_suppress_errors'    => sanitize_text_field($sfsi_icons_suppress_errors),

        'sfsi_icons_stick'              => sanitize_text_field($sfsi_icons_stick),
        /* mouse over texts */
        'sfsi_rss_MouseOverText'        => sanitize_text_field($sfsi_rss_MouseOverText),
        'sfsi_email_MouseOverText'      => sanitize_text_field($sfsi_email_MouseOverText),
        'sfsi_twitter_MouseOverText'    => sanitize_text_field($sfsi_twitter_MouseOverText),
        'sfsi_facebook_MouseOverText'   => sanitize_text_field($sfsi_facebook_MouseOverText),
        'sfsi_google_MouseOverText'     => sanitize_text_field($sfsi_google_MouseOverText),
        'sfsi_linkedIn_MouseOverText'   => sanitize_text_field($sfsi_linkedIn_MouseOverText),
        'sfsi_pinterest_MouseOverText'  => sanitize_text_field($sfsi_pinterest_MouseOverText),
        'sfsi_youtube_MouseOverText'    => sanitize_text_field($sfsi_youtube_MouseOverText),
        'sfsi_instagram_MouseOverText'  => sanitize_text_field($sfsi_instagram_MouseOverText),
        'sfsi_CustomIcons_order'        => $sfsi_custom_orders,
        'sfsi_rssIcon_order'            => intval($sfsi_rssIcon_order),
        'sfsi_emailIcon_order'          => intval($sfsi_emailIcon_order),
        'sfsi_facebookIcon_order'       => intval($sfsi_facebookIcon_order),
        'sfsi_googleIcon_order'         => intval($sfsi_googleIcon_order),
        'sfsi_twitterIcon_order'        => intval($sfsi_twitterIcon_order),
        'sfsi_youtubeIcon_order'        => intval($sfsi_youtubeIcon_order),
        'sfsi_pinterestIcon_order'      => intval($sfsi_pinterestIcon_order),
        'sfsi_instagramIcon_order'      => intval($sfsi_instagramIcon_order),
        'sfsi_linkedinIcon_order'       => intval($sfsi_linkedinIcon_order),
        'sfsi_custom_MouseOverTexts'    => $sfsi_custom_MouseOverTexts,
        'sfsi_custom_social_hide'       => $sfsi_custom_social_hide
    );

    if("yes"== $sfsi_icons_suppress_errors){
        update_option('sfsi_error_reporting_notice_dismissed',false);        
    }
    
    update_option('sfsi_section5_options',  serialize($up_option5));
    header('Content-Type: application/json');
    echo json_encode(array("success")); exit;
}
/* save settings for section 6 */ 
add_action('wp_ajax_updateSrcn6','sfsi_options_updater6');        
function sfsi_options_updater6()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step6")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }


    
    $sfsi_show_Onposts              = isset($_POST["sfsi_show_Onposts"]) ? sanitize_text_field( $_POST["sfsi_show_Onposts"] ): 'no'; 
    
    $sfsi_icons_postPositon         = isset($_POST["sfsi_icons_postPositon"]) ? sanitize_text_field( $_POST["sfsi_icons_postPositon"] ): ''; 
    $sfsi_icons_alignment           = isset($_POST["sfsi_icons_alignment"]) ? sanitize_text_field( $_POST["sfsi_icons_alignment"] ): 'center-right'; 
    $sfsi_textBefor_icons           = isset($_POST["sfsi_textBefor_icons"]) ? sanitize_text_field( $_POST["sfsi_textBefor_icons"] ): ''; 
    $sfsi_icons_DisplayCounts       = isset($_POST["sfsi_icons_DisplayCounts"]) ? sanitize_text_field( $_POST["sfsi_icons_DisplayCounts"] ): 'no'; 
    $sfsi_rectsub                   = isset($_POST["sfsi_rectsub"]) ? sanitize_text_field( $_POST["sfsi_rectsub"] ): 'no';
    $sfsi_rectfb                    = isset($_POST["sfsi_rectfb"]) ? sanitize_text_field( $_POST["sfsi_rectfb"] ): 'no';
    $sfsi_rectgp                    = isset($_POST["sfsi_rectgp"]) ? sanitize_text_field( $_POST["sfsi_rectgp"] ): 'no';
    $sfsi_rectshr                   = isset($_POST["sfsi_rectshr"]) ? sanitize_text_field( $_POST["sfsi_rectshr"] ): 'no';
    $sfsi_recttwtr                  = isset($_POST["sfsi_recttwtr"]) ? sanitize_text_field( $_POST["sfsi_recttwtr"] ): 'no';
    $sfsi_rectpinit                 = isset($_POST["sfsi_rectpinit"]) ? sanitize_text_field( $_POST["sfsi_rectpinit"] ): 'no';
    $sfsi_rectfbshare               = isset($_POST["sfsi_rectfbshare"]) ? sanitize_text_field( $_POST["sfsi_rectfbshare"] ): 'no';
    /* post options */
    $up_option6=array(
        
        'sfsi_show_Onposts'     => sanitize_text_field($sfsi_show_Onposts),
        
        'sfsi_icons_postPositon'=> sanitize_text_field($sfsi_icons_postPositon),
        'sfsi_icons_alignment'  => sanitize_text_field($sfsi_icons_alignment),
        'sfsi_textBefor_icons'  => sanitize_text_field(stripslashes($sfsi_textBefor_icons)),
        'sfsi_icons_DisplayCounts'=> sanitize_text_field($sfsi_icons_DisplayCounts),
        'sfsi_rectsub'          => sanitize_text_field($sfsi_rectsub),
        'sfsi_rectfb'           => sanitize_text_field($sfsi_rectfb),
        'sfsi_rectgp'           => sanitize_text_field($sfsi_rectgp),
        'sfsi_rectshr'          => sanitize_text_field($sfsi_rectshr),
        'sfsi_recttwtr'         => sanitize_text_field($sfsi_recttwtr),
        'sfsi_rectpinit'        => sanitize_text_field($sfsi_rectpinit),
        'sfsi_rectfbshare'      => sanitize_text_field($sfsi_rectfbshare)
    );
    update_option('sfsi_section6_options',serialize($up_option6));
    header('Content-Type: application/json');
    echo  json_encode(array("success")); exit;
}
/* save settings for section 7 */ 
add_action('wp_ajax_updateSrcn7','sfsi_options_updater7');        
function sfsi_options_updater7()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step7")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_popup_text                    = isset($_POST["sfsi_popup_text"]) ? sanitize_text_field( $_POST["sfsi_popup_text"] ): ''; 
    $sfsi_popup_background_color        = isset($_POST["sfsi_popup_background_color"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_popup_background_color"] ): '#fffff'; 
    $sfsi_popup_border_color            = isset($_POST["sfsi_popup_border_color"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_popup_border_color"] ): 'center-right'; 
    $sfsi_popup_border_thickness        = isset($_POST["sfsi_popup_border_thickness"]) ? intval( $_POST["sfsi_popup_border_thickness"] ): ''; 
    $sfsi_popup_border_shadow           = isset($_POST["sfsi_popup_border_shadow"]) ? sanitize_text_field( $_POST["sfsi_popup_border_shadow"] ): 'no'; 
    $sfsi_popup_font                    = isset($_POST["sfsi_popup_font"]) ? sanitize_text_field( $_POST["sfsi_popup_font"] ): ''; 
    $sfsi_popup_fontSize                = isset($_POST["sfsi_popup_fontSize"]) ? intval( $_POST["sfsi_popup_fontSize"] ): 'no'; 
    $sfsi_popup_fontStyle               = isset($_POST["sfsi_popup_fontStyle"]) ? sanitize_text_field( $_POST["sfsi_popup_fontStyle"] ): ''; 
    $sfsi_popup_fontColor               = isset($_POST["sfsi_popup_fontColor"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_popup_fontColor"] ): 'no'; 
    $sfsi_Show_popupOn                  = isset($_POST["sfsi_Show_popupOn"]) ? sanitize_text_field( $_POST["sfsi_Show_popupOn"] ): ''; 
    if(isset($_POST["sfsi_Show_popupOn_PageIDs"])){
        $sfsi_Show_popupOn_PageIDs_arr=array();
        foreach($_POST["sfsi_Show_popupOn_PageIDs"] as $index=>$sfsi_Show_popupOn_PageID){
            $sfsi_Show_popupOn_PageIDs_arr[$index]=intval($sfsi_Show_popupOn_PageID);
        }
    }
    $sfsi_Show_popupOn_PageIDs          = isset($sfsi_Show_popupOn_PageIDs_arr) ? serialize($sfsi_Show_popupOn_PageIDs_arr) : ''; 
    $sfsi_Shown_pop                     = isset($_POST["sfsi_Shown_pop"]) ? sanitize_text_field( $_POST["sfsi_Shown_pop"] ): ''; 
    $sfsi_Shown_popupOnceTime           = isset($_POST["sfsi_Shown_popupOnceTime"]) ? sanitize_text_field( $_POST["sfsi_Shown_popupOnceTime"] ): 'no'; 
    $sfsi_Shown_popuplimitPerUserTime   = isset($_POST["sfsi_Shown_popuplimitPerUserTime"]) ? sanitize_text_field( $_POST["sfsi_Shown_popuplimitPerUserTime"] ): ''; 
        
    /* icons pop options */
    $up_option7=array(  
        'sfsi_popup_text'               => sanitize_text_field(stripslashes($sfsi_popup_text)),
        'sfsi_popup_background_color'   => sfsi_sanitize_hex_color($sfsi_popup_background_color),
        'sfsi_popup_border_color'       => sfsi_sanitize_hex_color($sfsi_popup_border_color),
        'sfsi_popup_border_thickness'   => intval($sfsi_popup_border_thickness),
        'sfsi_popup_border_shadow'      => sanitize_text_field($sfsi_popup_border_shadow),
        'sfsi_popup_font'               => sanitize_text_field($sfsi_popup_font),
        'sfsi_popup_fontSize'           => intval($sfsi_popup_fontSize),
        'sfsi_popup_fontStyle'          => sanitize_text_field($sfsi_popup_fontStyle),
        'sfsi_popup_fontColor'          => sfsi_sanitize_hex_color($sfsi_popup_fontColor),
        
        'sfsi_Show_popupOn'             => sanitize_text_field($sfsi_Show_popupOn),
        'sfsi_Show_popupOn_PageIDs'     => $sfsi_Show_popupOn_PageIDs,
        
        'sfsi_Shown_pop'                => sanitize_text_field($sfsi_Shown_pop),
        'sfsi_Shown_popupOnceTime'      => intval($sfsi_Shown_popupOnceTime),
        //'sfsi_Shown_popuplimitPerUserTime'=> $sfsi_Shown_popuplimitPerUserTime,
    );
    update_option('sfsi_section7_options',serialize($up_option7)); 
    header('Content-Type: application/json');
    echo json_encode(array("success")); exit;
}
/* save settings for section 8 */ 
add_action('wp_ajax_updateSrcn8','sfsi_options_updater8');
function sfsi_options_updater8()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step8")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_form_adjustment       = isset($_POST["sfsi_form_adjustment"]) ? sanitize_text_field( $_POST["sfsi_form_adjustment"] ): 'yes';
    $sfsi_form_height           = isset($_POST["sfsi_form_height"]) ? intval( $_POST["sfsi_form_height"] ): '180';
    $sfsi_form_width            = isset($_POST["sfsi_form_width"]) ? intval( $_POST["sfsi_form_width"] ): '230';
    $sfsi_form_border           = isset($_POST["sfsi_form_border"]) ? sanitize_text_field( $_POST["sfsi_form_border"] ): 'no';
    $sfsi_form_border_thickness = isset($_POST["sfsi_form_border_thickness"]) ? intval( $_POST["sfsi_form_border_thickness"] ): '1';
    $sfsi_form_border_color     = isset($_POST["sfsi_form_border_color"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_form_border_color"] ): '#b5b5b5';
    $sfsi_form_background       = isset($_POST["sfsi_form_background"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_form_background"] ): '#eff7f7';
    
    $sfsi_form_heading_text     = isset($_POST["sfsi_form_heading_text"]) ? sanitize_text_field( $_POST["sfsi_form_heading_text"] ): 'Get new posts by email';
    $sfsi_form_heading_font     = isset($_POST["sfsi_form_heading_font"]) ? sanitize_text_field( $_POST["sfsi_form_heading_font"] ): 'Helvetica,Arial,sans-serif';
    $sfsi_form_heading_fontstyle= isset($_POST["sfsi_form_heading_fontstyle"]) ? sanitize_text_field( $_POST["sfsi_form_heading_fontstyle"] ): 'bold';
    $sfsi_form_heading_fontcolor= isset($_POST["sfsi_form_heading_fontcolor"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_form_heading_fontcolor"] ): '#000000';
    $sfsi_form_heading_fontsize = isset($_POST["sfsi_form_heading_fontsize"]) ? intval( $_POST["sfsi_form_heading_fontsize"] ): '16';
    $sfsi_form_heading_fontalign= isset($_POST["sfsi_form_heading_fontalign"]) ? sanitize_text_field( $_POST["sfsi_form_heading_fontalign"] ):'center';
    
    $sfsi_form_field_text       = isset($_POST["sfsi_form_field_text"]) ? sanitize_text_field( $_POST["sfsi_form_field_text"] ): 'Subscribe';
    $sfsi_form_field_font       = isset($_POST["sfsi_form_field_font"]) ? sanitize_text_field( $_POST["sfsi_form_field_font"] ): 'Helvetica,Arial,sans-serif';
    $sfsi_form_field_fontstyle  = isset($_POST["sfsi_form_field_fontstyle"]) ? sanitize_text_field( $_POST["sfsi_form_field_fontstyle"] ): 'normal';
    $sfsi_form_field_fontcolor  = isset($_POST["sfsi_form_field_fontcolor"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_form_field_fontcolor"] ): '#000000';
    $sfsi_form_field_fontsize   = isset($_POST["sfsi_form_field_fontsize"]) ? intval( $_POST["sfsi_form_field_fontsize"] ): '14';
    $sfsi_form_field_fontalign  = isset($_POST["sfsi_form_field_fontalign"]) ? sanitize_text_field( $_POST["sfsi_form_field_fontalign"] ):'center';
    
    $sfsi_form_button_text      = isset($_POST["sfsi_form_button_text"]) ? sanitize_text_field( $_POST["sfsi_form_button_text"] ): 'Subscribe';
    $sfsi_form_button_font      = isset($_POST["sfsi_form_button_font"]) ? sanitize_text_field( $_POST["sfsi_form_button_font"] ): 'Helvetica,Arial,sans-serif';
    $sfsi_form_button_fontstyle = isset($_POST["sfsi_form_button_fontstyle"]) ? sanitize_text_field( $_POST["sfsi_form_button_fontstyle"] ): 'bold';
    $sfsi_form_button_fontcolor = isset($_POST["sfsi_form_button_fontcolor"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_form_button_fontcolor"] ): '#000000';
    $sfsi_form_button_fontsize  = isset($_POST["sfsi_form_button_fontsize"]) ? intval( $_POST["sfsi_form_button_fontsize"] ): '16';
    $sfsi_form_button_fontalign = isset($_POST["sfsi_form_button_fontalign"]) ? sanitize_text_field( $_POST["sfsi_form_button_fontalign"] ):'center';
    $sfsi_form_button_background= isset($_POST["sfsi_form_button_background"]) ? sfsi_sanitize_hex_color( $_POST["sfsi_form_button_background"]):'#dedede';
    
    /* icons pop options */
    $up_option8 = array(    
        'sfsi_form_adjustment'      =>  sanitize_text_field($sfsi_form_adjustment),
        'sfsi_form_height'          =>  intval($sfsi_form_height),
        'sfsi_form_width'           =>  intval($sfsi_form_width),
        'sfsi_form_border'          =>  sanitize_text_field($sfsi_form_border),
        'sfsi_form_border_thickness'=>  intval($sfsi_form_border_thickness),
        'sfsi_form_border_color'    =>  sfsi_sanitize_hex_color($sfsi_form_border_color),
        'sfsi_form_background'      =>  sfsi_sanitize_hex_color($sfsi_form_background),
        
        'sfsi_form_heading_text'    =>  sanitize_text_field(stripslashes($sfsi_form_heading_text)),
        'sfsi_form_heading_font'    =>  sanitize_text_field($sfsi_form_heading_font),
        'sfsi_form_heading_fontstyle'=> sanitize_text_field($sfsi_form_heading_fontstyle),
        'sfsi_form_heading_fontcolor'=> sfsi_sanitize_hex_color($sfsi_form_heading_fontcolor),
        'sfsi_form_heading_fontsize'=>  intval($sfsi_form_heading_fontsize),
        'sfsi_form_heading_fontalign'=> sanitize_text_field($sfsi_form_heading_fontalign),
        
        'sfsi_form_field_text'      =>  sanitize_text_field(stripslashes($sfsi_form_field_text)),
        'sfsi_form_field_font'      =>  sanitize_text_field($sfsi_form_field_font),
        'sfsi_form_field_fontstyle' =>  sanitize_text_field($sfsi_form_field_fontstyle),
        'sfsi_form_field_fontcolor' =>  sfsi_sanitize_hex_color($sfsi_form_field_fontcolor),
        'sfsi_form_field_fontsize'  =>  intval($sfsi_form_field_fontsize),
        'sfsi_form_field_fontalign' =>  sanitize_text_field($sfsi_form_field_fontalign),
        
        'sfsi_form_button_text'     =>  sanitize_text_field(stripslashes($sfsi_form_button_text)),
        'sfsi_form_button_font'     =>  sanitize_text_field($sfsi_form_button_font),
        'sfsi_form_button_fontstyle'=>  sanitize_text_field($sfsi_form_button_fontstyle),
        'sfsi_form_button_fontcolor'=>  sfsi_sanitize_hex_color($sfsi_form_button_fontcolor),
        'sfsi_form_button_fontsize' =>  intval($sfsi_form_button_fontsize),
        'sfsi_form_button_fontalign'=>  sanitize_text_field($sfsi_form_button_fontalign),
        'sfsi_form_button_background'=> sfsi_sanitize_hex_color($sfsi_form_button_background),
    );
    update_option('sfsi_section8_options',serialize($up_option8)); 
    header('Content-Type: application/json');
    echo  json_encode(array("success")); exit;
}

/* save settings for section 3 */ 
add_action('wp_ajax_updateSrcn9','sfsi_options_updater9');
function sfsi_options_updater9(){
    
    if ( !wp_verify_nonce( $_POST['nonce'], "update_step9")) {
      echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $sfsi_show_via_widget           = isset($_POST["sfsi_show_via_widget"])          ? sanitize_text_field( $_POST["sfsi_show_via_widget"] )  : 'no';

    $sfsi_icons_float               = isset($_POST["sfsi_icons_float"])              ? sanitize_text_field( $_POST["sfsi_icons_float"] )           : 'no';    
    $sfsi_icons_floatPosition       = isset($_POST["sfsi_icons_floatPosition"])      ? sanitize_text_field( $_POST["sfsi_icons_floatPosition"] )   : 'center-right';
    $sfsi_icons_floatMargin_top     = isset($_POST["sfsi_icons_floatMargin_top"])    ? intval(sanitize_text_field( $_POST["sfsi_icons_floatMargin_top"] ))  : '';
    $sfsi_icons_floatMargin_bottom  = isset($_POST["sfsi_icons_floatMargin_bottom"]) ? intval(sanitize_text_field( $_POST["sfsi_icons_floatMargin_bottom"])): '';
    $sfsi_icons_floatMargin_left    = isset($_POST["sfsi_icons_floatMargin_left"])   ? intval(sanitize_text_field( $_POST["sfsi_icons_floatMargin_left"] )) : '';
    $sfsi_icons_floatMargin_right   = isset($_POST["sfsi_icons_floatMargin_right"])  ? intval(sanitize_text_field( $_POST["sfsi_icons_floatMargin_right"] )): '';
    $sfsi_disable_floaticons        = isset($_POST["sfsi_disable_floaticons"])       ? sanitize_text_field( $_POST["sfsi_disable_floaticons"] )     : 'no';

    $sfsi_show_via_shortcode        = isset($_POST["sfsi_show_via_shortcode"])       ? sanitize_text_field( $_POST["sfsi_show_via_shortcode"] )  : 'no';

    $sfsi_show_via_afterposts       = isset($_POST["sfsi_show_via_afterposts"])      ? sanitize_text_field( $_POST["sfsi_show_via_afterposts"] )  : 'no';


    /* icons pop options */
    $up_option9 = array(

        'sfsi_show_via_widget'          =>  sanitize_text_field($sfsi_show_via_widget),        

        'sfsi_icons_float'              =>  sanitize_text_field($sfsi_icons_float),
        'sfsi_icons_floatPosition'      =>  sanitize_text_field($sfsi_icons_floatPosition),
        'sfsi_icons_floatMargin_top'    =>  intval(sanitize_text_field($sfsi_icons_floatMargin_top)),
        'sfsi_icons_floatMargin_bottom' =>  intval(sanitize_text_field($sfsi_icons_floatMargin_bottom)),
        'sfsi_icons_floatMargin_left'   =>  intval(sanitize_text_field($sfsi_icons_floatMargin_left)),
        'sfsi_icons_floatMargin_right'  =>  intval(sanitize_text_field($sfsi_icons_floatMargin_right)),
        'sfsi_disable_floaticons'       =>  sanitize_text_field($sfsi_disable_floaticons),

        'sfsi_show_via_shortcode'       =>  sanitize_text_field($sfsi_show_via_shortcode),

        'sfsi_show_via_afterposts'      =>  sanitize_text_field($sfsi_show_via_afterposts)                        

    );

    update_option('sfsi_section9_options',serialize($up_option9));
    header('Content-Type: application/json');
    echo  json_encode(array("success")); exit;       
}
/* upload custom icons images */
/* get counts for admin section */        
function sfsi_getCounts()
{
    $socialObj              = new sfsi_SocialHelper();
    $sfsi_section4_options  = unserialize(get_option('sfsi_section4_options',false));
    $sfsi_section2_options  = unserialize(get_option('sfsi_section2_options',false));
    $scounts = array(
        'rss_count'     => '',
        'email_count'   => '',
        'fb_count'      => '',
        'twitter_count' => '',
        'google_count'  => '',
        'linkedIn_count'=> '',
        'youtube_count' => '',
        'pin_count'     => '',
        'share_count'   => ''
    );

    /* get rss count */
    if(isset($sfsi_section4_options['sfsi_rss_manualCounts']) && !empty($sfsi_section4_options['sfsi_rss_manualCounts']) )
    {
        $scounts['rss_count']=$sfsi_section4_options['sfsi_rss_manualCounts'];
    } 

    /* get email count */
    if(isset($sfsi_section4_options['sfsi_email_countsFrom'])){

        if($sfsi_section4_options['sfsi_email_countsFrom']=="source" )
        {
            $feed_id = sanitize_text_field(get_option('sfsi_feed_id',false));
            $feed_data = $socialObj->SFSI_getFeedSubscriber($feed_id);
     
            $scounts['email_count']= $socialObj->format_num($feed_data);
            if(empty($scounts['email_count']))
            {
              $scounts['email_count']=(string) "0";
            }
        }
        else
        {
            $scounts['email_count']=$sfsi_section4_options['sfsi_email_manualCounts'];
        }    
    }

    if(isset($sfsi_section4_options['sfsi_email_countsFrom'])){
        
        /* get fb count */
        if($sfsi_section4_options['sfsi_facebook_countsFrom']=="likes" )
        {
            $url = home_url();
            $fb_data = $socialObj->sfsi_get_fb($url);
            $scounts['fb_count']= $socialObj->format_num($fb_data['like_count']);
        }
        else if($sfsi_section4_options['sfsi_facebook_countsFrom']=="followers" )
        {
            $url = home_url();
            $fb_data = $socialObj->sfsi_get_fb($url);
            $scounts['fb_count']= format_num($fb_data['share_count']);
            if(empty($scounts['fb_count']))
            {
                $scounts['fb_count']=(string) "0";
            }
        }
        else if($sfsi_section4_options['sfsi_facebook_countsFrom']=="mypage" )
        {
            $url = $sfsi_section4_options['sfsi_facebook_mypageCounts'];
            $fb_data = $socialObj->sfsi_get_fb_pagelike($url);
            $scounts['fb_count']= $fb_data;
        }
        else
        {
            $scounts['fb_count'] = $sfsi_section4_options['sfsi_facebook_manualCounts'];
        }        
    }   

    if(isset($sfsi_section4_options['sfsi_twitter_countsFrom'])){

        /* get twitter counts */
        if($sfsi_section4_options['sfsi_twitter_countsFrom']=="source")
        {
            $twitter_user=$sfsi_section2_options['sfsi_twitter_followUserName'];
            $tw_settings=array(
                'tw_consumer_key'=>$sfsi_section4_options['tw_consumer_key'],
                'tw_consumer_secret'=> $sfsi_section4_options['tw_consumer_secret'],
                'tw_oauth_access_token'=> $sfsi_section4_options['tw_oauth_access_token'],
                'tw_oauth_access_token_secret'=> $sfsi_section4_options['tw_oauth_access_token_secret']
            );
          
            $followers=$socialObj->sfsi_get_tweets($twitter_user,$tw_settings);
            $scounts['twitter_count']= $socialObj->format_num($followers);
        }
        else
        {
            $scounts['twitter_count']=$sfsi_section4_options['sfsi_twitter_manualCounts'];
        }        
    }

    if(isset($sfsi_section4_options['sfsi_google_countsFrom'])){

        /* get google+ counts */
        if($sfsi_section4_options['sfsi_google_countsFrom']=="follower" )
        {   
            $followers = 0;

            if(isset($sfsi_section2_options['sfsi_google_pageURL']) && !empty($sfsi_section2_options['sfsi_google_pageURL'])
               && isset($sfsi_section2_options['sfsi_google_api_key']) && !empty($sfsi_section4_options['sfsi_google_api_key'])){
                $url       = $sfsi_section2_options['sfsi_google_pageURL'];
                $api_key   = $sfsi_section4_options['sfsi_google_api_key'];
                $followers = $socialObj->sfsi_get_google($url,$api_key);
            }

            $counts = $followers;
            $scounts['google_count']= $socialObj->format_num($followers);
        }
        else if($sfsi_section4_options['sfsi_google_countsFrom']=="likes" )
        {   
            $url=home_url();
            $api_key=$sfsi_section4_options['sfsi_google_api_key'];
            $followers=$socialObj->sfsi_get_google($url,$api_key);
            if(!is_int($followers))
            {
                $followers=0;
            }    
            $counts=$followers;
            $scounts['google_count']= $socialObj->format_num($followers);
        }
        else
        {
            $scounts['google_count']=$sfsi_section4_options['sfsi_google_manualCounts'];
        }

    }
 
    /* get linkedIn counts */

    if(isset($sfsi_section4_options['sfsi_linkedIn_countsFrom'])){

       if($sfsi_section4_options['sfsi_linkedIn_countsFrom']=="follower" )
       {   
            $linkedIn_compay=$sfsi_section2_options['sfsi_linkedin_followCompany']; 
            $linkedIn_compay=$sfsi_section4_options['ln_company'];
            $ln_settings=array(
                'ln_api_key'=>$sfsi_section4_options['ln_api_key'],
                'ln_secret_key'=>$sfsi_section4_options['ln_secret_key'],
                'ln_oAuth_user_token'=>$sfsi_section4_options['ln_oAuth_user_token']
            );
            $followers=$socialObj->sfsi_getlinkedin_follower($linkedIn_compay,$ln_settings);
            $scounts['linkedIn_count']= $socialObj->format_num($followers);
       }
       else
       {
            $scounts['linkedIn_count']=$sfsi_section4_options['sfsi_linkedIn_manualCounts'];
       }        
    }   

    if(isset($sfsi_section4_options['sfsi_youtube_countsFrom'])){

        /* get youtube counts */
       if($sfsi_section4_options['sfsi_youtube_countsFrom']=="subscriber" )
       {      
           if(isset($sfsi_section4_options['sfsi_youtube_user']))
           {
            $youtube_user=$sfsi_section4_options['sfsi_youtube_user'];
            $followers=$socialObj->sfsi_get_youtube($youtube_user);
            $scounts['youtube_count']= $socialObj->format_num($followers);
       
           }
           else
           {
                $scounts['youtube_count']=01;
           }    
       } 
       else
       {
             $scounts['youtube_count']=$sfsi_section4_options['sfsi_youtube_manualCounts'];
       }
    } 
 
    if(isset($sfsi_section4_options['sfsi_pinterest_countsFrom'])){

        /* get Pinterest counts */   
       if($sfsi_section4_options['sfsi_pinterest_countsFrom']=="pins" )
       {   
           $url=home_url();
           $pins=$socialObj->sfsi_get_pinterest($url);
           $scounts['pin_count']= $socialObj->format_num($pins);
       } 
       else
       {
            $scounts['pin_count']=$sfsi_section4_options['sfsi_pinterest_manualCounts'];
       }

    }


    if(isset($sfsi_section4_options['sfsi_instagram_countsFrom'])){
       /* get instagram count */
       if($sfsi_section4_options['sfsi_instagram_countsFrom']=="followers" )
       {
            $iuser_name= $sfsi_section4_options['sfsi_instagram_User'];
            /*$counts=$socialObj->sfsi_get_instagramFollowers($iuser_name);
            if(empty($counts))
            {
                $scounts['instagram_count']=(string) "0";
            }*/
            $counts = $socialObj->sfsi_get_instagramFollowers($iuser_name);
            if(empty($counts))
            {
                $scounts['instagram_count']=(string) "0";
            }
            else
            {
                $scounts['instagram_count']=$counts;
            }
       }
       else
       {
            $scounts['instagram_count']=$sfsi_section4_options['sfsi_instagram_manualCounts'];
       }           
    }    
   return $scounts; exit;
}
/* activate and remove footer credit link */
add_action('wp_ajax_activateFooter','sfsiActivateFooter');     
function sfsiActivateFooter()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "active_footer")) {
      echo  json_encode(array('res'=>'wrong_nonce')); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    update_option('sfsi_footer_sec', 'yes');
    echo json_encode(array('res'=>'success'));exit;
}
add_action('wp_ajax_removeFooter','sfsiremoveFooter');     
function sfsiremoveFooter()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "remove_footer")) {
      echo  json_encode(array('res'=>'wrong_nonce')); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }


    
    update_option('sfsi_footer_sec', 'no');
    echo json_encode(array('res'=>'success'));exit;
}


add_action("wp_ajax_notification_read", "notification_read");
function notification_read()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "notification_read")) {
      echo  json_encode(array('res'=>'wrong_nonce')); exit;
    }
    if(current_user_can('manage_options')){
        update_option("show_notification", "no");
        echo "success";
    }else{
        echo "Error";
    }
    die;
}

add_action("wp_ajax_new_notification_read", "new_notification_read");
function new_notification_read()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "new_notification_read")) {
      echo  json_encode(array('res'=>'wrong_nonce')); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }


    update_option("show_new_notification", "no");
    echo "success";
    die;
}
add_action("wp_ajax_sfsicurlerrornotification", "sfsicurlerrornotification");
function sfsicurlerrornotification()
{
    if ( !wp_verify_nonce( $_POST['nonce'], "sfsicurlerrornotification")) {
      echo  json_encode(array('res'=>'wrong_nonce')); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    update_option("sfsi_curlErrorNotices", "no");
    echo "success";
    die;
}

function sfsi_sanitize_field($value)
{
    return strip_tags(trim($value));
}
//Sanitize color code
if(@!function_exists("sfsi_sanitize_hex_color"))
{
    function sfsi_sanitize_hex_color( $color )
    {
        if ( '' === $color )
            return '';
     
        // 3 or 6 hex digits, or the empty string with or without !important
        if ( preg_match('(^#([A-Fa-f0-9]{3}){1,2}$|^#([A-Fa-f0-9]{3}){1,2}\s*!important$|^$)', $color ) )
            return $color;
    }
}
function sfsi_returningElement($element) {return $element[0];}


add_action('wp_ajax_bannerOption','sfsi_bannerOption');

function sfsi_bannerOption(){

    error_reporting(0);
    if ( !wp_verify_nonce( $_POST['nonce'], "bannerOption")) {
        echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    if(get_option("show_new_notification") == "yes"){

      $objThemeCheck = new sfsi_ThemeCheck();
        
      $domainname     = isset($_POST['domain'])?sanitize_text_field($_POST['domain']):$objThemeCheck->sfsi_plus_getdomain(get_bloginfo('url'));

      // Get all themes data which incudes nobrainer 
      $themeDataArr = $objThemeCheck->sfsi_plus_get_themeData();

      $matchFound = false;

      foreach ($themeDataArr as $themeDataObj) {

            if(isset($themeDataObj->themeName) && strlen($themeDataObj->themeName)>0){
    
                $themeName          = $themeDataObj->themeName;
                $noBrainerKeywords  = $themeDataObj->noBrainerKeywords;
                $separateKeywords   = $themeDataObj->separateKeywords;
                $negativeKeywords   = $themeDataObj->negativeKeywords;
                $noBrainerAndSeparateKeywords = array_merge($noBrainerKeywords,$separateKeywords);


                if($objThemeCheck->sfsi_plus_check_type_of_websiteWithNoBrainerAndSeparateAndNegativeKeywords($themeName,$noBrainerKeywords,$separateKeywords,$noBrainerAndSeparateKeywords,$negativeKeywords,$domainname)==$themeName)
            {
                $matchFound = true;         

                $themeName = strtolower($themeName);

                $objThemeCheck->sfsi_plus_bannereHtml(
                        $themeDataObj->headline, 
                        $themeDataObj->themeLink, 
                        SFSI_PLUGURL.'images/website_theme/'.$themeName.'.png', 
                        $themeDataObj->bottomtext
                    );

                break;
            }
        }
      }
    
      if(!$matchFound){
        foreach ($themeDataArr as $themeDataObj) {

            if(isset($themeDataObj->themeName) && strlen($themeDataObj->themeName)>0){
    
                $themeName          = $themeDataObj->themeName;
                $noBrainerKeywords  = $themeDataObj->noBrainerKeywords;
                $separateKeywords   = $themeDataObj->separateKeywords;
                $negativeKeywords   = $themeDataObj->negativeKeywords;
                $noBrainerAndSeparateKeywords = array_merge($noBrainerKeywords,$separateKeywords);


                if($objThemeCheck->sfsi_plus_check_type_of_metaTitleWithNoBrainerAndSeparateAndNegativeKeywords($themeName,$noBrainerKeywords,$separateKeywords,$noBrainerAndSeparateKeywords,$negativeKeywords,$domainname)==$themeName)
            {
                $matchFound = true;         

                $themeName = strtolower($themeName);

                $objThemeCheck->sfsi_plus_bannereHtml(
                        $themeDataObj->headline, 
                        $themeDataObj->themeLink, 
                        SFSI_PLUGURL.'images/website_theme/'.$themeName.'.png', 
                        $themeDataObj->bottomtext
                    );

                break;
            }
        }
      }
    }
    if(!$matchFound){
        foreach ($themeDataArr as $themeDataObj) {

            if(isset($themeDataObj->themeName) && strlen($themeDataObj->themeName)>0){
    
                $themeName          = $themeDataObj->themeName;
                $noBrainerKeywords  = $themeDataObj->noBrainerKeywords;
                $separateKeywords   = $themeDataObj->separateKeywords;
                $negativeKeywords   = $themeDataObj->negativeKeywords;
                $noBrainerAndSeparateKeywords = array_merge($noBrainerKeywords,$separateKeywords);


                if($objThemeCheck->sfsi_plus_check_type_of_metaKeywordsWithNoBrainerAndSeparateAndNegativeKeywords($themeName,$noBrainerKeywords,$separateKeywords,$noBrainerAndSeparateKeywords,$negativeKeywords,$domainname)==$themeName)
            {
                $matchFound = true;         

                $themeName = strtolower($themeName);

                $objThemeCheck->sfsi_plus_bannereHtml(
                        $themeDataObj->headline, 
                        $themeDataObj->themeLink, 
                        SFSI_PLUGURL.'images/website_theme/'.$themeName.'.png', 
                        $themeDataObj->bottomtext
                    );

                break;
            }
        }
      }
    }
    if(!$matchFound){
        foreach ($themeDataArr as $themeDataObj) {

            if(isset($themeDataObj->themeName) && strlen($themeDataObj->themeName)>0) {
    
                $themeName          = $themeDataObj->themeName;
                $noBrainerKeywords  = $themeDataObj->noBrainerKeywords;
                $separateKeywords   = $themeDataObj->separateKeywords;
                $negativeKeywords   = $themeDataObj->negativeKeywords;
                $noBrainerAndSeparateKeywords = array_merge($noBrainerKeywords,$separateKeywords);


                if($objThemeCheck->sfsi_plus_check_type_of_metaDescriptionWithNoBrainerAndSeparateAndNegativeKeywords($themeName,$noBrainerKeywords,$separateKeywords,$noBrainerAndSeparateKeywords,$negativeKeywords,$domainname)==$themeName)
            {
                $matchFound = true;         

                $themeName = strtolower($themeName);

                $objThemeCheck->sfsi_plus_bannereHtml(
                        $themeDataObj->headline, 
                        $themeDataObj->themeLink, 
                        SFSI_PLUGURL.'images/website_theme/'.$themeName.'.png', 
                        $themeDataObj->bottomtext
                    );

                break;
            }
        }
      }
    }

      // if(!$matchFound){
            
      //       echo '<div class="sfsi_new_notification_cat">
      //               <div class="sfsi_new_notification_header_cat">
      //                   <h1>New feature: Tailored icons</h1>
      //                   <h3>The <a href="https://www.ultimatelysocial.com/themed-icons-search/?utm_source=usmi_settings_page&utm_campaign=themed_icons_search&utm_medium=banner" target="_blank">Premium Plugin</a> Includes these icons...</h3>
      //                   <div class="sfsi_new_notification_cross_cat">X</div>
      //               </div>
                    
      //               <div class="sfsi_new_notification_body_link_cat">
      //                   <a class ="tailored_icons_img" href="https://www.ultimatelysocial.com/themed-icons-search/?utm_source=usmi_settings_page&utm_campaign=themed_icons_search&utm_medium=banner" target="_blank">
      //                       <div class="sfsi_new_notification_body_cat">
      //                           <div class="sfsi_new_notification_image_cat">
      //                                  <img src="'.SFSI_PLUGURL.'images/WPPlugin_V3.png" id="newImg" />
      //                           </div>
      //                       </div>
      //                   </a>
      //                   <div class="bottom_text">
      //                       <a target="_blank" href="https://www.ultimatelysocial.com/themed-icons-search/?utm_source=usmi_settings_page&utm_campaign=themed_icons_search&utm_medium=banner" >
      //                           See more-themed-icons >
      //                       </a>
      //                   </div>    
      //               </div>
      //           </div>';   
      // }
        
        echo '<script type="text/javascript">
                jQuery("body").on("click", ".sfsi_new_notification_cross", function(){
                    SFSI.ajax({
                        url:sfsi_icon_ajax_object.ajax_url,
                        type:"post",
                        data: {action: "new_notification_read", nonce:"'.(wp_create_nonce('new_notification_read')).'"},
                        success:function(msg){
                            if(jQuery.trim(msg) == "success")
                            {
                                jQuery(".sfsi_new_notification").hide("fast");
                            }
                        }
                    });
                });
                jQuery("body").on("click", ".sfsi_new_notification_cross_cat", function(){
                    SFSI.ajax({
                        url:sfsi_icon_ajax_object.ajax_url,
                        type:"post",
                        data: {action: "new_notification_read", nonce:"'.(wp_create_nonce('new_notification_read')).'"},
                        success:function(msg){
                            if(jQuery.trim(msg) == "success")
                            {
                                jQuery(".sfsi_new_notification_cat").hide("fast");
                            }
                        }
                    });
                });
        </script>';
    }
    die();
}
add_action('wp_ajax_sfsiOfflineChatMessage','sfsi_OfflineChatMessage');

function sfsi_OfflineChatMessage(){
    error_reporting(0);
    // extract($_POST);
    if ( !wp_verify_nonce( $_POST['nonce'], "OfflineChatMessage")) {
        echo  json_encode(array("wrong_nonce")); exit;
    }
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



    $email = isset($_POST) && isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $message = isset($_POST) && isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $body="<table><tr><th>Site:</th><td>".home_url()."</td></tr><tr><th>Plugin:</th><td>Old Plugin</td></tr><tr><th>Email:</th><td>".$email."</td></tr><tr><th>Message:</th><td>".$message."</td></tr></table>";
    $sent=wp_mail('help@ultimatelysocial.com',"New question from user",$body,array('Content-Type: text/html; charset=UTF-8'));
    if(isset($sent)&&(true===$sent)){
        echo "success";
    }else{
        echo "failure";
    }
    die();
}
?>
