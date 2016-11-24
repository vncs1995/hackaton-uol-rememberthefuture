<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

register_nav_menus( array(
	'wpforo-menu' => esc_html__( 'wpForo Menu', 'wpforo' ),
) );


function wpforo_login_url(){
	global $wpforo;
	if(isset($wpforo->member_options['login_url']) && $wpforo->member_options['login_url']){
		$wp_login_url = trim(get_bloginfo('url') , '/') . '/' . ltrim($wpforo->member_options['login_url'] , '/');
	}
	else{
		$wp_login_url = ((!is_wpforo_page() ? WPFORO_BASE_URL : '') . '?wpforo=signin');
	}
	return esc_url($wp_login_url);
}


function wpforo_register_url(){
	global $wpforo;
	if(isset($wpforo->member_options['register_url']) && $wpforo->member_options['register_url']){
		$wp_register_url = trim(get_bloginfo('url') , '/') . '/' . ltrim($wpforo->member_options['register_url'] , '/');
	}
	else{
		$wp_register_url = WPFORO_BASE_URL . '?wpforo=signup';
	}
	return esc_url($wp_register_url);
}


function wpforo_lostpass_url(){
	global $wpforo;
	if(isset($wpforo->member_options['lost_password_url']) && $wpforo->member_options['lost_password_url']){
		$wp_lostpass_url = trim(get_bloginfo('url') , '/') . '/' . ltrim($wpforo->member_options['lost_password_url'] , '/');
	}
	else{
		$wp_lostpass_url = wp_lostpassword_url( wpforo_full_url() );
	}
	return esc_url($wp_lostpass_url);
}


function wpforo_menu_filter( $items, $menu ) {
    global $wpforo;
	if ( !is_admin() ) {
		foreach ( $items as $key => $item ) {
			if(isset($item->url)){
				if( strpos($item->url, '%wpforo-') !== FALSE ){
					$shortcode = trim(str_replace(array('https://', 'http://', '/', '%'), '', $item->url));
					if(isset($wpforo->menu) && isset($wpforo->menu[$shortcode])){
						if(isset($wpforo->menu[$shortcode]['href'])) $item->url = $wpforo->menu[$shortcode]['href'];
						if(isset($wpforo->menu[$shortcode]['attr']) && strpos($wpforo->menu[$shortcode]['attr'], 'wpforo-active') !== FALSE ) $item->classes[] = 'wpforo-active';
					}
					else{
						unset($items[$key]);
					}	
				}
			}
		}
	}
    return $items;
}
add_filter( 'wp_get_nav_menu_items', 'wpforo_menu_filter', 1, 2 );

function wpforo_menu_nofollow_items($item_output, $item, $depth, $args) {
	if( isset($item->url) && strpos($item->url, '?wpforo') !== FALSE ) {
		$item_output = str_replace('<a ', '<a rel="nofollow" ', $item_output);
	}
	return $item_output;
}
add_filter('walker_nav_menu_start_el', 'wpforo_menu_nofollow_items', 1, 4);

function wpforo_profile_plugin_menu( $userid = 0 ){
	
	$menu_html = '<div class="wpf-profile-plugin-menu">';
	
	if($url = wpforo_has_shop_plugin($userid)){
		$menu_html .= '<div id="wpf-pp-shop-menu" class="wpf-pp-menu">
                <a class="wpf-pp-menu-item" href="' . esc_url($url) . '">
                    <i class="fa fa-shopping-cart"></i><span>'.wpforo_phrase('Shop Account', false).'</span>
                </a>
			</div>';
	}
	if($url = wpforo_has_profile_plugin($userid)){
            $menu_html .= '<div id="wpf-pp-site-menu" class="wpf-pp-menu">
                <a class="wpf-pp-menu-item" href="' . esc_url($url) . '">
                    <i class="fa fa-user"></i>
                    <span>'.wpforo_phrase('Site Profile', false).'</span>
                </a>
            </div>';
	}
    $menu_html .= '<div id="wpf-pp-forum-menu" class="wpf-pp-menu">
            <div class="wpf-pp-menu-item">
                <i class="fa fa-comments"></i>
                <span>'.wpforo_phrase('Forum Profile', false).'</span>
            </div>
        </div>';
	
	$menu_html .= "\r\n<div class=\"wpf-clear\"></div>\r\n</div>";
	$menu_html = apply_filters( 'wpforo_profile_plugin_menu_filter', $menu_html, $userid );
	echo $menu_html; //This is a HTML content//
}
add_action( 'wpforo_profile_plugin_menu_action', 'wpforo_profile_plugin_menu', 1 );

class wpforo_menu_walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">\n";
	}
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$args = apply_filters( 'wpforo_nav_menu_item_args', $args, $item, $depth );
		$class_names = join( ' ', apply_filters( 'wpforo_nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id = apply_filters( 'wpforo_nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		$output .= $indent . '<li' . $id . $class_names .'>';
		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts = apply_filters( 'wpforo_nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$title = apply_filters( 'wpforo_the_title', $item->title, $item->ID );
		$title = apply_filters( 'wpforo_nav_menu_item_title', $title, $item, $args, $depth );
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$output .= apply_filters( 'wpforo_walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>";
	}
}

function wpforo_widgets_init() {
	register_sidebar(array(
		'name' => __('wpForo Sidebar', 'wpforo'),
		'description' => __("NOTE: If you're going to add widgets in this sidebar, please use 'Full Width' template for wpForo index page to avoid sidebar duplication.", 'wpforo'),
		'id' => 'forum-sidebar',
		'before_widget' => '<aside id="%1$s" class="footer-widget-col %2$s clearfix">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
}
add_action('widgets_init', 'wpforo_widgets_init', 11);

class wpForo_Widget_search extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpForo_Widget_search', // Base ID
			'wpForo Search',        // Name
			array( 'description' => 'wpForo search form' ) // Args
		);
	}
	public function widget( $args, $instance ) {
		global $wpforo;
		echo $args['before_widget']; //This is a HTML content//
		echo '<div id="wpf-widget-search" class="wpforo-widget-wrap">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title']; //This is a HTML content//
		}
		echo '<div class="wpforo-widget-content">';
		?>
        <form action="<?php echo WPFORO_BASE_URL ?>" method="get">
            <input type="text" placeholder="<?php wpforo_phrase('Search...') ?>" name="wpfs" class="wpfw-70" value="<?php echo isset($_GET['wpfs']) ? esc_attr(sanitize_text_field($_GET['wpfs'])) : '' ?>" ><input type="submit" class="wpfw-20" value="&raquo;">
        </form>
		<?php
		echo '</div></div>';
		echo $args['after_widget']; //This is a HTML content//
	}
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : 'Forum Search';
		?>
		<p>
			<label><?php _e('Title', 'wpforo'); ?>:</label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // widget wpforo search

class wpForo_Widget_login_form extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpForo_Widget_login_form', // Base ID
			'wpForo Login Form',        // Name
			array( 'description' => 'wpForo login form' ) // Args
		);
	}
	public function widget( $args, $instance ) {
		global $wpforo;
		echo $args['before_widget']; //This is a HTML content//
		echo '<div id="wpf-widget-login" class="wpforo-widget-wrap">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title']; //This is a HTML content//
		}
		echo '<div class="wpforo-widget-content">';
		?>
		<?php if( is_user_logged_in() && !empty($wpforo->current_user) ) : ?>
			<?php extract($wpforo->current_object, EXTR_OVERWRITE); extract($wpforo->current_user, EXTR_OVERWRITE); ?>
			<div class="wpforo-profile-wrap">
			<div class="wpforo-profile-head">
			<div class="h-header">
	      	<?php if( wpforo_feature('avatars', $wpforo) ): $rsz =''; ?>
	        	<div class="h-left"><?php echo $wpforo->member->get_avatar($userid, 'alt="'.esc_attr($display_name).'"', 150); ?></div>
	        <?php else: $rsz = ' style="margin-left:10px;"'; endif; ?>
	        <div class="h-right" <?php echo $rsz; ?>>
	             <div class="h-top">
	                <div class="profile-display-name">
	                	<?php $wpforo->member->show_online_indicator($userid) ?>
	                    <?php echo $display_name ? esc_html($display_name) : esc_html($user_nicename) ?>
	                </div>
	                <div class="profile-stat-data">
	                    <div class="profile-stat-data-item"><?php wpforo_phrase('Group') ?>: <?php wpforo_phrase($groupname) ?></div>
	                    <div class="profile-stat-data-item"><?php wpforo_phrase('Joined') ?>: <?php esc_html(wpforo_date($user_registered, 'Y/m/d')) ?></div>
	                </div>
	            </div>
	        </div>
	      <div class="wpf-clear"></div>
	      </div>
	      <div class="h-footer wpfbg-2">
	      
	        <div class="h-bottom">
	            <?php $wpforo->tpl->member_menu($userid) ?>
	            <a href="?wpforo=logout"><?php wpforo_phrase('logout') ?></a>
	            <div class="wpf-clear"></div>
	        </div>
	      </div>
	    </div>
	      </div>
	      
		<?php else : ?>
		
	        <form name="wpflogin" action="" method="POST">
			  <div class="wpforo-login-wrap">
			    <div class="wpforo-login-content">
			     <table class="wpforo-login-table wpfcl-1" width="100%" border="0" cellspacing="0" cellpadding="0">
			          <tr class="wpfbg-9">
			            <td class="wpf-login-label">
			            	<p class="wpf-label wpfcl-1"><?php wpforo_phrase('Username') ?>:</p>
			            </td>
			            <td class="wpf-login-field"><input autofocus required="TRUE" type="text" name="log" class="wpf-login-text wpfw-60" /></td>
			          </tr>
			          <tr class="wpfbg-9">
			            <td class="wpf-login-label">
			            	<p class="wpf-label wpfcl-1"><?php wpforo_phrase('Password') ?>:</p>
			            </td>
			            <td class="wpf-login-field"><input required="TRUE" type="password" name="pwd" class="wpf-login-text wpfw-60" /></td>
			          </tr>
			          <tr class="wpfbg-9"><td colspan="2" style="text-align: center;"><?php do_action('login_form') ?></td></tr>
			          <tr class="wpfbg-9">
			            <td class="wpf-login-label">&nbsp;</td>
			            <td class="wpf-login-field">
			            <p class="wpf-extra wpfcl-1">
			            <input type="checkbox" value="1" name="rememberme" id="wpf-login-remember"> 
			            <label for="wpf-login-remember"><?php wpforo_phrase('Remember Me') ?> |</label>
			            <a href="<?php echo esc_url(wp_lostpassword_url(wpforo_full_url())); ?>" class="wpf-forgot-pass"><?php wpforo_phrase('Lost your password?') ?></a> 
			            <a href="<?php echo esc_url(WPFORO_BASE_URL) ?>?wpforo=register"><?php wpforo_phrase('register') ?></a>
			            </p>
			            <input type="submit" name="wpforologin" value="<?php wpforo_phrase('Sign In') ?>" />
			            </td>
			          </tr>
			       </table>
			  	</div>
			  </div>
			</form>
			
		<?php endif ?>
		<?php
		echo '</div></div>';
		echo $args['after_widget'];
	}
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : 'Account';
		?>
		<p>
			<label><?php _e('Title', 'wpforo'); ?>:</label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // widget wpforo login


class wpForo_Widget_online_members extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpForo_Widget_online_members', // Base ID
			'wpForo Online Members',        // Name
			array( 'description' => 'Online members.' ) // Args
		);
	}
	public function widget( $args, $instance ) {
		global $wpforo;
		echo $args['before_widget']; //This is a HTML content//
		echo '<div id="wpf-widget-online-users" class="wpforo-widget-wrap">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		// widget content from front end
		$online_members = $wpforo->member->get_online_members($instance['count']);
		echo '<div class="wpforo-widget-content">';
		if(!empty($online_members)){
			echo '<ul>
					 <li>
						<div class="wpforo-list-item">';
			foreach( $online_members as $member ){
				if( $instance['display_avatar'] ): ?>
						<a href="<?php echo esc_url($wpforo->member->get_profile_url( $member['ID'] )) ?>" class="onlineavatar">
							<?php echo $wpforo->member->get_avatar( $member['ID'], 'style="width:95%;" class="avatar" title="'.esc_attr($member['display_name']).'"'); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo esc_url($wpforo->member->get_profile_url( $member['ID'] )) ?>" class="onlineuser"><?php echo esc_html($member['display_name']) ?></a>
					<?php endif; ?>
				<?php
			}
			echo '<div class="wpf-clear"></div>
							</div>
						</li>
					</ul>
				</div>';
		}
		else{
			echo '<p class="wpf-widget-note">&nbsp;'.wpforo_phrase('No online members at the moment', false).'</p>';
		}
		echo '</div>';
		echo $args['after_widget'];//This is a HTML content//
	}
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Online Members';
		$count = ! empty( $instance['count'] ) ? $instance['count'] : '15';
		$display_avatar = isset( $instance['display_avatar'] ) ? (bool) $instance['display_avatar'] : false;
		?>
		<p>
			<label><?php _e('Title', 'wpforo'); ?>:</label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p><p>
			<label><?php _e('Number of Items', 'wpforo'); ?></label>&nbsp;
			<input type="number" min="1" style="width: 53px;" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>" value="<?php echo esc_attr( $count ) ; ?>">
		</p><p>
			<label>
            	<input<?php checked( $display_avatar ); ?> type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'display_avatar' )); ?>"/>
			 	<?php _e('Display Avatars', 'wpforo'); ?>
            </label>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : '';
		$instance['display_avatar'] = isset( $new_instance['display_avatar'] ) ? (bool) $new_instance['display_avatar'] : false;
		return $instance;
	}
} // widget online members

class wpForo_Widget_recent_topics extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpForo_Widget_recent_topics', // Base ID
			'wpForo Recent Topics',        // Name
			array( 'description' => 'Your forum\'s recent topics.' ) // Args
		);
	}
	public function widget( $args, $instance ) {
		global $wpforo;
		echo $args['before_widget'];//This is a HTML content//
		echo '<div id="wpf-widget-recent-replies" class="wpforo-widget-wrap">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];//This is a HTML content//
		}
		// widget content from front end
		$topic_args = array(  	// forumid, order, parentid
		  'orderby'		=> 'created',
		  'order'		=> 'DESC', 		// ASC DESC
		  'row_count'	=> $instance['count'] 		// 4 or 1 ...
		);
		$topics = $wpforo->topic->get_topics($topic_args);
		echo '<div class="wpforo-widget-content"><ul>';
		foreach( $topics as $topic ){
			if( !$wpforo->perm->forum_can( $topic['forumid'], 'vf' ) ) continue;
			$topic_url = $wpforo->topic->get_topic_url( $topic );
			$member = $wpforo->member->get_member( $topic['userid'] );
			?>
            <li>
                <div class="wpforo-list-item">
                    <?php if( $instance['display_avatar'] ): ?>
                        <div class="wpforo-list-item-left">
                            <?php echo $wpforo->member->get_avatar( $topic['userid']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="wpforo-list-item-right" <?php if( !$instance['display_avatar'] ): ?> style="width:100%"<?php endif; ?>>
                        <p class="posttitle"><a href="<?php echo esc_url($topic_url) ?>"><?php echo esc_html($topic['title']) ?></a></p>
                        <p class="postuser"><?php wpforo_phrase('by') ?> <a href="<?php echo esc_url($wpforo->member->profile_url($member)) ?>"><?php echo esc_html($member['display_name']) ?></a>, <span style="white-space:nowrap;"><?php esc_html(wpforo_date($topic['created'])) ?></span></p>
                    </div>
                    <div class="wpf-clear"></div>
                </div>
            </li>
            <?php
		}
		echo '</ul></div>';
		echo '</div>';
		echo $args['after_widget'];//This is a HTML content//
	}
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Recent Topics';
		$count = ! empty( $instance['count'] ) ? $instance['count'] : '9';
		$display_avatar = isset( $instance['display_avatar'] ) ? (bool) $instance['display_avatar'] : false;
		?>
		<p>
			<label><?php _e('Title', 'wpforo'); ?>:</label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p><p>
			<label><?php _e('Number of Items', 'wpforo'); ?></label>&nbsp;
			<input type="number" min="1" style="width: 53px;" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>"   value="<?php echo esc_attr($count) ; ?>">
		</p><p>
			<label><input <?php checked( $display_avatar ); ?> type="checkbox"  name="<?php echo esc_attr($this->get_field_name( 'display_avatar' )); ?>" >
			<?php _e('Display with Avatars', 'wpforo'); ?></label>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : '';
		$instance['display_avatar'] = isset( $new_instance['display_avatar'] ) ? (bool) $new_instance['display_avatar'] : false;
		return $instance;
	}
} // Recent topics


class wpForo_Widget_recent_replies extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpForo_Widget_recent_replies', // Base ID
			'wpForo Recent Posts',        // Name
			array( 'description' => 'Your forum\'s recent posts.' ) // Args
		);
	}
	public function widget( $args, $instance ) {
		global $wpforo;
		echo $args['before_widget'];//This is a HTML content//
		echo '<div id="wpf-widget-recent-replies" class="wpforo-widget-wrap">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];//This is a HTML content//
		}
		// widget content from front end
		$posts_args = array( 
		  'orderby'		=> 'created', 	// forumid, order, parentid
		  'order'		=> 'DESC', 		// ASC DESC
		  'row_count'	=> $instance['count'] 		// 4 or 1 ...
		);
		$recent_posts = $wpforo->post->get_posts($posts_args);
		echo '<div class="wpforo-widget-content"><ul>';
		foreach( $recent_posts as $post ){
			if( !$wpforo->perm->forum_can( $post['forumid'], 'vf' ) ) continue;
			$post_url = $wpforo->post->get_post_url( $post );
			$member = $wpforo->member->get_member( $post['userid'] );
			?>
            <li>
                <div class="wpforo-list-item">
                    <?php if( $instance['display_avatar'] ): ?>
                        <div class="wpforo-list-item-left">
                            <?php echo $wpforo->member->get_avatar( $post['userid']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="wpforo-list-item-right" <?php if( !$instance['display_avatar'] ): ?> style="width:100%"<?php endif; ?>>
                        <p class="posttitle"><a href="<?php echo esc_url($post_url) ?>"><?php echo esc_html($post['title']) ?></a></p>
                        <p class="posttext"><?php echo esc_html(wpforo_text($post['body'], 55)); ?></p>
                        <p class="postuser"><?php wpforo_phrase('by') ?> <a href="<?php echo esc_url($wpforo->member->profile_url($member)) ?>"><?php echo esc_html($member['display_name']) ?></a>, <?php esc_html(wpforo_date($post['created'])) ?></p>
                    </div>
                    <div class="wpf-clear"></div>
                </div>
            </li>
            <?php
		}
		echo '</ul></div>';
		echo '</div>';
		echo $args['after_widget'];//This is a HTML content//
	}
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Recent Posts';
		$count = ! empty( $instance['count'] ) ? $instance['count'] : '9';
		$display_avatar = isset( $instance['display_avatar'] ) ? (bool) $instance['display_avatar'] : false;
		?>
		<p>
			<label><?php _e('Title', 'wpforo'); ?>:</label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p><p>
			<label><?php _e('Number of Items', 'wpforo'); ?></label>&nbsp;
			<input type="number" min="1" style="width: 53px;" name="<?php echo esc_attr($this->get_field_name( 'count' )); ?>"   value="<?php echo esc_attr($count) ; ?>">
		</p><p>
			<label><input <?php checked( $display_avatar ); ?> type="checkbox"  name="<?php echo esc_attr($this->get_field_name( 'display_avatar' )); ?>" >
			<?php _e('Display with Avatars', 'wpforo'); ?></label>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : '';
		$instance['display_avatar'] = isset( $new_instance['display_avatar'] ) ? (bool) $new_instance['display_avatar'] : false;
		return $instance;
	}
} // Recent replies


class wpforo_widget_forums extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpforo_widget_forums', // Base ID
			'wpForo Forums',        // Name
			array( 'description' => 'Forum tree.' ) // Args
		);
	}
	public function widget( $args, $instance ) {
		global $wpforo;
		echo $args['before_widget'];//This is a HTML content//
		echo '<div id="wpf-widget-forums" class="wpforo-widget-wrap">';
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];//This is a HTML content//
		}
		echo '<div class="wpforo-widget-content">';
		$wpforo->forum->tree('front_list');
		echo '</div>';
		echo '</div>';
		echo $args['after_widget'];//This is a HTML content//
	}
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Forums';
		$display_avatar = isset( $instance['display_avatar'] ) ? (bool) $instance['display_avatar'] : false;
		?>
		<p>
			<label><?php _e('Title', 'wpforo'); ?>:</label> 
			<input class="widefat" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // forums tree


function wpforo_widget_search() {
    register_widget( 'wpForo_Widget_search' );
}
add_action( 'widgets_init', 'wpforo_widget_search' );

function wpforo_widget_login() {
	//Under development....
    //register_widget( 'wpForo_Widget_login_form' );
}
add_action( 'widgets_init', 'wpforo_widget_login' );

function wpforo_widget_online_members() {
    register_widget( 'wpForo_Widget_online_members' );
}
add_action( 'widgets_init', 'wpforo_widget_online_members' );

function wpforo_widget_recent_topics() {
    register_widget( 'wpForo_Widget_recent_topics' );
}
add_action( 'widgets_init', 'wpforo_widget_recent_topics' );

function wpforo_widget_recent_replies() {
    register_widget( 'wpForo_Widget_recent_replies' );
}
add_action( 'widgets_init', 'wpforo_widget_recent_replies' );

function wpforo_widget_forums() {
	//Under Development
    //register_widget( 'wpforo_widget_forums' );
}
add_action( 'widgets_init', 'wpforo_widget_forums' );

function wpforo_post_edited($post, $echo = true){
	$edit_html = '';
	if(!empty($post)){
		$created = wpforo_date($post['created'], 'd/m/Y g:i a', false);
		$modified = wpforo_date($post['modified'], 'd/m/Y g:i a', false);
		if( isset($modified) && $created != $modified ){
			$edit_html = '<div class="wpf-post-edited">' . wpforo_phrase('Edited: ', false) . wpforo_date($post['modified'], 'ago', false) . '</div>';
		}
	}
	if( $echo ) { 
		echo $edit_html;
	}
	else{ 
		return $edit_html;
	}
}

function wpforo_hide_title($title, $id = 0) {
	global $wpforo;
	if( !wpforo_feature('page-title', $wpforo) ){
		if( $wpforo_base_slug = basename(WPFORO_BASE_URL) ) $wpforo_page = get_page_by_path($wpforo_base_slug);
		if(!empty($wpforo_page)){
			if (in_the_loop() && is_page($wpforo_page->ID) && $id == get_the_ID()) {
				$title = '';
			}
		}
	}
	return $title;
}
add_filter('the_title', 'wpforo_hide_title', 10, 2);


function wpforo_validate_gravatar( $email ) {
	$hashkey = md5(strtolower(trim($email)));
	$uri = 'http://www.gravatar.com/avatar/' . $hashkey . '?d=404';
	$data = wp_cache_get($hashkey);
	if (false === $data) {
		$response = wp_remote_head($uri);
		if( is_wp_error($response) ) {
			$data = 'not200';
		} else {
			$data = $response['response']['code'];
		}
	    wp_cache_set($hashkey, $data, $group = '', $expire = 60*5);
	}		
	if ($data == '200'){
		return true;
	} else {
		return false;
	}
}

function wpforo_member_title( $member = array(), $echo = true ){
	global $wpforo;
	if(empty($member) || !$member['groupid']) return;
	$enabled_for_usergroup = ( isset($wpforo->member_options['rating_title_ug'][$member['groupid']]) && $wpforo->member_options['rating_title_ug'][$member['groupid']] ) ? true : false ;
	
	if( wpforo_feature('rating_title', $wpforo) && $enabled_for_usergroup  ){
		$title = esc_html($member['stat']['title']);
	} 
	else{
        $title = wpforo_phrase($member['title']);
	}
	
	if( $echo ) { 
		echo $title;
	}
	else{ 
		return $title;
	}
}

function wpforo_member_badge( $member = array(), $sep = '', $type = 'full' ){
	global $wpforo;
	$enabled_for_usergroup = ( isset($wpforo->member_options['rating_badge_ug'][$member['groupid']]) && $wpforo->member_options['rating_badge_ug'][$member['groupid']] ) ? true : false ;
	if( wpforo_feature('rating', $wpforo) && $enabled_for_usergroup ): ?>
        <div class="author-rating-<?php echo esc_attr($type) ?>" style="color:<?php echo esc_attr($member['stat']['color']) ?>" title="<?php wpforo_phrase('Member Rating Badge') ?>">
            <?php echo $wpforo->member->rating_badge($member['stat']['rating'], $type); ?>
        </div><?php if($sep): ?><span class="author-rating-sep"><?php echo esc_html($sep); ?></span><?php endif; ?>
    <?php endif;
}

?>