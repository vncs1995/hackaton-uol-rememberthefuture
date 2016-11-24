<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;


class wpForoTemplate{
	
	private $wpforo;
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
		if(!is_admin()){
			add_filter("mce_external_plugins", array(&$this, 'add_tinymce_buttons'), 15);
			add_filter("tiny_mce_plugins", array(&$this, 'filter_tinymce_plugins'), 15);
			add_filter("wp_mce_translation", array(&$this, 'add_tinymce_translations'));
		}
	}
	
	function add_tinymce_buttons($plugin_array) {
	  $plugin_array = array();
	  $plugin_array['wpforo_pre_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-pre.js';
	  $plugin_array['wpforo_link_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-link.js';
	  $plugin_array['wpforo_source_code_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-code.js';
	  return $plugin_array;
	}
	
	function filter_tinymce_plugins($plugins){
		return array('hr','lists','textcolor');
	}
	
	function add_tinymce_translations($mce_translation){
		$mce_translation['Insert link'] = __( 'Insert link' );
		$mce_translation['Link Text'] = __( 'Link Text' );
		$mce_translation['Open link in a new tab'] = __( 'Open link in a new tab' );
		return $mce_translation;
	}
	
	function topic_form($forumid){
		if(!isset($this->wpforo->post_options['max_upload_size']) || !$this->wpforo->post_options['max_upload_size']){ $server_mus = wpforo_human_size_to_bytes(ini_get('upload_max_filesize')); if( !$server_mus || $server_mus > 10485760 ) $server_mus = 10485760; $this->wpforo->post_options['max_upload_size'] = $server_mus;}
		?>
		<div id="wpf-topic-create" class="wpf-topic-create">
			<form name="topic" action="" enctype="multipart/form-data" method="POST">
				<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                <input type="hidden" name="topic[action]" value="add"/>
				<input type="hidden" id="parent" name="topic[forumid]" value="<?php echo intval($forumid) ?>" />
				
				<label style="padding-left: 8px;"> <?php wpforo_phrase('Topic Title') ?>:&nbsp; </label>
				<input required="true" autofocus type="text" name="topic[title]" class="wpf-subject" value="" id="title" autocomplete="off" placeholder="<?php wpforo_phrase('Enter title here') ?>">
				<?php
				$content   = '';
				$editor_id = 'postbody';
				$settings  = array(
					'wpautop'      => true,// use wpautop?
					'media_buttons'=> FALSE,// show insert / upload button(s)
					'textarea_name'=> $editor_id,// set the textarea name to something different, square brackets [] can be used here
					'textarea_rows'=> get_option('default_post_edit_rows', 20),// rows = "..."
					'tabindex'=> '',
					'editor_css'   => '',	// intended for extra styles for both visual and HTML editors buttons, needs to include the < style > tags, can use "scoped".
					'editor_class'=> '',	// add extra class(es) to the editor textarea
					'teeny'=> FALSE,		// output the minimal editor config used in Press This
					'dfw'=> false,			// replace the default fullscreen with DFW (supported on the front - end in WordPress 3.4)
					'tinymce'=> array(
						'toolbar1' => 'bold,italic,underline,strikethrough,forecolor,bullist,numlist,hr,alignleft,aligncenter,alignright,alignjustify,link,unlink,blockquote,pre,undo,redo,source_code',
						'toolbar2' => '', 
						'toolbar3' => '', 
						'toolbar4' => ''
					),		// load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
					'quicktags'=> true, 		// load Quicktags, can be used to pass settings directly to Quicktags using an array()
					'default_editor' => 'tinymce'
				);
				wp_editor( $content, $editor_id, $settings );
				?>
				<div class="wpf-extra-fields">
                	<?php if($this->wpforo->perm->forum_can($forumid, 's')) : ?>
                    	<input id="t_sticky" name="topic[type]" type="checkbox" value="0">&nbsp;&nbsp;
                    	<i class="fa fa-exclamation fa-0x"></i>&nbsp;&nbsp;<label for="t_sticky" style="padding-bottom:2px; cursor: pointer;"><?php wpforo_phrase('Set Topic Sticky'); ?>&nbsp;</label>
                    <?php endif ?>
                    <?php if($this->wpforo->perm->forum_can($forumid, 'a')) : ?>
	                    <?php if(defined('WPFOROATTACH_BASENAME') && function_exists('wpforo_add_attach_button')): ?>
	                    	&nbsp;&nbsp;|&nbsp;&nbsp; <?php wpforo_add_attach_button(); ?>
	                    <?php else: ?>
		                    <div class="wpf-default-attachment" style="padding-top:5px;">
		                    	<label for="file"><?php wpforo_phrase('Attach file:') ?> </label> <input id="file" type="file" name="attachfile" />
								<p><?php wpforo_phrase('Maximum allowed file size is'); echo ' ' . wpforo_print_size($this->wpforo->post_options['max_upload_size']); ?></p>
							</div>
						<?php endif; ?>
					<?php endif; ?>
                </div>
				<input id="formbutton" type="submit" name="topic[save]" class="button button-primary forum_submit" value="<?php wpforo_phrase('Save') ?>">
                <div class="wpf-clear"></div>
			</form>
		</div>
		
		<?php
	}
	
	/**
	* 
	* @param array $args
	*  
	* Please note that all array elements are required!
	* example of args
	* $default = array(
	*	"topic_closed" => $topic['closed'], 	// is topic closed or opened (values 1 or 0)
	* 	"topicid" => $topic['topicid'],  		// the id of topic
	* 	"forumid" => $forum_data['forumid'],
	* 	"layout" => $cat_layout,
	* 	"topic_title" => $topic['title']		// the title of topic
	* );
	* 
	* @return html form
	*/
		
	function reply_form($args){ 
		extract($args, EXTR_OVERWRITE); ?>
		<!-- Report Dialog  -->
		
		<div id="reportdialog" title="<?php wpforo_phrase('Report to Administration') ?>" style="display: none">
			<form id="reportform">
				<input type="hidden" id="reportpostid" value=""/>
				<textarea required style="width:100%; height:105px;" id="reportmessagecontent" placeholder="<?php wpforo_phrase('Write message') ?>"></textarea>
			</form>
			<input style="float: right;" id="sendreport" type="submit" value="<?php wpforo_phrase('Send Report') ?>"/>
		</div>
		
		<!-- Report Dialog end -->
		
		<!-- Move Dialog  -->
		
		<div id="movedialog" title="<?php wpforo_phrase('Move topic') ?>" style="display: none">
			<div class="form-field">
				<label for="parent"><?php wpforo_phrase('Choose target forum') ?></label>
				<form id="topicmoveform" method="POST">
                <?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
				<input type="hidden" name="movetopicid" value="<?php echo intval($topicid) ?>"/>
				<input type="hidden" name="post[save]" value="move"/>
					<select id="parent" name="topic[forumid]" class="postform">
						<?php $this->wpforo->forum->tree('select_box', FALSE, $topicid ); ?>
					</select>
					<input type="submit"  value="<?php wpforo_phrase('Move') ?>"/>
				</form>
			</div>
		</div>
		
		<!-- move Dialog end -->
		<?php
		if( $topic_closed ) return;
		
		$head_html = '<p id="wpf-reply-form-title">'.wpforo_phrase('Leave a reply', false).'</p>';
		$head_html = apply_filters( 'wpforo_reply_form_head', $head_html, $args ); 
		if(!isset($this->wpforo->post_options['max_upload_size']) || !$this->wpforo->post_options['max_upload_size']){$server_mus = wpforo_human_size_to_bytes(ini_get('upload_max_filesize')); if( !$server_mus || $server_mus > 10485760 ) $server_mus = 10485760; $this->wpforo->post_options['max_upload_size'] = $server_mus;}
		?>
		<div id="wpf-form-wrapper">
			<?php echo $head_html; //this is a HTML content ?>
			<div id="wpf-post-create" class="wpf-post-create">
				<form name="post" action="" enctype="multipart/form-data" method="POST" class="editor">
					<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                    <input type="hidden" id="formaction" name="post[action]" value="add"/>
					<input type="hidden" id="formtopicid" name="post[topicid]" value="<?php echo intval($topicid) ?>"/>
					<input type="hidden" id="postparentid" name="post[parentid]" value="0"/>
					<input type="hidden" id="formpostid" name="post[postid]" value=""/>
					<input type="hidden" id="parent" name="post[forumid]" value="<?php echo intval($forumid) ?>" />
	                <?php 
					$reply_title = wpforo_phrase('RE', false) . ': '. $topic_title; 
					$reply_title = apply_filters( 'wpforo_reply_form_field_title', $reply_title, $args );
					$reply_title = esc_attr($reply_title);
					?>
					<input id="title" required="true" type="text" name="post[title]" class="wpf-subject" value="<?php if($reply_title) echo esc_attr($reply_title); ?>" autocomplete="off" placeholder="<?php if($reply_title) echo esc_attr($reply_title); ?>"><br/>
					<?php
					$content   = '';
					$editor_id = 'postbody';
					$settings  = array(
						'wpautop'      => true,// use wpautop?
						'media_buttons'=> FALSE,// show insert / upload button(s)
						'textarea_name'=> $editor_id,// set the textarea name to something different, square brackets [] can be used here
						'textarea_rows'=> get_option('default_post_edit_rows', 5),// rows = "..."
						'editor_class'=> 'wpeditor',	// add extra class(es) to the editor textarea
						'teeny'=> false,		// output the minimal editor config used in Press This
						'dfw'=> false,			// replace the default fullscreen with DFW (supported on the front - end in WordPress 3.4)
						'tinymce'=> array(
							'toolbar1' => 'bold,italic,underline,strikethrough,forecolor,bullist,numlist,hr,alignleft,aligncenter,alignright,alignjustify,link,unlink,blockquote,pre,undo,redo,source_code',
							'toolbar2' => '', 
							'toolbar3' => '', 
							'toolbar4' => ''
						),		// load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
						'quicktags'=> true, 		// load Quicktags, can be used to pass settings directly to Quicktags using an array()
						'default_editor' => 'tinymce' 		// load Quicktags, can be used to pass settings directly to Quicktags using an array()
					);
					wp_editor( $content, $editor_id, $settings );
					?>
					<div class="wpf-extra-fields">
						<?php if($this->wpforo->perm->forum_can($forumid, 'a')) : ?>
		                    <?php if(defined('WPFOROATTACH_BASENAME') && function_exists('wpforo_add_attach_button')): ?>
	                        	<?php wpforo_add_attach_button(); ?>
	                        <?php else: ?>
		                        <div class="wpf-default-attachment">
		                        	<label for="file"><?php wpforo_phrase('Attach file:') ?> </label> <input id="file" type="file" name="attachfile" />
									<p><?php wpforo_phrase('Maximum allowed file size is'); echo ' ' . wpforo_print_size($this->wpforo->post_options['max_upload_size']); ?></p>
								</div>
							<?php endif; ?>
						<?php endif; ?>
	                </div>
					<input id="formbutton" type="submit" name="post[save]" class="button button-primary forum_submit" value="<?php wpforo_phrase('Save') ?>">
	                <div class="wpf-clear"></div>
				</form>
			</div>
		</div>
		<?php
	}
	
	function pagenavi($paged, $items_count, $permalink = TRUE){
		if($items_count <= $this->wpforo->post_options['posts_per_page']) return;
		
		$pages_count = ceil($items_count/$this->wpforo->post_options['posts_per_page']);
		
		if($permalink){
			$url = trim( preg_replace('#\/paged\/[\d]+\/*.*$#is', '', wpforo_full_url()), '/' ) . '/paged/';
		}else{
			$url = trim( preg_replace('#[\&\?]wpfpaged=[\d]*.*$#is', '', wpforo_full_url()), '/' );
			$url .= (strpos($url, '?') === FALSE ? '?' : '&') . 'wpfpaged=';
		}
		?>
		
		<div class="wpf-navi">
            <div class="wpf-navi-wrap">
                <span class="wpf-page-info"><?php wpforo_phrase('Page') ?> <?php echo intval($paged) ?> / <?php echo intval($pages_count) ?></span>
                <?php if( $paged - 1 > 0 ): ?><a href="<?php echo esc_url($url) . ($paged - 1) ?>" class="wpf-prev-button"><i class="fa fa-chevron-left fa-sx"></i> <?php wpforo_phrase('prev') ?></a><?php endif ?>
                <select class="wpf-navi-dropdown" onchange="if (this.value) window.location.href=this.value" title="<?php wpforo_phrase('Select Page') ?>">	
                    <?php for($i = 1; $i <= $pages_count; $i++) : ?>
                        <option value="<?php echo esc_url($url) . $i ?>" <?php echo $paged == $i ? ' selected="selected"' : '' ?>><?php echo intval($i); ?></option>
                    <?php endfor; ?>
                </select>
                <?php if( $paged + 1 <= $pages_count ): ?><a href="<?php echo esc_url($url) . ($paged + 1) ?>" class="wpf-prev-button"><?php wpforo_phrase('next') ?> <i class="fa fa-chevron-right fa-sx"></i></a><?php endif ?>
            </div>
		</div>
		
		<?php 
	} 
	
	function likers($postid){
		if(!$postid) return '';
		
		$return = '';
		if( $l_count =  $this->wpforo->post->get_post_likes_count($postid) ){
			$l_usernames = $this->wpforo->post->get_likers_usernames($postid);
			if($l_usernames[0]['ID'] == $this->wpforo->current_userid) $l_usernames[0]['display_name'] = wpforo_phrase('You', FALSE);
			if($l_count == 1){
				$return = sprintf( wpforo_phrase('%s liked', FALSE), '<a href="' . esc_url($this->wpforo->member->get_profile_url($l_usernames[0]['ID'])) . '">'.esc_html($l_usernames[0]['display_name']).'</a>' );
			}elseif($l_count == 2){
				$return = sprintf( wpforo_phrase('%s and %s liked', FALSE), '<a href="' . esc_url($this->wpforo->member->get_profile_url($l_usernames[0]['ID'])) . '">'.esc_html($l_usernames[0]['display_name']).'</a>', '<a href="'.esc_url($this->wpforo->member->get_profile_url($l_usernames[1]['ID'])).'">'.esc_html($l_usernames[1]['display_name']).'</a>' );
			}elseif($l_count == 3){
				$return = sprintf( wpforo_phrase('%s, %s and %s liked', FALSE), '<a href="' . esc_url($this->wpforo->member->get_profile_url($l_usernames[0]['ID'])) .'">'.esc_html($l_usernames[0]['display_name']).'</a>', '<a href="'.esc_url($this->wpforo->member->get_profile_url($l_usernames[1]['ID'])).'">'.esc_html($l_usernames[1]['display_name']).'</a>', '<a href="'.esc_url($this->wpforo->member->get_profile_url($l_usernames[2]['ID'])).'">'.esc_html($l_usernames[2]['display_name']).'</a>' );
			}elseif($l_count >= 4){
				$l_count = $l_count - 3;
				$return = sprintf( wpforo_phrase('%s, %s, %s and %d people liked', FALSE), '<a href="' . esc_url($this->wpforo->member->get_profile_url($l_usernames[0]['ID'])) .'">'.esc_html($l_usernames[0]['display_name']).'</a>', '<a href="'.esc_url($this->wpforo->member->get_profile_url($l_usernames[1]['ID'])).'">'.esc_html($l_usernames[1]['display_name']).'</a>', '<a href="'.esc_url($this->wpforo->member->get_profile_url($l_usernames[2]['ID'])).'">'.esc_html($l_usernames[2]['display_name']).'</a>', $l_count );
			}
		}
		return $return;
	}

	
	/**
	* Get actions buttons
	* 
	* @since 1.0.0
	* 
	* @param array buttons names function will return buttons by this array
	* 
	* @param int $forumid required
	* 
	* @param int $topicid required
	* 
	* @param int $postid required
	* 
	* @param int $is_topic required this is a first post in the loop
	* 
	* $buttons = array( 'reply', 'answer', 'comment', 'quote', 'like', 'report', 'sticky', 'close', 'move', 'edit', 'delete', 'link' );
	* 
	* @return html ( buttons )
	*/
	
	function buttons( $buttons, $forumid, $topicid, $postid, $is_topic = FALSE ){
		
		$button_html = array();
		
		foreach($buttons as $button){
			
			switch($button){
				
				case 'reply': 
					if( $this->wpforo->perm->forum_can($forumid, 'cr') ){
			   			$button_html[] = '<span class="wpforo-reply wpf-action add_post_button"><i class="fa fa-reply fa-rotate-180"></i>' . wpforo_phrase('Reply', false).'</span>';
			   		}else{
			   			$button_html[] = '<span class="wpf-action not_reg_user"><i class="fa fa-reply fa-rotate-180"></i> ' . wpforo_phrase('Reply', false).'</span>';
			   		}
					break; 
				case 'answer': 
					if( $this->wpforo->perm->forum_can($forumid, 'cr') ){
			   			$button_html[] = '<span class="wpforo-answer wpf-button add_post_button"><i class="fa fa-pencil"></i> ' . wpforo_phrase('Answer', false).'</span>';
			   		}else{
			   			$button_html[] = '<span class="wpf-button not_reg_user"><i class="fa fa-pencil"></i> ' . wpforo_phrase('Answer', false).'</span>';
			   		}
				 	break; 
				case 'comment': 
					$title = wpforo_phrase('Use comments to ask for more information or suggest improvements. Avoid answering questions in comments.', false);
					if( $this->wpforo->perm->forum_can($forumid, 'cr') ) {
						$button_html[] = '<span id="parentpostid'.intval($postid).'" class="wpforo-childreply wpf-button add_post_button" title="'.esc_attr($title).'"><i class="fa fa-comment"></i> ' . wpforo_phrase('Add a comment', false).'</span>';
			   		}else{
			   			$button_html[] = '<span class="not_reg_user wpf-button add_post_button" title="'.esc_attr($title).'"><i class="fa fa-comment"></i> ' . wpforo_phrase('Add a comment', false).'</span>';
			   		}
				 	break; 
				case 'quote':
					if( $this->wpforo->perm->forum_can($forumid, 'cr') ) {
						$button_html[] = '<span id="wpfquotepost'.intval($postid).'" class="wpforo-quote wpf-action"><i class="fa fa-quote-left fa-0x"></i>' . wpforo_phrase('Quote', false).'</span>';
			   		}else{
			   			$button_html[] = '<span class="wpf-action not_reg_user"><i class="fa fa-quote-left fa-0x"></i>' . wpforo_phrase('Quote', false).'</span>';
			   		}	
					 break; 
				case 'like':
					if( $this->wpforo->perm->forum_can($forumid, 'l') ) {
						$like_status = ( $this->wpforo->post->is_liked( $postid, $this->wpforo->current_userid ) === FALSE ? 'wpforo-like' : 'wpforo-unlike' );
						$like_icode = ( $like_status == 'wpforo-like') ? 'up' : 'down';
						$button_html[] = '<span  id="wpf'. intval($postid) .'" class="wpf-action '. sanitize_html_class($like_status) .'"><i id="like'. intval($postid) .'" class="fa fa-thumbs-o-'. esc_attr($like_icode) .' fa-0x"></i><span id="liketext'. intval($postid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $like_status), false) . '</span></span>';
					}	
				 	break; 
				case 'report':
					if( $this->wpforo->perm->forum_can($forumid, 'r') ) {
						$button_html[] = '<span id="wpfreport'. intval($postid) .'" class="wpf-action wpforo-report"><i class="fa fa-exclamation-triangle"></i>' . wpforo_phrase('Report', false).'</span>';
					}	
				 	break; 
				case 'sticky':
					if( $this->wpforo->perm->forum_can($forumid, 's') ) {
						$sticky_status = ( $this->wpforo->topic->is_sticky($topicid ) ? 'wpforo-unsticky' : 'wpforo-sticky');
						$button_html[] = '<span id="wpfsticky'. intval($topicid) .'" class="wpf-action '. sanitize_html_class($sticky_status) .'"><i class="fa fa-exclamation fa-0x"></i><span id="stickytext'. intval($topicid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $sticky_status), false).'</span></span>';
					}
				 	break; 
				case 'solved':
					$post = $this->wpforo->post->get_post($postid);
					if( $this->wpforo->perm->forum_can($forumid, 'sv') || ($this->wpforo->current_userid == $post['userid'] && $this->wpforo->perm->forum_can($forumid, 'osv')) ) {
						$solved_status = ( $this->wpforo->topic->is_solved($topicid) ? 'wpforo-unsolved' : 'wpforo-solved');
						$button_html[] = '<span id="wpfsolved'. intval($postid) .'" class="wpf-action '. sanitize_html_class($solved_status) .'"><i class="fa fa-check-circle fa-0x"></i><span id="solvedtext'. intval($postid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $solved_status), false).'</span></span>';
                    }
				 	break; 
				case 'close':
					if( $this->wpforo->perm->forum_can($forumid, 'cot') ) {
						$open_status = ( $this->wpforo->topic->is_closed($topicid) ? 'wpforo-open' : 'wpforo-close' );
						$open_icon = ($open_status == 'wpforo-open') ? 'unlock' : 'lock';
						$button_html[] = '<span id="wpfclose'. intval($topicid) .'" class="wpf-action '. sanitize_html_class($open_status) .'"><i id="closeicon'. intval($topicid) .'" class="fa fa-'. esc_attr($open_icon) .' fa-0x"></i><span id="closetext'. intval($topicid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $open_status), false).'</span></span>';
					}
				 	break; 
				case 'move':
					if( $this->wpforo->perm->forum_can($forumid, 'mt') ) {
						$button_html[] = '<span class="wpf-action wpforo-move"><i class="fa fa-share-square-o fa-0x"></i>' . wpforo_phrase('Move', false).'</span>';	
					}
				 	break; 
				case 'edit':
						$post = $this->wpforo->post->get_post($postid);
						$diff = current_time( 'timestamp', 1 ) - strtotime($post['created']);
                        if( $this->wpforo->perm->forum_can($forumid, ($is_topic ? 'et' : 'er') ) || ($this->wpforo->current_userid == $post['userid'] && $this->wpforo->perm->forum_can($forumid, ($is_topic ? 'eot' : 'eor' )) && $diff < $this->wpforo->post_options[($is_topic ? 'eot' : 'eor' ).'_durr'] ) ) {
                            $a = ( $is_topic ) ? 'wpfedittopicpid' : ''; 
                            $b = ( $is_topic ) ? $postid : $postid;
                            $button_html[] = '<span id="'. esc_attr( $a . $b ) .'" class="wpforo-edit wpf-action"><i class="fa fa-edit fa-0x"></i>' . wpforo_phrase('Edit', false).'</span>';
                        }
				 	break; 
				case 'delete':
					$post = $this->wpforo->post->get_post($postid);
					$diff = current_time( 'timestamp', 1 ) - strtotime($post['created']);
					if( $this->wpforo->perm->forum_can($forumid, ($is_topic ? 'dt' : 'dr' )) || ($this->wpforo->current_userid == $post['userid'] && $this->wpforo->perm->forum_can($forumid, ($is_topic ? 'dot' : 'dor' )) && $diff < $this->wpforo->post_options[($is_topic ? 'dot' : 'dor' ).'_durr']) ){
						$a = ( $is_topic ) ? 'wpftopicdelete' : 'wpfreplydelete'; 
						$b = ( $is_topic ) ? $topicid : $postid;
						$button_html[] = '<span id="'. esc_attr( $a . $b ) .'" class="wpf-action wpforo-delete"><i class="fa fa-times fa-0x"></i>' . wpforo_phrase('Delete', false).'</span>';
					}
				 	break; 
				case 'link':
					$url = ( $is_topic ) ? $this->wpforo->topic->get_topic_url( $topicid ) : $this->wpforo->post->get_post_url( $postid );
					$button_html[] = '<a href="'. esc_url($url) .'"><i class="fa fa-link fa-0x"></i></a>';
				 	break; 
				case 'positivevote':
					if( $this->wpforo->perm->forum_can($forumid, 'v') ) {
						$button_html[] = '<i itemtype="' . ( $is_topic ? 'topic' : 'reply' ) . '" id="wpfvote-up-'. intval($postid) .'" class="voteup fa fa-play fa-rotate-270 wpfcl-0"></i>';
					}else{
						$button_html[] = '<i class="not_reg_user fa fa-play fa-rotate-270 wpfcl-0"></i>';
					}
				 	break; 
				case 'negativevote':
					if( $this->wpforo->perm->forum_can($forumid, 'v') ) {
						$button_html[] = '<i itemtype="' . ( $is_topic ? 'topic' : 'reply' ) . '" id="wpfvote-down-'. intval($postid) .'" class="votedown fa fa-play fa-rotate-90 wpfcl-0"></i>';
					}else{
						$button_html[] = '<i class="not_reg_user fa fa-play fa-rotate-90 wpfcl-0"></i>';
					}
				 	break; 
				case 'isanswer':
					$is_answer = $this->wpforo->post->is_answered( $postid );
					$is_answer = ( $is_answer == 0 )  ? '-not' : '';
					if( is_user_logged_in() ){
						$button_html[] = '<div id="wpf-answer-'. intval($postid) .'" class="wpf-toggle'. esc_attr($is_answer) .'-answer"><i class="fa fa-check"></i></div>';
					}else{
						$button_html[] = '<div class="wpf-toggle'. esc_attr($is_answer) .'-answer not_reg_user"><i class="fa fa-check"></i></div>';
					}
				 	break; 
			} //switch
		} //foreach
		
		echo implode('', $button_html);
		
	}
	
	function breadcrumb($url_data){
		extract($url_data, EXTR_OVERWRITE);
		
		switch($template) :
			case 'search': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
			        <a href="#" class="active"><?php wpforo_phrase('Search') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
			    
			<?php break;
			case 'signup': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
			        <a href="#" class="active"><?php wpforo_phrase('Register') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
			    
			<?php break;
			case 'signin': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
			        <a href="#" class="active"><?php wpforo_phrase('Login') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
			    
			<?php break;
			case 'members': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
			        <?php if(isset($_GET['wpfms'])) : ?>
			        	
			        	<a href="<?php echo WPFORO_BASE_URL ?>members/"><?php wpforo_phrase('Members') ?></a>
			        	<a href="#" class="active"><?php wpforo_phrase('Search') ?></a>
			        	
			        <?php else : ?>
			        	
			        	<a href="#" class="active"><?php wpforo_phrase('Members') ?></a>
			        	
			        <?php endif ?>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
			    
			<?php break;
			case 'profile': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
			        <a href="<?php echo WPFORO_BASE_URL ?>members/"><?php wpforo_phrase('Members') ?></a>
			        <a href="#" class="active"><?php wpforo_text( !empty($user['display_name']) ? esc_html($user['display_name']) : !empty($user['user_nicename']) ? esc_html($user['user_nicename']) : '', 19 ) ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
			    
			<?php break;
			case 'account': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i>
			        
			        <a href="<?php echo WPFORO_BASE_URL ?>members/"><?php wpforo_phrase('Members') ?></a>
			        <a href="<?php echo esc_url($this->wpforo->member->get_profile_url($userid)) ?>"><?php wpforo_text( !empty($user['display_name']) ? esc_html($user['display_name']) : !empty($user['user_nicename']) ? esc_html($user['user_nicename']) : '', 19 ) ?></a>
			        <a href="#" class="active"><?php wpforo_phrase('Account') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
				
			<?php break;
			case 'activity': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i>
			        
			        <a href="<?php echo WPFORO_BASE_URL ?>members/"><?php wpforo_phrase('Members') ?></a>
			        <a href="<?php echo esc_url($this->wpforo->member->get_profile_url($userid)) ?>"><?php wpforo_text( !empty($user['display_name']) ? esc_html($user['display_name']) : !empty($user['user_nicename']) ? esc_html($user['user_nicename']) : '', 19 ) ?></a>
			        <a href="#" class="active"><?php wpforo_phrase('Activity') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
				
			<?php break;
			case 'subscriptions': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i>
			        
			        <a href="<?php echo WPFORO_BASE_URL ?>members/"><?php wpforo_phrase('Members') ?></a>
			        <a href="<?php echo esc_url($this->wpforo->member->get_profile_url($userid)) ?>"><?php wpforo_text( !empty($user['display_name']) ? esc_html($user['display_name']) : !empty($user['user_nicename']) ? esc_html($user['user_nicename']) : '', 19 ) ?></a>
			        <a href="#" class="active"><?php wpforo_phrase('Subscriptions') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
				
			<?php break;
//			TODO: move code to pm plugin
			case 'messages': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo WPFORO_BASE_URL ?>" class="wpf-root" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i>
			        
			        <a href="<?php echo WPFORO_BASE_URL ?>members/"><?php wpforo_phrase('Members') ?></a>
			        
			        <?php if(!empty($user)) : ?>
			        	
			        	<a href="<?php echo esc_url($this->wpforo->member->get_profile_url($userid)) ?>"><?php wpforo_text( !empty($user['display_name']) ? esc_html($user['display_name']) : !empty($user['user_nicename']) ? esc_html($user['user_nicename']) : '', 19 ) ?></a>
			        	
			        <?php endif ?>
			        
			        <a href="#" class="active"><?php wpforo_phrase('Messages') ?></a>
			        
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
				
			<?php break;
			case 'topic': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo ( !isset($forumid) ? '#' : WPFORO_BASE_URL ) ?>" class="wpf-root<?php echo ( !isset($forumid) ? ' active' : '' ) ?>" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
					<?php if(isset($forumid)) : ?>
						<?php $relative_ids = array();
						$this->wpforo->forum->get_all_relative_ids($forumid, $relative_ids);
						foreach( $relative_ids as $key => $rel_forumid ) : ?>
							<?php $forum = $this->wpforo->forum->get_forum($rel_forumid) ?>
							<?php if( $key != ( count($relative_ids) - 1 ) ) : ?>
								
								<a href="<?php echo esc_url($this->wpforo->forum->get_forum_url($rel_forumid)) ?>" title="<?php echo esc_html($forum['title']) ?>"><?php wpforo_text($forum['title'], 19) ?></a>
								
							<?php else : ?>
								
								<a href="#" class="active" title="<?php echo esc_html($forum['title']) ?>"><?php wpforo_text($forum['title'], 19) ?></a>
								
							<?php endif ?>
						<?php endforeach ?>
					<?php endif ?>
					
					<a href="#" class="wpf-end">&nbsp;</a>
				</div>
				
			<?php break;
			case 'post': ?>
				
				<div class="wpf-breadcrumb">
			        <a href="<?php echo ( !isset($forumid) ? '#' : WPFORO_BASE_URL ) ?>" class="wpf-root<?php echo ( !isset($forumid) ? ' active' : '' ) ?>" title="<?php wpforo_phrase('Forums') ?>"><i class="fa fa-home"></i></a>
			        
					<?php if(isset($forumid)) : ?>
						<?php $relative_ids = array();
						$this->wpforo->forum->get_all_relative_ids($forumid, $relative_ids);
						foreach( $relative_ids as $key => $rel_forumid ) : ?>
							<?php $forum = $this->wpforo->forum->get_forum($rel_forumid) ?>
							
							<a href="<?php echo esc_url($this->wpforo->forum->get_forum_url($rel_forumid)) ?>" title="<?php echo esc_html($forum['title']) ?>"><?php wpforo_text($forum['title'], 19) ?></a>
							
						<?php endforeach ?>
					<?php endif ?>
					<?php if(isset($topicid)) : ?>
						<?php $topic = $this->wpforo->topic->get_topic($topicid) ?>
						
						<a href="#" class="active" title="<?php echo esc_html($forum['title']) ?>"><?php wpforo_text($topic['title'], 19) ?></a>
						
					<?php endif ?>
					<a href="#" class="wpf-end">&nbsp;</a>
				</div>
				
			<?php break;
			default: ?>
				
				<div class="wpf-breadcrumb">
			        <a href="#" class="wpf-root active"><?php wpforo_phrase('Forums') ?></a>
			        <a href="#" class="wpf-end">&nbsp;</a>
			    </div>
			    
			<?php
		endswitch;
		
	}
	
	function icon($type, $item = array(), $echo = true, $data = 'icon' ){
		
		$icon = array();
		$status = false;
		if(isset($item['type'])){
			
			if( $type == 'topic' ){
				if($this->wpforo->topic->is_solved($item['topicid'])){
					$icon['class'] = 'fa-check-circle';
					$icon['color'] = 'wpfcl-8';
					$icon['title'] = wpforo_phrase('Solved', false);
					if($echo) { 
						$status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; 
					} 
					else{ 
						return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; 
					}
				}
			}
			
			if( $item['closed'] && $item['type'] == 1 ){
				$icon['class'] = 'fa-lock';
				$icon['color'] = 'wpfcl-1';
				$icon['title'] = wpforo_phrase('Closed', false);
				if($echo) { $status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			elseif( $item['closed'] && $item['type'] != 1  ){
				$icon['class'] = 'fa-lock';
				$icon['color'] = 'wpfcl-1';
				$icon['title'] = wpforo_phrase('Closed', false);
				if($echo) { $status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			elseif( !$item['closed'] && $item['type'] == 1  ){
				$icon['class'] = 'fa-thumb-tack';
				$icon['color'] = 'wpfcl-5';
				$icon['title'] = wpforo_phrase('Sticky', false);
				if($echo) { $status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			
			if( $status ){
				//do nothing
			}
			else{
				if( $type == 'forum' ){
					$icon['class'] = 'fa-comments';
					$icon['color'] = 'wpfcl-2';
				}
				elseif( $type == 'topic' ){
					if( $item['posts'] == 1 ){
						$icon['class'] = 'fa-file-o';
						$icon['color'] = 'wpfcl-2';
						$icon['title'] = '';
					}
					elseif( $item['posts'] > 1 && $item['posts'] <= 5 ){
						$icon['class'] = 'fa-file-text-o';
						$icon['color'] = 'wpfcl-2';
						$icon['title'] = '';
					}
					elseif( $item['posts'] > 5 && $item['posts'] <= 20 ){
						$icon['class'] = 'fa-file-text';
						$icon['color'] = 'wpfcl-2';
						$icon['title'] = '';
					}
					elseif( $item['posts'] > 20 ){
						$icon['class'] = 'fa-file-text';
						$icon['color'] = 'wpfcl-5';
						$icon['title'] = '';
					}
					else{
						$icon['class'] = 'fa-file-o';
						$icon['color'] = 'wpfcl-2';
						$icon['title'] = '';
					}
				}
				if($echo) { echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			
		}
		else{
			return false;
		}
		
	}
	
	public function init_member_templates(){
		$this->wpforo->member_tpls = array(
			'account' => wpftpl('profile-account.php'),
			'activity' => wpftpl('profile-activity.php'),
			'subscriptions' => wpftpl('profile-subscriptions.php')
		);
		$this->wpforo->member_tpls = apply_filters('wpforo_member_templates_filter', $this->wpforo->member_tpls);
		$this->wpforo->member_tpls['profile'] = wpftpl('profile-home.php');
	}
	
	function has_menu(){
		return has_nav_menu( 'wpforo-menu' );
	}
	
	function nav_menu(){
		if ( has_nav_menu( 'wpforo-menu' ) ){
			$defaults = array(
				'theme_location'  => 'wpforo-menu',
				'menu'            => '',
				'container'       => '',
				'container_class' => '',
				'container_id'    => '',
				'menu_class'      => 'wpf-menu',
				'menu_id'         => 'wpf-menu',
				'echo'            => true,
				'fallback_cb'     => 'wp_page_menu',
				'before'          => '',
				'after'           => '',
				'link_before'     => '',
				'link_after'      => '',
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'depth'           => 0,
				'walker'          => ''
			);
			wp_nav_menu( $defaults );
		}
	}
	
	function init_nav_menu(){
		
		if(isset($this->wpforo->current_object) && !empty($this->wpforo->current_object)){
			
			extract($this->wpforo->current_object, EXTR_OVERWRITE);
			
			$this->wpforo->menu['wpforo-home'] = array(
				'href' => WPFORO_BASE_URL,
				'label' => wpforo_phrase('forums', FALSE),
				'attr' => ((($template == 'forum' || $template == 'topic' || $template == 'post') && !isset($_GET['wpforo'])) ? ' class="wpforo-active"' : '' ),
				'submenues' => array()
			);
			$this->wpforo->menu['wpforo-members'] = array(
				'href' => WPFORO_BASE_URL . 'members/',
				'label' => wpforo_phrase('members', FALSE),
				'attr' => ( $template == 'members' ? ' class="wpforo-active"' : '' ),
				'submenues' => array()
			);
			
			if( is_user_logged_in() ){
				
				$this->wpforo->menu['wpforo-profile-home'] = array(
					'href' => $this->wpforo->member->get_profile_url($this->wpforo->current_userid),
					'label' => wpforo_phrase('my profile', FALSE),
					'attr' => ( isset($this->wpforo->member_tpls[$template]) && $this->wpforo->member_tpls[$template] ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				$this->wpforo->menu['wpforo-profile-account'] = array(
					'href' => $this->wpforo->member->get_profile_url($this->wpforo->current_userid, 'account'),
					'label' => wpforo_phrase('account', FALSE),
					'attr' => ( $template == 'account' ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				$this->wpforo->menu['wpforo-profile-activity'] = array(
					'href' => $this->wpforo->member->get_profile_url($this->wpforo->current_userid, 'activity'),
					'label' => wpforo_phrase('activity', FALSE),
					'attr' => ( $template == 'activity' ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				$this->wpforo->menu['wpforo-profile-subscriptions'] = array(
					'href' => $this->wpforo->member->get_profile_url($this->wpforo->current_userid, 'subscriptions'),
					'label' => wpforo_phrase('subscriptions', FALSE),
					'attr' => ( $template == 'subscriptions' ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				$this->wpforo->menu['wpforo-logout'] = array(
					'href' => WPFORO_BASE_URL . '?wpforo=logout',
					'label' => wpforo_phrase('logout', FALSE),
					'attr' => '',
					'submenues' => array()
				);
				
			}else{
				
				if( wpforo_feature('user-register', $this->wpforo) ){
					$this->wpforo->menu['wpforo-register'] = array(
						'href' => wpforo_register_url(),
						'label' => wpforo_phrase('register', FALSE),
						'attr' => ( isset($_GET['wpforo']) && $_GET['wpforo'] == 'signup' ? ' class="wpforo-active"' : '' ),
						'submenues' => array()
					);
				}
				$this->wpforo->menu['wpforo-login'] = array(
					'href' => wpforo_login_url(),
					'label' => wpforo_phrase('login', FALSE),
					'attr' => ( isset($_GET['wpforo']) && $_GET['wpforo'] == 'signin' ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
			}
			
			$this->wpforo->menu = apply_filters('wpforo_menu_array_filter', $this->wpforo->menu);
		}
	}
	
	/**
	*
	* Checks in current active theme options if certain layout exists.
	*
	* @since 1.0.0
	*
	* @param  mixed 	$identifier			Layout id (folder name) OR @layout variable in header ( 1 or Extended )
	* @param  string	$identifier_type	The type of first parameter 'id' OR 'name' (@layout)
	*
	* @return boolean						true/false
	* 
	**/
	function layout_exists( $identifier, $identifier_type = 'id' ){
		
		$layouts = $this->wpforo->theme_options['layouts'];
		
		if( $identifier_type == 'id' ){
			if( isset($layouts[$identifier]) && !empty($layouts[$identifier])){
				return true;
			}
			else{
				return false;
			}
		}
		elseif( $identifier_type = 'name' ){
			foreach( $layouts as $id => $layout ){
				if( !isset($layout['name']) && $layout['name'] == $identifier ){
					return true;
				}
			}
			return false;
		}
	}
	
	/**
	*
	* Finds and returns all layouts information in array from theme's /layouts/ folder
	*
	* @since 1.0.0
	*
	* @param  string 	$theme		Theme id ( folder name ) e.g. 'classic'
	*
	* @return array
	* 
	**/
	function find_layouts( $theme ){
		$layout_data = array();
		$layouts = $this->find_themes('/'.$theme.'/layouts', 'php', 'layout');
		if(!empty($layouts)){
			foreach( $layouts as $layout ){
				$lid = trim(basename(dirname( $layout['file']['value'] )), '/');
				$layout_data[$lid]['id'] = $lid;
				$layout_data[$lid]['name'] = $layout['name']['value'];
				$layout_data[$lid]['version'] = $layout['version']['value'];
				$layout_data[$lid]['description'] = $layout['description']['value'];
				$layout_data[$lid]['author'] = $layout['author']['value'];
				$layout_data[$lid]['url'] = $layout['layout_url']['value'];
				$layout_data[$lid]['file'] = $layout['file']['value'];
			}
		}
		return $layout_data;
	}
	
	function show_layout_selectbox($layoutid = 0){
		$layouts = $this->find_layouts( WPFORO_THEME );
		if( !empty($layouts) ){
			foreach( $layouts as $layout ) : ?>  
				<option value="<?php echo esc_attr(trim($layout['id'])) ?>" <?php echo ( $layoutid == $layout['id'] ? 'selected' : '' ); ?> ><?php echo esc_html($layout['name']) ?></option>
				<?php
			endforeach;
		}
	}
	
	/**
	*
	* Finds and returns styles array from theme's /styles/colors.php file
	*
	* @since 1.0.0
	*
	* @param  string 	$theme		Theme id ( folder name ) e.g. 'classic'
	*
	* @return array
	* 
	**/
	function find_styles( $theme ){
		$colors = array();
		$color_file = WPFORO_THEME_DIR . '/' . $theme . '/styles/colors.php';
		if( file_exists($color_file) ){
			include( $color_file );
		}
		return $colors;
	}
	
	/**
	*
	* Scans certain theme directory and returns all information in array ( theme header, layouts, styles ).
	*
	* @since 1.0.0
	*
	* @param  string 	$theme_file			Theme folder name or main css file base path ( 'classic' OR classic/style.css' )
	*
	* @return array
	* 
	**/
	function find_theme( $theme_file ){
		$theme = array();
		$theme_file = trim(trim($theme_file, '/'));
		
		if( preg_match('|\.[\w\d]{2,4}$|is', $theme_file) ){
			$theme_folder = trim(basename(dirname($theme_file)), '/');
		}
		else{
			$theme_folder = $theme_file;
			$theme_file = $theme_file . '/style.css';
		}
		
		if( !is_readable( WPFORO_THEME_DIR . '/' . $theme_file ) ){
			return $theme['error'] = __('Theme file not readable', 'wpforo') .' ('.$theme_file.')';
		}
		else{
			$theme_data = $this->find_theme_headers( WPFORO_THEME_DIR . '/' . $theme_file );
			$theme['id'] = $theme_folder;
			$theme['name'] = $theme_data['name']['value'];
			$theme['version'] = $theme_data['version']['value'];
			$theme['description'] = $theme_data['description']['value'];
			$theme['author'] = $theme_data['author']['value'];
			$theme['url'] = $theme_data['theme_url']['value'];
			$theme['file'] = $theme_file;
			$theme['folder'] = $theme_folder;
			$theme['layouts'] = $this->find_layouts( $theme_folder );
			$styles = $this->find_styles( $theme_folder );
			if(!empty($styles)){
				reset($styles);
				$theme['style'] = key($styles);
				$theme['styles'] = $styles;
			}
			return $theme;
		}
		
	}
	
	/**
	*
	* Scans wpForo themes (wpf-themes) folder, reads main files' headers and returns information about all themes in array.
	* This function can also be used to scan and get information about layouts in each theme /layouts/ folder.
	*
	* @since 1.0.0
	*
	* @param  string 	$base_dir		Absolute path to scan directory (e.g. /home/public_html/wp-content/plugins/wpforo/wpf-themes/) 
	* @param  string 	$ext			File extension which may contain header information
	* @param  string 	$mode			'theme' or 'layout'
	*
	* @return array
	* 
	**/
	function find_themes( $base_dir = '', $ext = 'css', $mode = 'theme' ){
		$themes = array ();
		$themes_dir = @opendir( WPFORO_THEME_DIR . $base_dir );
		$theme_files = array();
		if( $themes_dir ){
			while( ($file = readdir( $themes_dir )) !== false ){
				if( substr($file, 0, 1) == '.' ) continue;
				if( is_dir( WPFORO_THEME_DIR . $base_dir .'/'.$file ) ){
					$themes_subdir = @opendir( WPFORO_THEME_DIR . $base_dir .'/'.$file );
					if( $themes_subdir ){
						while(($subfile = readdir( $themes_subdir ) ) !== false ){
							if( substr($subfile, 0, 1) == '.' ) continue;
							if( substr($subfile, -4) == '.' . $ext ) $theme_files[] = "$file/$subfile";
						}
						closedir( $themes_subdir );
					}
				} 
				else{
					if( substr($file, -4) == '.' . $ext ) $theme_files[] = $file;
				}
			}
			closedir( $themes_dir );
		}
		if( empty($theme_files) ) return $themes;
		foreach( $theme_files as $theme_file ){
			if( !is_readable( WPFORO_THEME_DIR . $base_dir . '/' . $theme_file ) ) continue;
			if( $mode == 'theme' ){
				$theme_data = $this->find_theme_headers( WPFORO_THEME_DIR . $base_dir . '/' . $theme_file );
			}
			elseif( $mode == 'layout' ){
				$theme_data = $this->find_layout_headers( WPFORO_THEME_DIR . $base_dir . '/' . $theme_file );
			}
			if( empty($theme_data['name']['value']) ) continue;
			$themes[wpforo_clear_basename($theme_file)] = $theme_data;
		}
		return $themes;
	}
	
	/**
	*
	* Reads theme main file's header variables and returns information in array.
	*
	* @since 1.0.0
	*
	* @param  string 	$file	Absolute path to file (e.g. /home/public_html/wp-content/plugins/wpforo/wpf-themes/style.css) 
	*
	* @return array
	* 
	**/
	function find_theme_headers( $file ){
		$theme_headers = array();
		$headers = array(
			'name' => 'Theme Name',
			'version' => 'Version',
			'description' => 'Description',
			'author' => 'Author',
			'theme_url' => 'Theme URI',
		);
		$fp = fopen( $file, 'r' );
		$data = fread( $fp, 8192 );
		fclose( $fp );
		$data = str_replace( "\r", "\n", $data );
		foreach ( $headers as $header_key => $header_name ){
			if ( preg_match( '|^[\s\t\/*#@]*' . preg_quote( $header_name, '|' ) . ':(.*)$|mi', $data, $match ) && $match[1] ){
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			}
			else{
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = '';
			}
		}
		$theme_headers['file']['name'] = 'file';
		$theme_headers['file']['value'] = $file;
		return $theme_headers;
	}
	
	/**
	*
	* Reads layout main file's header variables and returns information in array.
	*
	* @since 1.0.0
	*
	* @param  string 	$file	Absolute path to file (e.g. /home/public_html/wp-content/plugins/wpforo/wpf-themes/layouts/1/forum.php) 
	*
	* @return array
	* 
	**/
	function find_layout_headers( $file ){
		$theme_headers = array();
		$headers = array(
			'name' => 'layout',
			'version' => 'version',
			'description' => 'description',
			'author' => 'author',
			'layout_url' => 'url',
		);
		$fp = fopen( $file, 'r' );
		$data = fread( $fp, 8192 );
		fclose( $fp );
		$data = str_replace( "\r", "\n", $data );
		foreach ( $headers as $header_key => $header_name ){
			if ( preg_match( '|^[\s\t\/*#@]*' . preg_quote( $header_name, '|' ) . ':(.*)$|mi', $data, $match ) && $match[1] ){
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			}
			else{
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = '';
			}
		}
		$theme_headers['file']['name'] = 'file';
		$theme_headers['file']['value'] = trim(str_replace( WPFORO_THEME_DIR, '', $file), '/');
		return $theme_headers;
	}

	public function copyright(){ ?>
		<span onclick='javascript:document.getElementById("bywpforo").style.display = "inline";document.getElementById("awpforo").style.display = "none";' id="awpforo"> <img align="absmiddle" title="<?php wpforo_phrase('Powered by') ?> wpForo version <?php echo esc_html(WPFORO_VERSION) ?>" alt="Powered by wpForo" class="wpdimg" src="<?php echo WPFORO_URL ?>/wpf-assets/images/wpforo-info.png" alt="wpForo"> </span><a id="bywpforo" target="_blank" href="http://wpforo.com/">&nbsp;<?php wpforo_phrase('Powered by') ?> wpForo version <?php echo esc_html(WPFORO_VERSION) ?></a>
		<?php
	}

	public function member_menu( $userid, $menu = array() ){ 
		if( empty($menu) ) $menu = array('profile' => 'fa-user', 'account' => 'fa-cog', 'activity' => 'fa-comments-o', 'subscriptions' => 'fa-rss');
		$menu = apply_filters('wpforo_member_menu_filter', $menu);
		if( !($userid == $this->wpforo->current_userid || $this->wpforo->perm->usergroup_can( $this->wpforo->current_user_groupid, 'em' )) ) unset($menu['account']);
		foreach( $menu as $key => $value ) : ?>
	        <a class="wpf-profile-menu <?php echo ( $this->wpforo->current_object['template'] == $key ? ' wpforo-active' : '' ) ?>" href="<?php echo esc_url($this->wpforo->member->get_profile_url($userid, $key)) ?>">
	        	<i style="font-size:14px; padding-right:3px;" class="fa <?php echo sanitize_html_class($value) ?>"></i> <?php wpforo_phrase($key) ?>
	        </a>
			<?php
		endforeach;
	}

	public function member_template(){
		global $wpforo;
		extract($this->wpforo->current_object, EXTR_OVERWRITE);
		extract($user, EXTR_OVERWRITE);
		
		include( (isset($this->wpforo->member_tpls[$template]) && $this->wpforo->member_tpls[$template] ? $this->wpforo->member_tpls[$template] : $this->wpforo->member_tpls['profile']) );
	}
	
	public function member_error(){
		echo apply_filters('wpforo_member_error_filter', wpforo_phrase('Members not found', FALSE));
	}

}

?>