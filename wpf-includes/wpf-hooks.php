<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;


add_action( 'admin_init', 'wpforo_do_uninstall', 10);
function wpforo_do_uninstall(){
	if( isset($_GET['action']) && $_GET['action'] == 'wpforo-uninstall' ){
		if( check_admin_referer( 'wpforo_uninstall' ) && current_user_can('administrator') ) wpforo_uninstall();
		wp_redirect( admin_url( 'plugins.php' ) );
		exit();
	}
}

add_filter( 'plugin_action_links_' . WPFORO_BASENAME, 'wpforo_action_link', 10, 2 );
function wpforo_action_link( $links, $file ) {
	
	$uninstall_url = wp_nonce_url( admin_url( 'plugins.php?action=wpforo-uninstall' ), 'wpforo_uninstall' );
	
	$links[] = '<a href="'.esc_url( $uninstall_url ).'" style="color:#a00;" onclick="return confirm(\'' . __('IMPORTANT! Uninstall is not a simple deactivation action. This action will permanently remove all forum data (forums, topics, replies, attachments...) from database. Please backup database before this action, you may need this forum data in future. If you are sure that you want to delete all forum data please confirm. If not, just cancel it, then you can deactivate this plugin, that will not remove forum data.', 'wpforo').'\')">' . __( 'Uninstall', 'wpforo' ) . '</a>';

	$settings_link = '<a href="'.esc_url( admin_url( 'admin.php?page=wpforo-community' ) ).'">' . __( 'Settings', 'wpforo' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}

function wpforo_notice_show(){
	global $wpforo;
	$wpforo->notice->show();
}
add_action( 'wpforo_top_hook', 'wpforo_notice_show', 10, 0 );

function wpforo_user_admin_bar(){
	if( !is_super_admin() ) show_admin_bar(wpforo_feature('user-admin-bar'));
}
add_action('init', 'wpforo_user_admin_bar');

function wpforo_admin_notice__menu_help(){
	if(strpos(wpforo_full_url(), 'nav-menus.php') !== FALSE){
		global $wpforo;
		
		$message = 'wpForo Menu Shortcodes<hr/><table>';
		foreach( $wpforo->menu as $key => $value ){
			$message .= "<tr><td> " . $value['label'] . ": </td><td> /%$key%/ </td></tr>";
		}
		$message .= "<tr><td> " . wpforo_phrase('register', FALSE) . ": </td><td> /%wpforo-register%/ </td></tr>
			<tr><td> " . wpforo_phrase('login', FALSE) . ": </td><td> /%wpforo-login%/ </td></tr>
			</table>";
		
		$class = 'notice notice-warning';
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
}
//add_action( 'admin_notices', 'wpforo_admin_notice__menu_help' );

function wpforo_disable_comments( $open, $post_id ) {
	if(is_wpforo_page()) return FALSE; 
	return $open;
}
add_filter( 'comments_open', 'wpforo_disable_comments', 10, 2 );

function wpforo_disable_comments_hide_existing_comments($comments) {
	if(is_wpforo_page()) return array();
	return $comments;
}
add_filter('comments_array', 'wpforo_disable_comments_hide_existing_comments', 10, 2);

function wpforo_remove_comment_support() {
	if(is_wpforo_page()){
   	 	remove_post_type_support( 'post', 'comments' );
	    remove_post_type_support( 'page', 'comments' );
	}
}
add_action('init', 'wpforo_remove_comment_support', 100);

function wpforo_change_author_default_page( $link ){
	global $wpforo;
	if(!wpforo_feature('author-link', $wpforo)) return $link;
	return $wpforo->member->get_profile_url($link);	 	  	 	 
}
function wpforo_change_comment_author_default_page( $link, $ID = 0, $object = NULL ){
	global $wpforo;
	if(!wpforo_feature('comment-author-link', $wpforo)) return $link;
	if(!isset($link) || !$link){
		if(isset($object->user_id) && $object->user_id){
			return $wpforo->member->get_profile_url($object->user_id);	
		}
	}
	return $link;
}
add_filter( 'author_link', 'wpforo_change_author_default_page', 10, 1 );	 	 
add_filter( 'get_comment_author_url', 'wpforo_change_comment_author_default_page', 10, 3 );

function wpforo_change_default_edit_user_page( $url, $userid, $scheme ) {
	global $wpforo;
	return $wpforo->member->get_profile_url($userid);	
}
add_filter( 'edit_profile_url', 'wpforo_change_default_edit_user_page', 10, 3 );

function wpforo_change_default_register_page( $register_url ) {
    if(!wpforo_feature('register-url')) return $register_url;
	return WPFORO_BASE_URL . '?wpforo=signup';
}
add_filter( 'register_url', 'wpforo_change_default_register_page' );

function  wpforo_change_default_login_page( $login_url, $redirect ) {
    if(!wpforo_feature('login-url')) return $login_url;
	return WPFORO_BASE_URL . '?wpforo=signin';
}
add_filter( 'login_url', 'wpforo_change_default_login_page', 10, 2 );

function wpftpl( $filename ){
	global $wpforo;
	$find = array();
	if ( $filename ) {
		$find[] = 'wpforo/'. $filename;
		$template = locate_template( array_unique( $find ) );
		if ( $template ) {
			return $template;
		}
		else{
			return WPFORO_THEME_DIR . '/'. $wpforo->theme .'/' . $filename;
		}
	}
}

function wpforo_init_template(){
	global $wpforo;
	if(is_admin()) return;
	include_once( wpftpl('index.php') );
}

add_shortcode( 'wpforo', 'wpforo_load' );
function wpforo_load( $atts ){
	if(is_admin()) return;
	global $wpforo, $post;
	
	if( is_wpforo_shortcode_page() ){
		$url = WPFORO_BASE_URL;
		
		$args = shortcode_atts( array(
	        'item' => 'forum',
	        'id' => 0,
	        'slug' => '',
	    ), $atts );
	    
	    if( $args['id'] || $args['slug'] ){
	    	$getid = ( $args['slug'] ? $args['slug'] : $args['id'] );
		    if( $args['item'] == 'topic' ){
				$url = $wpforo->topic->get_topic_url($getid);
			}elseif( $args['item'] == 'profile' ){
				$url = $wpforo->member->get_profile_url($getid);
			}else{
				$url = $wpforo->forum->get_forum_url($getid);
			}
		}
		
		$wpforo->init_current_object($url);
		$wpforo->tpl->init_nav_menu();
	}
	
	if(wpforo_feature('output-buffer') && function_exists('ob_start')){
		ob_start();
		wpforo_init_template();
		return ob_get_clean();
	}
	else{
		wpforo_init_template();
	}
	
}


function wpforo_template_include($template){
	if( is_wpforo_page() && !is_wpforo_shortcode_page() && ($wpforo_template = wpftpl('index.php')) ) return $wpforo_template;
	return $template;
}

add_action('wp', 'wpforo_set_404_to_false');
function wpforo_set_404_to_false(){
	if( is_wpforo_page() ){
		global $wp_query;
		$wp_query->is_404 = FALSE;
	}
}

function wpforo_do_rewrite(){
	if(!is_wpforo_page()) return;
	
	global $wpforo;
	$pattern = ($wpforo->use_home_url ? '(.*)' : '('.preg_quote($wpforo->permastruct).'(?:/|$).*)$');
	add_rewrite_rule($pattern, 'index.php?page_id=' . $wpforo->pageid, 'top');
	flush_rewrite_rules();
	nocache_headers();
	if( $wpforo->use_home_url )
	add_filter('template_include', 'wpforo_template_include');
}
add_action('init', 'wpforo_do_rewrite');

function wpforo_theme_functions(){
	$path = wpftpl('functions.php');
	if( file_exists($path) ){ 
		include_once($path);
	}
}
add_action('init', 'wpforo_theme_functions');

function wpforo_theme_functions_wp(){
	$path = wpftpl('functions-wp.php');
	if( file_exists($path) ){ 
		include_once($path);
	}
}
add_action('after_setup_theme', 'wpforo_theme_functions_wp');

function wpforo_meta_title($title) {
	global $wpforo; 
	$is404 = false; 
	$meta_title = array();
	
	if(!wpforo_feature('seo-title', $wpforo)) return $title;
	
	if(is_wpforo_page()){
		$template = $wpforo->current_object['template'];
		if( ($template == 'post' && $wpforo->current_object['topicid'] == 0) || 
			($template == 'topic' && $wpforo->current_object['forumid'] == 0) ||
			($template == 'profile' && $wpforo->current_object['userid'] == 0) ){
			$is404 = true;
		}
		if(!$is404){
			$paged = ( $wpforo->current_object['paged'] > 1 ) ? ' - ' .  wpforo_phrase( 'page', false) . ' ' . $wpforo->current_object['paged'] .' ' : '';
			if(!empty($wpforo->current_object['forum'])) $forum = $wpforo->current_object['forum'];
			if(!empty($wpforo->current_object['topic'])) $topic = $wpforo->current_object['topic'];
			if(!empty($wpforo->current_object['user'])) $user = $wpforo->current_object['user'];
			if(isset($topic['title']) && isset($forum['title']) && isset($wpforo->general_options['title'])){
				$meta_title = array($topic['title'] . $paged, $forum['title'], $wpforo->general_options['title']);
			}
			elseif(!isset($topic['title']) && isset($forum['title']) && isset($wpforo->general_options['title'])){
				$meta_title = array($forum['title'] . $paged, $wpforo->general_options['title']);
			}
			elseif( $template != 'forum' && $template != 'topic' && $template != 'post' ){
				if( $template == 'profile' || $template == 'account' || $template == 'activity' || $template == 'subscriptions' ){
					if(isset($user['display_name'])){
						$meta_title = array($user['display_name'], wpforo_phrase( ucfirst($template), false), $wpforo->general_options['title']);
					}
					elseif(isset($wpforo->current_object['username'])){
						$meta_title = array($wpforo->current_object['username'], wpforo_phrase( ucfirst($template), false), $wpforo->general_options['title']);
					}
					else{
						$meta_title = array(wpforo_phrase( 'Member', false), wpforo_phrase( ucfirst($template), false), $wpforo->general_options['title']);
					}
				}
				elseif($template){
					$wpfpaged = ( isset($_GET['wpfpaged']) && $_GET['wpfpaged'] > 1 ) ? ' - ' .  wpforo_phrase( 'page', false) . ' ' . $_GET['wpfpaged'] .' ' : '';
					$meta_title = array(wpforo_phrase( ucfirst($template), false) . $wpfpaged, $wpforo->general_options['title']);
				}
				elseif($title){
					$meta_title = (is_array($title)) ? $title : array($title);
				}
				else{
					$meta_title = array(wpforo_phrase('Forum', false), get_bloginfo('name'));
				}
			}
			elseif( isset($wpforo->general_options['title']) && $wpforo->general_options['title'] ){
				$meta_title = array($wpforo->general_options['title'], get_bloginfo('name'));
			}
			elseif($title){
				$meta_title = (is_array($title)) ? $title : array($title);
			}
			else{
				$meta_title = array(wpforo_phrase('Forum', false), get_bloginfo('name'));
			}
		}
		else{
			$meta_title = array(wpforo_phrase( '404 - Page not found', false), $wpforo->general_options['title']);
		}
	}
	if(!empty($meta_title)) {
		return $meta_title;
	}
	else{
		return $title;
	}
}
add_filter('document_title_parts', 'wpforo_meta_title', 100);

function wpforo_meta_wp_title($title){
	if(!wpforo_feature('seo-title')) return $title;
	$meta_title = wpforo_meta_title($title);
	if(is_array($meta_title) && !empty($meta_title)){
		$title = implode(' &#8211; ', $meta_title);
	}
	return $title;
}
add_filter( 'wp_title', 'wpforo_meta_wp_title', 100);

function wpforo_add_meta_tags(){
	global $wpforo;
	
	if(!wpforo_feature('seo-meta', $wpforo)) return;
	
	if(is_wpforo_page()){
		$title = '';
		$template = '';
		$description = '';
		$udata = array();
		$canonical = wpforo_full_url();
		$paged = ( $wpforo->current_object['paged'] > 1 ) ? wpforo_phrase( 'page', false) . ' ' . $wpforo->current_object['paged'] .' | ' : '';
		if(isset($wpforo->current_object['template'])) $template = $wpforo->current_object['template'];
		if(!empty($wpforo->current_object['forum'])) $forum = $wpforo->current_object['forum'];
		if(!empty($wpforo->current_object['topic'])) $topic = $wpforo->current_object['topic'];
		if(!empty($wpforo->current_object['user'])) $user = $wpforo->current_object['user'];
		if(isset($wpforo->current_object)){
			if( isset($wpforo->current_object['forumid']) && !isset($wpforo->current_object['topicid']) ){
				if(isset($forum['title'])) $title = $forum['title'];
				if(isset($wpforo->current_object['forum_meta_desc']) && $wpforo->current_object['forum_meta_desc'] !=''){
					$description = $paged . $wpforo->current_object['forum_meta_desc'];
				}
				elseif(isset($wpforo->current_object['forum_desc']) && $wpforo->current_object['forum_desc'] !=''){
					$description = $paged . $wpforo->current_object['forum_desc'];
				}
			}elseif( isset($wpforo->current_object['topicid']) && isset($topic['first_postid']) ){
				$post = $wpforo->post->get_post($topic['first_postid']);
				if(isset($post['title'])) $title = wpforo_text($paged . $post['title'], 60, false);
				if(isset($post['body'])) $description = wpforo_text($paged . $post['body'], 150, false);
			}elseif( $template == 'profile' || $template == 'account' || $template == 'activity' || $template == 'subscriptions' ){
				if( isset($wpforo->general_options['title']) ) $title = $paged . $wpforo->general_options['title'];
				$udata['name'] = (isset($user['display_name']) && $user['display_name']) ? wpforo_phrase( 'User', false ) . ': ' . $user['display_name'] : '';
				$udata['title'] = (isset($user['stat']['title']) && $user['stat']['title']) ?  wpforo_phrase( 'Title', false ) . ': ' . $user['stat']['title'] : '';
				$udata['about'] = (isset($user['about']) && $user['about']) ? wpforo_phrase( 'About', false ) . ': ' . wpforo_text($user['about'], 150, false) : '';
				$description =  $title . ' - ' . wpforo_phrase('Member Profile', false) . ' &gt; ' . wpforo_phrase( ucfirst($template), false ) . ' ' . wpforo_phrase( 'Page', false ) . '. ' . implode(', ', $udata);
			}elseif(isset($wpforo->current_object['template']) && $wpforo->current_object['template'] == 'member'){
				$wpfpaged = ( isset($_GET['wpfpaged']) && $_GET['wpfpaged'] > 1 ) ? wpforo_phrase( 'Page', false) . ' ' . $_GET['wpfpaged'] .' | ' : '';
				$description = $wpfpaged . wpforo_phrase( 'Forum Members List', false);
			}
			else{
				if( isset($wpforo->general_options['title']) ) $title = $paged . $wpforo->general_options['title'];
				if( isset($wpforo->general_options['description']) ) $description = $paged . $wpforo->general_options['description'];
			}
			$description = preg_replace('#[\t\r\n]+#isu', ' ', $description);
			echo "\r\n<!-- wpForo SEO -->\r\n<link rel=\"canonical\" href=\"".$canonical."\" />\r\n<meta name=\"description\" content=\"" . esc_html($description) . "\" />\r\n<meta property=\"og:title\" content=\"" . esc_html($title) . "\" />\r\n<meta property=\"og:description\" content=\"" . esc_html($description) . "\" />\r\n<meta property=\"og:url\" content=\"" . $canonical . "\" />\r\n<meta property=\"og:site_name\" content=\"" . get_bloginfo('name') . "\" />\r\n<meta name=\"twitter:description\" content=\"" . esc_html($description) . "\"/>\r\n<meta name=\"twitter:title\" content=\"" . esc_html($title) . "\" />\r\n<!-- wpForo SEO End -->\r\n\r\n";
		}
	}
}
add_action('wp_head', 'wpforo_add_meta_tags', 1);


add_action('wp_ajax_wpforo_like_ajax', 'wpf_like');
add_action('wp_ajax_nopriv_wpforo_like_ajax', 'wpf_like');

function wpf_like(){
	global $wpforo;
	$response = array('stat' => 0, 'likers' => '', 'notice' => $wpforo->notice->get_notices());
	if(!is_user_logged_in()){
		$wpforo->notice->add( sprintf( wpforo_phrase('Please %s or %s', FALSE), '<a href="' . wpforo_login_url() . '">'.wpforo_phrase('Login', FALSE).'</a>', '<a href="' . wpforo_register_url() . '">'.wpforo_phrase('Register', FALSE).'</a>' ) );
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !isset($_POST['likestatus']) || !isset($_POST['postid']) || !($postid = intval($_POST['postid'])) ){
		$wpforo->notice->add('action error', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !$post = $wpforo->post->get_post( $postid ) ){
		$wpforo->notice->add('post not found', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !$wpforo->perm->forum_can( $post['forumid'], 'l') ){
		$wpforo->notice->add('You haven\'t permission to like posts from this forum', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( $_POST['likestatus'] ){
		if( $wpforo->db->insert( 
			$wpforo->db->prefix . 'wpforo_likes', 
			array(
				'postid'	=> $postid, 
				'userid' 	=>  $wpforo->current_userid,
				'post_userid' 	=> $post['userid']
			), 
			array('%d','%d','%d')
		) ){
			$wpforo->notice->add('done', 'success');
			$response['stat'] = 1;
			$response['notice'] = $wpforo->notice->get_notices();
		}
	}else{
		if( $wpforo->db->delete( 
			$wpforo->db->prefix . 'wpforo_likes', 
			array(
				'postid'	=> $postid, 
				'userid' 	=>  $wpforo->current_userid
			), 
			array('%d','%d')
		) ){
			$wpforo->notice->add('done', 'success');
			$response['stat'] = 1;
			$response['notice'] = $wpforo->notice->get_notices();
		}
	}
	if(!isset($post['userid'])) $wpforo->member->reset($post['userid']);
	if(!isset($wpforo->current_userid)) $wpforo->member->reset($wpforo->current_userid);
	$response['likers'] = $wpforo->tpl->likers($postid);
	echo json_encode($response);
	exit();
}


add_action('wp_ajax_wpforo_vote_ajax', 'wpf_vote');
add_action('wp_ajax_nopriv_wpforo_vote_ajax', 'wpf_vote');
function wpf_vote(){
	
	if(!is_user_logged_in()) return;
	
	global $wpforo;
	
	if( !isset($_POST['postid']) || !$_POST['postid'] ){
		$wpforo->notice->add('Wrong post data', 'error');
		echo json_encode(array('stat' => 0, 'notice' => $wpforo->notice->get_notices()));
		exit();
	}
	if( $wpforo->db->get_var( "SELECT `voteid` FROM `".$wpforo->db->prefix."wpforo_votes` WHERE `postid` = " . intval($_POST['postid']) . " AND `userid` = " . intval($wpforo->current_userid) )){
		$wpforo->notice->add('You are already voted this post');
		echo json_encode(array('stat' => 0, 'notice' => $wpforo->notice->get_notices()));
		exit();
	}
	
	$reaction = 1;
	if( $_POST['votestatus'] == 'down' ) $reaction = -1;
	$post = $wpforo->post->get_post( intval($_POST['postid']) );
	
	$voted = $wpforo->db->insert( 
		$wpforo->db->prefix . 'wpforo_votes', 
		array(
			'postid'	=> intval($_POST['postid']), 
			'userid' 	=>  $wpforo->current_userid,
			'reaction' 	=>  $reaction,
			'post_userid' 	=>  $post['userid']
		), 
		array( 
			'%d', 
			'%d',
			'%d',
			'%d'
		)
	);	
	
	if(!isset($post['userid'])) $wpforo->member->reset($post['userid']);
	if(!isset($wpforo->current_userid)) $wpforo->member->reset($wpforo->current_userid);
	
	if( $voted !== FALSE ){
		if( $_POST['itemtype'] == 'topic' ){
			$incr = $wpforo->db->query( "UPDATE ".$wpforo->db->prefix . 'wpforo_topics'." SET `votes` = `votes` + $reaction  WHERE topicid = " . intval($post['topicid']) );
			$incr2 = $wpforo->db->query( "UPDATE ".$wpforo->db->prefix . 'wpforo_posts'." SET `votes` = `votes` + $reaction  WHERE postid = " . intval($post['postid']) );
		}else{
			$incr = $wpforo->db->query( "UPDATE ".$wpforo->db->prefix . 'wpforo_posts'." SET `votes` = `votes` + $reaction  WHERE postid = " . intval($post['postid']) );
			$incr2 = TRUE;
		}
		
		if($incr !== FALSE && $incr2 !== FALSE){
			$wpforo->notice->add('Successfully voted', 'success');
			echo json_encode(array('stat' => 1, 'notice' => $wpforo->notice->get_notices()));
			exit();
		}
	}
	
	$wpforo->notice->add('Wrong post data', 'error');
	echo json_encode(array('stat' => 0, 'notice' => $wpforo->notice->get_notices()));
	exit();
}

add_action('wp_ajax_wpforo_answer_ajax', 'wpf_answer');
add_action('wp_ajax_nopriv_wpforo_answer_ajax', 'wpf_answer');
function wpf_answer(){
	global $wpforo;
	$response = array('stat' => 0, 'notice' => $wpforo->notice->get_notices());
	if(!is_user_logged_in()){
		$wpforo->notice->add( sprintf( wpforo_phrase('Please %s or %s', FALSE), '<a href="' . wpforo_login_url() . '">'.wpforo_phrase('Login', FALSE).'</a>', '<a href="' . wpforo_register_url() . '">'.wpforo_phrase('Register', FALSE).'</a>' ) );
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !isset($_POST['answerstatus']) || !isset($_POST['postid']) || !$postid = intval($_POST['postid']) ){
		$wpforo->notice->add('action error', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !$post = $wpforo->post->get_post( $postid ) ){
		$wpforo->notice->add('post not found', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !$topic = $wpforo->topic->get_topic( $post['topicid'] ) ){
		$wpforo->notice->add('topic not found', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( !($wpforo->perm->forum_can( $post['forumid'], 'at') ||  ( $wpforo->perm->forum_can( $post['forumid'], 'oat') && $wpforo->current_userid == $topic['userid'] ) ) ){
		$wpforo->notice->add('You haven\'t permission to make topic answered', 'error');
		$response['notice'] = $wpforo->notice->get_notices();
		echo json_encode($response);
		exit();
	}
	if( FALSE !== $wpforo->db->query( "UPDATE ".$wpforo->db->prefix ."wpforo_posts SET is_answer = ".intval($_POST['answerstatus'])." WHERE postid = " . intval($postid) ) ){
		$wpforo->notice->add('done', 'success');
		$response['stat'] = 1;
		$response['notice'] = $wpforo->notice->get_notices();
	}
	echo json_encode($response);
	exit();
}

add_action('wp_ajax_wpforo_quote_ajax', 'wpf_quote');
add_action('wp_ajax_nopriv_wpforo_quote_ajax', 'wpf_quote');
function wpf_quote(){
	
	if(!is_user_logged_in()) return;
	
	global $wpforo;
	
	$post  = $wpforo->db->get_row('SELECT `userid`, `body` FROM '.$wpforo->db->prefix.'wpforo_posts WHERE postid =' . intval($_POST['postid']), ARRAY_A);
	$poster = $wpforo->member->get_member(intval($post['userid']));
	echo '<blockquote><div class="wpforo-post-quote-author">' . wpforo_phrase('Posted by', FALSE) . ': ' . ( $poster['display_name'] ? esc_textarea($poster['display_name']) : esc_textarea($poster['user_login']) ) . '</div><i class="fa fa-quote-left fa-0x wpfcl-0"></i> &nbsp; ' . wpautop($post['body']) . ' &nbsp; &#160;</blockquote><br /><br />';
	exit();
}

add_action('wp_ajax_wpforo_report_ajax', 'wpf_report');
add_action('wp_ajax_nopriv_wpforo_report_ajax', 'wpf_report');
function wpf_report(){
	
	if(!is_user_logged_in()) return;
	
	global $wpforo;
	
	if( !isset($_POST['reportmsg']) || !$_POST['reportmsg'] || !isset($_POST['postid']) || !$_POST['postid'] ){
		$wpforo->notice->add('Error: please inset some text to report.', 'error');
		echo json_encode( $wpforo->notice->get_notices() );
		exit();
	}
	
	############### Sending Email  ##################
		$report_text = substr($_POST['reportmsg'], 0, 1000);
		$postid = intval($_POST['postid']);
		$reporter = '<a href="'.$wpforo->current_user['profile_url'].'">'.($wpforo->current_user['display_name'] ? $wpforo->current_user['display_name'] : $wpforo->current_user['user_nicename']).'</a>';
		$reportmsg = wpforo_kses($report_text, 'email');
		$post_url = '<a target="_blank" href="'. esc_attr($wpforo->post->get_post_url($postid)).'">' . wpforo_phrase('Post link', false) . '&raquo;</a>';
		
		$subject = $wpforo->subscribe_options['report_email_subject']; 
		$message = $wpforo->subscribe_options['report_email_message']; 
		
		$from_tags = array("[reporter]", "[message]", "[post_url]");
		$to_words   = array(sanitize_text_field($reporter), $reportmsg, $post_url);
		
		$subject = str_replace($from_tags, $to_words, $subject);
		$message = str_replace($from_tags, $to_words, $message);
		
		$admin_email = get_option( 'admin_email' );
		$admin_emails = $wpforo->subscribe_options['admin_emails'];
		$admin_emails = trim($admin_emails);
		$admin_emails = explode(',', $admin_emails);
		$admin_emails = array_map('trim', $admin_emails);
		$admin_email = (isset($admin_emails[0]) && $admin_emails[0]) ? $admin_emails[0] : $admin_email;
		$headers = wpforo_admin_mail_headers();
		
		add_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
		if( wp_mail( $admin_email, $subject, $message, $headers ) ){
			remove_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
		}else{
			remove_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
			$wpforo->notice->add('Can\'t send report email', 'error');
			echo json_encode( $wpforo->notice->get_notices() );
			exit();
		}
		
	############### Sending Email end  ##############
	$wpforo->notice->add('Message has been sent', 'success');
	echo json_encode( $wpforo->notice->get_notices() );
	exit();
}

add_action('wp_ajax_wpforo_sticky_ajax', 'wpf_sticky');
add_action('wp_ajax_nopriv_wpforo_sticky_ajax', 'wpf_sticky');
function wpf_sticky(){
	
	if(!is_user_logged_in()) return;
	
	if( !isset($_POST['postid']) || !( $p_id = intval($_POST['postid']) ) ){ echo 0; exit(); }
	global $wpforo;
	if( $_POST['status'] == 'sticky' ){
		$sql = "UPDATE " . $wpforo->db->prefix . "wpforo_topics SET type = 1 WHERE topicid = " . intval($p_id);
		$wpforo->db->query( $sql );
	}elseif( $_POST['status'] == 'unsticky' ){
		$sql = "UPDATE ".$wpforo->db->prefix ."wpforo_topics SET type = 0 WHERE topicid = " . intval($p_id);
		$wpforo->db->query( $sql );
	}
	echo 1;
	exit();
}

add_action('wp_ajax_wpforo_solved_ajax', 'wpf_solved');
add_action('wp_ajax_nopriv_wpforo_solved_ajax', 'wpf_solved');
function wpf_solved(){
	
	if(!is_user_logged_in()) return;
	
	if( !isset($_POST['postid']) || !( $p_id = intval($_POST['postid']) ) ){ echo 0; exit(); }
	global $wpforo;
	if( $_POST['status'] == 'solved' ){
		$sql = "UPDATE " . $wpforo->db->prefix . "wpforo_posts SET is_answer = 1 WHERE postid = " . intval($p_id);
		$wpforo->db->query( $sql );
	}elseif( $_POST['status'] == 'unsolved' ){
		$sql = "UPDATE ".$wpforo->db->prefix ."wpforo_posts SET is_answer = 0 WHERE postid = " . intval($p_id);
		$wpforo->db->query( $sql );
	}
	echo 1;
	exit();
}

add_action('wp_ajax_wpforo_close_ajax', 'wpf_close');
add_action('wp_ajax_nopriv_wpforo_close_ajax', 'wpf_close');
function wpf_close(){
	
	if(!is_user_logged_in()) return;
	
	if( !isset($_POST['postid']) || !( $p_id = intval($_POST['postid']) ) ){ echo 0; exit(); }
	global $wpforo;
	if( $_POST['status'] == 'closed' ){
		$sql = "UPDATE ".$wpforo->db->prefix ."wpforo_topics SET closed = 0 WHERE topicid = " . intval($p_id);
		$wpforo->db->query( $sql );
	}elseif( $_POST['status'] == 'close' ){
		$sql = "UPDATE ".$wpforo->db->prefix ."wpforo_topics SET closed = 1 WHERE topicid = " . intval($p_id);
		$wpforo->db->query( $sql );
		echo 1;
		exit();
	}
	echo $wpforo->topic->get_topic_url($p_id);
	exit();
}

add_action('wp_ajax_wpforo_edit_ajax', 'wpf_edit');
add_action('wp_ajax_nopriv_wpforo_edit_ajax', 'wpf_edit');
function wpf_edit(){
	
	if(!is_user_logged_in()) return;
	
	if( !isset($_POST['postid']) || !$_POST['postid'] ){ echo 0; exit(); }
	global $wpforo;
	$sql = 'SELECT t.title AS topic_title, p.title AS post_title, p.`body` FROM '.$wpforo->db->prefix.'wpforo_posts p INNER JOIN '.$wpforo->db->prefix.'wpforo_topics t ON t.topicid = p.topicid WHERE p.postid =' . intval($_POST['postid']);
	if($post = $wpforo->db->get_row($sql, ARRAY_A) ){
		$post['body'] = wpautop($post['body']);
		echo json_encode($post);
		exit();
	}
	echo 0;
	exit();
}

add_action('wp_ajax_wpforo_move_ajax', 'wpf_move');
add_action('wp_ajax_nopriv_wpforo_move_ajax', 'wpf_move');
function wpf_move(){
	exit();
}

add_action('wp_ajax_wpforo_delete_ajax', 'wpf_delete');
add_action('wp_ajax_nopriv_wpforo_delete_ajax', 'wpf_delete');
function wpf_delete(){
	
	if(!is_user_logged_in()) return;
	
	global $wpforo;
	
	$resp = array();
	if( $_POST['status'] == 'topic' ){
		if( $wpforo->topic->delete(intval($_POST['postid'])) ){
			$resp = array(
				'postid' => intval($_POST['postid']),
				'location' => $wpforo->forum->get_forum_url(intval($_POST['forumid']))
			);
			$return = 1;
		}else{
			$return = 0;
		}
	}elseif($_POST['status'] == 'reply'){
		if( $wpforo->post->delete(intval($_POST['postid'])) ){
			$resp = array(
				'postid' => intval($_POST['postid'])
			);
			$return = 1;
		}else{
			$return = 0;
		}
	}
	
	$resp['stat'] = $return;
	$resp['notice'] = $wpforo->notice->get_notices();
	echo json_encode( $resp );
	exit();
}

add_action('wp_ajax_wpforo_subscribe_ajax', 'wpf_subscribe');
add_action('wp_ajax_nopriv_wpforo_subscribe_ajax', 'wpf_subscribe');
function wpf_subscribe(){
	
	if(!is_user_logged_in()) return;
	
	global $wpforo;
	
	$args = array(
		'itemid' => intval($_POST['itemid']),
		'type'   => sanitize_text_field($_POST['type']),
		'userid' => intval($wpforo->current_userid)
	);
	
	if(isset($_POST['status']) && $_POST['status'] == 'subscribe'){
		
		$args['confirmkey'] = $wpforo->sbscrb->get_confirm_key();
		
		if( wpforo_feature('subscribe_conf', $wpforo) ){	
			############### Sending Email  ##################
			$confirmlink = $wpforo->sbscrb->get_confirm_link($args);
			$member = $wpforo->current_user;
			$member_name = (isset($wpforo->current_user_display_name) && $wpforo->current_user_display_name) ? $wpforo->current_user_display_name : $wpforo->current_user;
			if($_POST['type'] == 'forum'){
				$forum = $wpforo->forum->get_forum(intval($_POST['itemid']));
				$item_title = $forum['title'];
			}elseif($_POST['type'] == 'topic'){
				$topic  = $wpforo->topic->get_topic(intval($_POST['itemid']));
				$item_title = $topic['title'];
			}
			$subject = $wpforo->subscribe_options['confirmation_email_subject']; 
			$message = $wpforo->subscribe_options['confirmation_email_message']; 
			$from_tags = array("[member_name]", "[entry_title]", "[confirm_link]");
			$to_words   = array(sanitize_text_field($member_name),  '<strong>' . sanitize_text_field($item_title) . '</strong>', '<br><br><a href="' . esc_url($confirmlink) . '"> ' . wpforo_phrase('Confirm my subscription', false) . ' </a>');
			$subject = str_replace($from_tags, $to_words, $subject);
			$message = str_replace($from_tags, $to_words, $message);
			$message = wpforo_kses($message, 'email');
			
			add_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
			$headers = wpforo_mail_headers();
			
			if( wp_mail( $wpforo->current_user_email , sanitize_text_field($subject), $message, $headers ) ){
				if( $wpforo->sbscrb->add($args) ){
					$return = 1;
				}else{
					$return = 0;
				}
			}else{
				$wpforo->notice->add('Can\'t send confirmation email', 'error');
				$return = 0;
			}
			remove_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
			############### Sending Email end  ##############
		}
		else{
			$args['active'] = 1;
			if( $wpforo->sbscrb->add($args) ){
				$return = 1;
			}else{
				$return = 0;
			}
		}
		
	}elseif(isset($_POST['status']) && $_POST['status'] == 'unsubscribe'){
		$subscribe = $wpforo->sbscrb->get_subscribe( $args );
		$return = (int) $wpforo->sbscrb->delete( $subscribe['confirmkey'] );
	}
	
	$resp['stat'] = $return;
	$resp['notice'] = $wpforo->notice->get_notices();
	echo json_encode( $resp );
	exit();
}

############### Sending Email  ##################
function wpforo_set_html_content_type(){
	return 'text/html';
}

function wpforo_wp_mail_from_name($name){
	global $wpforo;
	if(isset($wpforo->subscribe_options['from_name']) && $wpforo->subscribe_options['from_name']){
		return $wpforo->subscribe_options['from_name'];
	}
	else{
		return $name;
	}
}

function wpforo_wp_mail_from_email($email){
	global $wpforo;
	if(isset($wpforo->subscribe_options['from_email']) && $wpforo->subscribe_options['from_email']){
		return $wpforo->subscribe_options['from_email'];
	}
	else{
		return $email;
	}
}

function wpforo_mail_from_name(){
	global $wpforo; if(isset($wpforo->subscribe_options['from_name']) && $wpforo->subscribe_options['from_name']){ return $wpforo->subscribe_options['from_name']; } else {return get_option('blogname');}
}

function wpforo_mail_from_email(){
	global $wpforo; if(isset($wpforo->subscribe_options['from_email']) && $wpforo->subscribe_options['from_email']){return $wpforo->subscribe_options['from_email'];} else {return get_option( 'admin_email' );}
}

function wpforo_mail_headers($from_name = '', $from_email = '', $cc = array(), $bcc = array()){
	global $wpforo; 
	$H = array();
	if(!$from_name) $from_name = wpforo_mail_from_name();
	if(!$from_email) $from_email = wpforo_mail_from_email();
	$H[] = 'From: ' . $from_name . ' <' . $from_email . '>';
	if(!empty($cc)){
		foreach($cc as $c){ $c = sanitize_email($c); $H[] = 'CC: ' . $c; }
	}
	if(!empty($bcc)){
		foreach($bcc as $b){ $b = sanitize_email($b); $H[] = 'BCC: ' . $b; }
	}
	return $H;
}

function wpforo_admin_mail_headers($from_name = '', $from_email = '', $cc = array(), $bcc = array()){
	global $wpforo; 
	$H = array();
	if(!$from_name) $from_name = wpforo_mail_from_name();
	if(!$from_email) $from_email = wpforo_mail_from_email();
	$H[] = 'From: ' . $from_name . ' <' . $from_email . '>';
	if(empty($cc)){
		$cc = trim($wpforo->subscribe_options['admin_emails']);
		$cc = explode(',', $cc);
		$cc = array_map('trim', $cc);
	}
	if(!empty($cc)){
		foreach($cc as $c){ $c = sanitize_email($c); $H[] = 'CC: ' . $c; }
	}
	if(!empty($bcc)){
		foreach($bcc as $b){ $b = sanitize_email($b); $H[] = 'BCC: ' . $b; }
	}
	return $H;
}
############### Sending Email end  ##############

function wpforo_frontend_enqueue(){
	
	global $wpforo;
	
	if( is_wpforo_page() ){
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-dialog');
		wp_register_script( 'wpforo-frontend-js', WPFORO_URL . '/wpf-assets/js/frontend.js', array('jquery'), WPFORO_VERSION, false );
		wp_enqueue_script('wpforo-frontend-js');
		if( wpforo_feature( 'font-awesome', $wpforo) ){
			wp_register_style('wpforo-font-awesome', WPFORO_URL . '/wpf-assets/css/font-awesome/css/font-awesome.min.css', false, '4.6.3' );
			wp_enqueue_style('wpforo-font-awesome');
		}
		if(is_user_logged_in()){
			wp_register_script('wpforo-ajax', WPFORO_URL . '/wpf-assets/js/ajax.js', array('jquery'), WPFORO_VERSION, false);
			wp_enqueue_script('wpforo-ajax');
			wp_localize_script('wpforo-ajax', 'wpf_ajax_obj', array( 'url' => admin_url('admin-ajax.php'), 'phrases' => $wpforo->phrases ));
		}
		if (is_rtl()) {
			wp_register_style('wpforo-style-rtl', WPFORO_TEMPLATE_URL . '/style-rtl.css', false, WPFORO_VERSION );
			wp_enqueue_style('wpforo-style-rtl');
		}
		else{
			wp_register_style('wpforo-style', WPFORO_TEMPLATE_URL . '/style.css', false, WPFORO_VERSION );
			wp_enqueue_style('wpforo-style');
		}
		if( file_exists(WPFORO_TEMPLATE_DIR . '/colors.css') ){
			wp_register_style( 'wpforo-dynamic-style', WPFORO_TEMPLATE_URL . '/colors.css', false, WPFORO_VERSION );
			wp_enqueue_style('wpforo-dynamic-style');
		}
	}
	
	if (is_rtl()) {
		wp_register_style('wpforo-widgets-rtl', WPFORO_TEMPLATE_URL . '/widgets-rtl.css', false, WPFORO_VERSION );
		wp_enqueue_style('wpforo-widgets-rtl');
	}
	else{
		wp_register_style('wpforo-widgets', WPFORO_TEMPLATE_URL . '/widgets.css', false, WPFORO_VERSION );
		wp_enqueue_style('wpforo-widgets');
	}
}
add_action('wp_enqueue_scripts', 'wpforo_frontend_enqueue');

function wpforo_dynamic_style() {
	
	if(!is_wpforo_page()) return;
	
	global $wpforo;
	$inline = false;
	$dynamic_css_file = WPFORO_TEMPLATE_DIR . '/colors.css';
	$dynamic_css_matrix = WPFORO_TEMPLATE_DIR . '/styles/css.php';
	if( isset($wpforo->theme_options) ){ 
		if( !isset($wpforo->theme_options['style']) || !isset($wpforo->theme_options['styles']) ) return false;
		$style = $wpforo->theme_options['style'];
		$styles = $wpforo->theme_options['styles'];
		if( !empty($style) && !empty($styles) ){ 
			foreach( $styles[$style] as $color_key => $color_value ){ 
				if( $color_value ) {
					$COLORS[ 'WPFCOLOR_' . $color_key ] = $color_value; 
				}
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
	extract($COLORS, EXTR_OVERWRITE);
	if( file_exists( $dynamic_css_matrix ) ){
		require_once( $dynamic_css_matrix );
		$css = apply_filters('wpforo_dynamic_css_filter', $css, $COLORS);
	}
	else{
		return false;
	}
	$hach_new = md5($css);
	if( file_exists( $dynamic_css_file ) && filesize($dynamic_css_file) ){
		$css_current = wpforo_get_file_content( $dynamic_css_file );
		if( $css_current ){
			$hach_current = md5($css_current);
			if( $hach_new == $hach_current ){
				//CSS not changed
			}
			else{
				$result = wpforo_write_file( $dynamic_css_file, $css );
				if( isset($result['error']) && $result['error'] ){
					$inline = true;
				}
				else{
					//CSS updated
				}
			}
		}
	}
	else{
		$result = wpforo_write_file( $dynamic_css_file, $css );
		if( isset($result['error']) && $result['error'] ){
			$inline = true;
		}
	}
	if( $inline ){
		$css = preg_replace('|[\r\n\t]+|', '', $css );
		wp_register_style( 'wpforo-dynamic-inline', WPFORO_TEMPLATE_URL . '/css/wpforo_dynamic-inline.css', false, WPFORO_VERSION );
		wp_enqueue_style( 'wpforo-dynamic-inline' );
		wp_add_inline_style( 'wpforo-dynamic-inline', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'wpforo_dynamic_style' );

function wpforo_style_options($css, $COLORS){
	global $wpforo;
	if( !isset($css)) return;
	if( isset($wpforo->style_options['font_size_forum']) && $wpforo->style_options['font_size_forum'] != 17 ){
		$css .= "\r\n#wpforo-wrap .wpforo-forum-title{font-size: " . intval($wpforo->style_options['font_size_forum']) . "px!important; line-height: " . (intval($wpforo->style_options['font_size_forum']) + 1) . "px!important;}";
	}
	if( isset($wpforo->style_options['font_size_topic']) && $wpforo->style_options['font_size_topic'] != 16 ){
		$css .= "\r\n#wpforo-wrap .wpforo-topic-title a { font-size: " . intval($wpforo->style_options['font_size_topic']) . "px!important; line-height: " . (intval($wpforo->style_options['font_size_topic']) + 4) . "px!important; }";
	}
	if( isset($wpforo->style_options['font_size_post_content']) && $wpforo->style_options['font_size_post_content'] != 14 ){
		$css .= "\r\n#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-content {font-size: " . intval($wpforo->style_options['font_size_post_content']) . "px!important; line-height: " . (intval($wpforo->style_options['font_size_post_content']) + 4) . "px!important;}\r\n#wpforo-wrap .wpforo-post .wpf-right .wpforo-post-content p {font-size: " . intval($wpforo->style_options['font_size_post_content']) . "px;}";
	}
	if( isset($wpforo->style_options['custom_css']) ){
		$css .= "\r\n" . stripslashes($wpforo->style_options['custom_css']);
	}
	return $css;
}
add_filter( 'wpforo_dynamic_css_filter' , 'wpforo_style_options' , 10, 2 );

function wpforo_admin_enqueue(){
	global $wpforo;
	$phrases = array(  
		'move' => __('Move', 'wpforo'), 
		'delete' => __('Delete', 'wpforo')
	);
	if( strpos( wpforo_full_url(), '?page=wpforo' ) ){
		if( wpforo_feature( 'font-awesome', $wpforo) ){
			wp_register_style('wpforo-font-awesome', WPFORO_URL . '/wpf-assets/css/font-awesome/css/font-awesome.min.css', false, '4.6.3' );
			wp_enqueue_style('wpforo-font-awesome');
		}
		wp_register_style('wpforo-admin', WPFORO_URL . '/wpf-admin/css/admin.css', false, WPFORO_VERSION );	
		wp_enqueue_style('wpforo-admin');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-position');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-menu');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-color');
		wp_enqueue_script('wp-lists');
		if( strpos( wpforo_full_url(), '?page=wpforo-forums&action' ) ){
			//Just for excluding 'nav-menu' js loading//
		}
		elseif( strpos( wpforo_full_url(), '?page=wpforo-forums' ) ){
			if( isset($_GET['id']) && $_GET['id'] ){
				//Just for excluding 'nav-menu' js loading//
				wp_enqueue_script('postbox');
				wp_enqueue_script('link');
			}
			else{
				wp_enqueue_script('nav-menu');
			}
		}
		elseif( strpos( wpforo_full_url(), '?page=wpforo-settings&tab=styles') ){
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
			wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
			$colorpicker_l10n = array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'));
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n ); 
		}
		elseif( strpos( wpforo_full_url(), '?page=wpforo-community' ) ){
			wp_enqueue_script('postbox');
			wp_enqueue_script('link');
		}
		wp_register_script( 'wpforo-basic_js', WPFORO_URL . '/wpf-admin/js/functions.js', array('jquery'), WPFORO_VERSION, false );
		wp_enqueue_script( 'wpforo-basic_js' );
		wp_localize_script( 'wpforo-basic_js', 'wpforo_admin', array('phrases' => $phrases) );
	}
}
add_action( 'admin_enqueue_scripts', 'wpforo_admin_enqueue' );

function wpforo_admin_permalink_notice() {
    $permalink_structure = get_option( 'permalink_structure' );
	if( !$permalink_structure ){
		$class = 'notice notice-warning';
		$message = __( 'IMPORTANT: wpForo can\'t work with default permalink, please change permalink structure', 'wpforo' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
   
}
add_action( 'admin_notices', 'wpforo_admin_permalink_notice' );

function wpforo_userform_to_wpuser_html_form($wp_user){
	if( is_super_admin() ){
		global $wpforo;
		
		if( is_object($wp_user) ){
			$user = $wpforo->member->get_member($wp_user->ID);
			$groupid = $user['groupid'];
			$timezone = $user['timezone'];
		}
		if( !isset($groupid) ) $groupid = 0;
		if( !isset($timezone) ) $timezone = '';
		
		echo '<table class="form-table">
				<tr class="form-field">
					<th scope="row"><label for="wpforo_usergroup">' . __('wpForo Usergroup', 'wpforo') . '</label></th>
					<td>
						<select name="wpforo_usergroup" id="wpforo_usergroup">';
							$wpforo->usergroup->show_selectbox($groupid);	
		echo '			</select>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row"><label for="wpforo_usertimezone">' . __('wpForo User Timezone', 'wpforo') . '</label></th>
					<td>
						<select name="wpforo_usertimezone" id="wpforo_usertimezone">
						' . wp_timezone_choice($timezone) .	
						'</select>
					</td>
				</tr>
			</table>';
			
	}
}
add_action( 'user_new_form', 'wpforo_userform_to_wpuser_html_form' );
add_action( 'show_user_profile', 'wpforo_userform_to_wpuser_html_form' );
add_action( 'edit_user_profile', 'wpforo_userform_to_wpuser_html_form' );

function wpforo_do_hook_user_register($userid){
	global $wpforo;
	$wpforo->member->synchronize_user($userid);
}
add_action( 'user_register', 'wpforo_do_hook_user_register', 10, 1 );

function wpforo_do_hook_update_profile($userid){
	if( isset($_POST['wpforo_usergroup']) && $_POST['wpforo_usergroup'] ){
		global $wpforo;
		$wpforo->member->edit_profile( array( 'userid' => intval($userid), 
												'groupid' => intval($_POST['wpforo_usergroup']), 
													'site' => esc_url($_POST['url']), 
														'about' => wpforo_kses($_POST['description'], 'user_description'), 
															'timezone' => ( isset($_POST['wpforo_usertimezone']) ? sanitize_text_field($_POST['wpforo_usertimezone']) : '' ) ) );
	}
}
add_action('personal_options_update', 'wpforo_do_hook_update_profile');
add_action('edit_user_profile_update', 'wpforo_do_hook_update_profile');

function wpforo_update_last_login_date($user_login, $user){
	global $wpforo;
	$wpforo->member->edit_profile( array( 'userid' => intval($user->ID), 'last_login' => current_time( 'mysql', 1 ) ) );
}
add_action('wp_login', 'wpforo_update_last_login_date', 10, 2);

function wpforo_do_hook_deleted_user($userid){
	global $wpforo;
	$wpforo->member->delete( intval($userid), TRUE );
}
add_action( 'deleted_user', 'wpforo_do_hook_deleted_user' );

function wpforo_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	global $wpforo;
	if(!wpforo_feature('replace-avatar', $wpforo)) return $avatar;
    $user = false;
    if ( is_numeric( $id_or_email ) ) {
        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );
    }elseif( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }
    }else{
        $user = get_user_by( 'email', $id_or_email );	
    }

    if( $user && is_object( $user ) ){
        if( $src = $wpforo->member->get_avatar_url($user->data->ID) ){
            $avatar = "<img alt='" . esc_attr($alt) . "' src='" . esc_url($src) . "' class='avatar avatar-" . esc_attr($size) . " photo' height='" . esc_attr($size) . "' width='" . esc_attr($size) . "' />";
        }
    }
    return $avatar;
}
add_filter( 'get_avatar' , 'wpforo_avatar' , 10, 5 );

function wpforo_forum_subscribers_mail_sender( $topic ){
	global $wpforo;
	
	$subscribers = $wpforo->sbscrb->get_subscribes( array( 'itemid' => $topic['forumid'], 'type' => 'forum' ) );
	foreach($subscribers as $subscriber){
		$member = $wpforo->member->get_member( $subscriber['userid'] );
		$forum  = $wpforo->forum->get_forum( $topic['forumid'] );
		$unsubscribe_link = $wpforo->sbscrb->get_unsubscribe_link($subscriber['confirmkey']);
		
		############### Sending Email  ##################
			
			$subject = $wpforo->subscribe_options['new_topic_notification_email_subject']; 
		 	$message = $wpforo->subscribe_options['new_topic_notification_email_message']; 
			
			$from_tags = array( "[member_name]", "[forum]", "[unsubscribe_link]", "[topic_title]", "[topic_desc]");
			$to_words  = array( sanitize_text_field($member['display_name']), 
									'<a href="' . esc_url($forum['url']) . '">' . sanitize_text_field($forum['title']) . '</a>', 
										'<br><a target="_blank" href="' . esc_url($unsubscribe_link) . '">' . wpforo_phrase('Unsubscribe', false) . '</a>' , 
											'<a target="_blank" href="' . esc_url($topic['topicurl']) . '">' . sanitize_text_field($topic['title']) . '</a>' , 
												wpforo_text( wpforo_kses( $topic['body'], 'email'), 70, FALSE ) );
			
			$subject = str_replace($from_tags, $to_words, $subject);
			$message = str_replace($from_tags, $to_words, $message);
			
			add_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
			$headers = wpforo_mail_headers();
	 		$email_status = wp_mail( $member['user_email'] , $subject, $message, $headers );
	 		remove_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
	 		
	 	############### Sending Email end  ##############
		
	}
}
add_action( 'wpforo_after_add_topic', 'wpforo_forum_subscribers_mail_sender', 10, 1 );



function wpforo_topic_subscribers_mail_sender( $post ){
	global $wpforo;
	
	$subscribers = $wpforo->sbscrb->get_subscribes( array( 'itemid' => $post['topicid'], 'type' => 'topic' ) );
	foreach($subscribers as $subscriber){
		$member = $wpforo->member->get_member( $subscriber['userid'] );
		$topic  = $wpforo->topic->get_topic( $post['topicid'] );
		$unsubscribe_link = $wpforo->sbscrb->get_unsubscribe_link($subscriber['confirmkey']);
		
		
		############### Sending Email  ##################
			
			$subject = $wpforo->subscribe_options['new_post_notification_email_subject']; 
		 	$message = $wpforo->subscribe_options['new_post_notification_email_message']; 
			
			$from_tags = array( "[member_name]", "[topic]", "[unsubscribe_link]", "[reply_title]", "[reply_desc]");
			$to_words  = array( sanitize_text_field($member['display_name']), 
									'<a href="' . esc_url($post['posturl']) . '">' . sanitize_text_field($topic['title']) . '</a>', 
										'<br><a target="_blank" href="' . esc_url($unsubscribe_link) . '">'.wpforo_phrase('Unsubscribe', false).'</a>' , 
											'<a target="_blank" href="' . esc_url($post['posturl']) . '">' . sanitize_text_field($post['title']) . '</a>' , 
												wpforo_text( wpforo_kses($post['body'], 'email'), 70, FALSE ) );
			
			$subject = str_replace($from_tags, $to_words, $subject);
			$message = str_replace($from_tags, $to_words, $message);
			
			add_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
			$headers = wpforo_mail_headers();
	 		$email_status = wp_mail( $member['user_email'] , $subject, $message, $headers );
	 		remove_filter( 'wp_mail_content_type', 'wpforo_set_html_content_type' );
	 		
	 	############### Sending Email end  ##############
		
	}
}
add_action( 'wpforo_after_add_post', 'wpforo_topic_subscribers_mail_sender', 11, 1 );


function wpforo_add_default_attachment($args){
	if( !empty($_FILES['attachfile']) && !empty($_FILES['attachfile']['name']) ){
		global $wpforo;
		
		$name = sanitize_file_name($_FILES['attachfile']['name']); //myimg.png
		$type = sanitize_mime_type($_FILES['attachfile']['type']); //image/png
		$tmp_name = sanitize_text_field($_FILES['attachfile']['tmp_name']); //D:\wamp\tmp\php986B.tmp
		$error = sanitize_text_field($_FILES['attachfile']['error']); //0
		$size = intval($_FILES['attachfile']['size']); //6112
		
		$phpFileUploadErrors = array(
		    0 => 'There is no error, the file uploaded with success',
		    1 => 'The uploaded file size is too big',
		    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		    3 => 'The uploaded file was only partially uploaded',
		    4 => 'No file was uploaded',
		    6 => 'Missing a temporary folder',
		    7 => 'Failed to write file to disk.',
		    8 => 'A PHP extension stopped the file upload.',
		);
		
		if( $error ){
			$wpforo->notice->add($phpFileUploadErrors[$error], 'error');
			return FALSE;
		}elseif( $size > $wpforo->post_options['max_upload_size'] ){
			$wpforo->notice->add('The uploaded file size is too big', 'error');
			return FALSE;
		}
		
		if(function_exists('pathinfo')){
			$ext = pathinfo($name, PATHINFO_EXTENSION);
		}
		else{
			$ext = substr(strrchr($name, '.'), 1);
		}
		$ext = strtolower($ext);
		$mime_types = get_allowed_mime_types();
		$mime_types = array_flip($mime_types);
		if(!empty($mime_types)){
			$allowed_types = implode('|', $mime_types);
			$expld = explode('|', $allowed_types);
			if( !in_array($ext, $expld) ){
				$wpforo->notice->add('File type is not allowed', 'error');
				return FALSE;
			}
		}
		
		$wp_upload_dir = wp_upload_dir();
		$uplds_dir = $wp_upload_dir['basedir']."/wpforo";
		$attach_dir = $wp_upload_dir['basedir']."/wpforo/default_attachments";
		$attach_url = $wp_upload_dir['baseurl']."/wpforo/default_attachments";
		if(!is_dir($uplds_dir)) wp_mkdir_p($uplds_dir);
		if(!is_dir($attach_dir)) wp_mkdir_p($attach_dir);
		
        $fnm = pathinfo($name, PATHINFO_FILENAME);
        $fnm = str_replace(' ', '-', $fnm);
        while(strpos($fnm, '--') !== FALSE) $fnm = str_replace('--', '-', $fnm);
        $fnm = preg_replace("/[^-a-zA-Z0-9]/", "", $fnm);
        $fnm = trim($fnm, "-");
        $fnm_empty = ( $fnm ? FALSE : TRUE );
        
        $file_name = $fnm . "." . $ext;
		
		$attach_fname = current_time( 'timestamp', 1 ).( !$fnm_empty ? '-' : '' ) . $file_name;
		$attach_path = $attach_dir . "/" . $attach_fname;
		
		if( is_dir($attach_dir) && move_uploaded_file($tmp_name, $attach_path) ){
			$attach_id = wpforo_insert_to_media_library( $attach_path, $fnm );
			$args['body'] .= "\r\n" . '<div id="wpfa-' . $attach_id . '" class="wpforo-attached-file"><a class="wpforo-default-attachment" href="' . esc_url($attach_url.'/'.$attach_fname) . '" target="_blank"><i class="fa fa-paperclip"></i>' . esc_html(basename($name)) . '</a></div>';
			$args['has_attach'] = 1;
		}else{
			$wpforo->notice->add('Can`t upload file', 'error');
			return FALSE;
		}
	}
	return $args;
}

function wpforo_delete_attachment( $attach_post_id ){
	global $wpdb;
	if(!$attach_post_id) return;
	$posts = $wpdb->get_results("SELECT `postid`, `body` FROM `" . $wpdb->prefix . "wpforo_posts` WHERE `body` LIKE '%wpfa-" . intval( $attach_post_id ) . "%'", ARRAY_A );
	if(!empty($posts) || is_array($posts)){
		foreach( $posts as $post ){
			$body = preg_replace('|<div[^><]*id=[\'\"]+wpfa-' . $attach_post_id . '[\'\"]+[^><]*>.+?</div>|is', '<div class="wpforo-attached-file wpfa-deleted">' . wpforo_phrase('Attachment removed', FALSE) . '</div>', $post['body'] );
			if( $body ) $wpdb->query("UPDATE `" . $wpdb->prefix . "wpforo_posts` SET `body` = '" . esc_sql( $body ) . "' WHERE `postid` = " . intval($post['postid']));
		}
	}
}

add_action( 'delete_attachment', 'wpforo_delete_attachment', 10 );
add_filter( 'wpforo_add_topic_data_filter', 'wpforo_add_default_attachment' );
add_filter( 'wpforo_edit_topic_data_filter', 'wpforo_add_default_attachment' );
add_filter( 'wpforo_add_post_data_filter', 'wpforo_add_default_attachment' );
add_filter( 'wpforo_edit_post_data_filter', 'wpforo_add_default_attachment' );

add_filter('wpforo_body_text_filter', 'wp_encode_emoji', 9);
add_filter('wpforo_body_text_filter', 'convert_smilies');
?>