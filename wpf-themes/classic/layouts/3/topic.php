<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpfl-3">
	<div class="wpforo-topic-head">
	    <div class="head-title"><?php wpforo_phrase('Topic Title') ?></div>
	    <div class="head-stat-lastpost"><?php wpforo_phrase('Last Post') ?></div>
	    <br class="wpf-clear">
	</div>
    
	<?php foreach($topics as $key => $topic) : ?>
		
		<?php 
			$member = $wpforo->member->get_member($topic['userid'], true);
			if(isset($topic['last_post']) && $topic['last_post']){
				$last_post = $wpforo->post->get_post($topic['last_post']);
				$last_poster = (!empty($last_post)) ? $wpforo->member->get_member($last_post['userid'], true) : array('ID' => 0, 'display_name' => wpforo_phrase('Guest',false));
			}
			$topic_url = $wpforo->topic->get_topic_url($topic, $forum_data);
		?>
      <div class="topic-wrap">
          <div class="wpforo-topic">
          	  <?php if( wpforo_feature('avatars', $wpforo) ): ?>
              	<div class="wpforo-topic-avatar"><?php echo $wpforo->member->avatar($member, '', '', true) ?></div>
              <?php endif; ?>
              <div class="wpforo-topic-info">
                <p class="wpforo-topic-title"><a href="<?php echo esc_url($topic_url) ?>"><i class="fa fa-1x <?php $wpforo->tpl->icon('topic', $topic); ?>" title="<?php $icon_title = $wpforo->tpl->icon('topic', $topic, false, 'title'); if( $icon_title ) echo esc_html($icon_title) ?>"></i> <?php wpforo_text($topic['title'], 70); ?></a></p>
                <p class="wpforo-topic-start-info wpfcl-2"><a href="<?php echo esc_url($member['profile_url']) ?>"><?php echo esc_html($member['display_name']) ?></a>, <?php wpforo_date($topic['created']); ?></p>
              </div>
              <div class="wpforo-topic-status wpfcl-2">
				<div class="votes"><div class="count <?php echo $topic['votes'] == 0 ? 'wpfcl-6' : 'wpfbg-4 wpfcl-3' ?>"><?php echo intval($topic['votes']) ?></div><div class="wpforo-label <?php echo $topic['votes'] == 0 ? 'wpfcl-6' : 'wpfbg-4 wpfcl-3' ?>"><?php wpforo_phrase('Votes') ?></div></div>
                <div class="answers"><div class="count <?php echo $topic['answers'] == 0 ? 'wpfcl-5' : 'wpfbg-5 wpfcl-3' ?>"><?php echo intval($topic['answers']) ?></div><div class="wpforo-label <?php echo $topic['answers'] == 0 ? 'wpfcl-5' : 'wpfbg-5 wpfcl-3' ?>"><?php wpforo_phrase('Answers') ?></div></div>
				<div class="views"><div class="count"><?php echo intval($topic['views']) ?></div><div class="wpforo-label"><?php wpforo_phrase('Views') ?></div></div>
              </div>
			   <?php if(isset($topic['last_post']) && $topic['last_post']) : ?>
              		<div class="wpforo-topic-stat-lastpost"><span style="white-space:nowrap"><?php wpforo_phrase('by') ?>&nbsp;<a href="<?php echo esc_url($last_poster['profile_url']) ?>"><?php wpforo_text($last_poster['display_name'], 9); ?></a> <a href="<?php echo esc_url($wpforo->post->get_post_url($last_post['postid'])) ?>" title="<?php wpforo_phrase('View the latest post') ?>"><i class="fa fa-chevron-right fa-sx wpfcl-a"></i></a></span><br> <?php wpforo_date($last_post['created']); ?></div>
			  <?php else: ?>
			  		<div class="wpforo-topic-stat-lastpost"><?php wpforo_phrase('Replies not found') ?></div>
			  <?php endif; ?>
          </div><!-- wpforo-topic -->
      </div><!-- topic-wrap -->
	  
	  <?php do_action( 'wpforo_loop_hook', $key ) ?>
	  
    <?php endforeach; ?>
</div><!-- topic-wrap -->
