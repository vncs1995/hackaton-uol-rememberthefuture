<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<?php $plugins = true; ?>
<div id="icon-tools" class="icon32"><br></div><div class="wrap"><h2 style="padding:20px 0px 30px 0px;line-height: 20px;"><?php _e('Forum Settings') ?></h2></div>
<?php $wpforo->notice->show(FALSE) ?>
<div id="wpf-admin-wrap" class="wrap"><div id="icon-users" class="icon32"><br /></div>
<?php
	$tabs = array( 
		'general' => __('General', 'wpforo'), 
		'forums' => __('Forums', 'wpforo'), 
		'accesses' => __('Forum Accesses', 'wpforo'),
		'posts' => __('Topics &amp; Posts', 'wpforo'), 
		'members' => __('Members', 'wpforo'),
		'subscriptions' => __('Emails', 'wpforo'),
		'features' => __('Features', 'wpforo')
	);
	if( !empty( $wpforo->theme_options['styles'] ) ) $tabs['styles'] = __('Styles', 'wpforo');
	if( $plugins ) $tabs['plugins'] = __('Addons', 'wpforo');
	wpforo_admin_options_tabs( $tabs, ( isset($_GET['tab']) ? $_GET['tab'] : 'general' ) ); 
	?>
    <div class="wpf-info-bar"><br />
		<?php 
			if(isset($_GET['tab'])){
				switch($_GET['tab']){
					case 'accesses':
						include( 'options-tabs/accesses.php' );
					break;
					case 'posts':
						include( 'options-tabs/posts.php' );
					break;
					case 'forums':
						include( 'options-tabs/forums.php' );
					break;
					case 'members':
						include( 'options-tabs/members.php' );
					break;
					case 'features':
						include( 'options-tabs/features.php' );
					break;
					case 'styles':
						include( 'options-tabs/styles.php' );
					break;
					case 'subscriptions':
						include( 'options-tabs/subscriptions.php' );
					break;
					case 'plugins':
						include( 'options-tabs/plugins.php' );
					break;
					default:
					include_once( 'options-tabs/general.php' );
				}
			}else{
				include_once( 'options-tabs/general.php' );
			}
		?>
	</div>
</div>