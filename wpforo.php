<?php
/*
* Plugin Name: Forum - wpForo
* Plugin URI: http://wpforo.com
* Description: Next Generation of WordPress Forum Softwares. Everything you need to run an efficient and professional community. Powerful and beautiful bulletin board with unique features.
* Author: gVectors Team (A. Chakhoyan, R. Hovhannisyan)
* Author URI: http://gvectors.com/
* Version: 1.0.2
* Text Domain: wpforo
* Domain Path: /wpf-languages
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
if( !defined( 'WPFORO_VERSION' ) ) define('WPFORO_VERSION', '1.0.2');

function wpforo_load_plugin_textdomain() { load_plugin_textdomain( 'wpforo', FALSE, basename( dirname( __FILE__ ) ) . '/wpf-languages/' ); }
add_action( 'plugins_loaded', 'wpforo_load_plugin_textdomain' );

if( !class_exists( 'wpForo' ) ) {

	define('WPFORO_DIR', rtrim( plugin_dir_path( __FILE__ ), '/'));
	define('WPFORO_URL', rtrim( plugins_url( plugin_basename(dirname(__FILE__))), '/'));
	define('WPFORO_FOLDER', rtrim( plugin_basename(dirname(__FILE__)), '/'));
	define('WPFORO_BASENAME', plugin_basename(__FILE__)); //wpforo/wpforo.php
	
	define('WPFORO_THEME_DIR', WPFORO_DIR . '/wpf-themes' );
	define('WPFORO_THEME_URL', WPFORO_URL . '/wpf-themes' );
	
	include( WPFORO_DIR . '/wpf-includes/wpf-hooks.php' );
	include( WPFORO_DIR . '/wpf-includes/wpf-actions.php');
	include( WPFORO_DIR . '/wpf-includes/functions.php' );
	if(is_admin()) {
		include( WPFORO_DIR . '/wpf-includes/functions-installation.php' );
	}
	include( WPFORO_DIR . '/wpf-includes/functions-integration.php' );
	include( WPFORO_DIR . '/wpf-includes/functions-template.php' );
	include( WPFORO_DIR . '/wpf-includes/class-forums.php' );
	include( WPFORO_DIR . '/wpf-includes/class-topics.php' );
	include( WPFORO_DIR . '/wpf-includes/class-posts.php' );
	include( WPFORO_DIR . '/wpf-includes/class-usergroups.php' );
	include( WPFORO_DIR . '/wpf-includes/class-members.php' );
	include( WPFORO_DIR . '/wpf-includes/class-permissions.php' );
	include( WPFORO_DIR . '/wpf-includes/class-phrases.php');
	include( WPFORO_DIR . '/wpf-includes/class-subscribes.php' );
	include( WPFORO_DIR . '/wpf-includes/class-template.php' );
	include( WPFORO_DIR . '/wpf-includes/class-notices.php' );
	include( WPFORO_DIR . '/wpf-includes/class-feed.php' );
	
	class wpForo{
	
		public $options = array();
		public $db;
		public $phrases;
		public $theme;
		public $current_object;
		public $menu = array();
		
		public	function __construct(){
			$this->options();
			$this->setup();
		}
		
		public	function init(){
			$this->member->init_current_user();
			$this->init_current_object();
			$this->tpl->init_member_templates();
			$this->tpl->init_nav_menu();
			wpforo_actions();
		}
		
		private function options(){
			global $wpdb;
			$this->db = $wpdb;
			$this->file = __FILE__;
			$this->error = NULL;
			$this->basename = plugin_basename( $this->file );
			
			//OPTIONS
			$this->permastruct = trim( get_wpf_option('wpforo_permastruct'), '/' );
			$this->use_home_url = get_wpf_option('wpforo_use_home_url');
			$this->url = ( $this->use_home_url ? esc_url( home_url('/') ) : esc_url( home_url('/') ) . $this->permastruct . "/" );
			$this->general_options = get_wpf_option( 'wpforo_general_options');
			$this->pageid = get_wpf_option( 'wpforo_pageid');
			$this->default_groupid = get_wpf_option('wpforo_default_groupid') ? get_option('wpforo_default_groupid') : 3;
			$this->usergroup_cans = get_wpf_option('wpforo_usergroup_cans');
			$this->forum_options = get_wpf_option('wpforo_forum_options');
			$this->forum_cans = get_wpf_option('wpforo_forum_cans');
			$this->post_options = get_wpf_option('wpforo_post_options');
			$this->member_options = get_wpf_option('wpforo_member_options');
			$this->subscribe_options = get_wpf_option('wpforo_subscribe_options');
			$this->countries = get_wpf_option('wpforo_countries');
			$this->features = get_wpf_option('wpforo_features');
			$this->style_options = get_wpf_option('wpforo_style_options');
			$this->theme_options = get_wpf_option('wpforo_theme_options');
			$this->theme = $this->theme_options['folder'];
			//CONSTANTS
			define('WPFORO_BASE_URL', $this->url );
			define('WPFORO_THEME', $this->theme );
			define('WPFORO_TEMPLATE_DIR', WPFORO_THEME_DIR . '/' . $this->theme );
			define('WPFORO_TEMPLATE_URL', WPFORO_THEME_URL . '/' . $this->theme );
		}
		
		private function setup(){
			add_action( 'activate_'   . $this->basename, 'wpforo_activation' );
			add_action( 'deactivate_' . $this->basename, 'wpforo_deactivation' );
		}
		
		public function phrases(){
			if($this->general_options){
				$phrases = $this->phrase->get_phrases( array( 'langid' => $this->general_options['lang'] ) );
				foreach($phrases as $phrase){
					$this->phrases[addslashes(strtolower($phrase['phrase_key']))] = $phrase['phrase_value'];
				}
			}
		}
		
		public function get_statistic(){
			$stats = array();
			$stats['forums'] = $this->forum->get_count();
			$stats['topics'] = $this->topic->get_count();
			$stats['posts'] = $this->post->get_count();
			$stats['members'] = $this->member->get_count();
			$stats['online_members_count'] = $this->member->online_members_count();
			
			$stats['last_post_title'] = '';
			$stats['last_post_url'] = '';
			
			$posts = $this->topic->get_topics( array( 'orderby' => 'modified', 'order' => 'DESC', 'row_count' => 1 ) );
			if(isset($posts[0]) && !empty($posts[0])){
				$stats['last_post_title'] = $posts[0]['title'];
				$stats['last_post_url'] = $this->post->get_post_url($posts[0]['last_post']);
			}
			
			$stats['newest_member_dname'] = '';
			$stats['newest_member_profile_url'] = '';
			
			$members = $this->member->get_members( array( 'orderby' => 'userid', 'order' => 'DESC', 'row_count' => 1 ) );
			if(isset($members[0]) && !empty($members[0])){
				$stats['newest_member_dname'] = $members[0]['display_name'] ? $members[0]['display_name'] : $members[0]['user_nicename'];
				$stats['newest_member_profile_url'] = $this->member->get_profile_url($members[0]['ID']);
			}
			
			return apply_filters('wpforo_get_statistic_array_filter', $stats);
		}
		
		public function init_current_object($url = ''){
			$this->current_object = array('template' => '', 'paged' => 1);
			if( !is_wpforo_page($url) ) return;
			
			if(!$url) $url = wpforo_full_url();
			
			$current_url = wpforo_get_url_query_vars_str($url);
			
			if( $this->use_home_url ) $this->permastruct = '';
			
			$current_object = array();
			$current_object['template'] = '';
			
			$wpf_url = preg_replace( '#^/?'.preg_quote($this->permastruct).'#isu', '' , $current_url, 1 );
			$wpf_url = preg_replace('#\/?\?.*$#is', '', $wpf_url);
			$wpf_url_parse = explode('/', trim($wpf_url, '/'));
			$wpf_url_parse = array_reverse($wpf_url_parse);
			
			if(isset($_GET['wpfs'])){ 
				$current_object['template'] = 'search';
			}
			
			if( isset($_GET['wpforo']) ){
				switch($_GET['wpforo']){
					case 'signup':
						if(!is_user_logged_in()) $current_object['template'] = 'register';
					break;
					case 'signin':
						if(!is_user_logged_in()) $current_object['template'] = 'login';
					break;
					case 'logout':
						wp_logout();
						wp_redirect( preg_replace('#\?.*$#is', '', wpforo_full_url()) );
						exit();
					break;
				}
			}
			
			if(in_array('paged', $wpf_url_parse)){
				foreach($wpf_url_parse as $key => $value){
					if( $value == 'paged'){
						unset($wpf_url_parse[$key]);
						break;
					}
					if(is_numeric($value)) $paged = intval($value);
					
					unset($wpf_url_parse[$key]);
				}
			}
			if(isset($_GET['wpfpaged']) && intval($_GET['wpfpaged'])) $paged = intval($_GET['wpfpaged']);
			$current_object['paged'] = (isset($paged) && $paged) ? $paged : 1;
			
			$wpf_url_parse = array_values($wpf_url_parse);
			
			if( !isset($current_object['template']) || !$current_object['template'] ) {
				if(in_array('members', $wpf_url_parse) && $wpf_url_parse[0] == 'members'){
					$current_object['template'] = 'members';
				}elseif(in_array('profile', $wpf_url_parse)){
					$current_object['template'] = 'profile';
					foreach($wpf_url_parse as $value){
						if( $value == 'profile') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('messages', $wpf_url_parse)){
					$current_object['template'] = 'messages';
					foreach($wpf_url_parse as $value){
						if( $value == 'messages') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('account', $wpf_url_parse)){
					$current_object['template'] = 'account';
					foreach($wpf_url_parse as $value){
						if( $value == 'account') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('activity', $wpf_url_parse)){
					$current_object['template'] = 'activity';
					foreach($wpf_url_parse as $value){
						if( $value == 'activity') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('subscriptions', $wpf_url_parse)){
					$current_object['template'] = 'subscriptions';
					foreach($wpf_url_parse as $value){
						if( $value == 'subscriptions') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}else{
					$current_object['template'] = 'forum';
					if($wpf_url_parse[0] && $wpf_url_parse[0] != 'wpforo'){
						
						if(isset($wpf_url_parse[1])){
							$current_object['topic_slug'] = $wpf_url_parse[0];
							$current_object['forum_slug'] = $wpf_url_parse[1];
							$current_object['template'] = 'post';
						}else{
							$current_object['forum_slug'] = $wpf_url_parse[0];
							$current_object['template'] = 'topic';
						}
					}
				}
			}
			
			if( isset($current_object['userid']) || isset($current_object['username']) ){
				$args = array();
				if(isset($current_object['userid'])) $args['userid'] = $current_object['userid'];
				if(isset($current_object['username'])) $args['username'] = $current_object['username'];
				$selected_user = $this->member->get_member($args);
				if(isset($current_object['userid']) && empty($selected_user)) $selected_user = $this->member->get_member(array('username' => $current_object['userid']));
				if(!empty($selected_user)){
					$current_object['user'] = $selected_user;
					$current_object['userid'] = $selected_user['ID'];
					$current_object['username'] = $selected_user['user_nicename'];
					
					switch($current_object['template']){
						case 'activity':
							$args = array(
								'offset' => ($current_object['paged'] - 1) * $this->post_options['posts_per_page'],
								'row_count' => $this->post_options['posts_per_page'],
								'userid' => $current_object['userid'],
								'order' => 'DESC'
							);
							$current_object['items_count'] = 0;
							$current_object['activities'] = $this->post->get_posts( $args, $current_object['items_count']);
						break;
						case 'subscriptions':
							$args = array(
								'offset' => ($current_object['paged'] - 1) * $this->post_options['posts_per_page'],
								'row_count' => $this->post_options['posts_per_page'],
								'userid' => $current_object['userid'],
								'order' => 'DESC'
							);
							$current_object['items_count'] = 0;
							$current_object['subscribes'] = $this->sbscrb->get_subscribes( $args, $current_object['items_count']);
						break;
					}
					
				}else{
					$current_object['user'] = array();
					$current_object['userid'] = 0;
					$current_object['username'] = '';
				}
			}
			
			if(isset($current_object['forum_slug']) && $current_object['forum_slug']){
				$forum = $this->forum->get_forum(array('slug' => $current_object['forum_slug']));
				if(!empty($forum)){
					$current_object['forum'] = $forum;
					$current_object['forumid'] = $forum['forumid'];
					$current_object['forum_desc'] = $forum['description'];
					$current_object['forum_meta_key'] = $forum['meta_key'];
					$current_object['forum_meta_desc'] = $forum['meta_desc'];
				}else{
					$current_object['forum'] = array();
					$current_object['forumid'] = 0;
					$current_object['forum_desc'] = '';
					$current_object['forum_meta_key'] = '';
					$current_object['forum_meta_desc'] = '';
				}
			}
			
			if(isset($current_object['topic_slug']) && $current_object['topic_slug']){
				$topic = $this->topic->get_topic(array('slug' => $current_object['topic_slug']));
				if(!empty($topic)){
					$current_object['topic'] = $topic;
					$current_object['topicid'] = $topic['topicid'];
				}else{
					$current_object['topic'] = array();
					$current_object['topicid'] = 0;
				}
			}
			
			$this->current_object = apply_filters('wpforo_current_object_filter', $current_object);
		}
	}
	
	$wpforo = new wpForo();
	$wpforo->phrase = new wpForoPhrase( $wpforo );
	$wpforo->phrases();
	$wpforo->forum = new wpForoForum( $wpforo );
	$wpforo->topic = new wpForoTopic( $wpforo );
	$wpforo->post = new wpForoPost( $wpforo );
	$wpforo->usergroup = new wpForoUsergroup( $wpforo );
	$wpforo->member = new wpForoMember( $wpforo );
	$wpforo->perm = new wpForoPermissions( $wpforo );
	$wpforo->sbscrb = new wpForoSubscribe( $wpforo );
	$wpforo->tpl = new wpForoTemplate( $wpforo );
	$wpforo->notice = new wpForoNotices( $wpforo );
	$wpforo->feed = new wpForoFeed( $wpforo );
	if(is_admin()) include( WPFORO_DIR .'/wpf-admin/admin.php' );
	$GLOBALS['wpforo'] = $wpforo;
	add_action('init', array($wpforo, 'init'));
}
?>