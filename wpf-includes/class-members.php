<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

class wpForoMember{
	
	private $wpforo;
	private static $cache = array();
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
 
 	private function add_profile($args){
 		if(empty($args)) return FALSE;
 		if(!isset($args['userid']) || !$args['userid'] || !isset($args['username']) || !$args['username'] ) return FALSE;
		extract( $args, EXTR_OVERWRITE );
		$this->reset($userid);
		return $this->wpforo->db->insert(
			$this->wpforo->db->prefix . 'wpforo_profiles', 
			array(  'userid' => intval($userid), 
					'username' => sanitize_user($username), 
					'groupid' => intval((isset($groupid) && $groupid ? $groupid : $this->wpforo->default_groupid)), 
					'site' => (isset($site) ? sanitize_text_field($site) : '' ), 
					'timezone' => ( isset($timezone) ? sanitize_text_field($timezone) : 'UTC+0' ), 
					'about' => ( isset($about) ? stripslashes( wpforo_kses(trim($about), 'user_description') ) : '' ), 
					'last_login' => ( isset($last_login) ? $last_login : current_time('mysql', 1) ) ), 
			array( '%d', '%s', '%d', '%s', '%s', '%s', '%s' )
		);
	}
	
	function edit_profile($args){
		if(empty($args)) return FALSE;
 		if( !isset($args['userid']) || !$args['userid'] ) return FALSE;
		extract( $args, EXTR_OVERWRITE );
		
		$fields = array();
		$fields_types = array();
		
		if(isset($last_login) && $last_login){
			$fields['last_login'] = sanitize_text_field($last_login);
			$fields_types[] = '%s';
		}
		
		if(isset($groupid) && $groupid){
			if( $this->wpforo->current_user_groupid == 1 || current_user_can('administrator') ){
				$fields['groupid'] = intval($groupid);
				$fields_types[] = '%d';
			}
		}
		
		if(isset($title) && $title){
			$fields['title'] = sanitize_text_field(trim($title));
			$fields_types[] = '%s';
		}
		if(isset($site)){
			$fields['site'] = sanitize_text_field(trim($site));
			$fields_types[] = '%s';
		}
		if(isset($icq)){
			$fields['icq'] = sanitize_text_field(trim($icq));
			$fields_types[] = '%s';
		}
		if(isset($aim)){
			$fields['aim'] = sanitize_text_field(trim($aim));
			$fields_types[] = '%s';
		}
		if(isset($yahoo)){
			$fields['yahoo'] = sanitize_text_field(trim($yahoo));
			$fields_types[] = '%s';
		}
		if(isset($msn)){
			$fields['msn'] = sanitize_text_field(trim($msn));
			$fields_types[] = '%s';
		}
		if(isset($facebook)){
			$fields['facebook'] = sanitize_text_field(trim($facebook));
			$fields_types[] = '%s';
		}
		if(isset($twitter)){
			$fields['twitter'] = sanitize_text_field(trim($twitter));
			$fields_types[] = '%s';
		}
		if(isset($gtalk)){
			$fields['gtalk'] = sanitize_text_field(trim($gtalk));
			$fields_types[] = '%s';
		}
		if(isset($skype)){
			$fields['skype'] = sanitize_text_field(trim($skype));
			$fields_types[] = '%s';
		}
		if(isset($signature)){
			$fields['signature'] = stripslashes(wpforo_kses(trim($signature), 'user_description'));
			$fields_types[] = '%s';
		}
		if(isset($about)){
			$fields['about'] = stripslashes(wpforo_kses(trim($about), 'user_description'));
			$fields_types[] = '%s';
		}
		if(isset($occupation)){
			$fields['occupation'] = stripslashes(sanitize_text_field(trim($occupation)));
			$fields_types[] = '%s';
		}
		if(isset($location)){
			$fields['location'] = stripslashes(sanitize_text_field(trim($location)));
			$fields_types[] = '%s';
		}
		if(isset($timezone)){
			$fields['timezone'] = sanitize_text_field(trim($timezone));
			$fields_types[] = '%s';
		}
		if(isset($avatar_type) && $avatar_type != 'gravatar' && isset($avatar_url) && $avatar_url){
			$fields['avatar'] = esc_url(trim($avatar_url));
			$fields_types[] = '%s';
		}
		if(isset($avatar_type) && $avatar_type == 'gravatar'){
			$fields['avatar'] = '';
			$fields_types[] = '%s';
		}
		
		$this->reset($userid);
		
		return $this->wpforo->db->update(
			$this->wpforo->db->prefix.'wpforo_profiles',
			$fields,
			array('userid' => intval($userid)),
			$fields_types,
			array('%d')
		);
	}
 	
 	function create($args){
		if(!wpforo_feature('user-register', $this->wpforo)){
			$this->wpforo->notice->add('User registration is disabled.', 'error');
			return FALSE;
		}
		if(!empty($args) && is_array($args)){
			extract($args, EXTR_OVERWRITE);
			$user_login = sanitize_user( $user_login );
			$user_email = apply_filters( 'user_registration_email', sanitize_email($user_email) );
			$user_pass1 = trim(substr($user_pass1, 0, 100));
			$user_pass2 = trim(substr($user_pass2, 0, 100));
			if ( $user_login == '' ) {
				$this->wpforo->notice->add('Username is missed.', 'error');
				return FALSE;
			}elseif ( ! validate_username( $user_login ) ) {
				$this->wpforo->notice->add('Illegal character in username.', 'error');
				$user_login = '';
				return FALSE;
			}elseif( strlen($user_login) < 3 || strlen($user_login) > 15 ){
				$this->wpforo->notice->add('Username length must be between 3 characters and 15 characters.', 'error');
				return FALSE;
			}elseif ( username_exists( $user_login ) ) {
				$this->wpforo->notice->add('Username exists. Please insert another.', 'error');
				return FALSE;
			}elseif ( $user_email == '' ) {
				$this->wpforo->notice->add('Insert your Email address.', 'error');
				return FALSE;
			}elseif ( ! is_email( $user_email ) ) {
				$this->wpforo->notice->add('Invalid Email address', 'error');
				$user_email = '';
				return FALSE;
			}elseif ( email_exists( $user_email ) ) {
				$this->wpforo->notice->add('Email address exists. Please insert another.', 'error');
				return FALSE;
			}elseif( strlen($user_pass1) < 6 || strlen($user_pass1) > 20 ){
				$this->wpforo->notice->add('Password length must be between 6 characters and 20 characters.', 'error');
				return FALSE;
			}elseif($user_pass1 != $user_pass2){
				$this->wpforo->notice->add('Password mismatch.', 'error');
				return FALSE;
			}else{
				$user_id = wp_create_user( $user_login, $user_pass1, $user_email );
				if ( !is_wp_error( $user_id ) && $user_id ) {
					$creds = array('user_login' => $user_login, 'user_password' => $user_pass1 );
					wp_signon($creds);
					$this->wpforo->notice->add('Success! Thank you Dear Friend', 'success');
					return $user_id;
				}
			}
		}
		if(!empty($user_id->errors)){
			$args = array();
			foreach($user_id->errors as $u_err) $args[] = $u_err[0];
			$this->wpforo->notice->add($args, 'error');
			return FALSE;
		}
		$this->wpforo->notice->add('Registration Error', 'error');
		return FALSE;
	}
 	
	function edit( $args = array() ){
		
		if( empty($args) && empty($_REQUEST['member']) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['member']) ) $args = $_REQUEST['member'];
		extract($args, EXTR_OVERWRITE);
		
		if( isset($userid) && isset($display_name) && isset($user_email) ){
			$userid = intval($userid);
			$display_name = sanitize_text_field($display_name);
			$user_email = sanitize_email($user_email);
			if ( ! is_email( $user_email ) ) {
				$this->wpforo->notice->add('Invalid Email address', 'error');
				$user_email = '';
				return FALSE;
			}elseif ( ( $owner_id = email_exists( $user_email ) ) && ( $owner_id != $userid ) ) {
				$this->wpforo->notice->add('This email address is already registered. Please insert another.', 'error');
				return FALSE;
			}
			
			if ( is_user_logged_in() ) {
				$current_user_id = get_current_user_id();
				if(!$this->wpforo->perm->user_can_manage_user( $current_user_id, $userid )){
					$this->wpforo->notice->add('Permission denied', 'error');
					return FALSE;
				}
			}
			else{
				$this->wpforo->notice->add('Permission denied', 'error');
				return FALSE;
			}
			
			if( $display_name && $user_email ){
				$this->wpforo->db->update(
					$this->wpforo->db->prefix.'users',
					array(
						'display_name' => sanitize_text_field($display_name),
						'user_email' => sanitize_email($user_email)
					),
					array('ID' => $userid),
					array(
						'%s',
						'%s'
					),
					array('%d')
				);
				$this->reset($userid);
			}
			
			if( FALSE !== $this->edit_profile($args) ){
				$this->wpforo->notice->add('Your profile data have been successfully updated.', 'success');
				return $userid;
			}
		}
		
		$this->wpforo->notice->add('Something wrong with profile data.', 'error');
		return FALSE;
	}
	
	function upload_avatar(){
		if( !isset($_POST['member']['userid']) || !$userid = intval($_POST['member']['userid']) ) return;
		if( !$user = $this->get_member($userid) ) return;
		$username = $user['user_nicename'];
		if(isset($_FILES['avatar']) && !empty($_FILES['avatar']) && isset($_FILES['avatar']['name']) && $_FILES['avatar']['name']){
			
			$name = sanitize_file_name($_FILES['avatar']['name']); //myimg.png
			$type = sanitize_mime_type($_FILES['avatar']['type']); //image/png
			$tmp_name = sanitize_text_field($_FILES['avatar']['tmp_name']); //D:\wamp\tmp\php986B.tmp
			$error = sanitize_text_field($_FILES['avatar']['error']); //0
			$size = intval($_FILES['avatar']['size']); //6112
		
			if( $error ){
				$error = wpforo_file_upload_error($error);
				$this->wpforo->notice->clear();
				$this->wpforo->notice->add($error, 'error');
				return FALSE;
			}
			
			$upload_dir = wp_upload_dir();
			$uplds_dir = $upload_dir['basedir']."/wpforo";
			$avatar_dir = $upload_dir['basedir']."/wpforo/avatars";
			if(!is_dir($uplds_dir)){
				wp_mkdir_p($uplds_dir);
			}
			if(!is_dir($avatar_dir)){
				wp_mkdir_p($avatar_dir);
			}
			
			$ext = pathinfo($name, PATHINFO_EXTENSION);
			if( !wpforo_is_image($ext) ){
				$this->wpforo->notice->clear();
				$this->wpforo->notice->add('Incorrect file format. Allowed formats: jpeg, jpg, png, gif.', 'error');
				return false;
			}
			$avatar_fname = $username."_".$userid.".".$ext;
			$avatar_path = $avatar_dir."/".$avatar_fname;
			
			if(is_dir($avatar_dir)){
				if(move_uploaded_file($tmp_name, $avatar_path)) {
					$image = wp_get_image_editor( $avatar_path );
					if ( ! is_wp_error( $image ) ) {
						$image->resize( 150, 150, true );
						$image->save( $avatar_path );
					}
					$blog_url = preg_replace('|^https?\:|is', '', $upload_dir['baseurl']);
					$this->wpforo->db->update($this->wpforo->db->prefix.'wpforo_profiles', array('avatar' => $blog_url . "/wpforo/avatars/" . $avatar_fname), array('userid' => intval($userid)), array('%s'), array('%d'));
					$this->reset($userid);
				}
			} 
		}
	}
	
	function synchronize_user($userid){
		if(!$userid) return FALSE;
		if( $user = get_userdata($userid) ){
			if( in_array('administrator', $user->roles) ){
				$groupid = 1;
			}elseif( in_array('editor', $user->roles) ){
				$groupid = 2;
			}elseif( in_array('customer', $user->roles) ){
				$groupid = 5;
			}else{
				$groupid = $this->wpforo->default_groupid;
			}
			$insert_groupid = (isset($_POST['wpforo_usergroup'])) ? intval($_POST['wpforo_usergroup']) : $groupid;
			$insert_timezone = (isset($_POST['wpforo_usertimezone'])) ? sanitize_text_field($_POST['wpforo_usertimezone']) : '';
			$about = get_user_meta( $userid, 'description', true );
			return $this->add_profile( 
							array(  'userid' => intval($userid), 
									'username' => sanitize_user($user->user_login), 
									'groupid' => intval($insert_groupid), 
									'site' => esc_url($user->user_url), 
									'timezone' => sanitize_text_field($insert_timezone), 
									'about' => stripslashes( wpforo_kses(trim($about), 'user_description') ), 
									'last_login' => sanitize_text_field($user->user_registered) ) );
		}
		return FALSE;
	}
	
	function synchronize_users(){
		$sql = "SELECT `ID` FROM `".$this->wpforo->db->prefix."users` WHERE `ID` NOT IN( SELECT `userid` FROM `".$this->wpforo->db->prefix."wpforo_profiles` )";
		$userids = $this->wpforo->db->get_col($sql);
		if( !empty($userids) ){
			foreach($userids as $userid){
				$this->synchronize_user($userid);
			}
		}
	}
	
	function get_member($args = array(), $cache = false){
		
		if(is_array($args)){
			
			$default = array(
			  'userid' => NULL, // userid
			  'username' => '' // username
			);
			
		}else{
			
			$default = array(
			  'userid' => $args, // userid
			  'username' => '' // username
			);
			
		}
		
		$args = wpforo_parse_args( $args, $default );
		
		if(isset($args['userid'])){
			if( $cache && isset(self::$cache['user'][$args['userid']]) ){
				return self::$cache['user'][$args['userid']];
			}
		}
		
		$user_meta_obj = true;
		
		if(!empty($args)){
			
			extract($args, EXTR_OVERWRITE);
			
			$do_db_cache =  wpforo_feature('member_cashe', $this->wpforo);
			
			$userid = intval($userid);
			$username = sanitize_user($username);
			
			if( $do_db_cache ){
				if( $username != '' ){
					$user_obj = get_user_by( 'user_nicename', $username );
					if(!empty($user_obj)) $userid = $user_obj->ID;
					$member = get_user_meta( $userid, '_wpf_member_obj', true );
				}
				elseif( $userid != NULL ){
					$member = get_user_meta( $userid, '_wpf_member_obj', true );
				}
				else{
					$user_meta_obj = true;
				}
			}
			else{
				$member = array();
			}
			
			if(empty($member)){
				$user_meta_obj = false;
				$sql = "SELECT *, ug.name AS groupname FROM `".$this->wpforo->db->prefix."users` u 
				LEFT JOIN `".$this->wpforo->db->prefix."wpforo_profiles` p ON p.`userid` = u.`ID`
				LEFT JOIN `".$this->wpforo->db->prefix."wpforo_usergroups` ug ON ug.`groupid` = p.`groupid`";
				$wheres = array();
				if($userid != NULL)  $wheres[] = "`ID` = "   . intval($userid);
				if($username != '')   $wheres[] = "`user_nicename` = '"   . esc_sql($username) . "'";
				if( !empty($wheres) ) $sql .= " WHERE " . implode($wheres, " AND ");
				$member = $this->wpforo->db->get_row($sql, ARRAY_A);
			}
			
			if(!empty($member)) {
				if( $do_db_cache ){
					if(!$user_meta_obj) {
						$member['profile_url'] = $this->profile_url( $member );
						$member['stat'] = $this->get_stat( $member, false, true );
						update_user_meta( $userid, '_wpf_member_obj', $member );
					}
				}
				else{
					$member['profile_url'] = $this->profile_url( $member );
					$member['stat'] = $this->get_stat( $member, false, true );
				}
			}
			
			if($cache && isset($userid)){
				return self::$cache['user'][$userid] = $member;
			}
			else{
				return $member;
			}
		}
	}
	
	function get_members($args = array(), &$items_count = 0){
		
		$default = array(
		  'include' => array(), // array( 2, 10, 25 )
	  	  'exclude' => array(),  // array( 2, 10, 25 )
	  	  'status' => 'active',  // 'active', 'blocked', 'trashed', 'spamer'
		  'groupid' => NULL, // groupid
		  'orderby' => 'userid', //
		  'order' => 'ASC', // ASC DESC
		  'offset' => 0, // OFFSET
		  'row_count' => NULL // ROW COUNT
		);
		
		$args = wpforo_parse_args( $args, $default );
		if(!empty($args)){
			extract($args, EXTR_OVERWRITE);
			
			$include = wpforo_parse_args( $include );
			$exclude = wpforo_parse_args( $exclude );
			
			$sql = "SELECT *, ug.name AS groupname FROM `".$this->wpforo->db->prefix."users` u 
				LEFT JOIN `".$this->wpforo->db->prefix."wpforo_profiles` p ON p.`userid` = u.`ID`
				LEFT JOIN `".$this->wpforo->db->prefix."wpforo_usergroups` ug ON ug.`groupid` = p.`groupid`";
			$wheres = array();
			if(!empty($include))        $wheres[] = "u.`ID` IN(" . implode(', ', array_map('intval', $include)) . ")";
			if(!empty($exclude))        $wheres[] = "u.`ID` NOT IN(" . implode(', ', array_map('intval', $exclude)) . ")";
			if(isset($status))        $wheres[] = " p.`status` = '" . esc_sql(sanitize_text_field($status)) . "' ";
			if($groupid != NULL) $wheres[] = "p.`groupid` = " . intval($groupid);
			
			if(!empty($wheres)) $sql .= " WHERE " . implode($wheres, " AND ");
			
			$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			if( $item_count_sql ) $items_count = $this->wpforo->db->get_var($item_count_sql);
			
			$sql .= esc_sql(" ORDER BY $orderby " . $order);
			if($row_count) $sql .= esc_sql(" LIMIT $offset,$row_count");
			
			return $this->wpforo->db->get_results($sql, ARRAY_A);
		}
	}
	
	function search($needle, $fields = array()){
		
		if($needle != ''){
			$needle = sanitize_text_field($needle);
			if(empty($fields)){
				$fields = array( 
				  'title',
				  'user_nicename',
				  'user_email',
				  'signature'
				);
			}
			
			$sql = "SELECT `ID` FROM `".$this->wpforo->db->prefix."users` u LEFT JOIN `".$this->wpforo->db->prefix."wpforo_profiles` p ON p.`userid` = u.`ID`";
			$wheres = array();
			
			foreach($fields as $field){
				$field = sanitize_text_field($field);
				$wheres[] = "`".esc_sql($field)."` LIKE '%" . esc_sql($needle) ."%'";
			}
			
			if(!empty($wheres)){
				$sql .= " WHERE " . implode($wheres, " OR ");
				$results = $this->wpforo->db->get_results($sql, ARRAY_A);
				$userids = array();
				foreach($results as $result){
					$userids[] = $result['ID'];
				}
				return $userids;
			}else{
				return array();
			}
		}else{
			return array();
		}
		
	}
	
	/**
	 * make user trashed  
	 * NOTE there is no way to delete user
	 * 
	 * @since 1.0.0
	 *
	 * @return	true or false
	 */
	 
	
	function delete( $userid, $permanently = FALSE ){
		if( $permanently ){
			if( FALSE !== $this->wpforo->db->delete(
					$this->wpforo->db->prefix.'wpforo_profiles', array( 'userid' => intval( $userid ) ), array( '%d' )
				)
			){
				$this->wpforo->notice->add('User successfully deleted from wpforo', 'success');
				return TRUE;
			}
		}else{
			if( FALSE !== $this->wpforo->db->update(
					$this->wpforo->db->prefix.'wpforo_profiles',
					array('status' => 'trashed'),
					array('userid' => intval( $userid )),
					array('%s'),
					array('%d')
				) 
			){
				$this->wpforo->notice->add('User successfully deleted from wpforo', 'success');
				return TRUE;
			}
		}
		
		$this->wpforo->notice->add('User delete error', 'error');
		return FALSE;
	}
	
	function avatar($member, $attr = '', $size = '', $cache = false){
		
		if(!isset($member['userid'])) return;
		
		$src = $member['avatar'];
		$userid = $member['userid'];
		if(isset(self::$cache['avatar'][$userid])){
			if(self::$cache['avatar'][$userid]['attr'] == $attr && self::$cache['avatar'][$userid]['size'] == $size){
				if(isset(self::$cache['avatar'][$userid]['img'])){
					return self::$cache['avatar'][$userid]['img'];
				}
			}
		}
		if($src && wpforo_feature('custom-avatars', $this->wpforo)){
			$attr = ($attr ? $attr : 'height="96" width="96"');
			$img = '<img class="avatar" src="'.esc_url($src).'" '. $attr .' />';
		}else{
			$img = ($size) ? get_avatar($userid, $size) : get_avatar($userid);
			if($attr) $img = str_replace('<img', '<img ' . $attr, $img);
		}
		if($cache){
			self::$cache['avatar'][$userid]['attr'] = $attr;
			self::$cache['avatar'][$userid]['size'] = $size;
			return self::$cache['avatar'][$userid]['img'] = $img;
		}
		else{
			return $img;
		}
	}
	
	function get_avatar($userid, $attr = '', $size = '', $cache = false){
		if(isset(self::$cache['avatar'][$userid])){
			if(self::$cache['avatar'][$userid]['attr'] == $attr && self::$cache['avatar'][$userid]['size'] == $size){
				if(isset(self::$cache['avatar'][$userid]['img'])){
					return self::$cache['avatar'][$userid]['img'];
				}
			}
		}
		$src = $this->wpforo->db->get_var("SELECT `avatar` FROM `".$this->wpforo->db->prefix."wpforo_profiles` WHERE `userid` = ".intval($userid));
		if($src && wpforo_feature('custom-avatars', $this->wpforo)){
			$attr = ($attr ? $attr : 'height="96" width="96"');
			$img = '<img class="avatar" src="'.esc_url($src).'" '. $attr .' />';
		}else{
			$img = ($size) ? get_avatar($userid, $size) : get_avatar($userid);
			if($attr) $img = str_replace('<img', '<img ' . $attr, $img);
		}
		if($cache){
			self::$cache['avatar'][$userid]['attr'] = $attr;
			self::$cache['avatar'][$userid]['size'] = $size;
			return self::$cache['avatar'][$userid]['img'] = $img;
		}
		else{
			return $img;
		}
	}
	
	public function get_avatar_url($userid){
		return $this->wpforo->db->get_var("SELECT `avatar` FROM `".$this->wpforo->db->prefix."wpforo_profiles` WHERE `userid` = ".intval($userid));
	}
	
	function get_topics_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(topicid) FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `userid` = ".intval($userid));
		return $count;
	}
	
	function get_questions_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(topicid) FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `userid` = ".intval($userid));
		return $count;
	}
	
	function get_answers_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(postid) FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `is_answer` = 1 AND `userid` = ".intval($userid));
		return $count;
	}
	
	function get_question_comments_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(postid) FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `parentid` > 0 AND `userid` = ".intval($userid));
		return $count;
	}
	
	function get_replies_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(postid) FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `userid` = ".intval($userid));
		return $count;
	}
	
	function get_likes_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(likeid) FROM `".$this->wpforo->db->prefix."wpforo_likes` WHERE `userid` = ".intval($userid));
		return $count;
	}
	
	function get_votes_count( $userid ){
		$count = $this->wpforo->db->get_var("SELECT count(voteid) FROM `".$this->wpforo->db->prefix."wpforo_votes` WHERE `userid` = ".intval($userid));
		return $count;
	}
	
	// how many times the user like or vote
	function get_votes_and_likes_count( $userid ){
		return $this->get_votes_count( intval($userid) ) + $this->get_likes_count( intval($userid) );
	}
	
	//getting user's posts votes and likes count
	function get_user_votes_and_likes_count( $userid ){
		$votes_count = $this->wpforo->db->get_var("SELECT count(voteid) FROM `".$this->wpforo->db->prefix."wpforo_votes` WHERE `post_userid` = ".intval($userid));
		$likes_count = $this->wpforo->db->get_var("SELECT count(likeid) FROM `".$this->wpforo->db->prefix."wpforo_likes` WHERE `post_userid` = ".intval($userid));
		return $votes_count + $likes_count;
	}
	
	function get_profile_url( $arg, $template = 'profile' ){
		if(!$arg) return WPFORO_BASE_URL;
		$userid = intval( basename($arg) );
		$member_args = ( $userid ? $userid : array( 'username' => basename($arg) ) );
		$user = $this->get_member( $member_args );
		if(empty($user)) return WPFORO_BASE_URL;
		$user_slug = ( wpfo($this->wpforo->member_options['url_structure'], false) == 'id' ? $user['ID'] : $user['user_nicename'] );
		return  WPFORO_BASE_URL . "$template/$user_slug";
	}
	
	function profile_url( $member = array(), $template = 'profile' ){
		if(isset($member['ID']) || isset($member['user_nicename'])){
			$user_slug = ( wpfo($this->wpforo->member_options['url_structure'], false) == 'id' ? $member['ID'] : $member['user_nicename'] );
			$profile_url = WPFORO_BASE_URL . "$template/$user_slug";
			$profile_url = apply_filters( 'wpforo_profile_url', $profile_url, $member, $template );
		}
		else{
			$profile_url = WPFORO_BASE_URL;
			$profile_url = apply_filters( 'wpforo_no_profile_url', $profile_url, $template );
			
		}
		return $profile_url;
	}
	
	//$args = UserID or Member Object
	//$live_count = TRUE / FALSE
	function get_stat( $args = array(), $live_count = false, $cache = false ){
		
		$stat = array(	'points' => 0, 
						'rating' => 0, 
						'rating_procent' => 0, 
						'color' => $this->rating(0, 'color'), 
						'badge' => $this->rating(0, 'icon'), 
						'posts' => 0, 
						'topics' => 0, 
						'questions' => 0, 
						'answers' => 0, 
						'question_comments' => 0, 
						'likes' => 0, 
						'liked' => 0,
						'title' => $this->rating(0, 'title'));
		
		$userid = ( isset($args['userid']) && $args['userid'] ) ? $args['userid'] : $args;
		
		if(  $cache && isset(self::$cache['stat'][$userid]) ){
			return self::$cache['stat'][$userid];
		}
		
		if( is_array($args) && isset($args['userid']) ){
			$userid = $args['userid'];
			$stat['topics'] = (int)$this->get_topics_count( $userid );
			if(isset($args['questions'])) $stat['questions'] = intval($args['questions']);
			if(isset($args['answers'])) $stat['answers'] = intval($args['answers']);
			if(isset($args['posts'])) $stat['posts'] = intval($args['posts']);
			if(isset($args['comments'])) $stat['question_comments'] = intval($args['comments']);
		}
		elseif($userid = wpforo_bigintval($args)){
			$stat['topics'] = (int)$this->get_topics_count( $userid );
			if($live_count){
				if($questions = $this->get_questions_count( $userid )) $stat['questions'] = $questions;
				if($answers = $this->get_answers_count( $userid )) $stat['answers'] = $answers;
				if($posts = $this->get_replies_count( $userid )) $stat['posts'] = $posts;
				if($question_comments = $this->get_question_comments_count( $userid )) $stat['question_comments'] = $question_comments;
			}
			else{
				$profile = $this->wpforo->db->get_var("SELECT `posts`, `questions`, `answers`, `comments` FROM `".$this->wpforo->db->prefix."wpforo_profiles` WHERE `userid` = ".intval($userid));
				if(isset($profile['questions'])) $stat['questions'] = intval($profile['questions']);
				if(isset($profile['answers'])) $stat['answers'] = intval($profile['answers']);
				if(isset($profile['posts'])) $stat['posts'] = intval($profile['posts']);
				if(isset($profile['comments'])) $stat['question_comments'] = intval($profile['comments']);
			}
		}
		
		if( $userid ){
			if($likes = $this->get_votes_and_likes_count( $userid )) $stat['likes'] = $likes;
			if($liked = $this->get_user_votes_and_likes_count( $userid )) $stat['liked'] = $liked;
			if($stat['posts']) $stat['points'] = $stat['posts']; //TO-DO: Point counter function based on all stat values.
			if($stat['points']) $stat['rating'] = $this->rating_level($stat['points'], false);
			if($stat['rating']) {
				$stat['rating_procent'] = $stat['rating'] * 10;
				$stat['title'] = $this->rating(intval($stat['rating']), 'title');
				$stat['color'] = $this->rating(intval($stat['rating']), 'color');
				$stat['badge'] = $this->rating(intval($stat['rating']), 'icon');
			}
		}
		
		if($cache && isset($userid)){
			return self::$cache['stat'][$userid] = $stat;
		}
		else{
			return $stat;
		}
	}
	
	function get_count(){
		return $this->wpforo->db->get_var( "SELECT COUNT(`userid`) FROM `".$this->wpforo->db->prefix."wpforo_profiles`" );
	}
	
	
	function is_online( $userid, $duration = 240, $cache = true ){
		if(isset(self::$cache['online'][$userid])){
			if(self::$cache['online'][$userid]['durration'] == $duration ){
				if(isset(self::$cache['online'][$userid]['status'])){
					return self::$cache['online'][$userid]['status'];
				}
			}
		}
		if($duration == 240) $duration = $this->wpforo->member_options['online_status_timeout'];
		$online_time = intval( get_user_meta($userid, 'wpforo_online_time', TRUE) ); 
		$current_time =  current_time( 'timestamp', 1 );
		$online_duration = $current_time - $online_time;
		if( $online_duration < $duration ) {
			$status = true;
		} 
		else{
			$status = false;
		}
		if($cache){
			self::$cache['online'][$userid]['durration'] = $duration;
			return self::$cache['online'][$userid]['status'] = $status;
		}
		else{
			return $status;
		}
	}
	
	public function show_online_indicator($userid, $ico = TRUE){
		if( $this->is_online($userid)) : ?>
			
			<?php if($ico) : ?>
            	<i class="fa fa-lightbulb-o fa-0x wpfcl-8" title="<?php wpforo_phrase('Online') ?>"></i>
            <?php else : wpforo_phrase('Online'); endif ?>
            
        <?php else : ?>
        	
        	<?php if($ico) : ?>
            	<i class="fa fa-lightbulb-o fa-0x wpfcl-0" title="<?php wpforo_phrase('Offline') ?>"></i>
            <?php else : wpforo_phrase('Offline'); endif ?>
            
        <?php endif;  
	}
	
	function online_members_count( $duration = 240 ){
		if($duration == 240) $duration = $this->wpforo->member_options['online_status_timeout'];
		$current_time =  current_time( 'timestamp', 1 );
		$online_timeframe = $current_time - $duration;
		return $this->wpforo->db->get_var( "SELECT COUNT(`user_id`) FROM `".$this->wpforo->db->prefix."usermeta` WHERE meta_key = 'wpforo_online_time' AND meta_value  > " . wpforo_bigintval($online_timeframe) );
		
	}
	
	function get_online_members( $count = 1, $duration = 240 ){
		if($duration == 240) $duration = $this->wpforo->member_options['online_status_timeout'];
		$current_time =  current_time( 'timestamp', 1 );
		$online_timeframe = $current_time - $duration;
		$onlinemembers_ids = $this->wpforo->db->get_col( "SELECT `user_id` FROM `".$this->wpforo->db->prefix."usermeta` WHERE meta_key = 'wpforo_online_time' AND meta_value  > " . wpforo_bigintval($online_timeframe) );
		if(!empty($onlinemembers_ids)){
			$args = array(
			  'include' => $onlinemembers_ids, // array( 2, 10, 25 )
			  'orderby' => 'userid', // forumid, order, parentid
			  'row_count'	=> $count,
			  'order' => 'ASC', // ASC DESC
			);
			return $this->get_members( $args );
		}
		else{
			return array();
		}
	}
	
	function levels(){
		$levels = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
		return $levels;
	}
	
	function rating( $level = false, $var = false, $default = false ){
		
		$rating = array();
		$rating['color'] = array( 0 => '#d2d2d2', 1 => '#4dca5c', 2 => '#4dca5c', 3 => '#4dca5c', 4 => '#4dca5c', 5 => '#4dca5c', 6 => '#E5D600', 7 => '#E5D600', 8 => '#E5D600', 9 => '#FF812D', 10 => '#E04A47' );
		$rating['points'] = array( 0 => 0, 1 => 5, 2 => 20, 3 => 50, 4 => 100, 5 => 250, 6 => 500, 7 => 750, 8 => 1000, 9 => 2500, 10 => 5000 );
		$rating['title'] = array( 0 => 'New Member', 1 => 'Active Member', 2 => 'Eminent Member', 3 => 'Trusted Member', 4 => 'Estimable Member', 5 => 'Reputable  Member', 6 => 'Honorable Member', 7 => 'Prominent Member', 8 => 'Noble Member', 9 => 'Famed Member', 10 => 'Illustrious Member' );
		$rating['icon']  = array( 0 => 'fa-star-half-o', 1 => 'fa-star', 2 => 'fa-star', 3 => 'fa-star', 4 => 'fa-star', 5 => 'fa-star', 6 => 'fa-certificate', 7 => 'fa-certificate', 8 => 'fa-certificate', 9 => 'fa-shield', 10 => 'fa-trophy' );
		
		if(!empty($this->wpforo->member_options['rating'])){
			
			if($level === false) return $this->wpforo->member_options['rating'];
			if(!empty($this->wpforo->member_options['rating'][$level])){
				
				if(!$var) return $this->wpforo->member_options['rating'][$level];
				if(!empty($this->wpforo->member_options['rating'][$level][$var])){
					
					return $this->wpforo->member_options['rating'][$level][$var];
					
				}
			}
		}
		if( $level !== false && $var ) { return $rating[$var][$level]; }
		elseif( $level !== false && !$var ){ foreach( $rating as $variable => $values ){ $level_data[$variable] = $values[$level];} return $level_data; }
		elseif( $level === false && !$var ) return $rating;
		else return array();
	}
	
	function rating_level($member_posts, $percent = TRUE){
		$bar = 0;
		if($member_posts < $this->rating(1, 'points')){$bar = 0;}
		elseif($member_posts < $this->rating(2, 'points')){$bar = 10;}
		elseif($member_posts < $this->rating(3, 'points')){$bar = 20;}
		elseif($member_posts < $this->rating(4, 'points')){$bar = 30;}
		elseif($member_posts < $this->rating(5, 'points')){$bar = 40;}
		elseif($member_posts < $this->rating(6, 'points')){$bar = 50;}
		elseif($member_posts < $this->rating(7, 'points')){$bar = 60;}
		elseif($member_posts < $this->rating(8, 'points')){$bar = 70;}
		elseif($member_posts < $this->rating(9, 'points')){$bar = 80;}
		elseif($member_posts < $this->rating(10, 'points')){$bar = 90;}
		else{$bar = 100;}
		if($percent){
			return $bar;
		}else{
			return floor($bar/10);
		}
	}
	
	function rating_badge($level = 0, $view = 'short'){
		
		$level = ( $level > 10 ) ? floor($level/10) : $level;
		
		if($level == 0){
			return '<i class="fa '. sanitize_html_class($this->rating($level, 'icon')) .'"></i>';
		}
		elseif($level > 0 && $level < 6){
			if( $view == 'full' ){
				return str_repeat(' <i class="fa '. sanitize_html_class($this->rating($level, 'icon')) .'"></i> ', $level);
			}
			else{
				return '<span>' . esc_html($level) . '</span> <i class="fa '. sanitize_html_class($this->rating($level, 'icon')) .'"></i>';
			}
		}
		elseif($level > 5 && $level < 9){
			if( $view == 'full' ){
				return str_repeat(' <i class="fa '. sanitize_html_class($this->rating($level, 'icon')) .'"></i> ', ($level-5));
			}
			else{
				return '<span>' . esc_html($level-5) . '</span> <i class="fa '. sanitize_html_class($this->rating($level, 'icon')) .'"></i>';
			}
		}
		elseif($level > 8){
			return '<i class="fa '. sanitize_html_class($this->rating($level, 'icon')) .'"></i>';
		}
		else{
			return;
		}
	}
	
	public function reset( $userid ){
		if( !$userid ) return;
		$this->wpforo->db->query( "DELETE FROM `" . $this->wpforo->db->prefix ."usermeta` WHERE `meta_key` = '_wpf_member_obj' AND `user_id` = " . intval($userid) );
	}
	
	public function clear_db_cache(){
		$this->wpforo->db->query( "DELETE FROM `" . $this->wpforo->db->prefix ."usermeta` WHERE `meta_key` = '_wpf_member_obj'" );
	}
	
	public function init_current_user(){
		if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			update_user_meta( $current_user->ID, 'wpforo_online_time', current_time( 'timestamp', 1 ) );
			$this->wpforo->current_user = $this->get_member( $current_user->ID );
			$this->wpforo->current_user_groupid = $this->wpforo->current_user['groupid'];
			$this->wpforo->current_userid  = $current_user->ID;
			$this->wpforo->current_username  = $current_user->user_login;
			$this->wpforo->current_user_email  = $current_user->user_email;
			$this->wpforo->current_user_display_name  = $current_user->display_name;
		}else{
			$this->wpforo->current_user = array();
			$this->wpforo->current_user_groupid = 4;
			$this->wpforo->current_userid = 0;
			$this->wpforo->current_username  = '';
			$this->wpforo->current_user_email  = '';
			$this->wpforo->current_user_display_name  = '';
		}
	}
	
	public function blog_posts( $userid ){
		if( isset($userid) && $userid ) return count_user_posts( $userid , 'post' );
	}
	
	public function blog_comments($userid, $user_email){
		global $wpdb;
		if( !$userid || !$user_email ) return 0;
		return (int) $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->comments. " WHERE `user_id` = " . intval($userid) . " OR `comment_author_email` = '" . esc_sql($user_email) . "'");
	}
	
}

?>