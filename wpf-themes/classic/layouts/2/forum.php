<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

/**
* 
* @layout: Simplified
* @url: http://gvectors.com/
* @version: 1.0.0
* @author: gVectors Team
* @description: Simplified layout looks simple and clean.
* 
*/
?>

<div class="wpfl-2">
	<div class="wpforo-category">
	    <div class="cat-title"><?php echo esc_html($cat['title']); ?></div>
	    <div class="cat-lastpostinfo"><?php wpforo_phrase('Last Post Info'); ?></div>
	    <br class="wpf-clear" />
	</div><!-- wpforo-category -->
  
  	<?php foreach($forums as $forum) : 
  		if( !$wpforo->perm->forum_can( $forum['forumid'], 'vf' ) ) continue; ?>
  		
	  	<div class="forum-wrap">
	      	<div class="wpforo-forum">
		        <div class="wpforo-forum-icon"><i class="fa fa-comments wpfcl-0"></i></div>
		        <div class="wpforo-forum-info">
		        	<h3 class="wpforo-forum-title"><a href="<?php echo esc_url($wpforo->forum->get_forum_url($forum)) ?>"><?php echo esc_html($forum['title']); ?></a></h3>
		        	<p class="wpforo-forum-description"><?php wpforo_text( $forum['description'], 1000 ) ?></p>
					<?php
					 	$data = array();
						$wpforo->forum->get_childs($forum['forumid'], $data);
						$counts = $wpforo->forum->get_counts( $data );
						if(!isset($forum['last_postid']) || !$forum['last_postid']){ 
							$lastinfo = $wpforo->forum->get_lastinfo( $data );
							if(!empty($lastinfo) && is_array($lastinfo)) $forum = array_merge($forum, $lastinfo);
						}
					?>
		        	<span class="wpforo-forum-stat">
		            	<?php wpforo_phrase('Topics') ?>: <?php echo wpforo_print_number($counts['topics']) ?> &nbsp;<span class="wpfcl-1">|</span>&nbsp; <?php wpforo_phrase('Posts'); ?>: <?php echo wpforo_print_number($counts['posts']) ?>
		        	</span>
					
					<?php $sub_forums = $wpforo->forum->get_forums( array( "parentid" => $forum['forumid'], "type" => 'forum' ) ); ?>
		            <?php if(is_array($sub_forums) && !empty($sub_forums)) : ?>
						
			            <div class="wpforo-subforum">
			                <ul>
			                    <li class="first wpfcl-0"><?php wpforo_phrase('Subforums'); ?>: </li>
								
								<?php foreach($sub_forums as $sub_forum) : 
									if( !$wpforo->perm->forum_can( $sub_forum['forumid'], 'vf' ) ) continue; ?>
			                      	
			                      	<li><i class="fa fa-comments wpfcl-0"></i>&nbsp;<a href="<?php echo esc_url($wpforo->forum->get_forum_url($sub_forum)) ?>"><?php echo esc_html($sub_forum['title']); ?></a></li>
									
		                     	<?php endforeach; ?>
								
			                </ul>
			                <br class="wpf-clear" />
			            </div><!-- wpforo-subforum -->
						
					<?php endif; ?>
					
		    	</div><!-- wpforo-forum-info -->
				
				<?php if($forum['last_userid'] != 0) : ?>
					<?php $member = $wpforo->member->get_member($forum['last_userid'], true) ?>
					<?php $last_post = $wpforo->post->get_post($forum['last_postid']); ?>
					<?php $last_post_topic = $wpforo->topic->get_topic($last_post['topicid']); ?>
			        <div class="wpforo-last-post">
			            <p class="wpforo-last-post-title"><a href="<?php echo esc_url($wpforo->post->get_post_url($forum['last_postid'])) ?>"><?php wpforo_text($last_post_topic['title'], 30); ?></a></p>
			            <p class="wpforo-last-post-info"><?php wpforo_phrase('by'); ?>&nbsp;<a href="<?php echo esc_url($member['profile_url']) ?>"><?php echo esc_html($member['display_name']) ?></a>, <?php wpforo_date($forum['last_post_date']) ?></p>
			        </div>
			        <?php if( wpforo_feature('avatars', $wpforo) ): ?>
                    	<div class="wpforo-last-post-avatar"><a href="<?php echo esc_url($member['profile_url']) ?>"><?php echo $wpforo->member->get_avatar($forum['last_userid'], 'alt="'.esc_attr($member['display_name']).'" title="'.esc_attr($member['display_name']).'"', 40) ?></a></div>
					<?php endif; ?>
			        <br class="wpf-clear" />
				<?php else: ?>
                    <div class="wpforo-last-post">
                        <p class="wpforo-last-post-title"><br /><?php wpforo_phrase('Forum is empty'); ?></p>
                    </div>
                    <div class="wpforo-last-post-avatar">&nbsp;</div>
                    <br class="wpf-clear" />
				<?php endif ?>
				
	      	</div><!-- wpforo-forum -->
	  	</div><!-- forum-wrap -->
	  
    <?php endforeach; ?> <!-- $forums as $forum -->
</div><!-- wpfl-2 -->

