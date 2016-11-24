<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpfl-2">

    <div class="wpforo-post-head"> 
        <div class="wpf-left">&nbsp;<a href="<?php echo esc_url($wpforo->post->get_post_url($topic['last_post'])) ?>" class="wpfcl-2"><i class="fa fa-caret-square-o-down fa-0x wpfcl-3"></i> &nbsp; <span class="wpfcl-3"><?php wpforo_phrase('Last Post'); ?></span></a></div>
        <div class="wpf-right">&nbsp;<a href="<?php $wpforo->feed->rss2_url(); ?>" class="wpfcl-2" title="<?php wpforo_phrase('Topic RSS Feed') ?>"><span class="wpfcl-3"><?php wpforo_phrase('RSS') ?></span> <i class="fa fa-rss fa-0x wpfcl-3"></i></a></div>
        <div class="wpf-clear"></div>
    </div>
  
	<?php foreach($posts as $key => $post) : ?>
		
		<?php $member = $wpforo->member->get_member($post['userid'], true); ?>
		<div id="post-<?php echo intval($post['postid']) ?>" class="post-wrap">
              <div class="wpforo-post wpfcl-1">
                <div class="wpf-left">
                	<?php if( wpforo_feature('avatars', $wpforo) ): ?>
                		<div class="author-avatar"><?php echo $wpforo->member->avatar($member, 'alt="'.esc_attr($member['display_name']).'"', 110, true) ?></div>
                    <?php endif; ?>
                    <div class="author-data">
                        <div class="author-name"><span><?php $wpforo->member->show_online_indicator($member['userid']) ?></span>&nbsp;<a href="<?php echo esc_url($member['profile_url']) ?>"><?php echo esc_html($member['display_name']) ?></a></div>
                        <div class="author-title">
                            <?php wpforo_member_title($member) ?>
                        </div>
                        <?php wpforo_member_badge($member) ?>
                	</div>
                    <div class="wpf-clear"></div>
                </div><!-- left -->
                <div class="wpf-right">
                	<div class="wpforo-post-content-top">
                    	<div class="wpf-post-actions">
							<?php if( $post['is_first_post'] ){
                                $buttons = array( 'solved', 'sticky', 'close', 'report', 'move', 'delete' );
                                $wpforo->tpl->buttons( $buttons, $forum_data['forumid'], $topic['topicid'], $post['postid'], TRUE );  
                            }else{
                                $buttons = array( 'report', 'delete' );
                                $wpforo->tpl->buttons( $buttons, $forum_data['forumid'], $topic['topicid'], $post['postid'] );
                            } ?>
                        </div>
                        <a href="<?php echo esc_url($wpforo->post->get_post_url($post['postid'])) ?>"><i class="fa fa-link fa-0x"></i></a>
                    </div>
                    <div class="wpforo-post-content">
                        <?php echo wpforo_kses($post['body'], 'post') ?>
                        <?php wpforo_post_edited($post); ?>
                        <?php if( wpforo_feature('signature', $wpforo) ): ?>
                        	<?php if($member['signature']): ?><div class="wpforo-post-signature"><?php echo wpautop(wpforo_kses(stripslashes($member['signature']), 'user_description')) ?></div><?php endif; ?>
                        <?php endif; ?>
                        <div class="wpf-post-button-actions">
                        <?php if( $post['is_first_post'] ){
							$buttons = array( 'reply', 'quote', 'edit',	'like' );
							$wpforo->tpl->buttons( $buttons, $forum_data['forumid'], $topic['topicid'], $post['postid'], TRUE );  
						}else{
							$buttons = array( 'reply', 'quote', 'edit', 'like' );
							$wpforo->tpl->buttons( $buttons, $forum_data['forumid'], $topic['topicid'], $post['postid'] );
						} ?>
                        </div>
                    </div>
                    <div class="wpforo-post-content-bottom">
                    	<div class="cbleft wpfcl-0"><?php wpforo_phrase('Posted') ?> : <?php wpforo_date($post['created'], 'd/m/Y g:i a') ?> 
							<?php echo $wpforo->tpl->likers($post['postid']); ?>
						</div>
                    	<div class="wpf-clear"></div>
                    </div>
                </div><!-- right -->
                <div class="wpf-clear"></div>
              </div><!-- wpforo-post -->
          </div><!-- post-wrap -->
       
        <?php do_action( 'wpforo_loop_hook', $key ) ?>
       
    <?php endforeach; ?>
</div><!-- wpfl-2 -->