<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpfl-1">
          <div class="wpforo-topic-head">
              <div class="head-title"><?php wpforo_phrase('Topic title') ?></div>
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
			if(isset($topic['first_postid']) && $topic['first_postid'] != 0){
				$first_post = $wpforo->post->get_post($topic['first_postid']);
				$intro_posts = $wpforo->post_options['layout_extended_intro_posts_count']; if( $intro_posts < 1 ){ $intro_posts = NULL; } else { $intro_posts = ($intro_posts > 1) ? ($intro_posts - 1) : $intro_posts = 0; }
				$first_poster = (!empty($first_post)) ? $wpforo->member->get_member($first_post['userid'], true) : array('display_name' => wpforo_phrase('Guest'));
				$posts = $wpforo->post->get_posts( array('topicid' => $topic['topicid'], 'exclude' => $topic['first_postid'], 'order' => 'DESC', 'row_count' => $intro_posts) );
				$posts = array_reverse($posts);
			}
			$topic_url = $wpforo->topic->get_topic_url($topic, $forum_data);
			$post_toglle = $wpforo->post_options['layout_extended_intro_posts_toggle'];
		?>
		
		<div class="topic-wrap">
          <div class="wpforo-topic">
              <div class="wpforo-topic-icon" title="<?php $icon_title = $wpforo->tpl->icon('topic', $topic, false, 'title'); if( $icon_title ) echo esc_html($icon_title) ?>">
              <i class="fa fa-1x <?php $wpforo->tpl->icon('topic', $topic); ?>"></i>
              </div>
              <div class="wpforo-topic-info">
                <p class="wpforo-topic-title"><a href="<?php echo esc_url($topic_url) ?>"><?php echo esc_html($topic['title']); ?></a></p>
                <p class="wpforo-topic-start-info wpfcl-1">
                <span class="wpfcl-5"><?php wpforo_phrase('First post and replies') ?></span>&nbsp; <i id="button-arrow-<?php echo intval($topic['topicid']) ?>" class="topictoggle wpfcl-a fa fa-chevron-<?php echo ( $post_toglle == 1 ? 'up' : 'down' ) ?>"></i>
				<?php if(isset($last_post) && !empty($last_post)) : ?>
                	<span class="wpf-vsep">|</span>
                    <span class="wpf-last-post-by"><a href="<?php echo esc_url($wpforo->post->get_post_url($last_post['postid'])) ?>"><?php echo sprintf( wpforo_phrase('Last post by %s', FALSE), $last_poster['display_name'] ) ?>, <?php wpforo_date($last_post['created']); ?> <i class="fa fa-chevron-right fa-sx wpfcl-a"></i></a></span>
                <?php endif; ?>
					
                </p> 
              </div>
              <div class="wpforo-topic-stat-views"><?php echo intval($topic['views']) ?></div>
              <div class="wpforo-topic-stat-posts"><?php echo intval($topic['posts']) ?></div>
              <br class="wpf-clear">
          </div><!-- wpforo-topic -->
          <div class="wpforo-last-posts-<?php echo intval($topic['topicid']) ?>" style="display: <?php echo ( $post_toglle == 1 ? 'block' : 'none' ); ?>">
              <div class="wpforo-last-posts-tab">&nbsp;</div>
              <div class="wpforo-last-posts-list">
                <ul>
                    <li> 
                        <div class="wpforo-last-post-title"><i class="fa fa-comments fa-flip-horizontal fa-0x wpfcl-0"></i> &nbsp; <a href="<?php echo esc_url($wpforo->post->get_post_url($first_post['postid'])) ?>"><?php echo esc_html( wpforo_text($first_post['body'], 50, false)) ?></a></div> 
                        <div class="wpforo-last-post-user"><a href="<?php echo esc_url($first_poster['profile_url']) ?>"><?php echo sprintf( wpforo_phrase('by %s', FALSE), esc_html( wpforo_text($first_poster['display_name'], 9, FALSE)) ) ?></a></div>
                        <div class="wpforo-last-post-date"><?php wpforo_date($first_post['created']); ?></div> 
                        <br class="wpf-clear">
                    </li>
					<?php if(!empty($posts) && is_array($posts)) : ?>
						<?php foreach($posts as $post) : ?>
							<?php $poster = $wpforo->member->get_member($post['userid'], true); ?>
		                    <li>
		                        <div class="wpforo-last-post-title"><i class="fa fa-reply fa-rotate-180 fa-0x wpfcl-0"></i> &nbsp; <a href="<?php echo esc_url($wpforo->post->get_post_url($post['postid'])) ?>" title="<?php wpforo_phrase('REPLY:') ?> <?php echo esc_html( wpforo_text($post['body'], 100, false)) ?>"><?php echo (( $post['body'] ) ?  esc_html(wpforo_text($post['body'], 50, FALSE)) : esc_html($post['title'])) ?></a></div> 
		                        <div class="wpforo-last-post-user"><a href="<?php echo esc_url($poster['profile_url']) ?>"><?php echo sprintf( wpforo_phrase('by %s', FALSE), esc_html(wpforo_text($poster['display_name'], 9, FALSE)) ) ?></a></div>
		                        <div class="wpforo-last-post-date"><?php wpforo_date($post['created']); ?></div> 
		                        <br class="wpf-clear">
		                    </li>
						<?php endforeach ?>
                        <?php if(intval($topic['posts']) > ($intro_posts+1)): ?>
                            <li style="text-align:right;"><a href="<?php echo esc_url($topic_url) ?>"><?php wpforo_phrase('view all posts', true, 'lower');  ?> <i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
                        <?php endif ?>
					<?php endif ?>
                </ul>
              </div><!-- wpforo-last-posts-list -->
              <br class="wpf-clear">
          </div><!-- wpforo-last-posts -->
	    </div>
		
		<?php do_action( 'wpforo_loop_hook', $key ) ?>
		
	<?php endforeach; ?>
</div> <!-- wpfl-1 -->
