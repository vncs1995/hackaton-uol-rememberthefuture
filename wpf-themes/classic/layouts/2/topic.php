<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

	<div class="wpfl-2">
    	
		<div class="wpforo-topic-head">
			<div class="head-title"><?php wpforo_phrase('Topic Title') ?></div>
			<div class="head-stat-lastpost"><?php wpforo_phrase('Last Post') ?></div>
			<div class="head-stat-views"><?php wpforo_phrase('Views') ?></div>
			<div class="head-stat-posts"><?php wpforo_phrase('Posts') ?></div>
			<br class="wpf-clear">
		</div>
        
		<?php foreach($topics as $key => $topic) : ?>
			
			<?php 
				$member = $wpforo->member->get_member($topic['userid'], true); 
				if(isset($topic['last_post']) && $topic['last_post'] != 0){
					$last_post = $wpforo->post->get_post($topic['last_post']); 
					$last_poster = (!empty($last_post)) ? $wpforo->member->get_member($last_post['userid'], true) : array('ID' => 0, 'display_name' => wpforo_phrase('Guest'));
				}
			 
			?>
			  
          <div class="topic-wrap">
              <div class="wpforo-topic">
				  <?php if( wpforo_feature('avatars', $wpforo) ): ?>
                      <div class="wpforo-topic-avatar"><?php echo $wpforo->member->avatar($member, 'alt="'.esc_attr($member['display_name']).'"', 36, true) ?></div>
                  <?php endif; ?>
                  <div class="wpforo-topic-info">
                    <p class="wpforo-topic-title"><a href="<?php echo esc_url($wpforo->topic->get_topic_url($topic, $forum_data)) ?>"><i class="fa fa-1x <?php $wpforo->tpl->icon('topic', $topic); ?>" title="<?php $icon_title = $wpforo->tpl->icon('topic', $topic, false, 'title'); if( $icon_title ) echo esc_html($icon_title) ?>"></i> <?php echo esc_html($topic['title']) ?></a></p>
                    <p class="wpforo-topic-start-info wpfcl-2"><a href="<?php echo esc_url($member['profile_url']) ?>"><?php echo esc_html($member['display_name']) ?></a>, <?php wpforo_date($topic['created']); ?></p>
                  </div>
				  <?php if(isset($topic['last_post']) && $topic['last_post'] != 0) : ?>
                  		<div class="wpforo-topic-stat-lastpost"><span><?php wpforo_phrase('by') ?> <a href="<?php echo esc_url($last_poster['profile_url']) ?>"><?php echo esc_html($last_poster['display_name']) ?></a> <a href="<?php echo esc_url($wpforo->post->get_post_url($last_post['postid'])) ?>" title="<?php wpforo_phrase('View the latest post') ?>"><i class="fa fa-chevron-right fa-sx wpfcl-a"></i></a></span><br> <?php wpforo_date($last_post['created']); ?></div>
				  <?php else: ?>
				  		<div class="wpforo-topic-stat-lastpost"></span><?php wpforo_phrase('Replies not found') ?></div>
				  <?php endif; ?>
                  <div class="wpforo-topic-stat-views"><?php echo intval($topic['views']) ?></div>
                  <div class="wpforo-topic-stat-posts"><?php echo intval($topic['posts']) ?></div>
                  <br class="wpf-clear">
              </div><!-- wpforo-topic -->
          </div><!-- topic-wrap -->
	    	
	        <?php do_action( 'wpforo_loop_hook', $key ) ?>  
	        
		<?php endforeach; ?>
    </div><!-- wpfl-2 -->
