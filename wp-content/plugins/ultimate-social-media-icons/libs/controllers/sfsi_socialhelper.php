<?php 

/* social helper class include all function which are used to intract with  */
class sfsi_SocialHelper
{
	private $url,$timeout=10;
	
	/* get twitter followers */
	function sfsi_get_tweets($username,$tw_settings)
	{
		require_once(SFSI_DOCROOT.'/helpers/twitteroauth/twiiterCount.php');
		return sfsi_twitter_followers();
	}
	
	/* get linkedIn counts */
	function sfsi_get_linkedin($url)
	{
	   $json_string = $this->file_get_contents_curl("http://www.linkedin.com/countserv/count/share?url=$url&format=json");
	   $json = json_decode($json_string, true);
	   return isset($json['count'])? intval($json['count']):0;
	}
	
	/* get linkedIn follower */
	function sfsi_getlinkedin_follower($ln_company,$APIsettings)
	{      
	   require_once(SFSI_DOCROOT.'/helpers/linkedin-api/linkedin-api.php');

	   // $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
	   // $url=$scheme.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	   $url= sfsi_get_current_page_url();

	   $linkedin = new LinkedIn(
			$APIsettings['ln_api_key'],
			$APIsettings['ln_secret_key'],
			$APIsettings['ln_oAuth_user_token'],
			$url
	   );
	   $followers = $linkedin->getCompanyFollowersByName($ln_company); 
	   
	   if (strpos($followers, '404') === false)
	   {   return  strip_tags($followers); }
	   else
	   {   return  0; }
	}
	
	/* get facebook likes */
	function sfsi_get_fb($url)
	{
		$count 		 = 0; 
		$json_string = $this->file_get_contents_curl('https://graph.facebook.com/?id='.$url);
		$json 		 = json_decode($json_string);

		if(isset($json) && isset($json->share) && isset($json->share->share_count)){
			$count  = $json->share->share_count;
		}
		return $count;
	}
	
	/* get facebook page likes */
	function sfsi_get_fb_pagelike($url)
	{
		$appid = '959456867427268';
		$appsecret = '7cc27f382c47fd5cc3a7203e40d70bf1';
		$json_url ='https://graph.facebook.com/'.$url.'?fields=fan_count&access_token='.$appid.'|'.$appsecret;
		$json_string = $this->file_get_contents_curl($json_url);
		$json = json_decode($json_string, true);
		return isset($json['fan_count'])? $json['fan_count']:0;
	}
	
	/* get google+ follwers  */
	function sfsi_get_google($url,$google_api_key)
	{   
		if(
			filter_var($url, FILTER_VALIDATE_URL) &&
			!empty($google_api_key)
		)
		{
			$url = parse_url($url);
			$url_path = explode('/',$url['path']);
			if(isset($url_path))
			{
				end($url_path);
				$key = key($url_path);
				$page_id = $url_path[$key];
			}
			
			if($this->sfsi_get_http_response_code("https://www.googleapis.com/plus/v1/people/$page_id?key=$google_api_key")!="404")
			{        
				$data = $this->file_get_contents_curl("https://www.googleapis.com/plus/v1/people/$page_id?key=$google_api_key");     
				$data = json_decode($data, true);
				return $this->format_num($data['circledByCount']); 
			}
			else
			{
				return 0;
			}    
		}
		else
		{
			return 0;
		}
	}
	
	/* get google+ likes */
	function sfsi_getPlus1($url){

	  if(_is_curl_installed()){
		  $curl = curl_init();
		  curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		  curl_setopt($curl, CURLOPT_POST, 1);
		  curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		  $curl_results = curl_exec ($curl);
		  curl_close ($curl);
		  $json = json_decode($curl_results, true);
		  
		  return intval( $json[0]['result']['metadata']['globalCounts']['count'] );	  	
	  }
	  else{
	  		return 0;
	  }

	}
	
	/* get youtube subscribers  */
	function sfsi_get_youtube($user)
	{
		if($user == 'SpecificFeeds')
		{
			$sfsi_section4_options =  unserialize(get_option('sfsi_section4_options',false));
			$user = (
				isset($sfsi_section4_options['sfsi_youtube_channelId']) &&
				!empty($sfsi_section4_options['sfsi_youtube_channelId'])
			) ? $sfsi_section4_options['sfsi_youtube_channelId'] : 'UCYQyWnJPrY4XY3Avc7BU9aA';
			
			$xmlData = $this->file_get_contents_curl('https://www.googleapis.com/youtube/v3/channels?part=statistics&id='.$user.'&key=AIzaSyA_SqAZGCpZ22vHzOUr3St5xf5XMy78oTY');
		}
		else
		{
			$xmlData = $this->file_get_contents_curl('https://www.googleapis.com/youtube/v3/channels?part=statistics&forUsername='.$user.'&key=AIzaSyA_SqAZGCpZ22vHzOUr3St5xf5XMy78oTY');
		}
		
		if($xmlData)
		{   
			$xmlData = json_decode($xmlData);
			if(
				isset($xmlData->items) &&
				!empty($xmlData->items)
			)
			{
				$subs = $xmlData->items[0]->statistics->subscriberCount;
				$subs = $this->format_num($subs);
			}
			else
			{
				$subs=0;
			}
		}
		else
		{
			$subs=0;
		}    
		return $subs;
	}
	
	/* get pinit counts  */       
	function sfsi_get_pinterest($url)
	{
		//'https://api.pinterest.com/v3/pidgets/users/[username]/pins/'
		$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url='.$url);
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}
	
	/* get pinit counts for a user  */
	function get_UsersPins($user_name,$board)
	{   
		$query=$user_name.'/'.$board;
		$url_respon=$this->sfsi_get_http_response_code('http://api.pinterest.com/v3/pidgets/boards/'.$query.'/pins/');
		if($url_respon!=404)
		{    
			$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v3/pidgets/boards/'.$query.'/pins/');
			$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
			$json = json_decode($json_string, true);
		}
		else
		{
			$json['data']['user']['pin_count']=0;
		}    
		return isset($json['data']['user']['pin_count'])? intval($json['data']['user']['pin_count']):0;
	}

	/* send curl request   */
	private function file_get_contents_curl($url)
	{
		$user_Agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] :'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
		
		if(_is_curl_installed()){
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_Agent);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$cont = curl_exec($ch);
			if(curl_error($ch))
			{
				//die(curl_error($ch));
			}
			return $cont;			
		}
		else{
			return false;
		}

	}

	private function get_content_curl($url)
	{
		if(_is_curl_installed()){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_HTTPGET, 1);
			curl_setopt($curl, CURLOPT_URL, $url );
			curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
			curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 2 );
			$cont = curl_exec($curl);
		
			if(curl_error($curl))
			{
				//die(curl_error($ch));
			}
			return $cont;
		}
		else{
			return false;
		}
	}

	/* convert no. to 2K,3M format   */
	function format_num($num, $precision = 0)
	{
		if ($num >= 1000 && $num < 1000000) {
			$n_format = number_format($num/1000,$precision).'k';
		} else if ($num >= 1000000 && $num < 1000000000) {
			$n_format = number_format($num/1000000,$precision).'m';
		} else if ($num >= 1000000000) {
			$n_format=number_format($num/1000000000,$precision).'b';
		} else {
			$n_format = $num;
		}
		return $n_format;
	}
  
  	/* create on page facebook links option */
	public function sfsi_FBlike($permalink)
	{
		$send = 'false';
		$width = 180;
		$show_count=0;
		/*$fb_like_html = '<fb:like href="'.$permalink.'" width="'.$width.'" send="'.$send.'" showfaces="false" ';
		if($show_count) { 
				$fb_like_html .= 'layout="button"';
		} else {
				$fb_like_html .= 'layout="button"';
		}
		$fb_like_html .= ' action="like"></fb:like>';*/
		$fb_like_html = '';
		$fb_like_html .= '<div class="fb-like" data-href="'.$permalink.'"';
		$fb_like_html .= ($show_count==1) ?  ' data-layout="button_count"' : ' data-layout="button"';
		$fb_like_html .= ' data-action="like" data-show-faces="false" data-share="true"></div>';
		return $fb_like_html;exit;
	}
	
	/*subscribe like*/
	function sfsi_Subscribelike($permalink, $show_count)
	{
	
	}
	
	/*twitter like*/
	function sfsi_twitterlike($permalink, $show_count)
	{
		$twitter_text = '';
		return sfsi_twitterShare($permalink,$twitter_text);
	}
	
	/* create on page facebook share option */
	public function sfsiFB_Share($permalink)
	{
		/*$fb_share_html = '<fb:share-button href="'.$permalink.'" width="140" ';
		$fb_share_html .= 'type="button"';
		$fb_share_html .= '></fb:share-button>';*/
		$fb_share_html = '';
		$fb_share_html .= '<div class="fb-share-button" data-href="'.$permalink.'" data-layout="button"></div>';
		return $fb_share_html;
	}
	
	/* create on page google share option */      
	public function sfsi_Googlelike($permalink)
	{
	  	$show_count=0;  
	  	$google_html = '<div class="g-plusone" data-href="' . $permalink . '" ';
		if($show_count) {
				$google_html .= 'data-size="bubble" ';
		} else {
				$google_html .= 'data-size="large" data-annotation="none" ';
		}
		$google_html .= '></div>';
		return $google_html;
	}
	
	/* create on page google share option */      
	public function sfsi_GoogleShare($permalink)
	{
		$show_count=1;
		$google_html = '<div class="g-plus" data-action="share" data-annotation="none" data-height="24" data-href="'.$permalink.'">' . $permalink . '"></div>';
		return $google_html;
	}
	
	/* create on page twitter follow option */ 
	public function sfsi_twitterFollow($tw_username)
	{
		$twitter_html = '<a href="https://twitter.com/'.trim($tw_username).'" class="twitter-follow-button"  data-show-count="false" data-show-screen-name="false">Follow </a>';
		return $twitter_html;
	} 
	
	/* create on page twitter share icon */
	public function sfsi_twitterShare($permalink,$tweettext)
	{
		$twitter_html = '<a rel="nofollow" href="http://twitter.com/share" data-count="none" class="sr-twitter-button twitter-share-button" lang="en" data-url="'.$permalink.'" data-text="'.stripslashes($tweettext).'" ></a>';
		return $twitter_html;
	} 
	
	/* create on page twitter share icon with count */
	public function sfsi_twitterSharewithcount($permalink,$tweettext, $show_count)
	{
		if($show_count)
		{
			$twitter_html = '<a href="http://twitter.com/share" class="sr-twitter-button twitter-share-button" lang="en" data-counturl="'.$permalink.'" data-url="'.$permalink.'" data-text="'.$tweettext.'" ></a>';
		}
		else
		{
			$twitter_html = '<a href="http://twitter.com/share" data-count="none" class="sr-twitter-button twitter-share-button" lang="en" data-url="'.$permalink.'" data-text="'.$tweettext.'" ></a>';
		}
	   return $twitter_html;
	}
 
	/* create on page youtube subscribe icon */       
	public function sfsi_YouTubeSub($yuser)
	{
		$option4=  unserialize(get_option('sfsi_section4_options',false));
		$option2=  unserialize(get_option('sfsi_section2_options',false));

		if($option2['sfsi_youtubeusernameorid'] == 'name')
		{
			$yuser = $option2['sfsi_ytube_user'];
			$youtube_html = '<div class="g-ytsubscribe" data-channel="'.$yuser.'" data-layout="default" data-count="hidden"></div>';
		}
		else
		{
			$yuser = $option2['sfsi_ytube_chnlid'];
			$youtube_html = '<div class="g-ytsubscribe" data-channelid="'.$yuser.'" data-layout="default" data-count="hidden"></div>';
		}
		return $youtube_html;
	}  
	
	/* create on page pinit button icon */      
	public function sfsi_PinIt($url='')
	{       
		$pin_it_html = '<a data-pin-do="buttonPin" data-pin-save="true" href="https://www.pinterest.com/pin/create/button/?url=&media=&description="></a>';
		return $pin_it_html;
	}
	
	/* get instragram followers */
	public function sfsi_get_instagramFollowers($user_name)
	{
		$sfsi_instagram_sf_count = unserialize(get_option('sfsi_instagram_sf_count',false));
		
		/*if date is empty (for decrease request count)*/
		if(empty($sfsi_instagram_sf_count["date_instagram"]))
		{
			$sfsi_instagram_sf_count["date_instagram"] = strtotime(date("Y-m-d"));
			$counts = $this->sfsi_get_instagramFollowersCount($user_name);
			$sfsi_instagram_sf_count["sfsi_instagram_count"] = $counts;
			update_option('sfsi_instagram_sf_count',  serialize($sfsi_instagram_sf_count));
		}
		else
		{   
			$phpVersion = phpVersion();
			if($phpVersion >= '5.3')
			{
				$diff = date_diff(
				 	date_create(
						date("Y-m-d", $sfsi_instagram_sf_count["date_instagram"])
					),
					date_create(
						date("Y-m-d")
				));
			}	
			if((isset($diff) && $diff->format("%a") < 1) || 1 == 1)	
			{
				$sfsi_instagram_sf_count["date_instagram"] = strtotime(date("Y-m-d"));
				$counts = $this->sfsi_get_instagramFollowersCount($user_name);
				$sfsi_instagram_sf_count["sfsi_instagram_count"] = $counts;
				update_option('sfsi_instagram_sf_count',  serialize($sfsi_instagram_sf_count));
			}
			else
			{
				$counts = $sfsi_instagram_sf_count["sfsi_instagram_count"];
			}
		}
		return $counts;
	}
	
	/* get instragram followers Count*/
	public function sfsi_get_instagramFollowersCount($user_name)
	{
		/* get instagram user id */
		$option4 	= unserialize(get_option('sfsi_section4_options',false));
		$token 		= $option4['sfsi_instagram_token'];

		$count 		= 0;

		if(isset($token) && !empty($token)){

			$return_data = $this->get_content_curl('https://api.instagram.com/v1/users/self/?access_token='.$token);
			$objData 	 = json_decode($return_data);

			if(isset($objData) && $objData->data && $objData->data->counts && $objData->data->counts->followed_by){
				$count 	 = $objData->data->counts->followed_by;
			}			
		}
		return $this->format_num($count,0);
	}
	
	/* create linkedIn  follow button */
	public function sfsi_LinkedInFollow($company_id)
	{
		return  $ifollow='<script type="IN/FollowCompany" data-id="'.$company_id.'" data-counter="none"></script>';
	}
	
	/* create linkedIn  recommend button */
	public function sfsi_LinkedInRecommend($company_name,$product_id)
	{
		return  $ifollow='<script type="IN/RecommendProduct" data-company="'.$company_name.'" data-product="'.$product_id.'"></script>';
	}
	
	/* create linkedIn  share button */
	public function sfsi_LinkedInShare($url='')
	{
	  $url=(isset($url))? $url :  home_url();
	  return  $ifollow='<script type="IN/Share" data-url="'.$url.'"></script>';
	}
	
	/* get no of subscribers from specificfeeds for current blog */
	public function SFSI_getFeedSubscriber($feedid)
	{
		$sfsi_instagram_sf_count_option = get_option('sfsi_instagram_sf_count',false);
		if(is_array($sfsi_instagram_sf_count_option)){
			$sfsi_instagram_sf_count = $sfsi_instagram_sf_count_option;
		}else{
			$sfsi_instagram_sf_count = unserialize($sfsi_instagram_sf_count_option);
		}
		
		/*if date is empty (for decrease request count)*/
		if(empty($sfsi_instagram_sf_count["date_sf"]))
		{
			$sfsi_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
			$counts = $this->SFSI_getFeedSubscriberCount($feedid);
			$sfsi_instagram_sf_count["sfsi_sf_count"] = $counts;
			update_option('sfsi_instagram_sf_count',  serialize($sfsi_instagram_sf_count));
		}
		else
		{   
			$phpVersion = phpVersion();
			if($phpVersion >= '5.3')
			{
				$diff = date_diff(
				 	date_create(
						date("Y-m-d", $sfsi_instagram_sf_count["date_sf"])
					),
					date_create(
						date("Y-m-d")
				));
			}
			if((isset($diff) && $diff->format("%a") >= 1)||$sfsi_instagram_sf_count["sfsi_sf_count"]=="")
			{
				$sfsi_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
				$counts = $this->SFSI_getFeedSubscriberCount($feedid);
				$sfsi_instagram_sf_count["sfsi_sf_count"] = $counts;
				update_option('sfsi_instagram_sf_count',  serialize($sfsi_instagram_sf_count));
			}
			else
			{
				$counts = $sfsi_instagram_sf_count["sfsi_sf_count"];
			}
		}
		
		if(empty($counts) || $counts == "O")
		{
			$counts = 0;
		}
		
		return $counts;
	}
	
	/* get no of subscribers from specificfeeds for current blog count */
	public function  SFSI_getFeedSubscriberCount($feedid)
	{
		
		if(_is_curl_installed()){

			$curl = curl_init();  
			
			curl_setopt_array($curl, array(

				CURLOPT_RETURNTRANSFER 	=> 1,
				CURLOPT_URL 			=> 'http://www.specificfeeds.com/wordpress/wpCountSubscriber',
				CURLOPT_USERAGENT 		=> 'sf rss request',
				CURLOPT_POST 			=> 1,
				CURLOPT_TIMEOUT 	    => 30,
				CURLOPT_POSTFIELDS 		=> array('feed_id' => $feedid, 'v' => "newplugincount")
			));
			
			/* Send the request & save response to $resp */
			$resp = curl_exec($curl);

			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if($httpcode == 200){
				
				if(!empty($resp))
				{
					$resp     = json_decode($resp);
					
					curl_close($curl);

					$feeddata = stripslashes_deep($resp->subscriber_count);
				}
				else{
					$sfsi_premium_instagram_sf_count = unserialize(get_option('sfsi_sf_count',false));
					$feeddata = $sfsi_premium_instagram_sf_count["sfsi_sf_count"];
				}
			}
			else{
				$sfsi_premium_instagram_sf_count = unserialize(get_option('sfsi_sf_count',false));
				$feeddata = $sfsi_premium_instagram_sf_count["sfsi_sf_count"];
			}
			return $this->format_num($feeddata);			
		}
		else{
			return 0;
		}

		exit;
	}
	
	/* check response from a url */
	private function sfsi_get_http_response_code($url)
	{
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}

	
}
/* end of class */
?>