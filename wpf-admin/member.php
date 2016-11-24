<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !$wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vm') ) exit;
?>

<div id="wpf-admin-wrap" class="wrap">
	<?php wpforo_screen_option() ?>
	<div id="icon-users" class="icon32"><br></div>
	<h2 style="padding:30px 0px 0px 0px;line-height: 20px;"><?php _e('Members', 'wpforo'); ?></h2>
	<?php $wpforo->notice->show(FALSE) ?>
	<?php if(!isset( $_GET['action'] ) || ( isset( $_GET['action']) &&  $_GET['action'] == -1 ) ) : ?>
		<?php 
			$fields[] = 'display_name';
			$search_fields[] = 'title';
			$search_fields[] = 'display_name';
			$filter_fields = array();
			if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vmu')){
				$fields[] = 'user_login';
				$search_fields[] = 'user_login';
			}
			if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vmm')){
				$fields[] = 'user_email';
				$search_fields[] = 'user_email';
			}
			if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vmg')){
				$fields[] = 'groupid';
				$filter_fields[] = 'groupid';
			}
			if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vmr')){
				$fields[] = 'rank';
			}
			if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'vms')){
				$search_fields[] = 'signature';
			}
			$actions = array('button');
			if( $wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'em') ) $actions = array('edit_user', 'edit_profile');
			if( $wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'dm') ) $actions[] = 'delete';
			wpforo_create_form_table('member', 'userid', $fields, $search_fields, $filter_fields, $actions);
		?>
	<?php endif; ?>
</div>

