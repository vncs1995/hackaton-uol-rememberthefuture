<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

	if(!empty($_GET['wpfpaged'])) $paged = intval($_GET['wpfpaged']);

	$args = array( 
	  'offset' => ($paged - 1) * $wpforo->post_options['posts_per_page'],
	  'row_count' => $wpforo->post_options['posts_per_page'],
	);
	
	if(!empty($_GET['wpfs'])) $args['needle'] = sanitize_text_field($_GET['wpfs']);
	if(!empty($_GET['wpff'])) $args['forumids'] = $_GET['wpff'];
	if(!empty($_GET['wpfd'])) $args['date_period'] = sanitize_text_field($_GET['wpfd']);
	if(!empty($_GET['wpfin'])) $args['type'] = sanitize_text_field($_GET['wpfin']);
	if(!empty($_GET['wpfob'])) $args['orderby'] = sanitize_text_field($_GET['wpfob']);
	if(!empty($_GET['wpfo'])) $args['order'] = sanitize_text_field($_GET['wpfo']);
	
	$items_count = 0;
	$posts = $wpforo->post->search($args, $items_count);
	$wpfs = (isset($_GET['wpfs'])) ? sanitize_text_field($_GET['wpfs']) : '';
?>


<p id="wpforo-search-title"><?php wpforo_phrase('Search result for') ?>: <span class="wpfcl-5"><?php echo esc_html($wpfs) ?></span></p>
  
  <div class="wpforo-search-wrap">
    <div class="wpf-search-bar">
        <form action="" method="get">
            <div class="wpforo-table">
              <div class="wpforo-tr">
                <div class="wpforo-td wpfw-50 wpfltd">
                    <span class="wpf-search-label wpfcl-1">&nbsp;<?php wpforo_phrase('Search in Forums') ?>:</span><br />
                    <select name="wpff[]" class="wpfw-90 wpff" multiple="multiple">
                    	<?php $wpforo->forum->tree('select_box', FALSE); ?>
                    </select>
                </div>
                <div class="wpforo-td wpfrtd">
                    <span class="wpf-search-label wpfcl-1">&nbsp;<?php wpforo_phrase('Search in date period') ?>:</span><br />
                    <select name="wpfd" class="wpfw-60 wpfd">
                        <option value="0"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 0 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Any Date') ?></option>
                        <option value="1"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 1 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Last 24 hours') ?></option>
                        <option value="7"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 7 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Last Week') ?></option>
                        <option value="30"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 30 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Last Month') ?></option>
                        <option value="90"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 90 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Last 3 Months') ?></option>
                        <option value="180"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 180 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Last 6 Months') ?></option>
                        <option value="365"<?php echo !empty($_GET['wpfd']) && $_GET['wpfd'] == 365 ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Last Year ago') ?></option>
                    </select>
                    <br />
                    <span class="wpf-search-label wpfcl-1">&nbsp;<?php wpforo_phrase('Sort Search Results by') ?>:</span><br />
                    <select class="wpfw-60 wpfob" name="wpfob">
                        <option value="relevancy"<?php echo !empty($_GET['wpfob']) && $_GET['wpfob'] == 'relevancy' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Relevancy') ?></option>
                        <option value="date"<?php echo !empty($_GET['wpfob']) && $_GET['wpfob'] == 'date' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Date') ?></option>
                        <option value="user"<?php echo !empty($_GET['wpfob']) && $_GET['wpfob'] == 'user' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('User') ?></option>
                        <option value="forum"<?php echo !empty($_GET['wpfob']) && $_GET['wpfob'] == 'forum' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Forum') ?></option>
                    </select><br>
                    <select class="wpfw-60" name="wpfo" class="wpfo">
                        <option value="desc"<?php echo !empty($_GET['wpfo']) && $_GET['wpfo'] == 'desc' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Descending order') ?></option>
                        <option value="asc"<?php echo !empty($_GET['wpfo']) && $_GET['wpfo'] == 'asc' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Ascending order') ?></option>
                    </select>
                </div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-td wpfw-40 wpfltd">
                    <span class="wpf-search-label wpfcl-1">&nbsp;<?php wpforo_phrase('Search Phrase') ?>:</span><br />
                    <input type="text" name="wpfs" class="wpfs" value="<?php echo esc_attr($wpfs) ?>" />
                </div>
                <div class="wpforo-td wpfrtd">
                    <span class="wpf-search-label wpfcl-1">&nbsp;<?php wpforo_phrase('Search Type') ?>:</span><br />
                    <select name="wpfin" class="wpfw-60 wpfin">
                        <option value="entire-posts"<?php echo !empty($_GET['wpfin']) && $_GET['wpfin'] == 'entire-posts' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Search Entire Posts') ?></option>
                        <option value="titles-only"<?php echo !empty($_GET['wpfin']) && $_GET['wpfin'] == 'titles-only' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Search Titles Only') ?></option>
                        <option value="user-posts"<?php echo !empty($_GET['wpfin']) && $_GET['wpfin'] == 'user-posts' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Find Posts by User') ?></option>
                        <option value="user-topics"<?php echo !empty($_GET['wpfin']) && $_GET['wpfin'] == 'user-topics' ? ' selected' : '' ?>>&nbsp;<?php wpforo_phrase('Find Topics Started by User') ?></option>
                    </select>
                </div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-td wpfw-40 wpfltd wpf-last">
                	<input type="submit" class="wpf-search" value="<?php wpforo_phrase('Search') ?>" />
                </div>
                <div class="wpforo-td wpfrtd wpf-last"></div>
              </div>
            </div>
        </form>
    </div>
    <hr/>
    <div class="wpf-snavi"><?php $wpforo->tpl->pagenavi($paged, $items_count, FALSE); ?></div>
    <div class="wpforo-search-content">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
     	  <tr class="wpf-htr">
            <td class="wpf-shead-icon">#</td>
            <td class="wpf-shead-title"><?php wpforo_phrase('Post Title') ?></td>
            <td class="wpf-shead-result"><?php wpforo_phrase('Result Info') ?></td>
            <td class="wpf-shead-date"><?php wpforo_phrase('Date') ?></td>
            <td class="wpf-shead-user"><?php wpforo_phrase('User') ?></td>
            <td class="wpf-shead-forum"><?php wpforo_phrase('Forum') ?></td>
          </tr>
          
            <?php foreach($posts as $post) : extract($post, EXTR_OVERWRITE); ?>
            	
	          <tr class="wpf-ttr">
	            <td class="wpf-spost-icon"><i class="fa fa-comments fa-1x wpfcl-0"></i></td>
	            <td class="wpf-spost-title"><a href="<?php echo esc_url($wpforo->post->get_post_url($postid)) ?>" title="<?php wpforo_phrase('View entire post') ?>"><?php echo esc_html($title) ?> &nbsp;<i class="fa fa-chevron-right" style="font-weight:100; font-size:11px;"></i></a></td>
	            <td class="wpf-spost-result wpfcl-5"><?php echo ( isset($matches) ? $matches : '' ) ?> <?php wpforo_phrase('matches') ?></td>
	            <td class="wpf-spost-date"><?php wpforo_date($created); ?></td>
	            <td class="wpf-spost-user"><?php $user = $wpforo->member->get_member($userid, true); echo ( $user['display_name'] ? esc_html($user['display_name']) : esc_html($user['user_nicename']) ); ?></td>
	            <td class="wpf-spost-forum"><?php $forum = $wpforo->forum->get_forum($forumid); echo esc_html($forum['title']); ?></td>
	          </tr>
	              <tr class="wpf-ptr">
	                <td class="wpf-spost-icon">&nbsp;</td>
	                <td colspan="5" class="wpf-stext">
	                	<?php
							$body = wpforo_content_filter( $body );
	                		$body = preg_replace('#\[attach\][^\[\]]*\[\/attach\]#is', '', strip_tags($body));
	                		if(!empty($_GET['wpfs'])){
		                		$words = explode(' ', trim($_GET['wpfs']));
		                		if(!empty($words)){
									$body_len = 564;
									$pos = mb_stripos( $body, " ".trim($words[0]), 0, get_option('blog_charset') );
									if( strlen($body) > $body_len && $pos !== FALSE ){
										if($pos > ($body_len/2)){
											$bef_body = "... ";
											$start = mb_stripos( $body, " ", ($body_len/2), get_option('blog_charset') );;
										}else{
											$bef_body = "";
											$start = 0;
										}
										if( (mb_strlen($body, get_option('blog_charset')) - $start) > $body_len ){
											$aft_body = " ...";
										}else{
											$aft_body = "";
										}
										$body = $bef_body . mb_substr( $body, $start, $body_len, get_option('blog_charset') ) . $aft_body;
									}
			                		foreach($words as $word){
			                			$word = trim($word);
			                			$body = str_ireplace(' '.esc_html($word), ' <span class="wpf-sword wpfcl-b">'.esc_html($word).'</span>', $body);
									}
								}
							}
							echo $body;
	                	?>
	                </td>
	              </tr>
	        	
            <?php endforeach ?>
          
       </table>
  	</div>
    <div class="wpf-snavi"><?php $wpforo->tpl->pagenavi($paged, $items_count, FALSE); ?></div>
  </div>
 
<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>