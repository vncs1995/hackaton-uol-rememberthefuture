<?php

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

	function wpforo_add_menu(){
		global $wpforo;
		$position = ( isset($wpforo->general_options['menu_position']) && $wpforo->general_options['menu_position'] > 0 ) ? $wpforo->general_options['menu_position'] : 23;
		add_menu_page(__('Dashboard', 'wpforo'), __('Forums', 'wpforo'), 'read', 'wpforo-community', 'wpforo_toplevel_page', 'dashicons-format-chat', $position);
		add_submenu_page('wpforo-community', __('Dashboard', 'wpforo'), __('Dashboard', 'wpforo'), 'read', 'wpforo-community', 'wpforo_toplevel_page' );
		if( $wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'cf' ) && $wpforo->perm->usergroup_can(  $wpforo->current_user_groupid, 'ef' ) && $wpforo->perm->usergroup_can(  $wpforo->current_user_groupid, 'df' ) ) add_submenu_page('wpforo-community', __('Forums', 'wpforo'), __('Forums', 'wpforo'), 'read',   'wpforo-forums', 'wpforo_forum_menu');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Settings', 'wpforo'), __('Settings', 'wpforo'), 'read', 'wpforo-settings', 'wpforo_settings');
		if( $wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vm' ) ) add_submenu_page('wpforo-community', __('Members', 'wpforo'), __('Members', 'wpforo'), 'read', 'wpforo-members', 'wpforo_member_menu');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Usergroups', 'wpforo'), __('Usergroups', 'wpforo'), 'read', 'wpforo-usergroups', 'wpforo_usergroups_menu');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Phrases', 'wpforo'), __('Phrases', 'wpforo'), 'read', 'wpforo-phrases', 'wpforo_phrases');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Themes', 'wpforo'), __('Themes', 'wpforo'), 'read', 'wpforo-themes', 'wpforo_themes');
		//exit();
	}
	add_action('admin_menu', 'wpforo_add_menu', 39);
	
	function wpforo_toplevel_page(){
		global $wpforo; 
		require( WPFORO_DIR . '/wpf-admin/dashboard.php' );
	}
	
	function wpforo_forum_menu(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/forum.php' );
	}
	
	function wpforo_member_menu(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/member.php' );
	}
	
	function wpforo_usergroups_menu(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/usergroup.php' );
	}
	
	function wpforo_settings(){
		global $wpforo, $wpdb;
		require( WPFORO_DIR . '/wpf-admin/options.php' );
	}
	
	function wpforo_themes(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/themes.php' );
	}
	
	function wpforo_phrases(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/phrase.php' );
	}
	
	function wpforo_integrations(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/integration.php' );
	}
?>