<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 
function wpforo_verify_form(){
	if(!isset($_POST['wpforo_form']) || !wp_verify_nonce( $_POST['wpforo_form'], 'wpforo_verify_form' )) { wpforo_phrase('Sorry, something wrong with your data.'); exit(); }
}

function is_wpforo_page($url = ''){
	if( is_admin() ) return FALSE;
	if( is_wpforo_shortcode_page() ) return TRUE;
	return is_wpforo_url($url);
}

function is_wpforo_url($url = ''){
	if( is_admin() ) return FALSE;
	if(!$url) $url = wpforo_full_url();
	global $wpforo;
	
	if( $wpforo->use_home_url ) return TRUE;
	
	$current_url = wpforo_get_url_query_vars_str($url);
	
	if( $wpforo->permastruct && strpos($current_url, $wpforo->permastruct) !== FALSE 
		&& strpos($current_url, $wpforo->permastruct) == 0 
			&& !is_admin() ) return TRUE;
	
	return FALSE;
}

function is_wpforo_shortcode_page(){
	if( is_admin() ) return FALSE;
	global $wpforo, $post;
	if( $post && isset($post->post_content) && has_shortcode( $post->post_content, 'wpforo' ) && !is_wpforo_url() ) return TRUE;
	return FALSE;
}

function wpforo_get_url_query_vars_str($url = ''){
	if(!$url) $url = wpforo_full_url();
	
	$current_url = preg_replace('#https?://[^/\?]+\.[^/\?]+/?#isu', '', $url);
	$site_url = preg_replace('#https?://[^/\?]+\.[^/\?]+/?#isu', '', esc_url( home_url('/') ));
	$current_url = trim( trim( preg_replace( '#^/?'.preg_quote($site_url).'#isu', '' , $current_url, 1 ), '/' ) );
	
	return $current_url;
}

function wpforo_use_home_url(){
	global $wpforo;
	
	$current_url = wpforo_get_url_query_vars_str();
	if( !$current_url && $wpforo->use_home_url ) return TRUE;
	return FALSE;
}

function is_wpforo_home(){
	global $wpforo;
	$wp_object = get_queried_object();
	$wpf_page = get_option('wpforo_pageid');
	if(!empty($wp_object) && isset($wpf_page) && !is_admin()){
		if(isset($wp_object->ID) && $wp_object->ID == $wpf_page ){
			$current_wpforo = trim( wpforo_full_url(), '/');
			$current_wppage = trim( WPFORO_BASE_URL, '/');
			if($current_wpforo == $current_wppage){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

function wpforo_feature( $option, $wpforo = NULL ){
	if( empty($wpforo) ) global $wpforo;
	if( isset($wpforo->features[$option]) && $wpforo->features[$option] ){
		return true;
	}
	else{
		return false;
	}
}

#################################################################################
/**
 * Returns merged arguments array from defined and default arguments.
 *
 * @since 1.0.0
 *
 * @param	mixed		defined arguments array or query 
 *
 * @param	mixed		default arguments array or query 
 * 
 * @return	array	
 */
function wpforo_parse_args( $args, $default = array() ) {
	
	if( is_object($args) ) {
		$defined = get_object_vars($args);
	}
	elseif( is_array($args) ) {
		$defined = $args; 
	}
	elseif( preg_match('|^[\d\,\s]+$|', $args) ){
		$defined = explode(',', trim($args));
	}
	elseif( is_integer($args) || is_float($args) ){
		$defined[0] = $args;
	}
	elseif( is_serialized($args) ){
		$defined = unserialize($args);
	}
	elseif( strpos($args, '=') !== FALSE ) {
		parse_str($args, $defined);
	}
	else{
		$defined[0] = $args;
	}
	if(!empty($default))  {
		return array_merge( $default, $defined );
	} 
	else {
		return $defined;
	}
}

// #############################################################################
/**
* Detects serialized data
*
* @since 	1.0.0
*
* @param	string	
*
* @return	boolean		
*/
if(!function_exists('is_serialized')){
	function is_serialized( $value ){
		if( $value == '' ) return false;
		if( $value === 0 ) return false;
		$value = trim($value);
		$chsd = @unserialize($value);
		if( $chsd !== false || $value === 'b:0;' ) {
			return true;
		}
		else{
			return false;
		}
	}
}


function wpforo_full_url($with_port = FALSE){
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol . "://" . $_SERVER['SERVER_NAME'] . ($with_port ? $port : '') . $_SERVER['REQUEST_URI'];
}


function wpforo_arr_group_by($array, $key_by){
	if(!empty($array)){
		$fltrd = array();
		foreach($array as $key => $arr){
			if(is_numeric($key)) $fltrd[] = $arr[$key_by];
		}
		$uniq_arr = array_unique($fltrd);
		asort($uniq_arr);
		return $uniq_arr;
	}
}

// #############################################################################
/**
* Print item's (topics, replyes ...) table in dashboard 
* @since 	1.0.0
* @param	string	  item(component) class name
* @param	string	  This table primary key field name
* @param	array	  table fields
* @param	array	  table search fields
* @param	array	  table filter fields
* @param	array	  action buttons ( full posible values $actions = array( 'edit', 'delete', 'view' ); )
*
* @return	echo      html table		
*/
function wpforo_create_form_table($varname, $primary_key, $fields = array(), $search_fields = array(), $filter_fields = array(), $actions = array() ){
	
	if(empty($actions)) $actions = array( 'edit', 'delete', 'view' );
	
	if(!empty($fields)) :
		global $wpdb;
		global $wpforo;
		
		$args = array();
		
		if(isset($_GET['s']) && $_GET['s'] != '') $args['include'] = $wpforo->$varname->search(sanitize_text_field($_GET['s']), $search_fields);
		if(isset($_GET['orderby'])) $args['orderby'] = sanitize_text_field($_GET['orderby']);
		if(isset($_GET['order'])) $args['order'] = intval($_GET['order']);
		if(isset($_GET['forumid']) && $_GET['forumid'] != 0) $args['forumid'] = intval($_GET['forumid']);
		if(isset($_GET['groupid']) && $_GET['groupid'] != 0) $args['groupid'] = intval($_GET['groupid']);
		if(isset($_GET['userid']) && $_GET['userid'] != 0) $args['userid'] = intval($_GET['userid']);
		$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
		
		$args['offset'] = ($paged - 1) * get_option('wpforo_count_per_page');
		$args['row_count'] = get_option('wpforo_count_per_page');
		
		
		if($varname == 'reply'){
			$func = 'get_replies';
		}else{
			$func = 'get_'.$varname.'s';
		}
		
		$items_count = 0;
		
		$items = $wpforo->$varname->$func($args, $items_count);
		
		if(isset($args['include']) && empty($args['include'])){
			$items_count = 0;
			$items = array();
		}
		$pages_count = ceil($items_count/get_option('wpforo_count_per_page')); ?>
		
		<script type="text/javascript">
			function wpforo_check_uncheck_all(status){
				var inpts = document.getElementById("items").getElementsByTagName("input");
				for (var i = 0; i < inpts.length; i++) {
			        if (inpts[i].type == 'checkbox') {
			            inpts[i].checked = status;
			        }
			    }
			}
			
			function wpforo_doaction(){
				var action = document.items.action.value;
				var action2 = document.items.action2.value;
				if(action != -1 || action2 != -1){
					var inpts = document.getElementById("items").getElementsByTagName("input");
					
					var j = 0;
					var ids_arr = new Array();
					for (var i = 0; i < inpts.length; i++) {
				        if (inpts[i].type == 'checkbox' && inpts[i].checked == true && inpts[i].value != 'on') {
				            ids_arr[j] = inpts[i].value;
							j++
				        }
				    }
					if(ids_arr.length != 0){
						document.items.ids.value = ids_arr.join();
						document.items.submit();
					}
				}
			}
		</script>
		
        
		<div class="wpforo-search-box">
            <form name="search" id="srch" action="<?php echo admin_url( 'admin.php' ) ?>" method="GET">
                <input type="hidden" name="page" value="wpforo-<?php echo esc_attr($varname); ?>s"/>
                    <label class="screen-reader-text" for="post-search-input">Search <?php echo esc_attr(ucfirst($varname)); ?>s:</label>
                    <input type="search" id="post-search-input" name="s" value="<?php echo ( isset($_GET['s']) ? esc_attr(sanitize_text_field($_GET['s'])) : '' ) ?>">
                    <input type="submit" name="" id="search-submit" class="button" value="Search <?php echo esc_attr(ucfirst($varname)); ?>s">
            </form>
		</div>
        
		<form name="items" id="items" action="<?php echo admin_url( 'admin.php' ) ?>" method="GET">
			
			<input type="hidden" name="ids" />
			<input type="hidden" name="page" value="wpforo-<?php echo esc_attr($varname); ?>s"/>
			
			<div class="tablenav top">
				
				<div class="alignleft actions">
					<select name="action">
						<option value="-1" selected="selected"><?php _e('Bulk Actions', 'wpforo'); ?></option>
						<?php if($varname == 'phrase') : ?>
							<option value="edit"><?php _e('Edit', 'wpforo'); ?></option>
						<?php else : ?>
							<option value="del"><?php _e('Delete', 'wpforo'); ?></option>
						<?php endif ?>
					</select>
					<input type="button" onclick="wpforo_doaction()" id="doaction" class="button action" value="<?php _e('Apply', 'wpforo'); ?>">
				</div>
				<div class="alignleft actions">
					
					<?php if(!empty($filter_fields)) : ?>
						<?php foreach($filter_fields as $filter_field) : ?>
							<?php if($filter_field == 'forumid') : ?>
								
								<select name="<?php echo esc_attr($filter_field) ?>">
									<option value="0"><?php _e('Show all forums', 'wpforo'); ?></option>
									<?php $wpforo->forum->tree('select_box'); ?>
								</select>
								
							<?php elseif($filter_field == 'langid') : ?>
								
								<select name="<?php echo esc_attr($filter_field) ?>">
									<?php  $wpforo->phrase->show_lang_list() ?>
								</select>
								
							<?php elseif($filter_field == 'groupid') : ?>
								
								<select name="<?php echo esc_attr($filter_field) ?>">
									<?php foreach($wpforo->usergroup->get_usergroups() as $group) : ?>
										<?php if( $group['groupid'] != 4 ) : ?>
											<option value="<?php echo esc_attr($group['groupid']) ?>" <?php echo ( isset($_GET['groupid']) && $_GET['groupid'] == $group['groupid'] ? 'selected' : '' ) ?>><?php echo esc_html($group['name']) ?></option>
										<?php endif ?>
									<?php endforeach; ?>
								</select>	
								
							<?php endif; ?>
							
						<?php endforeach; ?>
						
						<input type="submit" name="" id="post-query-submit" class="button" value="Filter">
						
					<?php endif; ?>
					
				</div>
				<div class="tablenav-pages <?php echo ((($items_count/get_option('wpforo_count_per_page')) <= 1) ? 'one-page' : '') ?>"><span class="displaying-num"><?php echo intval($items_count) ?> <?php _e('item', 'wpforo') ?></span>
					<span class="pagination-links">
					<?php if( !isset($_GET['paged']) || $_GET['paged'] <= 2 ) : ?>
						<span class="tablenav-pages-navspan">&laquo;</span>
					<?php else : ?>
						<a class="first-page" title="Go to the first page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>">&laquo;</a>
					<?php endif ?>
					<?php if( !isset($_GET['paged']) || $_GET['paged'] <= 1 ) : ?>
						<span class="tablenav-pages-navspan">‹</span>
					<?php else : ?>
						<a class="prev-page" title="Go to the previous page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>&paged=<?php echo !isset($_GET['paged']) || $_GET['paged'] <= 1 ? 1 : $_GET['paged'] - 1 ?>">‹</a>
					<?php endif ?>
					
					<span class="paging-input"><input class="current-page" title="Current page" type="text" name="paged" value="<?php echo isset($_GET['paged']) ? intval($_GET['paged']) : 1 ?>" size="1"> of <span class="total-pages"><?php echo intval($pages_count) ?></span></span>
					
					<?php if( isset($_GET['paged']) && $_GET['paged'] >= $pages_count ) : ?>
						<span class="tablenav-pages-navspan">›</span>
					<?php else : ?>
						<a class="next-page" title="Go to the next page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>&paged=<?php echo (isset($_GET['paged']) ? ( $_GET['paged'] >= $pages_count ? intval($pages_count) : intval($_GET['paged']) + 1 ) : (!isset($_GET['paged']) ? 2 : intval($_GET['paged']) + 1)) ?>">›</a>
					<?php endif ?>
					<?php if( (isset($_GET['paged']) &&  $_GET['paged'] >= $pages_count - 1) || 1 >= $pages_count - 1  ) : ?>
						<span class="tablenav-pages-navspan">&raquo;</span>
					<?php else : ?>
						<a class="last-page" title="Go to the last page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>&paged=<?php echo intval($pages_count) ?>">&raquo;</a></span>
					<?php endif ?>
				</div> <br class="clear">
				
			</div>
			
			<table class="wp-list-table widefat fixed pages" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" id="cb" class="manage-column column-cb check-column" style="vertical-align:middle; padding:17px 0px 10px 5px;">
							<label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All', 'wpforo') ?></label><input id="cb-select-all-1" type="checkbox" onclick="wpforo_check_uncheck_all(this.checked)">
						</th>
						<?php foreach($fields as $key => $field) : ?>
							<th scope="col" <?php if($key===0) echo ' style="width:30%"' ?> id="<?php echo esc_attr(sanitize_text_field($field)) ?>" class="manage-column column-<?php echo esc_attr(sanitize_text_field($field)) ?> <?php echo isset($_GET['order']) ? ($_GET['orderby'] == $field ? 'sorted ' : '') . $_GET['order'] : 'sortable asc' ?>">
								<a href="<?php echo esc_url(preg_replace('#(\&*orderby\=[^\&]*\&*order\=[^\&]*)#is', '', wpforo_full_url())); ?>&orderby=<?php echo esc_attr(sanitize_text_field($field)) ?>&order=<?php echo isset($_GET['order']) ? ( $_GET['order'] == 'asc' ? 'desc' : 'asc' ) : 'asc' ?>">
									<span><?php echo sanitize_text_field(str_replace('_', ' ', wpforo_phrase( $field, false ))) ?></span>
									<span class="sorting-indicator"></span>
								</a>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-cb check-column"  style="vertical-align:middle; padding:17px 0px 10px 5px;">
							<label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All', 'wpforo') ?></label><input id="cb-select-all-2" type="checkbox" onclick="wpforo_check_uncheck_all(this.checked)">
						</th>
						<?php foreach($fields as $key => $field) : ?>
							<th scope="col" <?php if($key===0) echo ' style="width:30%"' ?> id="<?php echo esc_attr($field) ?>" class="manage-column column-<?php echo esc_attr($field) ?> <?php echo isset($_GET['order']) ? ($_GET['orderby'] == $field ? 'sorted ' : '') . $_GET['order'] : 'sortable asc' ?>">
								<a href="<?php echo admin_url( 'admin.php?page=wpforo-' . esc_attr(sanitize_text_field($varname)) . 's&orderby=' . esc_attr(sanitize_text_field($field)) . '&order=' . ( isset($_GET['order']) ? ( $_GET['order'] == 'asc' ? 'desc' : 'asc' ) : 'asc' ) ) ?>">
									<span><?php echo esc_html(sanitize_text_field(str_replace('_', ' ', wpforo_phrase( $field, false )))) ?></span>
									<span class="sorting-indicator"></span>
								</a>
							</th>
						<?php endforeach; ?>
		     		</tr>
				</tfoot>
				
				<tbody id="the-list">
					
					<?php
					if(!empty($items)) :
						
						if(!empty($items)) :
							foreach($items as $key => $item) : ?>
								
								<tr id="post-<?php echo esc_attr($item[$primary_key]) ?>" class="post-<?php echo esc_attr($item[$primary_key]) ?> type-page status-publish hentry <?php echo ($key % 2 == 0) ? 'alternate' : ''  ?> iedit author-self" valign="top">
									<th scope="row" class="check-column">
										<label class="screen-reader-text" for="cb-select-<?php echo esc_attr($item[$primary_key]) ?>">
											Select <?php echo esc_html(ucfirst($varname)) ?>
										</label>
										<input id="cb-select-<?php echo esc_attr($item[$primary_key]) ?>" type="checkbox" value="<?php echo esc_attr($item[$primary_key]) ?>">
										<div class="locked-indicator"></div>
									</th>
									<?php $i = 0; foreach($fields  as $field) :
										if($i == 0) : ?>
											<?php 
												if($varname == 'member'){
													$url = $url_user = admin_url( 'user-edit.php?user_id=' . intval($item[$primary_key]));
													$url_profile = $wpforo->$varname->get_profile_url($item[$primary_key], 'account');
												}else{
													$url = admin_url( 'admin.php?page=wpforo-'. $varname .'s&id='.$item[$primary_key] .'&action=edit' );
												}
											?>
											<td class="post-<?php echo esc_attr($item[$field]) ?> page-<?php echo esc_attr($item[$field]) ?> column-<?php echo esc_attr($item[$field]) ?>">
												<strong>
													<a class="row-<?php echo esc_attr($item[$field]) ?>" href="<?php echo esc_url($url) ?>" title="Edit “<?php echo esc_attr($varname) ?>”">
														<?php 
															if($varname == 'forum'){
																$depth = 0;
																$wpforo->forum->count_depth($item[$primary_key], $depth);
																echo str_repeat( '— ', $depth);
															}
															wpfo($item[$field], true, 'esc_html');
														?>
													</a>
												</strong>
												<div class="row-actions">
													<?php foreach($actions as $act_key => $action) : ?>
														<?php switch($action) : 
															case 'edit': ?>
																<span class="edit"><a href="<?php echo esc_url($url); ?>" title="Edit this item"><?php wpforo_phrase('edit'); ?></a></span>
															<?php break; 
															case 'edit_user': ?>
																<span class="edit"><a href="<?php echo esc_url($url_user); ?>"><?php wpforo_phrase('edit user'); ?></a></span>
															<?php break; 
															case 'edit_profile': ?>
																<span class="edit"><a href="<?php echo esc_url($url_profile); ?>"><?php wpforo_phrase('edit profile'); ?></a></span>
															<?php break; 
															case 'delete': ?>
                                                            	<?php $delete_url = wp_nonce_url( admin_url( 'admin.php?page=wpforo-' . esc_attr(sanitize_text_field($varname)) . 's&id=' . intval($item[$primary_key]) . '&action=del' ), 'wpforo_admin_table_action_delete' ); ?>
																<span class="trash"><a class="submitdelete" title="<?php _e('Delete this item', 'wpforo') ?>" onclick="return confirm('<?php _e('Are you sure you whant to DELETE this item?', 'wpforo') ?>');" href="<?php echo esc_url($delete_url); ?>"><?php wpforo_phrase('delete'); ?></a></span>
															<?php break; 
															case 'view': ?>
																<?php
																	if($varname != 'member'){
																		$fn_name = "get_".$varname."_url";
																		$item_id = $item[$varname.'id'];
																	}else{
																		$fn_name = "get_profile_url";
																		$item_id = $item['ID'];
																	}
																?>
																<span class="view">
																	<a href="<?php echo esc_url($wpforo->$varname->$fn_name($item_id)) ?>" title="<?php echo esc_attr(wpforo_phrase('view', false)); ?> “<?php echo esc_attr($varname) ?>”" rel="permalink">
																		<?php wpforo_phrase('view'); ?>
																	</a>
																</span>
															<?php break;
														endswitch ?>
														<?php if( $act_key != ( count($actions) - 1 ) ) echo "&nbsp;|&nbsp;"; ?>
													<?php endforeach ?>
												</div>
											</td>
										<?php else : ?>
											<td class="<?php echo esc_attr($item[$field]) ?> column-<?php echo esc_attr($item[$field]) ?>">
												<?php if($field == 'forumid') : ?>
													<?php $data = $wpforo->forum->get_forum($item[$field]); echo esc_html($data['title']); ?>
												<?php elseif($field == 'userid') : ?>
													<?php $data = $wpforo->member->get_member($item[$field]); echo esc_html($data['user_login']); ?>
												<?php elseif($field == 'groupid') : ?>
													<?php $data = $wpforo->usergroup->get_usergroup($item[$field]); echo esc_html($data['name']); ?>
												<?php elseif($field == 'rank') : ?>
													 <div class="author-rating"><div class="bar wpfw-<?php echo esc_attr($wpforo->member->rating_level($item['posts']));  ?> wpfbg-4"></div></div>
												<?php else : ?>
													<?php wpfo($item[$field], true, 'esc_html') ?>
												<?php endif; ?>
											</td>
											
										<?php endif; $i++; ?>
										
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php else : ?>
						<tr class="no-items"><td class="colspanchange" colspan="7"><?php _e('No items found', 'wpforo') ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
			
			<div class="tablenav bottom">
				<div class="alignleft actions">
					<select name="action2">
						<option value="-1" selected="selected"><?php _e('Bulk Actions', 'wpforo') ?></option>
						<?php if($varname == 'phrase') : ?>
							<option value="edit"><?php _e('Edit', 'wpforo') ?></option>
						<?php else : ?>
							<option value="del"><?php _e('Delete', 'wpforo') ?></option>
						<?php endif ?>
					</select>
					<input type="button" onclick="wpforo_doaction()" id="doaction2" class="button action" value="Apply">
				</div>
				<div class="alignleft actions"></div>
				<div class="tablenav-pages <?php echo (($items_count/get_option('wpforo_count_per_page') <= 1) ? 'one-page' : '') ?>"><span class="displaying-num"><?php echo intval($items_count) ?> <?php _e('item', 'wpforo') ?></span>
					<span class="pagination-links">
					<?php if( !isset($_GET['paged']) || $_GET['paged'] <= 2 ) : ?>
						<span class="tablenav-pages-navspan">&laquo;</span>
					<?php else : ?>
						<a class="first-page" title="Go to the first page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>">&laquo;</a>
					<?php endif ?>
					<?php if( !isset($_GET['paged']) || $_GET['paged'] <= 1 ) : ?>
						<span class="tablenav-pages-navspan">‹</span>
					<?php else : ?>
						<a class="prev-page" title="Go to the previous page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>&paged=<?php echo !isset($_GET['paged']) || $_GET['paged'] <= 1 ? 1 : intval($_GET['paged']) - 1 ?>">‹</a>
					<?php endif ?>
					
					<span class="paging-input"><input class="current-page" title="Current page" type="text" name="paged" value="<?php echo isset($_GET['paged']) ? intval($_GET['paged']) : 1 ?>" size="1"> of <span class="total-pages"><?php echo intval($pages_count) ?></span></span>
					
					<?php if( isset($_GET['paged']) && $_GET['paged'] >= $pages_count ) : ?>
						<span class="tablenav-pages-navspan">›</span>
					<?php else : ?>
						<a class="next-page" title="Go to the next page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>&paged=<?php echo (isset($_GET['paged']) ? ( $_GET['paged'] >= $pages_count ? intval($pages_count) : intval($_GET['paged']) + 1 ) : (!isset($_GET['paged']) ? 2 : intval($_GET['paged']) + 1)) ?>">›</a>
					<?php endif ?>
					<?php if( (isset($_GET['paged']) &&  $_GET['paged'] >= $pages_count - 1) || 1 >= $pages_count - 1  ) : ?>
						<span class="tablenav-pages-navspan">&raquo;</span>
					<?php else : ?>
						<a class="last-page" title="Go to the last page" href="<?php echo esc_url(preg_replace('#(\&*paged\=[^\&]*)#is', '', wpforo_full_url())); ?>&paged=<?php echo intval($pages_count) ?>">&raquo;</a></span>
					<?php endif ?>
				</div> <br class="clear">
			</div>
		</form>
	<?php endif; ?>
<?php 
}

function wpforo_phrase($key, $echo = TRUE, $format = 'first-upper'){
	global $wpforo;
	$phrase = (isset($wpforo->phrases[addslashes(strtolower($key))])) ? $wpforo->phrases[addslashes(strtolower($key))] : $key;
	if( $key == $phrase ){
		$phrase = __($key, 'wpforo');
	}
	
	if( $format == 'first-upper' ){
		$phrase = ucfirst($phrase);
	}
	elseif( $format == 'upper' ){
		if(function_exists('mb_strtoupper')){
			$phrase = mb_strtoupper($phrase);
		}
		else{
			$phrase = strtoupper($phrase);
		}
	}
	elseif( $format == 'lower' ){
		if(function_exists('mb_strtolower')){
			$phrase = mb_strtolower($phrase);
		}
		else{
			$phrase = strtolower($phrase);
		}
	}
	
	$phrase = str_replace('{number}', '', $phrase);
	
	if($echo){
		echo $phrase;
	}else{
		return $phrase;
	}
}

function wpforo_screen_option(){ ?>

	<div id="screen-meta" class="metabox-prefs" style="display: none; ">
		<div id="screen-options-wrap" class="hidden" tabindex="-1" aria-label="Screen Options Tab" style="display: none; ">
			<form id="adv-settings" action="" method="POST">
				<h5><?php _e('Show on screen', 'wpforo') ?></h5>
				
				<div class="screen-options">
					<input type="number" step="1" min="1" max="999" class="screen-per-page" name="wpforo_screen_option[value]" id="edit_post_per_page" maxlength="3" value="<?php echo intval(get_option('wpforo_count_per_page')) ?>">
					<label for="edit_post_per_page"><?php _e('Items', 'wpforo') ?></label>
					<input type="submit" name="screen-options-apply" id="screen-options-apply" class="button" value="<?php _e('Apply', 'wpforo') ?>">
				</div>
			</form>
		</div>
	</div>
	
	<div id="screen-meta-links">
		<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle" style="">
			<a href="#screen-options-wrap" id="show-settings-link" class="show-settings screen-meta-active" aria-controls="screen-options-wrap" aria-expanded="true">
				<?php _e('Screen Options', 'wpforo') ?>
			</a>
		</div>
	</div>
  
  <?php
}


function wpforo_text( $text, $length, $echo = TRUE ){
	$text = strip_tags($text);
	if(function_exists('mb_substr')){
		if($echo){
			echo trim( mb_substr( $text, 0, $length, get_option('blog_charset') ) . ( ( function_exists('mb_strlen') ? mb_strlen( $text, get_option('blog_charset') ) : strlen($text) ) > $length ? '...' : '' ) ); 
		}else{
			return trim( mb_substr( $text, 0, $length, get_option('blog_charset') ) . ( ( function_exists('mb_strlen') ? mb_strlen( $text, get_option('blog_charset') ) : strlen($text) ) > $length ? '...' : '' ) ); 
		}
	}else{
		if($echo){
			echo trim( substr( $text, 0, $length ) . ( strlen($text) > $length ? '...' : '' ) ); 
		}else{
			return trim( substr( $text, 0, $length ) . ( strlen($text) > $length ? '...' : '' ) ); 
		}
	}
}

function wpforo_admin_options_tabs( $tabs, $current = 'general', $subtab = FALSE, $sub_current = 'general' ) {
    if(!empty($tabs)){
    	$class_attr = $subtab ? 'vert_tab' : '';
	    echo '<h2 class="nav-tab-wrapper ' . esc_attr($class_attr) . '">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current || ($subtab && $tab == $sub_current ) ) ? ' nav-tab-active' : '';
	        $sub = $subtab ? '&subtab='.esc_attr($tab) : '';
			$class = esc_attr($class);
			$class_attr = $class_attr;
			$current = esc_attr($current);
			$tab =  esc_attr($tab);
			$sub =  esc_attr($sub);
			$name = esc_html($name);
	        echo "<a class='nav-tab$class $class_attr' href='?page=wpforo-settings&tab=". ($subtab ? $current : $tab ) ."$sub'>$name</a>";
	    }
	    echo '</h2>';
	}
}

function wpforo_content_filter( $content ){
	$content = apply_filters('wpforo_body_text_filter', $content);
	$content = preg_replace('#([^\'\"]|^)(https?://[^\s\'\"<>]+\.(?:jpg|jpeg|png|gif|ico|svg|bmp|tiff))([^\'\"]|$)#isu', '$1 <a class="wpforo-auto-embeded-link" href="$2" target="_blank"><img class="wpforo-auto-embeded-image" src="$2"/></a> $3', $content);
	$content = preg_replace('#([^\'\"]|^)(https?://[^\s\'\"<>]+)([^\'\"]|$)#isu', '$1 <a class="wpforo-auto-embeded-link" href="$2" target="_blank">$2</a> $3', $content);
	if(preg_match_all('#<pre([^<>]*)>(.*?class=[\'"]wpforo-auto-embeded[^\'"]*[\'"].*?)</pre>#isu', $content, $matches, PREG_SET_ORDER)){
		foreach($matches as $match){
			$match[2] = preg_replace('#<img[^<>]*class=[\'"]wpforo-auto-embeded-image[\'"][^<>]*src=[\'"]([^\'"]*)[\'"][^<>]*>#isu', '$1', $match[2]);
			$match[2] = preg_replace('#<a[^<>]*class=[\'"]wpforo-auto-embeded-link[\'"][^<>]*href=[\'"]([^\'"]*)[\'"][^<>]*>.*?</a>#isu', '$1', $match[2]);
			$content = str_replace($match[0], '<pre'.$match[1].'>'.$match[2].'</pre>', $content);
		}
	}
	$content = preg_replace('#(<a[^<>]*>[^<>]*)<a[^<>]*class=[\'"]wpforo-auto-embeded-link[\'"][^<>]*href=[\'"]([^\'"]*)[\'"][^<>]*>[^<>]*</a>([^<>]*</a>)#isu', '$1$2$3', $content);
	$content = apply_filters('wpforo_content_filter', $content);
	return wpautop($content);
}

add_filter('wpforo_content_filter', 'wpforo_nofollow_tag', 20);
function wpforo_nofollow_tag( $content ){
    $content = preg_replace_callback('|<a[^><]*href=[\'\"]+([^\'\"]+)[\'\"]+[^><]*>.+?</a>|is', 'wpforo_nofollow', $content);
    return $content;
}
function wpforo_nofollow($match){
    $url = get_bloginfo('url');
    $parse = parse_url($url);
	if( isset($match[0]) ) $link = $match[0];
    if( isset($match[1]) && strpos($match[1], $parse['host']) === FALSE ){
		$link = preg_replace('|(<a[^><]+)>|is', '$1 rel="nofollow">', $match[0]);
    }
    return $link;
}

add_action('wpforo_bottom_hook', 'wpforo_page_logging');
function wpforo_page_logging(){
	global $wpforo;
	$data = $wpforo->current_object;
	
	if( $data['template'] == 'post'  && isset($data['topicid']) && $data['topicid'] ){
		
		$current_user_id = get_current_user_id();
		$current_time =  current_time( 'timestamp', 1 );
		
		if( $current_user_id ){
			//registered user
			$view = $wpforo->db->get_row("SELECT `vid`, `created` FROM `". $wpforo->db->prefix . "wpforo_views` WHERE `topicid` = " . intval($data['topicid']) ." AND `userid` = " . intval($current_user_id), ARRAY_A);
			if( !$view['vid'] ){
				$sql  = "INSERT INTO ". $wpforo->db->prefix ."wpforo_views(  `userid` ,  `topicid` ,  `created` ) VALUES ( '".intval($current_user_id)."', " . intval($data['topicid']) . ",  '" . esc_sql($current_time) . "' ) ";
				$wpforo->db->query($sql);
				$wpforo->db->query("UPDATE `".$wpforo->db->prefix."wpforo_topics` SET `views` = `views` + 1 WHERE `topicid` = " . intval($data['topicid']));
			}else{
				$sql = "UPDATE ". $wpforo->db->prefix ."wpforo_views SET `created` = '" . esc_sql($current_time) . "' WHERE `userid` =  " . intval($current_user_id) . "  AND  `topicid` =  " . intval($data['topicid']);
				$wpforo->db->query($sql);
				if( $current_time - $view['created'] > 86400  ){
					$wpforo->db->query("UPDATE `".$wpforo->db->prefix."wpforo_topics` SET `views` = `views` + 1 WHERE `topicid` = " . intval($data['topicid']));
				}
			}
		}else{ 
			//Guest user
			$viwed_topics_arr = wpforo_getcookie( 'wpforo_view_topics' );
			if( !in_array( $data['topicid'] , (array)$viwed_topics_arr ) ){
				$wpforo->db->query("UPDATE `".$wpforo->db->prefix."wpforo_topics` SET `views` = `views` + 1 WHERE `topicid` = " . intval($data['topicid']));
				$viwed_topics_arr[] = $data['topicid'];
				$args['topics'] = $viwed_topics_arr;
				wpforo_setcookie( 'wpforo_view_topics' , $args );
			}
		}
	}
}

add_action( 'init', 'wpforo_setcookie' );
function wpforo_setcookie( $mode = '', $args = array() ) {
	if( $mode == 'wpforo_view_topics' && $args['topics']){
		$args['topics'] = trim( implode( '|', $args['topics'] ), '|' );
   		@setcookie( 'wpforo_view_topics', $args['topics'] , time() + 86400, COOKIEPATH, COOKIE_DOMAIN );
	}
}

add_action( 'wp_head', 'wpforo_getcookie' );
function wpforo_getcookie( $mode ) {
	if( $mode == 'wpforo_view_topics' ){
		if( isset($_COOKIE['wpforo_view_topics']) && $_COOKIE['wpforo_view_topics'] ){
			return explode('|', $_COOKIE['wpforo_view_topics']);
		}else{
			return FALSE;
		}
	}
}

//Option value filter and output
function wpfo( $option = '', $echo = true, $esc = 'esc_attr' ){
	if( is_string($option) ){
		$option = stripslashes( $option );
		if( $esc == 'esc_attr' ){
			$option = esc_attr( $option );
		}
		elseif( $esc == 'esc_html' ){
			$option = esc_html( $option );
		}
		elseif( $esc == 'esc_url' ){
			$option = esc_url( $option );
		}
		elseif( $esc == 'esc_textarea' ){
			$option = esc_textarea( $option );
		}
	}
	
	if( $echo ){
		echo $option;
	}
	else{
		return $option;
	}
}

//Option maker for checkbox, radio and select
function wpfo_check( $option = '', $value = '', $type = 'checked' , $echo = true ){
	$option = (isset($option) && isset($value) && $option == $value ) ? $type : '';
	if( $echo ){
		echo $option;
	}
	else{
		return $option;
	}
}

function wpforo_human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . '&nbsp;' . @$size[$factor];
}

function wpforo_date($date, $type = 'ago', $echo = true ) {
	global $wpforo;
	$d = $date;
	$sep = ' ';
	$timestamp = strtotime($date);
	$timezone_string = get_option('timezone_string');
	$current_offset = get_option('gmt_offset');
	if(!is_string($type)) $type = 'ago';
	
	if( is_user_logged_in() && !empty($wpforo->current_user) ){
		if( isset($wpforo->current_user['timezone']) && $wpforo->current_user['timezone'] != '' ){
			if(preg_match('|UTC([\-\+]+.?)|is', $wpforo->current_user['timezone'], $timezone_array)){
				$timezone_string = '';
				$current_offset = str_replace('+', '', $timezone_array[1]);
			}
			else{
				if(in_array($wpforo->current_user['timezone'], timezone_identifiers_list())){
					$timezone_string = $wpforo->current_user['timezone'];
					$current_offset = '';
				}
				else{
					$timezone_string = '';
					$current_offset = '';
				}
			}
		}
	}
	
	if( $timezone_string == '' && $current_offset != '' ){
		$timezone_string = timezone_name_from_abbr('', $current_offset * 3600, false);	
	}
	
	if( class_exists('DateTime') && class_exists('DateTimeZone') && $timezone_string ){
		$dt = new DateTime("now", new DateTimeZone($timezone_string)); 
		if( method_exists($dt, 'setTimestamp') ) $dt->setTimestamp($timestamp); //DateTime::setTimestamp() available in PHP 5.3 and higher versions
		if( $type == 'human' ){
			$d = human_time_diff($timestamp);
		}
		elseif( $type == 'ago' ){
			$d = human_time_diff($timestamp) . '&nbsp;';
			$d = sprintf( wpforo_phrase('%s ago', false, false), $d );
		}
		else{
			if( wpforo_feature('wp-date-format', $wpforo) ){
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				$type = $date_format . $sep . $time_format;
			}
			$d = date_i18n($type, strtotime($dt->format('Y-m-d H:i:s')));
		}
	}
	else{
		
		if( $type == 'human' ){
			$d = human_time_diff($timestamp);
		}
		elseif( $type == 'ago' ){
			$d = human_time_diff($timestamp) . '&nbsp;';
			$d = sprintf( wpforo_phrase('%s ago', false, false), $d );
		}
		else{
			if( wpforo_feature('wp-date-format', $wpforo) ){
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				$type = $date_format . $sep . $time_format;
			}
			$d = date_i18n( $type, $timestamp);
		}
	}
	
	if( $echo ){
		echo $d;
	}
	else{
		return $d;
	}
}


function wpforo_write_file( $new_file, $content ){
	$return = array( 'error' => false, 'file' => '' );
	$ifp = @fopen( $new_file, 'wb' );
	if ( ! $ifp ) {
		$return = array( 'error' => sprintf( __( 'Could not write file %s' ), $new_file ) );
	}
	else{
		@fwrite( $ifp, $content );
		fclose( $ifp );
		clearstatcache();
		// Set correct file permissions
		$stat = @stat( dirname( $new_file ) );
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		@chmod( $new_file, $perms );
		clearstatcache();
		$return = array( 'file' => $new_file );
	}
	return $return;
}


function wpforo_get_file_content( $file ){
	$fp = @fopen( $file, 'r' );
	if( !$fp ){
		return false;
	}
	else{
		$file_data = fread( $fp, filesize($file) );
		fclose( $fp );
		return $file_data;
	}
}

################################################################################
/**
 * Clears file basename and removes trailing slash
 *
 * @since 1.0.0
 *
 * @param	string		filename
 *
 * @return	string	
 */
function wpforo_clear_basename( $file ) {
	$file = str_replace('\\','/',$file);
	$file = preg_replace('|/+|','/', $file); 
	$file = trim($file, '/');
	return $file;
}

#################################################################################
/**
 * Removes directory with all files and folders
 *
 * @since 1.0.0
 *
 * @param	string		directory name
 *
 */
function wpforo_remove_directory( $directory ) {
	$directory = trim( $directory, '/') . '/';
    foreach( glob( $directory . '/*' ) as $item ) {
        if( is_dir( $item ) ){
			wpforo_remove_directory( $item );
		}
        else{
			unlink( $item );
		}
    }
    return rmdir( $directory );
}
#################################################################################
/**
 * Converts bytes to KB, MB, GB
 *
 * @since 1.0.0
 *
 * @param	integer		Bytes
 *
 * @return	string	
 */
function wpforo_print_size($value, $points = true ){

	if($value <= 1024)
	{
		return $value . (($points) ? " B" : '' );
	}
	if($value > 1024 && $value <= (1024*1024))
	{
		$value=round(($value/1024)*10)/10;
		return $value . (($points) ? " KB" : '' );
	}
	if($value > 1024*1024 && $value <= 1024*1024*1024)
	{
		$value=round(($value/(1024*1024))*10)/10;
		return $value . (($points) ? " MB" : '' );
	}
	if($value > 1024*1024*1024 && $value <= 1024*1024*1024*1024)
	{
		$value=round(($value/(1024*1024*1024))*10)/10;
		return $value . (($points) ? " GB" : '' );
	}
}

function wpforo_human_size_to_bytes($sSize)  
{  
    if ( is_numeric( $sSize) ) {
       return $sSize;
    }
    $sSuffix = substr($sSize, -1);  
    $iValue = substr($sSize, 0, -1);  
    switch(strtoupper($sSuffix)){  
    case 'P':  
        $iValue *= 1024;  
    case 'T':  
        $iValue *= 1024;  
    case 'G':  
        $iValue *= 1024;  
    case 'M':  
        $iValue *= 1024;  
    case 'K':  
        $iValue *= 1024;  
        break;  
    }  
    return $iValue;  
}  


function wpforo_print_number($n, $echo = false) {
	$n = (0+str_replace(",","",$n));
	if(!is_numeric($n)) return false;
	if($n>1000000000000) return round(($n/1000000000000),1).' '.wpforo_phrase('{number}T',false);
	else if($n>1000000000) return round(($n/1000000000),1).' '.wpforo_phrase('{number}B',false);
	else if($n>1000000) return round(($n/1000000),1).' '.wpforo_phrase('{number}M',false);
	else if($n>10000) return round(($n/1000),1).' '.wpforo_phrase('{number}K',false);
	if($echo){
		echo number_format($n);
	}
	else{
		return number_format($n);
	}
}

function wpforo_bigintval($value) {
	$value = trim($value);
	if (ctype_digit($value)) {
	  return $value;
	}
	$value = preg_replace("/[^0-9](.*)$/", '', $value);
	if (ctype_digit($value)) {
	  return $value;
	}
	return 0;
}

function wpforo_removebb($string){
	if(isset($string) && $string ){
		$string = preg_replace('|\[\/*[^\]\[]+\]|is', '', $string);
	}
	return $string;
}

function wpforo_file_upload_error($code){
	switch ($code) {
		case UPLOAD_ERR_INI_SIZE:
			$message = wpforo_phrase("The uploaded file exceeds the upload_max_filesize directive in php.ini", false);
			break;
		case UPLOAD_ERR_FORM_SIZE:
			$message = wpforo_phrase("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", false);
			break;
		case UPLOAD_ERR_PARTIAL:
			$message = wpforo_phrase("The uploaded file was only partially uploaded", false);
			break;
		case UPLOAD_ERR_NO_FILE:
			$message = wpforo_phrase("No file was uploaded", false);
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$message = wpforo_phrase("Missing a temporary folder", false);
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$message = wpforo_phrase("Failed to write file to disk", false);
			break;
		case UPLOAD_ERR_EXTENSION:
			$message = wpforo_phrase("File upload stopped by extension", false);
			break;
		default:
			$message = wpforo_phrase("Unknown upload error", false);
			break;
	}
	return $message;
}

//$key allowed values are post, strip, data, user_description entities or the name of a field filter such as pre_user_description. 
//More info https://core.trac.wordpress.org/browser/tags/4.5.2/src/wp-includes/kses.php#L624
function wpforo_kses( $string = '', $key = 'data' ){
	
	if(!$string || !$key) return $string;
	if( $key == 'email' ){
		$allowed_html = array( 'a' => array( 'href' => array(), 'title' => array()),
							   'blockquote' => array(),
							   'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
							   'hr' => array(),
							   'br' => array(),
							   'p' => array(),
							   'strong' => array());
	}
	else{
		$allowed_html = wp_kses_allowed_html( $key );
		if( $key == 'user_description' ){ 
			$allowed_html['img'] = array( 'alt' => array(), 'align' => array(), 'border' => array(), 'height' => array(), 'hspace' => array(), 'longdesc' => array(), 'vspace' => array(), 'src' => array(), 'usemap' => array(), 'width' => array());
		}
	}
	$allowed_html['a']['data-gallery'] = array();
	$allowed_html['a']['download'] = array();
	return wp_kses( $string, $allowed_html );
}

function wpforo_deep_merge($default, $current = array()){
	foreach($default as $k => $v){
		if(!empty($v) && is_array($v)){
			foreach($v as $kk => $vv){
				if(!empty($vv) && is_array($vv)){
					foreach($vv as $kkk => $vvv){
						if(!empty($vvv) && is_array($vvv)){
							foreach($vvv as $kkkk => $vvvv){
								if(!empty($vvv) && is_array($vvv)){
									//Stop on 5th level
								}
								else{
									if(isset($current[$k][$kk][$kkk][$kkkk])) $default[$k][$kk][$kkk][$kkkk] = $current[$k][$kk][$kkk][$kkkk];
								}
							}
						}
						else{
							if(isset($current[$k][$kk][$kkk])) $default[$k][$kk][$kkk] = $current[$k][$kk][$kkk];
						}
					}
				}
				else{
					if(isset($current[$k][$kk])) $default[$k][$kk] = $current[$k][$kk];
				}
			}
		}
		else{
			if(isset($current[$k])) $default[$k] = $current[$k];
		}
	}
	return $default;
}


function wpforo_is_image($e){
    $is_image = false;
    $e = strtolower($e);
	if( $e == 'jpg' || $e == 'jpeg' || $e == 'png' || $e == 'gif' ){
		$is_image = true;
	}
    return $is_image;
}

function get_wpf_option( $key ){
	global $wpdb;
    $value = get_option($key);
	if( $value ){
		$value = maybe_unserialize( $value );
	}
	else{
		$value = $wpdb->get_var("SELECT `option_value` FROM `".$wpdb->prefix."options` WHERE `option_name` = '". esc_sql($key) . "'");
		if(is_serialized( $value )) {
			$check = @unserialize($value);
			if( !$check ) $value = wpforo_fixSerializedArray($value);
			if( !$value && $value !== 0 ) return NULL;
		}
	}
	return $value;
}

/**
* Extract what remains from an unintentionally truncated serialized string
* $data contains your original array (or what remains of it).
* @param string The serialized array
*/
function wpforo_fixSerializedArray($serialized){
    $tmp = preg_replace('/^a:\d+:\{/', '', $serialized);
    return wpforo_fixSerializedArray_R($tmp);
}
/**
* The recursive function that does all of the heavy lifing. Do not call directly.
* @param string The broken serialzized array
* @return string Returns the repaired string
*/
function wpforo_fixSerializedArray_R(&$broken){
    $data       = array();
    $index      = NULL;
    $len        = strlen($broken);
    $i          = 0;
    while(strlen($broken)) {
        $i++;
        if ($i > $len) { break; }
        if (substr($broken, 0, 1) == '}') {
            $broken = substr($broken, 1); return $data;
        }
		else{
            $bite = substr($broken, 0, 2);
            switch($bite) {   
                case 's:':
                    $re = '/^s:\d+:"([^\"]*)";/';
                    if (preg_match($re, $broken, $m)){
                        if ($index === NULL){ $index = $m[1]; }
                        else{$data[$index] = $m[1]; $index = NULL;}
                        $broken = preg_replace($re, '', $broken);
                    }
                break;
                case 'i:':
                    $re = '/^i:(\d+);/';
                    if (preg_match($re, $broken, $m)){
                        if ($index === NULL){$index = (int) $m[1]; }
                        else{$data[$index] = (int) $m[1]; $index = NULL; }
                        $broken = preg_replace($re, '', $broken);
                    }
                break;
                case 'b:':
                    $re = '/^b:[01];/';
                    if (preg_match($re, $broken, $m)){
                        $data[$index] = (bool) $m[1]; $index = NULL; $broken = preg_replace($re, '', $broken);
                    }
                break;
                case 'a:':
                    $re = '/^a:\d+:\{/';
                    if (preg_match($re, $broken, $m)){
                        $broken = preg_replace('/^a:\d+:\{/', '', $broken); $data[$index] = wpforo_fixSerializedArray_R($broken); $index = NULL;
                    }
                break;
                case 'N;':
                    $broken = substr($broken, 2); $data[$index] = NULL; $index = NULL;
                break;
            }
        }
    }
    return $data;
}

function wpforo_insert_to_media_library( $attach_path, $title = '' ){
	if( wpforo_feature('attach-media-lib') ){
		if(!$attach_path ) return 0;
		$attach_fname = basename($attach_path);
		if(!$title) $title = $attach_fname;
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$wp_upload_dir = wp_upload_dir();
		$filetype = wp_check_filetype( $attach_fname, NULL );
		$attachment = array( 'guid' => $attach_path, 'post_mime_type' => $filetype['type'], 'post_title' => $title, 'post_content' => '', 'post_status' => 'inherit');
		$attach_id = wp_insert_attachment( $attachment, $attach_path );
		add_filter( 'intermediate_image_sizes', 'wpforo_attachment_sizes' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $attach_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		remove_filter( 'intermediate_image_sizes', 'wpforo_attachment_sizes' );
		return $attach_id;
	}
}

function wpforo_attachment_sizes( $sizes ){
	return array('thumbnail');	
}

function wpforo_debug( $wpforo ){
	?>
	<div id="wpforo-debug" style="display:none">
        <h4>Current Object</h4>
        <p><?php @print_r($wpforo->current_object); ?></p>
        <h4>Super Globals</h4>
        <p>Requests: <?php print_r($_REQUEST); ?></p>
        <p>Server: <?php print_r($_REQUEST); ?></p>
        <h4>Options and Features</h4>
        <textarea style="width:500px; height:300px;"><?php echo @ 'permastruct: ' . $wpforo->permastruct . "\r\n";
        echo @ 'use_home_url: ' . $wpforo->use_home_url . "\r\n";
        echo @ 'url: ' . $wpforo->url . "\r\n";
        @print_r($wpforo->general_options) . "\r\n";
        echo @ 'pageid:' . $wpforo->pageid . "\r\n";
        echo @ 'default_groupid: ' . $wpforo->default_groupid . "\r\n";
        @print_r($wpforo->usergroup_cans) . "\r\n";
        @print_r($wpforo->forum_options) . "\r\n";
        @print_r($wpforo->forum_cans) . "\r\n";
        @print_r($wpforo->post_options) . "\r\n";
        @print_r($wpforo->member_options) . "\r\n";
        @print_r($wpforo->subscribe_options) . "\r\n";
        @print_r($wpforo->features) . "\r\n";
        @print_r($wpforo->style_options) . "\r\n";
        @print_r($wpforo->theme_options) . "\r\n";
        @print_r($wpforo->theme) . "\r\n";
        ?>
        </textarea>
    </div>
    <?php
}
?>