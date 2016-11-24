<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<!-- Screen Options -->
<?php if( isset($_GET['action']) && $_GET['action'] == 'add' || isset($_GET['action']) && $_GET['action'] == 'edit') : ?>
	
	<div id="screen-meta" class="metabox-prefs" style="display: none; ">
		<div id="screen-options-wrap" class="hidden" tabindex="-1" aria-label="Screen Options Tab" style="display: none; ">
			<form id="adv-settings" action="" method="post">
				<h5><?php _e('Show on screen', 'wpforo'); ?></h5>
				<div class="metabox-prefs">
					<label for="forum_cat-hide"><input class="hide-postbox-tog" name="forum_cat-hide" type="checkbox" id="forum_cat-hide" value="forum_cat" checked="checked"><?php _e('Forum Options', 'wpforo'); ?></label>
					<label for="forum_permissions-hide"><input class="hide-postbox-tog" name="forum_permissions-hide" type="checkbox" id="forum_permissions-hide" value="forum_permissions" checked="checked"><?php _e('Permissions', 'wpforo'); ?></label>
					<label for="forum_slug-hide"><input class="hide-postbox-tog" name="forum_slug-hide" type="checkbox" id="forum_slug-hide" value="forum_slug"><?php _e('Slug', 'wpforo'); ?></label>
					<label for="forum_meta-hide"><input class="hide-postbox-tog" name="forum_meta-hide" type="checkbox" id="forum_meta-hide" value="forum_meta" checked="checked"><?php _e('Forum Meta', 'wpforo'); ?></label>
					<br class="clear">
				</div>
				<h5 class="screen-layout"><?php _e('Screen Layout', 'wpforo'); ?></h5>
				<div class="columns-prefs"><?php _e('Number of Columns', 'wpforo'); ?>:				
					<label class="columns-prefs-1"><input type="radio" name="screen_columns" value="1">1</label>
					<label class="columns-prefs-2"><input type="radio" name="screen_columns" value="2" checked="checked">2</label>
				</div>
			</form>
		</div>
	</div>
	
	<div id="screen-meta-links">
		<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle" style="">
			<button aria-expanded="true" aria-controls="screen-options-wrap" class="button show-settings screen-meta-active" id="show-settings-link" type="button"><?php _e('Screen Options', 'wpforo'); ?></button>
		</div>
	</div>
    
<?php endif; ?>
<!-- end Screen Options -->

	<div id="icon-edit" class="icon32 icon32-posts-post"></div>
	<div id="wpf-admin-wrap" class="wrap">

	<h2 style="padding:30px 0px 10px 0px;line-height: 20px;">
		<?php _e('Categories and Forums', 'wpforo'); ?> &nbsp; 
		<a href="<?php echo admin_url( 'admin.php?page=wpforo-forums&action=add' ) ?>" class="add-new-h2"><?php _e('Add New', 'wpforo'); ?></a>
	</h2>

	<?php $wpforo->notice->show(FALSE) ?>
	
	<!-- Forum Hierarchy -->
	<?php if( !isset($_GET['action'])) : ?>
		<?php if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'ef')): ?>
			
            
            <div class="wpf-info-bar" style="box-shadow:none; margin:20px 0px;">
                <p style="font-size:15px; padding:0px; margin:10px 0px;"><?php _e('Please drag and drop forum panels to set parent-child hierarchy.', 'wpforo'); ?></p>
            </div>
            
            <form id="forum-hierarchy" encType="multipart/form-data" method="post" action="">
            	<?php wp_nonce_field( 'wpforo-forums-hierarchy' ); ?>
				<input type="hidden" name="forums_hierarchy_submit"/>
				<div id="post-body">
					<ul id="menu-to-edit" class="menu">
						
						<?php $wpforo->forum->tree('drag_menu'); ?>
						
					</ul>
				</div><br />
				<div class="major-publishing-actions">
					<div class="publishing-action"><input id="save_menu_footer" class="button button-primary menu-save" name="save_menu" value="<?php _e('Save forums order and hierarchy', 'wpforo'); ?>" onclick="get_forums_hierarchy()" type="button"></div>
				</div>
			</form>	
            <script>
            var menus = false;
			navMenuL10n.saveAlert = null;
			window.onbeforeunload=function(){if(a.menusChanged){return navMenuL10n.saveAlert}}
            </script>
		<?php endif; ?><!--checking edit forum permission-->
	<?php endif; ?>
	<!-- end Forum Hierarchy -->
	
	<!-- Forum Add || Edit -->
	<?php if( ( isset($_GET['action']) && $_GET['action'] == 'add' ) || ( isset($_GET['action']) && $_GET['action'] == 'edit' ) ) : ?>
		<?php if($wpforo->perm->usergroup_can( $wpforo->current_user_groupid, 'cf')): ?>
			<?php if(isset($_GET['id'])) $data = $wpforo->forum->get_forum( array('forumid' => $_GET['id']) );?>
			<div id="poststuff">
				<form name="forum" action="" method="post">
                	<?php wp_nonce_field( 'wpforo-forum-addedit' ); ?>
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<input type="hidden" name="wpforo_submit" value="1"/>
							<input type="hidden" name="forum[order]" value="<?php echo esc_attr(isset($data['order']) ? $data['order'] : '') ?>"/>
							<div class="form-wrap">
								<div class="form-field form-required" style="margin-bottom:0px; padding-bottom:0px;">
									<div id="titlediv">
										<div id="titlewrap">
											<input id="title" name="forum[title]"  type="text" value="<?php echo esc_attr(isset($data['title']) ? $data['title'] : '') ?>" size="40" autocomplete="off" required="TRUE" placeholder="<?php _e('Enter forum title here', 'wpforo'); ?>" />
										</div>
									</div>
									<p>&nbsp;</p>
									<div class="form-field">
										<textarea placeholder="<?php _e('Enter description here . . .', 'wpforo'); ?>" name="forum[description]" rows="5" cols="40" style="padding:10px;"><?php echo esc_textarea(isset($data['description']) ? $data['description'] : '') ?></textarea>
										<p><?php _e('This is a forum description. This content will be displayed under forum title on the forum list.', 'wpforo'); ?></p>
									</div>
								</div>
							</div>	
						</div>
						
						<div id="postbox-container-1" class="postbox-container">
							<div id="side-sortables" class="meta-box-sortables ui-sortable">
								
                                
								<div id="forum_cat" class="postbox" style="display: block; ">
									<div class="handlediv" title="Click to toggle"><br></div>
									<h3 class="hndle"><span><?php _e('Forum Options', 'wpforo'); ?></span></h3>
									<div class="inside">
										<div class="form-field">
											<p><strong><?php _e('Parent Forum', 'wpforo'); ?></strong></p>
											<p>
                                            <select id="parent" name="forum[parentid]" class="postform" <?php echo (isset($data['is_cat']) && $data['is_cat'] == 1 ? 'disabled' : '') ?>>
												<option value="0"><?php _e('No parent', 'wpforo'); ?></option>
												<?php $wpforo->forum->tree('select_box'); ?>
											</select>
											</p>
											<p class="form-field">
												<label for="use_us_cat"><?php _e('Use as Category', 'wpforo'); ?> &nbsp;<input id="use_us_cat" onclick="document.getElementById('parent').disabled = this.checked; document.getElementById('cat_layout').disabled = !this.checked;" type="checkbox" name="forum[is_cat]" value="1" <?php echo (isset($data['is_cat']) && $data['is_cat'] == 1 ? 'checked' : '') ?>/> </label>
											</p>
											<p><strong><?php _e('Category Layout', 'wpforo'); ?></strong></p>
											<p>
                                            <?php $layouts = $wpforo->tpl->find_layouts( WPFORO_THEME ); ?>
                                            <?php if(!empty($layouts)): ?>
                                                <select id="cat_layout" name="forum[cat_layout]" class="postform" <?php $data['cat_layout'] = ( isset($data['cat_layout']) ? $data['cat_layout'] : 1 ); echo ( isset($data['is_cat']) && $data['is_cat'] == 1  ? '' : 'disabled="TRUE"' ); ?> >
                                                    <?php $wpforo->tpl->show_layout_selectbox($data['cat_layout']); ?>
                                                </select>
                                            <?php else: ?>
                                            	<p><?php _e('No layout found.', 'wpforo'); ?></p>
                                            <?php endif; ?>
                                            </p>
										</div>
									</div>
								</div>
								
                                <div id="submitdiv" class="postbox" style="display: block; ">
									<div class="handlediv" title="Click to toggle"><br></div>
									<h3 class="hndle"><span><?php _e('Publish', 'wpforo'); ?></span></h3>
									<div class="inside">
										<div id="major-publishing-actions" style="text-align:right;">
											<?php if( $_GET['action'] == 'edit' ) : ?>
												<a class="wpf-delete button" href="?page=wpforo-forums&id=<?php echo intval($data['forumid']) ?>&action=del" onclick="if (!confirm('<?php _e('Are you sure you want to delete this forum?', 'wpforo'); ?>')) { return false; }"><?php _e('Delete', 'wpforo'); ?></a> &nbsp; 
												<a class="preview button" href="<?php echo WPFORO_BASE_URL . (isset($data['slug']) ? $data['slug'] : '')  ?>" target="wp-preview" id="post-preview"  style="display:inline-block;float:none;"><?php _e('View', 'wpforo'); ?></a> &nbsp; 
											<?php endif; ?>
											<input type="submit" name="forum[save_edit]" class="button button-primary forum_submit" style="display:inline-block;float:none;" value="<?php _e('Publish', 'wpforo'); ?>">
											<div class="clear"></div>
										</div>
									</div>
								</div>
                                
                                
                                <div id="forum_permissions" class="postbox" style="display: block; ">
									<div class="handlediv" title="Click to toggle"><br></div>
									<h3 class="hndle"><span>Forum Permissions</span></h3>
									<div class="inside">
										<table>
											<?php $wpforo->forum->permissions(); ?>
										</table>
									</div>
								</div>
                                
                                <?php if( get_option('wpforo_integrate_s2member') == 1 && false ) : ?>
									
									<?php 
										
										if(isset( $_GET['id'] )){
											$srlz = get_option( 'wpforo_s2member_items_levels' );
											$s2member_items_levels = unserialize( $srlz );
											
											for( $i = 0; $i < 5; $i++ ){
												$f_ids = explode(',', $s2member_items_levels['level'.$i.'_forums']);
												if(in_array( $_GET['id'], $f_ids)){
													$lvl = $i;
													break;
												}
												unset($f_ids);
											}
										}else{
											$lvl = -1;
										}
										
									?>
									
									<div id="ws-plugin--s2member-security" class="postbox " style="display: block; ">
										<div class="handlediv" title="Click to toggle"><br></div>
										<h3 class="hndle"><span>s2Member®</span></h3>
										<div class="inside">
											
											<p style="margin-left:2px;"><strong>Forum Level Restriction?</strong></p>
											<label class="screen-reader-text" for="ws-plugin--s2member-security-meta-box-level">Add Level Restriction?</label>
											<select name="forum[s2member_level]" id="ws-plugin--s2member-security-meta-box-level" style="width:99%;">
												<option value=""></option>
												<option value="0" <?php echo ( $lvl != null && $lvl == 0 ? 'selected' : '' ); ?> >Require Level #0 (or higher)</option>
												<option value="1" <?php echo ( $lvl != null && $lvl == 1 ? 'selected' : '' ); ?> >Require Level #1 (or higher)</option>
												<option value="2" <?php echo ( $lvl != null && $lvl == 2 ? 'selected' : '' ); ?> >Require Level #2 (or higher)</option>
												<option value="3" <?php echo ( $lvl != null && $lvl == 3 ? 'selected' : '' ); ?> >Require Level #3 (or higher)</option>
												<option value="4" <?php echo ( $lvl != null && $lvl == 4 ? 'selected' : '' ); ?> >Require Highest Level #4</option>
											</select><br>
											
										</div>
									</div>
									
								<?php endif; ?>
                                
							</div>
						</div>
						
						<div id="postbox-container-2" class="postbox-container">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								
								<div id="forum_slug" class="postbox  hide-if-js" style="display: none; ">
									<div class="handlediv" title="Click to toggle"><br></div>
									<h3 class="hndle"><span><?php _e('Forum Slug', 'wpforo'); ?></span></h3>
									<div class="inside">
										<input name="forum[slug]"  type="text" value="<?php echo esc_attr(isset($data['slug']) ? $data['slug'] : '') ?>" size="40" />
										<p><?php _e('The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wpforo'); ?> </p><br /> 
									</div>
								</div>
								
								<div id="forum_meta" class="postbox  hide-if-js" style="display: block; ">
									<div class="handlediv" title="Click to toggle"><br></div>
									<h3 class="hndle"><span><?php _e('Forum SEO', 'wpforo'); ?></span></h3>
									<div class="inside" style="padding-top:10px;">
										<div class="form-field">
											<label for="tag-description" style="display:block; padding-bottom:5px;"><?php _e('Meta Description', 'wpforo'); ?>:</label>
											<textarea name="forum[meta_desc]" rows="3" cols="40"><?php echo esc_html(isset($data['meta_desc']) ? $data['meta_desc'] : '') ?></textarea>
										</div>
									</div>
								</div>
								
							</div>
							<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
						</div>
						
					</div>
				</form>
			</div>
		<?php endif; ?><!-- chekcing creat forum permission-->
	<?php endif; ?>
	<!-- end Forum Add || Edit -->
	
	<!-- Forum Delete -->
	<?php if( isset($_GET['action']) && $_GET['action'] == 'del') : ?>
		
		<form action="" method="post">
        	<?php wp_nonce_field( 'wpforo-forum-delete' ); ?>
			<input type="hidden" name="wpforo_delete" value="1"/>
			<div class="form-wrap">
				<div class="form-field form-required">			
					<div class="form-field wpf-info-bar" style="padding:25px 20px 15px 20px; margin-top:20px;">
						<table class="wpforo_settings_table">
							<tr>
								<td style="width:50%;">
									<label for="delete_forum" class="menu_delete" style="color: red; font-size:13px; line-height:18px;"><?php _e('This action will also delete all sub-forums, topics and replies.', 'wpforo'); ?></label>
								</td>
								<td width="20px">
									<input id="delete_forum" type="radio" name="forum[delete]" value="1" checked="" onchange="mode_changer('false');"/>
								</td>
							</tr>
							<tr>
								<td>
									<label for="marge"  style="font-size:13px; line-height:18px;"><?php _e('If you want to delete this forum and keep its sub-forums, topics and replies, please select a new target forum in dropdown below', 'wpforo'); ?></label>
								</td>
								<td><input id="marge" type="radio" name="forum[delete]" value="0" onchange="mode_changer('true');"/> </td>
							</tr>
							<tr>
								<td colspan="2">
                                    <select id="forum_select" name="forum[mergeid]" class="postform" disabled="" >
                                        <?php $wpforo->forum->tree('select_box'); ?>
                                    </select>
                                    <p><?php _e('All sub-forums, topics and replies will be attached to selected forum. Layout will be inherited from this forum.', 'wpforo'); ?></p>
                                </td>
							</tr>
                            <tr>
								<td colspan="2">
                                    <input id="forum_submit"  type="submit" name="forum[submit]" class="button button-primary" value="Delete" />
                                </td>
							</tr>
						</table>
					</div>
				</div>
			</div>	
		</form>
	<?php endif; ?>
	<!-- end Forum Delete -->
	
</div><!-- wpwrap -->