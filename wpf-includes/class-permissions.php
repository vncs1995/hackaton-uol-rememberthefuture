<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

class wpForoPermissions{
	
	private $wpforo;
	private static $cache = array();
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
 	
 	/**
	 * 
	 * @param string $access
	 * 
	 * @return array access row by access key
	 */
 	function get_access($access){
		$access = sanitize_text_field($access);
		$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_accesses` WHERE `access` = '" . esc_sql($access) . "'";
		return $this->wpforo->db->get_row($sql, ARRAY_A);
	}
	
	
 	/**
	* get all accesses from accesses table
	* 
	* @return assoc array with accesses
	*/
 	function get_accesses(){
		$sql = "SELECT * FROM ".$this->wpforo->db->prefix."wpforo_accesses";
		return $this->wpforo->db->get_results($sql, ARRAY_A);
	}
 	
 	function usergroup_cans_form( $groupid = FALSE ){
		
		$can_data = array();
		$cans = $this->wpforo->usergroup_cans;
		
		if( $groupid == FALSE ){
			foreach($cans as $can => $name){ 
				$can_data[$can]['value'] = 0;
				$can_data[$can]['name'] = $name;
			}
		}else{
			$usegroup = $this->wpforo->usergroup->get_usergroup( $groupid );
			$ug_cans = unserialize($usegroup['cans']);
			foreach($cans as $can => $name){ 
				$can_data[$can]['value'] = $ug_cans[$can];
				$can_data[$can]['name'] = $name;
			}
		}
		
		return $can_data;
	}
	
	function forum_cans_form( $access = FALSE ){
		
		$can_data = array();
		$cans = $this->wpforo->forum_cans;
		
		if( !$access ){
			foreach($cans as $can => $name){ 
				$can_data[$can]['value'] = 0;
				$can_data[$can]['name'] = $name;
			}
		}else{
			$access = $this->get_access( $access );
			$access_cans = unserialize($access['cans']);
			foreach($cans as $can => $name){ 
				$can_data[$can]['value'] = $access_cans[$can];
				$can_data[$can]['name'] = $name;
			}
		}
		
		return $can_data;
	}
	
	
	/**
	* 
	* @param  string (required)
	* @param  array
	* @param  int 
	* 
	* @return affected rows count or false
	*/
	function add( $title, $cans = array(), $key = '' ){
		$default = array_map('intval', $this->wpforo->forum_cans);
		$cans = wpforo_parse_args($cans, $default);
		if(!$key) $key = $title;
		
		$i = 2;
		while( $this->wpforo->db->get_var("SELECT `access` FROM ".$this->wpforo->db->prefix."wpforo_accesses WHERE `access` = '". esc_sql(sanitize_text_field($key)) . "'") ){
			$key = $key . '-' . $i;
			$i++;
		}
		
		if( $this->wpforo->db->insert( 
			$this->wpforo->db->prefix . 'wpforo_accesses', 
				array( 
					'title'		=> sanitize_text_field($title), 
					'access' 	=> sanitize_text_field($key), 
					'cans'		=> serialize($cans)
				), 
				array( 
					'%s',
					'%s',
					'%s'
				)
			)
		){
			$this->wpforo->notice->add( sprintf( __('%s access successfully added', 'wpforo') , esc_html($title)) , 'success');
			return $this->wpforo->db->insert_id;
		}
		
		$this->wpforo->notice->add('Access add error', 'error');
		return FALSE;
	}
	
	function edit( $title, $cans, $key ){
		$default = array_map('intval', $this->wpforo->forum_cans);
		$cans = wpforo_parse_args($cans, $default);
		
		if( FALSE !== $this->wpforo->db->update( 
			$this->wpforo->db->prefix . 'wpforo_accesses', 
			array( 
				'title' =>  sanitize_text_field($title), 
				'cans' => serialize( $cans ), 
			),
			array( 'access' => sanitize_text_field($key) ),
			array( 
				'%s',
				'%s'
			),
			array( '%s' ))
		){
			$this->wpforo->notice->add( sprintf( __('%s access successfully edited', 'wpforo'), esc_html($title)) , 'success');
			return $key;
		}
		
		$this->wpforo->notice->add('Access edit error', 'error');
		return FALSE;
	}
	
	function delete($accessid){
		
		$accessid = intval($accessid);
		
		if(!$accessid){
			$this->wpforo->notice->add('Access delete error', 'error');
			return FALSE;
		}
		
		if( FALSE !== $this->wpforo->db->delete( $this->wpforo->db->prefix.'wpforo_accesses', array( 'accessid' => $accessid ), array( '%d' ) ) ){
			$this->wpforo->notice->add('Access successfully deleted', 'success');
			return $accessid;
		}
		
		$this->wpforo->notice->add('Access delete error', 'error');
		return FALSE;
	}
	
	function forum_can( $forumid, $do ){
		$can = 0;
		$forumid = intval($forumid);
		if( !$this->wpforo->current_user_groupid ) return 0;
		$forum = $this->wpforo->forum->get_forum($forumid, true);
		$permissions = unserialize($forum['permissions']);
		if( isset($permissions[$this->wpforo->current_user_groupid]) ){
			$access = $permissions[$this->wpforo->current_user_groupid];
			$access_arr = $this->get_access($access);
			$cans = unserialize($access_arr['cans']);
			$can = ( isset($cans[$do]) ? $cans[$do] : 0 );
		}
		return $can;
	}
	
	function usergroup_can( $usergroupid, $do ){
		$usergroupid = intval($usergroupid);
		$usergroup = $this->wpforo->usergroup->get_usergroup( $usergroupid );
		$cans = unserialize($usergroup['cans']);
		return ( isset($cans[$do]) ? $cans[$do] : 0 );
	}
	
	function user_can_manage_user( $user_id, $managing_user_id ){
		
		if( !$user_id || !$managing_user_id ) return false;
		if( $user_id == $managing_user_id ) return true;
		
		$user = new WP_User( $user_id ); 
		$user_level = $this->user_wp_level( $user );
		if( !empty($user->roles) && is_array($user->roles) ) $user_role = array_shift($user->roles);
		
		$managing_user = new WP_User( $managing_user_id );  
		$managing_user_level = $this->user_wp_level( $managing_user );
		if( !empty($managing_user->roles) && is_array($managing_user->roles) ) $managing_user_role = array_shift($managing_user->roles);
		
		if( (int)$user_level > (int)$managing_user_level ){
			return true;
		}
		elseif( $user_id == 1 && $user_role == 'administrator' ){
			return true;
		}
		elseif( (int)$user_level == (int)$managing_user_level ){
			$member = $this->wpforo->member->get_member( $user_id );
			$managing_member = $this->wpforo->member->get_member( $managing_user_id );
			$user_wpforo_can = $this->usergroup_can( $member['groupid'], 'em' );
			$managing_user_wpforo_can = $this->usergroup_can( $managing_member['groupid'], 'em' );
			if( $user_wpforo_can && !$managing_user_wpforo_can ){
				return true;
			}
			else{
				return false;
			}
		}
		elseif( $user_id != 1 && $managing_user_id == 1 && $managing_user_role == 'administrator' ){
			return false;
		}
		else{
			return false;
		}
	}
	
	function user_wp_level( $user_object ){
		$level = 0;
		$levels = array();
		if( is_int($user_object) ){
			$user_object = new WP_User( $user_object );
		}
		if( isset($user_object->allcaps) && is_array($user_object->allcaps) && !empty($user_object->allcaps) ){
			foreach($user_object->allcaps as $level_key => $level_value){
				if( strpos($level_key, 'level_') !== FALSE && $level_value == 1 ){
					$levels[] = intval(str_replace('level_', '', $level_key));
				}	
			}
			if(!empty($levels)){
				$level = max($levels);
			}
		}
		return $level;
	}
	
	
}

?>