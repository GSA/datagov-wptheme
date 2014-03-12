<?php
/*
 * SETUP THE CRON
*/
function dg_tw_load_next_items() {
	global $dg_tw_queryes, $dg_tw_time, $dg_tw_publish, $dg_tw_tags, $dg_tw_cats, $dg_tw_ft, $wpdb,$connection;
	
	if (!function_exists('curl_init')){
		error_log('The DG Twitter to blog plugin require CURL libraries');
		return;
	}
	
	$dg_tw_exclusions = get_option('dg_tw_exclusions');
	
	if(empty($dg_tw_exclusions)) {
		$dg_tw_exclusions = array();
	}
	
	$mega_tweet = array();

	foreach($dg_tw_queryes as $slug=>$query) {
		$parameters = array(
				'q' => $query['value'],
				'since_id' => $query['last_id'],
				'include_entities' => true,
				'count' => $dg_tw_ft['ipp']
		);
		
		$count = 0;
		
		error_log('Loop query string \n');
		$dg_tw_data = $connection->get('search/tweets', $parameters);

		//Set the last tweet id
		if(count($dg_tw_data->statuses)) {
			$status = end($dg_tw_data->statuses);
			$dg_tw_queryes[urlencode($query['value'])]['last_id'] = $status->id_str;
		}
		
		foreach($dg_tw_data->statuses as $key=>$item) {
			$count++;
			
			if($dg_tw_ft['exclude_retweets'] && isset($item->retweeted_status))
				continue;
			
			if($dg_tw_ft['exclude_no_images'] && !count($item->entities->media))
				continue;
			
			if(!isset($dg_tw_ft['method']) || $dg_tw_ft['method'] == 'multiple') {
				if(dg_tw_iswhite($item)) {
					$result = dg_tw_publish_tweet($item,$query);
				} //iswhite
			} elseif(!in_array($item->id_str,$dg_tw_exclusions)) {
				$mega_tweet[] = array(
						'text'=>$item->text,
						'author'=> isset($item->user->display_name) ? $item->user->display_name : $item->user->name,
						'id'=>$item->id_str,
						'created_at'=>$item->created_at
				);
				
				$dg_tw_exclusions[$item->id_str] = $item->id_str;
			}
			
			if($count == $dg_tw_ft['ipp'])
				break;
		}
	}
	
	update_option('dg_tw_queryes',$dg_tw_queryes);
	
	if(!empty($mega_tweet)) {
		dg_tw_publish_mega_tweet($mega_tweet);
		
		update_option('dg_tw_exclusions',$dg_tw_exclusions);
	}
}

/*
 * Add cron times
 */
function dg_tw_schedule($schedules) {
	$schedules['dg_tw_oneminute'] = array(
			'interval'=> 60,
			'display'=> __('Once Every Minute')
	);

	$schedules['dg_tw_fiveminutes'] = array(
			'interval'=> 300,
			'display'=> __('Once Every 5 Minutes')
	);

	$schedules['dg_tw_tenminutes'] = array(
			'interval'=> 600,
			'display'=> __('Once Every 10 Minutes')
	);

	$schedules['dg_tw_twentynminutes'] = array(
			'interval'=> 1200,
			'display'=> __('Once Every 20 Minutes')
	);

	$schedules['dg_tw_twicehourly'] = array(
			'interval'=> 1800,
			'display'=> __('Once Every 30 Minutes')
	);

	$schedules['dg_tw_weekly'] = array(
			'interval'=> 604800,
			'display'=> __('Once Every 7 Days')
	);

	$schedules['dg_tw_bi_weekly'] = array(
			'interval'=> 1209600,
			'display'=> __('Once Every 14 Days')
	);

	$schedules['dg_tw_monthly'] = array(
			'interval'=> 2592000,
			'display'=> __('Once Every 30 Days')
	);

	return $schedules;
}

/*
 * Create admin menu element
 */
function dg_add_menu_item() {
	$privilege = get_option('dg_tw_ft');
	
	add_menu_page( 'Twitter To WP', 'Twitter To WP', $privilege['privileges'], 'dg_tw_admin_menu', 'dg_tw_drawpage', '', NULL);
	add_submenu_page( 'dg_tw_admin_menu', 'Manual Posting', 'Manual Posting', $privilege['privileges'], 'dg_tw_retrieve_menu', 'dg_tw_drawpage_retrieve' );
	
	wp_enqueue_script( "twitter-posts-to-blog-js",plugins_url('js/twitter-posts-to-blog.js', __FILE__),array('jquery','jquery-ui-core','jquery-ui-tabs'));
	wp_enqueue_style( "twitter-posts-to-blog-css", plugins_url('css/twitter-posts-to-blog.css', __FILE__), array('colors-fresh'), '1.7.0');
	wp_enqueue_style( "twitter-posts-to-blog-css-ui", plugins_url('css/twitter-posts-to-blog-ui.css', __FILE__), array('twitter-posts-to-blog-css'), '1.7.0');
}

/*
 * Call admin page for this plugin
 */
function dg_tw_drawpage() {
	global $dg_tw_queryes,$dg_tw_time, $dg_tw_publish, $dg_tw_ft, $dg_tw_tags, $dg_tw_cats,$tokens_error,$wp_post_types;
	
	require_once('admin_page.php');
}

/*
 * Call admin page for this plugin
 */
function dg_tw_drawpage_retrieve() {
	global $dg_tw_queryes, $dg_tw_publish, $dg_tw_ft, $dg_tw_tags, $dg_tw_cats,$connection,$tokens_error;
	
	require_once('retrieve_page.php');
}

/*
 * Print admin page message for feedback
 */
function dg_tw_feedback() {
	$dg_tw_ft = get_option('dg_tw_ft');
	
	if(isset($dg_tw_ft['feedback']) && $dg_tw_ft['feedback'] == true)
		return true;
	
	?>
		<div class="updated">
			<p>
				Thanks for using this plugin, please leave feedback in the <a href="http://wordpress.org/plugins/twitter-posts-to-blog/">plugin page</a> 
				and if you want you can <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QV5Y8ZNVWGEA8">offer me a beer</a>
				<br/>
				<a href="?page=dg_tw_admin_menu&feedback=true">Close message!</a>
			</p>
		</div>
	<?php
}

/*
 * Simple function to get file content via curl or via file get contents
 */
function dg_tw_file_get_contents($url) {
	global $dg_tw_ft;

	if(isset($dg_tw_ft['request_method']) && $dg_tw_ft['request_method'] == 'curl')
		return dg_tw_curl_file_get_contents($url);
	else
		return file_get_contents($url);
}

/*
 * Simple function to get curl content (json)
 */
function dg_tw_curl_file_get_contents($url) {
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 12);

	$contents = curl_exec($curl);
	curl_close($curl);
	return $contents;
}

/*
 * 
 */
function dg_tw_slug($str) {
	$string = sanitize_title($str);
	$string = preg_replace('/-+/', "-", $string);
	return $string;
}

/*
 * Check if there is blacklisted words in the text of the tweet
 */
function dg_tw_iswhite($tweet) {
	global $dg_tw_queryes, $dg_tw_publish, $dg_tw_tags, $dg_tw_cats, $dg_tw_ft, $wpdb;
	
	if(empty($dg_tw_ft['badwords']) && empty($dg_tw_ft['baduser']))
		return true;
	
	if(!empty($dg_tw_ft['badwords'])) {
		$exploded = explode(',',$dg_tw_ft['badwords']);
		
		foreach($exploded as $word) {
			$this_word = trim($word);
			
			if(empty($this_word))
				continue;
			
			if(!isset($tweet->text) || stristr ($tweet->text , $this_word ))
				return false;
		}
	}
	
	if(!empty($dg_tw_ft['baduser'])) {
		$exploded = explode(',',$dg_tw_ft['baduser']);
	
		foreach($exploded as $word) {
			$this_word = trim($word);
			$username = dg_tw_tweet_user($tweet);
			
			if(empty($this_word))
				continue;
				
			if(stristr ($username , $this_word ))
				return false;
		}
	}
	
	return true;
}

/*
 * Starting up author filter dg_tw_the_author
 */
function dg_tw_loop_start() {
	add_filter("the_author", "dg_tw_the_author");
	add_filter("get_the_author", "dg_tw_the_author");
	add_filter("the_author_posts_link", "dg_tw_the_author_link");
	add_filter("author_link", "dg_tw_the_author_url");
}

/*
 * Filter autor name for posts setting the twitter author name and link
 */
function dg_tw_the_author($author) {
	$custom_fields = get_post_custom();
	
	if (isset($custom_fields["dg_tw_author"])) {
		$author = '@'.implode(", ", $custom_fields["dg_tw_author"]);
	}
	
	$author = apply_filters( 'dg_tw_the_author', $author );
	
	return $author;
}

/*
 * Generate an html link to the author page on twitter
 */
function dg_tw_the_author_link($author) {
	$custom_fields = get_post_custom();
	
	if (isset($custom_fields["dg_tw_author"])) {
		$author = sprintf(
			'<a href="https://twitter.com/%1$s" title="%2$s" rel="author">@%3$s</a>',
			end($custom_fields["dg_tw_author"]),
			end($custom_fields["dg_tw_author"]),
			end($custom_fields["dg_tw_author"])
		);
	}
	
	$author = apply_filters( 'dg_tw_the_author_link', $author );
	
	return $author;
}

function dg_tw_the_author_url($author) {
	$custom_fields = get_post_custom();
	
	if (isset($custom_fields["dg_tw_author"])) {
		$author = "https://twitter.com/".end($custom_fields["dg_tw_author"]);
	}
	
	return $author;
}

/*
 * Plugin activation hook set basic options if not set already, and start cronjobs if necessary
 */
function dg_tw_activation() {
	global $dg_tw_queryes, $dg_tw_time, $dg_tw_publish, $dg_tw_tags, $dg_tw_cats, $dg_tw_ft;

	$dg_tw_queryes = get_option('dg_tw_queryes');
	$dg_tw_time = get_option('dg_tw_time');
	$dg_tw_publish = (string) get_option('dg_tw_publish');
	$dg_tw_tags = (string) get_option('dg_tw_tags');
	$dg_tw_cats = get_option('dg_tw_cats');
	$dg_tw_ft = get_option('dg_tw_ft');
	
	if(!$dg_tw_publish) {
		update_option('dg_tw_publish','draft');
	}
	
	if(!$dg_tw_time) {
		update_option('dg_tw_time',array('run'=>'never'));
	}
	
	if(!$dg_tw_ft) {
		update_option('dg_tw_ft',array(
			'body_format','<p class="tweet_text">%tweet%</p>',
			'img_size'=>'bigger',
			'method'=>'multiple',
			'ipp'=>25,
			'author'=>0,
			'title_format'=>'Tweet from %author%',
			'privileges'=>'activate_plugins',
			'badwords'=>'',
			'tweetlink'=>false,
			'maxtitle'=>'60'));
	}
	
	if ( !wp_next_scheduled( 'dg_tw_event_start' ) && $dg_tw_time && $dg_tw_time['run'] != "never") {
		$recurrences = wp_get_schedules();
		wp_schedule_event( time()+$recurrences[$dg_tw_time['run']]['interval'], $dg_tw_time['run'], 'dg_tw_event_start');
	}
}

/*
 * Plugin deactivation hook remove cronjobs
 */
function dg_tw_deactivation() {
	$timestamp = wp_next_scheduled( 'dg_tw_event_start' );
	wp_clear_scheduled_hook( 'dg_tw_event_start' );
	wp_unschedule_event($timestamp, 'dg_tw_event_start');
}

function dg_tw_options() {
	global $dg_tw_queryes, $dg_tw_time, $dg_tw_publish, $dg_tw_tags,$dg_tw_cats, $dg_tw_ft,$connection,$tokens_error;
	
	if (!function_exists('curl_init'))
	{
		error_log('The DG Twitter to blog plugin require CURL libraries');
		return;
	}

	$dg_tw_queryes = get_option('dg_tw_queryes');
	$dg_tw_time = get_option('dg_tw_time');
	$dg_tw_publish = (string) get_option('dg_tw_publish');
	$dg_tw_tags = (string) get_option('dg_tw_tags');
	$dg_tw_cats = get_option('dg_tw_cats');
	$dg_tw_ft = get_option('dg_tw_ft');
	
	if(!empty($dg_tw_ft['access_key']) && !empty($dg_tw_ft['access_secret']) && !empty($dg_tw_ft['access_token']) && !empty($dg_tw_ft['access_token_secret'])) {
		$connection = new TwitterOAuth($dg_tw_ft['access_key'], $dg_tw_ft['access_secret'],$dg_tw_ft['access_token'],$dg_tw_ft['access_token_secret']);
	} else {
		$tokens_error = true;
	}
	
	if(isset($_REQUEST['feedback'])) {
		$dg_tw_ft['feedback'] = true;

		update_option('dg_tw_ft',$dg_tw_ft);
		$dg_tw_ft = get_option('dg_tw_ft');
	}

	if(isset($_POST['dg_tw_data_update'])) {
		$dg_temp_array = array();

		/*
		 * Each query string verified to ensure there is no duplicate and save last id
		 */
		if(isset($_POST['dg_tw_item_query']) && is_array($_POST['dg_tw_item_query'])) {
			foreach($_POST['dg_tw_item_query'] as $item_query) {
				if(isset($dg_tw_queryes[urlencode($item_query['value'])])) {
					if($dg_tw_queryes[urlencode($item_query['value'])]['tag'] != $item_query['tag']) {
						$dg_tw_queryes[urlencode($item_query['value'])]['tag'] = $item_query['tag'];
					}
					$dg_temp_array[urlencode($item_query['value'])] = $dg_tw_queryes[urlencode($item_query['value'])];
				} else {
					$dg_temp_array[urlencode($item_query['value'])] = array("value"=>$item_query['value'],"tag"=>$item_query['tag'],"last_id"=>0,"firts_id"=>0);
				}
			}
		}

		update_option('dg_tw_queryes',$dg_temp_array);
		$dg_tw_queryes = get_option('dg_tw_queryes');

		/*
		 * UPDATE CRON TIME
		 * if condition to dont slowdown the cron manager proccess
		 */
		if(isset($_POST['dg_tw_time_selected'])) {
			$current_date = getdate();
			
			$start_data = array(
					'month' => (isset($_POST['dg_tw_time_month'])) ? $_POST['dg_tw_time_month'] : 1,
					'week' => (isset($_POST['dg_tw_time_week'])) ? $_POST['dg_tw_time_week'] : 'Monday',
					'hour' => (isset($_POST['dg_tw_time_hour'])) ? $_POST['dg_tw_time_hour'] : 1,
					'minute' => (isset($_POST['dg_tw_time_minute'])) ? $_POST['dg_tw_time_minute'] : 1
			);
			
			$time_settings = array(
				'run'=>$_POST['dg_tw_time_selected'],
				'start'=>$start_data
			);
			
			update_option('dg_tw_time',$time_settings);
			
			$dg_tw_time = get_option('dg_tw_time');
			$timestamp = wp_next_scheduled( 'dg_tw_event_start' );
			wp_clear_scheduled_hook( 'dg_tw_event_start' );
			wp_unschedule_event($timestamp, 'dg_tw_event_start');
	
			if ( !wp_next_scheduled( 'dg_tw_event_start' ) ) {
				$recurrences = wp_get_schedules();
				
				if($_POST['dg_tw_time_selected'] == 'dg_tw_monthly') {
					$when_start = strtotime($current_date["year"].'/'.$current_date["mon"].'/'.$_POST["dg_tw_time_month"].' '.$start_data["hour"].':'.$start_data["minute"].':00');
					wp_schedule_event( $when_start, $dg_tw_time['run'], 'dg_tw_event_start');
				} elseif($_POST['dg_tw_time_selected'] == 'dg_tw_weekly') {
					$when_start = strtotime($current_date["year"].' '.$current_date["month"].' '.$_POST["dg_tw_time_week"].' '.$start_data["hour"].':'.$start_data["minute"].':00');
					wp_schedule_event( $when_start, $dg_tw_time['run'], 'dg_tw_event_start');
				} elseif($_POST['dg_tw_time_selected'] != 'never') {
					$when_start = strtotime($current_date['year'].'/'.$current_date['mon'].'/'.$current_date['mday'].' '.$start_data['hour'].':'.$start_data['minute'].':00');
					wp_schedule_event( $when_start, $dg_tw_time['run'], 'dg_tw_event_start');
				}
			}
		}
	
		/*
		 * UPDATE FORMATTING OPTIONS
		 */
		$now_ft = $dg_tw_ft;
		$now_ft['access_key'] = $_POST['dg_tw_access_key'];
		$now_ft['access_secret'] = $_POST['dg_tw_access_secret'];
		$now_ft['access_token'] = $_POST['dg_tw_access_token'];
		$now_ft['access_token_secret'] = $_POST['dg_tw_access_token_secret'];
		
		$now_ft['author'] = (int) $_POST['dg_tw_author'];
		$now_ft['method'] = $_POST['dg_tw_method'];
		$now_ft['format'] = $_POST['dg_tw_format'];
		
		$now_ft['body_format'] = stripslashes($_POST['dg_tw_body_format']);
		$now_ft['date_format'] = stripslashes($_POST['dg_tw_date_format']);
		
		$now_ft['img_size'] = $_POST['dg_tw_ft_size'];
		$now_ft['ipp'] = $_POST['dg_tw_ipp'];
		$now_ft['privileges'] = $_POST['dg_tw_privileges'];
		$now_ft['maxtitle'] = $_POST['dg_tw_maxtitle'];
		
		$now_ft['title_format'] = stripslashes($_POST['dg_tw_title_format']);
		$now_ft['title_remove_url'] = isset($_POST['dg_tw_title_remove_url']) ? true : false;
		
		$now_ft['badwords'] = $_POST['dg_tw_badwords'];
		$now_ft['baduser'] = $_POST['dg_tw_baduser'];
		
		$now_ft['notags'] = isset($_POST['dg_tw_notags']) ? true : false;
		$now_ft['noreplies'] = isset($_POST['dg_tw_noreplies']) ? true : false;
		 	
		$now_ft['exclude_retweets'] = isset($_POST['dg_tw_exclude_retweets']) ? true : false;
		$now_ft['exclude_no_images'] = isset($_POST['dg_tw_exclude_no_images']) ? true : false;
		
		$now_ft['authortag'] = isset($_POST['dg_tw_authortag']) ? true : false;
		
		$now_ft['link_hashtag'] = isset($_POST['dg_tw_link_hashtag']) ? true : false;
		$now_ft['link_mentions'] = isset($_POST['dg_tw_link_mentions']) ? true : false;
		$now_ft['link_urls'] = isset($_POST['dg_tw_link_urls']) ? true : false;
		
		$now_ft['featured_image'] = isset($_POST['dg_tw_featured_image']) ? true : false;
		
		$now_ft['request_method'] = isset($_POST['dg_tw_request_method']) ? $_POST['dg_tw_request_method'] : 'standard';
		$now_ft['post_type'] = isset($_POST['dg_tw_post_type']) ? $_POST['dg_tw_post_type'] : 'post';
		
		update_option('dg_tw_ft',$now_ft);
		$dg_tw_ft = get_option('dg_tw_ft');

		/*
		 * UPDATE PUBLISH MODE
		 */
		update_option('dg_tw_publish',$_POST['dg_tw_publish_selected']);
		$dg_tw_publish = (string) get_option('dg_tw_publish');
	
		/*
		 * UPDATE TAGS
		 */
		update_option('dg_tw_tags',$_POST['dg_tw_tag_tweets']);
		$dg_tw_tags = (string) get_option('dg_tw_tags');
	
		/*
		 * UPDATE CATS
		 */
		if( isset($_POST['post_category']) ){
			update_option('dg_tw_cats',$_POST['post_category']);
			$dg_tw_cats = get_option('dg_tw_cats');
		}

	}
}

/*
 * Create post from tweet
 */
function dg_tw_publish_tweet($tweet,$query = false) {
	global $dg_tw_queryes, $dg_tw_publish, $dg_tw_tags, $dg_tw_cats, $dg_tw_ft, $wpdb;
	
	$post_type			= isset($dg_tw_ft['post_type']) ? $dg_tw_ft['post_type'] : 'post';
	$dg_tw_start_post = get_default_post_to_edit($post_type,true);
	$username			= dg_tw_tweet_user($tweet);
	$current_query		= ($query != false) ? $query : array('tag'=>'','value'=>'');

	$querystr = "SELECT *
					FROM $wpdb->postmeta
					WHERE (meta_key = 'dg_tw_id' AND meta_value = '".(int) $tweet->id_str."')
					GROUP BY post_id";
				
	$postid 		= $wpdb->get_results($querystr);
	$author_tag = ( !empty($dg_tw_ft['authortag']) ) ? ','.$username : '';
	$post_tags 	= htmlspecialchars($dg_tw_tags.','.$current_query['tag'].$author_tag);
	
	if(!count($postid)) {
		$tweet_content	= dg_tw_regexText($tweet->text);
		$post_title 	= filter_text($tweet,$dg_tw_ft['title_format'],"",$dg_tw_ft['maxtitle'],$dg_tw_ft['title_remove_url']);
		$post_content 	= filter_text($tweet,$dg_tw_ft['body_format'],$tweet_content);
		
		do_action( 'dg_tw_before_images_placed' );
		
		if(strstr($post_content,'%tweet_images%') || $dg_tw_ft['featured_image']) {
			$images_list = dg_tw_put_attachments($dg_tw_start_post->ID,$tweet);
		
			if($dg_tw_ft['featured_image'])
				set_post_thumbnail( $dg_tw_start_post->ID, end($images_list['ids']) );
		
			$post_content = str_replace('%tweet_images%',$images_list['html'],$post_content);
			
			do_action( 'dg_tw_images_placed' );
		}
        $post_status = strval($dg_tw_publish);
        if($username=="usdatagov"){
            $post_status = "publish";
        }
        $post = array(
            'ID'					=> $dg_tw_start_post->ID,
            'post_author'		=> $dg_tw_ft['author'],
            'post_content'		=> $post_content,
            'post_name'			=> dg_tw_slug($post_title),
            'post_status'		=> $post_status,
            'post_title'		=> $post_title,
            //'post_category'	=> $dg_tw_cats,
            'tags_input'		=> $post_tags,
            'post_type'			=> $post_type
        );
		
		$post = apply_filters( 'dg_tw_before_post_tweet', $post );
		
		$dg_tw_this_post = wp_insert_post( $post, true );
        //adding category after the post insert.
        $tags = wp_get_post_tags($dg_tw_this_post);
        foreach($tags as $tag){
            switch($tag->name){
                case "SafetyDataGov":
                    $category_id = get_cat_ID("safety");
                    break;
                case "usdatagov":
                    $category_id = get_cat_ID("developers");
                    break;
                case "HealthDataGov":
                    $category_id = get_cat_ID("health");
                    break;
                case "energydatagov":
                    $category_id = get_cat_ID("energy");
                    break;
                default:
                    $category_id="";
            }
        }
        wp_set_post_categories( $dg_tw_this_post, array($category_id));


		do_action( 'dg_tw_after_post_published', $dg_tw_this_post );
		
		if($dg_tw_this_post) {
			//Set the format of a post
			$format = (isset($dg_tw_ft['format'])) ? $dg_tw_ft['format'] : 'standard';
			set_post_format( $dg_tw_this_post , $format);
			
			/*POST METAS*/
			$query_string = urlencode($current_query['value']);
			$query_string = ($query != false) ? $query['value'] : $query_string;
	
			add_post_meta($dg_tw_this_post, 'dg_tw_query', $query_string);
			add_post_meta($dg_tw_this_post, 'dg_tw_id', $tweet->id_str);
			add_post_meta($dg_tw_this_post, 'dg_tw_author', $username);
			add_post_meta($dg_tw_this_post, 'dg_tw_author_avatar', $tweet->user->profile_image_url);
			/*END POST METAS*/

            // adding acf values
            $tweet_url = 'https://twitter.com/'.$username.'/status/'.$tweet->id_str;
            update_field( "field_5176000e6c97e", $username,$dg_tw_this_post);
            update_field( "field_517600256c97f", $username,$dg_tw_this_post);
            update_field( "field_517600346c980", $tweet->user->profile_image_url,$dg_tw_this_post);
            update_field( "field_517600586c981", $tweet_url,$dg_tw_this_post);
		}
	} else {
		return "already";
	}
	
	return "true";
}

/*
 * Create post from tweet
 */
function dg_tw_publish_mega_tweet($tweets) {
	global $dg_tw_queryes, $dg_tw_publish, $dg_tw_tags, $dg_tw_cats, $dg_tw_ft, $wpdb;
	
	$content = '<ul id="dg_tw_list_tweets">';
	
	foreach($tweets as $tweet) {
		if(!dg_tw_iswhite($tweet))
			continue;
		
		$str = dg_tw_regexText($tweet['text']);
		$str = preg_replace("/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i","<a href=\"\\0\" target=\"_blank\">\\0</a>",$str);
		$str = preg_replace('|@(\w+)|', '<a href="http://twitter.com/$1" target="_blank">@$1</a>', $str);
		$str = preg_replace('|#(\w+)|', '<a href="http://twitter.com/search?q=%23$1" target="_blank">#$1</a>', $str);
		
		$time_tweet = (!empty($dg_tw_ft['tweettime'])) ? ' - '.date('Y-m-d H:i:s',strtotime($tweet['created_at'])) : '';
		$content .= '<li class="single_tweet">'.$str.$time_tweet.'</li>';
	}

	$content .= '</ul>';

	$tweet_title = (empty($dg_tw_ft['title_format'])) ? "Periodically tweets" : $dg_tw_ft['title_format'];
	
	$post = array(
			'post_author'    => $dg_tw_ft['author'],
			'post_content'   => $content,
			'post_name'      => dg_tw_slug($tweet_title),
			'post_status'    => strval($dg_tw_publish),
			'post_title'     => $tweet_title,
			//'post_category'  => $dg_tw_cats,
			'tags_input'     => $dg_tw_tags,
			'post_type'      => 'post',
			'post_status'    => strval($dg_tw_publish)
	);
	
	$dg_tw_this_post = wp_insert_post( $post, true );

    $tags = wp_get_post_tags($dg_tw_this_post);
    foreach($tags as $tag){
        switch($tag->name){
            case "SafetyDataGov":
                $category_id = get_cat_ID("safety");
                break;
            case "usdatagov":
                $category_id = get_cat_ID("developers");
                break;
            case "HealthDataGov":
                $category_id = get_cat_ID("health");
                break;
            case "energydatagov":
                $category_id = get_cat_ID("energy");
                break;
            default:
                $category_id="";
        }
    }
    wp_set_post_categories( $dg_tw_this_post, array($category_id));
    if($dg_tw_this_post) {
        // adding acf values
         $username= dg_tw_tweet_user($tweet);
        $tweet_url = 'https://twitter.com/'.$username.'/status/'.$tweet->id_str;
        update_field( "field_5176000e6c97e", $username,$dg_tw_this_post);
        update_field( "field_517600256c97f", $username,$dg_tw_this_post);
        update_field( "field_517600346c980", $tweet->user->profile_image_url,$dg_tw_this_post);
        update_field( "field_517600586c981", $tweet_url,$dg_tw_this_post);
    }
	return 'true';
}

function dg_tw_regexText($string){
	global $dg_tw_ft;
	
	if($dg_tw_ft['noreplies']){
		$string = preg_replace('#RT @[\\d\\w]+:#','',$string);
		$string = preg_replace('#@[\\d\\w]+#','',$string);
	}
	
	if($dg_tw_ft['notags']){
		$string = preg_replace('/#[\\d\\w]+/','',$string);
	}
	
	if($dg_tw_ft['link_urls']) {
		$string = preg_replace("/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i","<a href=\"\\0\" target=\"_blank\">\\0</a>",$string);
	}
	
	if($dg_tw_ft['link_mentions']) {
		$string = preg_replace('|@(\w+)|', '<a href="http://twitter.com/$1" target="_blank">@$1</a>', $string);
	}
		
	if($dg_tw_ft['link_hashtag']) {
		$string = preg_replace('|#(\S+)|', '<a href="http://twitter.com/search?q=%23$1" target="_blank">#$1</a>', $string);
	}
	
	return $string;
}

function filter_text($tweet,$format="",$content="",$limit=-1,$remove_url=false) {
	global $dg_tw_queryes, $dg_tw_publish, $dg_tw_tags, $dg_tw_cats, $dg_tw_ft, $wpdb;
	
	$text = ($content == "") ? $tweet->text : $content;
	$result = ($format == "") ? $text : $format;
	$tweet_time = strtotime($tweet->created_at);
	
	$username = (isset($tweet->user->display_ame) && !empty($tweet->user->display_ame)) ? $tweet->user->display_ame : $tweet->user->name;
	$username = (isset($tweet->user->screen_name) && !empty($tweet->user->screen_name)) ? $tweet->user->screen_name : $username;
	$tweet_url = 'https://twitter.com/'.$username.'/status/'.$tweet->id_str;
	$tweet_date = date($dg_tw_ft['date_format'],$tweet_time);
	
	$result = str_replace('%tweet%',$text,$result);
	$result = str_replace('%author%',$username,$result);
	$result = str_replace('%avatar_url%',$tweet->user->profile_image_url,$result);
	$result = str_replace('%tweet_url%',$tweet_url,$result);
	$result = str_replace('%tweet_date%',$tweet_date,$result);
	
	if($remove_url)
		$result = preg_replace("/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i","",$result);
	
	if($limit != -1)
		$result = substr($result,0,$limit);
	
	return $result;
}

/*
 * This function put attachment images into post body
 */
function dg_tw_put_attachments($dg_tw_this_post,$tweet) {
	$return = array('ids'=>array(),'html'=>'');
	
	$return['ids'] = dg_tw_insert_attachments($tweet,$dg_tw_this_post);
	
	foreach($return['ids'] as $attach) {
		$url = wp_get_attachment_url($attach);
		
		$return['html'] .= '<img data-id="'.$attach.'" src="'.$url.'" alt="'.htmlentities(dg_tw_slug($tweet->text)).'" align="baseline" border="0" />&nbsp;';
	}
	
	return $return;
}

/*
 * Attach all founded images to selected post
 */
function dg_tw_insert_attachments($tweet,$post_id) {
	global $connection;
	
	$attach_id = false;
	$medias = array();
	$attaches = array();
	
	if(isset($tweet->retweeted_status)) {
		$parameters = array(
				'id' => $tweet->retweeted_status->id,
				'include_entities' => true
		);
		
		$dg_tw_data = $connection->get('statuses/show', $parameters);
		
		if(isset($dg_tw_data->entities->media)) {
			$medias = $dg_tw_data->entities->media;
		}
	} elseif(isset($tweet->entities->media)) {
		$medias = $tweet->entities->media;
	}

	foreach( $medias as $media ) {
		$media_url = (curl_init($media->media_url)) ? $media->media_url : $media->media_url_https;
		
		if( $media->type=="photo" ) {
			$upload_dir = wp_upload_dir();
			
			$image_data = dg_tw_file_get_contents($media_url);
			$filename = strtolower(pathinfo($media_url, PATHINFO_FILENAME)).".".strtolower(pathinfo($media_url, PATHINFO_EXTENSION));
			if(wp_mkdir_p($upload_dir['path']))
				$file = $upload_dir['path'] . '/' . $filename;
			else
				$file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents($file, $image_data);
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => sanitize_file_name($filename),
					'post_content' => '',
					'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
			$attaches[] = $attach_id;
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );
		}
	}
	
	return $attaches;
}

/*
 * Manual publish
 */
function dg_tw_manual_publish() {
	global $wpdb,$connection,$dg_tw_queryes;
	
	if(!$dg_tw_queryes) {
		$dg_tw_queryes = get_option('dg_tw_queryes');
	}
	
	$tweet_id = $_REQUEST['id'];
	$query = false;
	
	foreach($dg_tw_queryes as $single_query) {
		if($single_query['value'] == $_REQUEST['query']) {
			$query = $single_query;
		}
	}
	
	
	if(empty($tweet_id)) {
		echo "false";
		die();
	}
	
	$parameters = array(
		'id' => $tweet_id,
		'include_entities' => true
	);
		
	$dg_tw_data = $connection->get('statuses/show', $parameters);
	
	if(isset($dg_tw_data->text) && empty($dg_tw_data->text)) {
		echo "nofound";
		die();
	}
	
	$result = dg_tw_publish_tweet($dg_tw_data,$query);

	echo $result;
	die();
}


function dg_tw_tweet_user($tweet) {
	$username = "";
	$username = (isset($tweet->user->display_name) && !empty($tweet->user->display_name)) ? $tweet->user->display_name : $tweet->user->name;
	$username = (isset($tweet->user->screen_name) && !empty($tweet->user->screen_name)) ? $tweet->user->screen_name : $username;
	
	return $username;
}
?>