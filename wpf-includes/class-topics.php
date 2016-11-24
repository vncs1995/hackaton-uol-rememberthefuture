<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

class wpForoTopic{
	
	private $wpforo;
	private static $cache = array();
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
	
	private function unique_slug($slug){
		$new_slug = wpforo_text($slug, 250, false);
		$i = 2;
		while( $this->wpforo->db->get_var("SELECT `topicid` FROM ".$this->wpforo->db->prefix."wpforo_topics WHERE `slug` = '" . esc_sql($new_slug) . "'") ){
			$new_slug = wpforo_text($slug, 250, false) . '-' . $i;
			$i++;
		}
		return $new_slug;
	}
	
	public function add( $args = array() ){
		
		if( empty($args) && empty($_REQUEST['topic']) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['topic']) ){ 
			$args = $_REQUEST['topic']; 
			$args['body'] = $_REQUEST['postbody']; 
		}
		
		if( !isset($args['forumid']) || !$args['forumid'] = intval($args['forumid']) ){
			$this->wpforo->notice->add('Add Topic error: No forum selected', 'error');
			return FALSE;
		}
		
		if( !$this->wpforo->perm->forum_can( $args['forumid'], 'ct') ){
			$this->wpforo->notice->add('You haven\'t permission to create topic into this forum', 'error');
			return FALSE;
		}
		
		if( !isset($args['title']) || !$args['title'] = trim(strip_tags($args['title'])) ){
			$this->wpforo->notice->add('Please insert required fields!', 'error');
			return FALSE;
		}
		
		$args['title'] = wpforo_text($args['title'], 250, false);
		$args['body'] = (isset($args['body']) ? preg_replace('#</pre>[\r\n\t\s\0]*<pre>#isu', "\r\n", $args['body']) : '' );
		$args['slug'] = (isset($args['slug']) && $args['slug']) ? sanitize_title($args['slug']) : ((isset($args['title'])) ? sanitize_title($args['title']) : md5(time()));
		$args['slug'] = $this->unique_slug($args['slug']);
		$args['created'] = (isset($args['created']) ? sanitize_text_field($args['created']) : current_time( 'mysql', 1 ) );
		$args['userid'] = (isset($args['userid']) ? intval($args['userid']) : $this->wpforo->current_userid );
		
		$args = apply_filters('wpforo_add_topic_data_filter', $args);
		if(empty($args)) return FALSE;
		
		extract($args, EXTR_OVERWRITE);
		
		if(isset($forumid)) $forumid = intval($forumid);
		if(isset($title)) $title = sanitize_text_field(trim($title));
		if(isset($slug)) $slug = sanitize_title($slug);
		if(isset($created)) $created = sanitize_text_field($created);
		if(isset($userid)) $userid = intval($userid);
		if(isset($type)) $type = intval($type);
		if(isset($meta_key)) $meta_key = sanitize_text_field($meta_key);
		if(isset($meta_desc)) $meta_desc = sanitize_text_field($meta_desc);
		if(isset($body)) $body = wpforo_kses(trim($body), 'post');
		$meta_key = (isset($meta_key) ? $meta_key : '');
		$meta_desc = (isset($meta_desc) ? $meta_desc : '');
		$has_attach = ( isset($has_attach) && $has_attach ) ? 1 : ((strpos($body, '[attach]') !== FALSE) ? 1 : 0);
		
		do_action( 'wpforo_before_add_topic', $args );
		
		if(
			$this->wpforo->db->insert( 
				$this->wpforo->db->prefix . 'wpforo_topics', 
				array( 
					'title'		=> stripslashes($title), 
					'slug' 		=> $slug, 
					'forumid'	=> $forumid, 
					'userid' 	=> $userid,
					'type'		=> (isset($type) ? 1 : 0),
					'created'	=> $created,
					'modified'	=> $created,
					'last_post'	=> 0,
					'views'		=> 0,
					'posts'		=> 1,
					'meta_key' 	=> $meta_key, 
					'meta_desc' => $meta_desc, 
					'has_attach'=> $has_attach
				), 
				array('%s','%s','%d','%d','%s','%s','%s','%d','%d','%d','%s','%s','%d')
			)
		){
			$topicid = $this->wpforo->db->insert_id;
			if(
				$this->wpforo->db->insert( 
					$this->wpforo->db->prefix . 'wpforo_posts', 
					array( 
						'forumid'	=> $forumid,
						'topicid'	=> $topicid, 
						'userid' 	=> $userid,
						'title'     => stripslashes($title), 
						'body'      => stripslashes($body), 
						'created'	=> $created,
						'modified'	=> $created,
						'is_first_post' => 1
					), 
					array('%d','%d','%d','%s','%s','%s','%s','%d')
				)
			){
				$first_postid = $this->wpforo->db->insert_id;
				if( FALSE !== $this->wpforo->db->update( 
						$this->wpforo->db->prefix . 'wpforo_topics', 
						array( 'first_postid' => $first_postid, 'last_post' => $first_postid ),
						array( 'topicid' => $topicid ), 
						array( '%d', '%d' ),
						array( '%d' )
					)
				){
					$questions = '';
					$forum = $this->wpforo->forum->get_forum($forumid);
					if( isset($forum['cat_layout']) && $forum['cat_layout'] == 3 ) $questions = ', `questions` = `questions` + 1 ';
					
					$this->wpforo->db->query( "UPDATE "  . $this->wpforo->db->prefix . "wpforo_forums SET `last_post_date` = '" . esc_sql($created). "', `last_userid` = " . intval($userid). ", `last_topicid` = " . intval($topicid) . ", `last_postid` = " . intval($first_postid) . ", `topics` = `topics` + 1 , `posts` = `posts` + 1 WHERE `forumid` = " . intval($forumid) );
					$this->wpforo->db->query( "UPDATE "  . $this->wpforo->db->prefix . "wpforo_profiles SET `posts` = `posts` + 1 $questions WHERE `userid` = " . intval($userid) );
					
					$args['topicid'] = $topicid;
					$args['topicurl'] = $this->get_topic_url($topicid);
					
					do_action( 'wpforo_after_add_topic', $args );
					
					$this->wpforo->member->reset($userid);
					$this->wpforo->notice->add('Your topic successfully added', 'success');
					return $topicid;
				}
			}
			
		}
		
		$this->wpforo->notice->add('Topic add error', 'error');
		return FALSE;
	}
	
	public function edit( $args = array() ){
		if( empty($args) && empty($_REQUEST['topic']) ) return FALSE;
		if( !isset($args['topicid']) && isset($_GET['id']) ) $args['topicid'] = $_GET['id'];
		if( empty($args) && !empty($_REQUEST['topic']) ){ $args = $_REQUEST['topic']; $args['body'] = $_REQUEST['postbody']; }
		
		$args = apply_filters('wpforo_edit_topic_data_filter', $args);
		if(empty($args)) return FALSE;
		
		extract($args, EXTR_OVERWRITE);
		
		if(isset($topicid)) $topicid = intval($topicid);
		if(isset($forumid)) $forumid = intval($forumid);
		if(isset($title)) $title = sanitize_text_field(trim($title));
		if(isset($slug)) $slug = sanitize_title($slug);
		if(isset($created)) $created = sanitize_text_field($created);
		if(isset($userid)) $userid = intval($userid);
		if(isset($type)) $type = intval($type);
		if(isset($meta_key)) $meta_key = sanitize_text_field($meta_key);
		if(isset($meta_desc)) $meta_desc = sanitize_text_field($meta_desc);
		if(isset($has_attach)) $has_attach = intval($has_attach);
		if(isset($body)) $body = wpforo_kses(trim($body), 'post');
		
		
		if( !isset($topicid) ){
			$this->wpforo->notice->add('Topic edit error', 'error');
			return FALSE;
		}
		if( !isset($title) || !$title = trim(strip_tags($title)) ){
			$this->wpforo->notice->add('Please insert required fields!', 'error');
			return FALSE;
		}
		
		$title = wpforo_text($title, 250, false);
		if(isset($body)) $body = preg_replace('#</pre>[\r\n\t\s\0]*<pre>#isu', "\r\n", $body);
		
		if( !$topic = $this->get_topic($topicid) ){
			$this->wpforo->notice->add('Topic not found.', 'error');
			return FALSE;
		}
		$diff = current_time( 'timestamp', 1 ) - strtotime($topic['created']);
		if( !($this->wpforo->perm->forum_can($topic['forumid'], 'et') || ($this->wpforo->current_userid == $topic['userid'] && $this->wpforo->perm->forum_can($topic['forumid'], 'eot') && $diff < $this->wpforo->post_options['eot_durr'])) ){
			$this->wpforo->notice->add('You have no permission to edit this topic', 'error');
			return FALSE;
		}
		
		$t_update = $this->wpforo->db->update(
			$this->wpforo->db->prefix."wpforo_topics",
			array( 
				'title' => stripslashes($title),
				'type'  => ( isset($type) ? $type : intval($topic['type']) ),
				'has_attach'=> (strpos($body, '[attach]') !== FALSE ? 1 : 0)
			), 
			array( 'topicid' => intval($topicid) ),
			array( '%s','%d','%d' ), 
			array( '%d' ) 
		);
		
		$p_update = $this->wpforo->db->update(
			$this->wpforo->db->prefix."wpforo_posts",
			array( 
				'title' => stripslashes($title),
				'body'    => stripslashes($body),
				'modified'	=> current_time( 'mysql', 1 ),
			), 
			array( 'postid' => intval($topic['first_postid']) ),
			array( '%s', '%s', '%s' ), 
			array( '%d' ) 
		);
		
		if($t_update !== FALSE && $p_update !== FALSE){
			$this->wpforo->notice->add('Topic successfully updated', 'success');
			return $topicid;
		}
		
		$this->wpforo->notice->add('Topic edit error', 'error');
		return FALSE;
	}
	
	private function users_stats_incr_minus($topicid){
		$topicid = intval($topicid);
		$sql = "SELECT `userid`, IF(`parentid` = 0, 'answers', 'comments') AS `type`, COUNT(*) AS `quantity`
					FROM `".$this->wpforo->db->prefix."wpforo_posts` 
						WHERE `is_first_post` != 1 AND `topicid` IN( $topicid ) 
						GROUP BY `userid`, `parentid` = 0 
						ORDER BY `userid`, `type`";
		if( $users_incr_stats = $this->wpforo->db->get_results($sql, ARRAY_A) ){
			$prev_userid = 0;
			$sets = array();
			foreach( $users_incr_stats as $users_incr_stat ){
				if( $prev_userid == 0 ) $prev_userid = $users_incr_stat['userid'];
				
				if( $prev_userid != $users_incr_stat['userid'] && $prev_userid != 0 ){
					if( !empty($sets) ){
						$sql = "UPDATE IGNORE `".$this->wpforo->db->prefix."wpforo_profiles` SET ".implode(', ', $sets)." WHERE `userid` = " . intval($prev_userid);
						$this->wpforo->db->query($sql);
					}
					$prev_userid = $users_incr_stat['userid'];
					$sets = array();
				}
				
				if( $users_incr_stat['type'] == 'answers' ) $sets[] = "`answers` = IF( (`answers` - " . esc_sql($users_incr_stat['quantity']) . ") < 0, 0, `answers` - " . esc_sql($users_incr_stat['quantity']) . " )";
				if( $users_incr_stat['type'] == 'comments' ) $sets[] = "`comments` = IF( (`comments` - " . esc_sql($users_incr_stat['quantity']) . ") < 0, 0, `comments` - " . esc_sql($users_incr_stat['quantity']) . " )";
				
			}
			
			if( !empty($sets) ){
				$sql = "UPDATE IGNORE `".$this->wpforo->db->prefix."wpforo_profiles` SET ".implode(', ', $sets)." WHERE `userid` = " . intval($users_incr_stat['userid']);
				$this->wpforo->db->query($sql);
			}
		}
	}
	
	#################################################################################
	/**
	 * Delete topic from DB
	 * 
	 * Returns true if successfully deleted or false.
	 *
	 * @since 1.0.0
	 *
	 * @return	bool	
	 */
	 
	function delete($topicid = 0){
		if(!$topicid && isset( $_REQUEST['id'] ) ) $topicid = intval($_REQUEST['id']);
		
		$topic = $this->get_topic($topicid);
		$diff = current_time( 'timestamp', 1 ) - strtotime($topic['created']);
		if( !($this->wpforo->perm->forum_can($post['forumid'], 'dt') || ($this->wpforo->current_userid == $topic['userid'] && $this->wpforo->perm->forum_can($post['forumid'], 'dot') && $diff < $this->wpforo->post_options['dot_durr'])) ){
			$this->wpforo->notice->add('You haven\'t permission to delete topic from this forum', 'error');
			return FALSE;
		}
		
		if( $topicid = intval($topicid) ){
			if( $forumid = $topic['forumid'] ){
				
				$questions = '';
				$forum = $this->wpforo->forum->get_forum($forumid);
				if( isset($forum['cat_layout']) && $forum['cat_layout'] == 3 ){
					$questions = ', `questions` = `questions` - 1 ';
					$this->users_stats_incr_minus($topicid);
				}
				
				$posts_count = $this->wpforo->db->delete($this->wpforo->db->prefix . 'wpforo_posts',  array( 'topicid' => $topicid));
				if($this->wpforo->db->delete($this->wpforo->db->prefix . 'wpforo_topics', array( 'topicid' => $topicid))){
					if($this->wpforo->db->query( "UPDATE IGNORE "  . $this->wpforo->db->prefix . "wpforo_forums SET `topics` = IF( (`topics` - 1) < 0, 0, `topics` - 1 ), `posts` = IF( (`posts` - ".intval($posts_count).") < 0, 0, `posts` - ".intval($posts_count)." ) WHERE `forumid` = " . intval($forumid))){
						$this->wpforo->db->query( "UPDATE IGNORE `"  . $this->wpforo->db->prefix . "wpforo_profiles` SET `posts` = IF( (`posts` - " . intval($posts_count) .") < 0, 0, `posts` - ".intval($posts_count)." ) $questions WHERE `userid` = " . intval($topic['userid']) );
						$this->wpforo->member->reset($topic['userid']);
						$this->wpforo->forum->rebuild_last_infos($forumid);
						$this->wpforo->notice->add('This topic successfully deleted', 'success');
						do_action( 'wpforo_after_delete_topic', $topic );
						return TRUE;
					}else{
						$this->wpforo->notice->add('Topic delete error', 'error');
						return FALSE;
					}
				}
			}
			
		}elseif( isset($_REQUEST['ids']) && !empty($_REQUEST['ids']) ){
			$topicids = explode(',',$_REQUEST['ids']);
			if(is_array($topicids)){
				foreach($topicids as $topicid){
					$topic = $this->get_topic($topicid);
					if( $forumid = $topic['forumid'] ){
						
						$questions = '';
						$forum = $this->wpforo->forum->get_forum($forumid);
						if( isset($forum['cat_layout']) && $forum['cat_layout'] == 3 ){
							$questions = ', `questions` = `questions` - 1 ';
							$this->users_stats_incr_minus($topicid);
						}
						// It's a same code (deleting one topic)
						$posts_count = $this->wpforo->db->delete($this->wpforo->db->prefix . 'wpforo_posts',  array( 'topicid' => $topicid));
						if($this->wpforo->db->delete($this->wpforo->db->prefix . 'wpforo_topics', array( 'topicid' => $topicid))){
							$this->wpforo->db->query( "UPDATE IGNORE "  . $this->wpforo->db->prefix . "wpforo_forums SET `topics` = IF( (`topics` - 1) < 0, 0, `topics` - 1 ), `posts` = IF( (`posts` - ".intval($posts_count).") < 0, 0, `posts` - ".intval($posts_count)." ) WHERE `forumid` = " . intval($forumid));
							$this->wpforo->db->query( "UPDATE IGNORE `"  . $this->wpforo->db->prefix . "wpforo_profiles` SET `posts` = IF( (`posts` - ".intval($posts_count).") < 0, 0, `posts` - ".intval($posts_count)." ) $questions WHERE `userid` = " . intval($topic['userid']) );
							$this->wpforo->forum->rebuild_last_infos($forumid);
						}
					}
				}
				$this->wpforo->notice->add('All Checked topics successfully deleted', 'success');
				do_action( 'wpforo_after_bulk_delete_topics', $topicids );
				return TRUE;
			}
			
			$this->wpforo->notice->add('Topics delete error', 'error');
			return FALSE;
			
		}
		
		$this->wpforo->notice->add('Topics delete error', 'error');
		return FALSE;
	}
	
	#################################################################################
	/**
	 * array get_topic(array or id(num)) 
	 * 
	 * Returns array from defined and default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param	mixed		defined arguments array for returning 
	 *
	 * @return	array	
	 */
	 
	function get_topic( $args = array(), $cache = false){
		if( !$args ) return;
		
		if(is_array($args)){
			$default = array(
			  'topicid' => NULL,
			  'slug' => '',
			);
		}elseif(is_numeric($args)){
			$default = array(
			  'topicid' => $args,
			  'slug' => '',
			);
		}elseif(is_string($args)){
			$default = array(
			  'topicid' => NULL,
			  'slug' => $args,
			);
		}
		
		$args = wpforo_parse_args( $args, $default );
		
		if(isset($args['topicid'])){
			if( isset(self::$cache['topic'][$args['topicid']]) ){
				return self::$cache['topic'][$args['topicid']];
			}
		}
		
		if(!empty($args)){
			extract($args, EXTR_OVERWRITE);
			
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_topics`";
			$wheres = array();
			if($topicid != NULL)  $wheres[] = "`topicid` = "   . intval($topicid);
			if($slug != '') $wheres[] = "`slug` = '" . esc_sql($slug) . "'";
			
			if(!empty($wheres)){
				$sql .= " WHERE " . implode($wheres, " AND ");
			}
			
			$topic = $this->wpforo->db->get_row($sql, ARRAY_A);
			
			if($cache){
				return self::$cache['topic'][$topicid] = $topic;
			}
			else{
				return $topic;
			}
		}
		
	}
	/**
	 * array get_topic(array or id(num)) 
	 * Returns merged arguments array from defined and default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param	array		defined arguments array for returning
	 *
	 * @return associative array where count is topic count and other numeric arrays with topic
	 */
	function get_topics($args = array(), &$items_count = 0){
		$default = array( 
		  'include' => array(), 		// array( 2, 10, 25 )
		  'exclude' => array(),  		// array( 2, 10, 25 )
		  'forumids' => array(),
		  'forumid' => NULL,
		  'userid'		=> NULL,		// user id in DB
		  'type'		=> 0, 			//sticki, etc . . .
		  'orderby'		=> 'type, topicid', 	// type, topicid, modified, created
		  'order'		=> 'DESC', 		// ASC DESC
		  'offset' 		=> NULL,			// this use when you give row_count
		  'row_count'	=> NULL 			// 4 or 1 ...
		);
		
		$args = wpforo_parse_args( $args, $default );
		if(is_array($args) && !empty($args)){
			extract($args, EXTR_OVERWRITE);
			
			if( $row_count === 0 ) return array();
			
			$include = wpforo_parse_args( $include );
			$exclude = wpforo_parse_args( $exclude );
			$forumids = wpforo_parse_args( $forumids );
			
			$wheres = array();
			
			if(!empty($include))        $wheres[] = "`topicid` IN(" . implode(', ', array_map('intval', $include)) . ")";
			if(!empty($exclude))        $wheres[] = "`topicid` NOT IN(" . implode(', ', array_map('intval', $exclude)) . ")";
			if(!empty($forumids))       $wheres[] = "`forumid` IN(" . implode(', ', array_map('intval', $forumids)) . ")";
			if(!is_null($forumid)) $wheres[] = "`forumid` = " . intval($forumid);
			if(!is_null($userid)) $wheres[] = "`userid` = " . intval($userid);
			if($type != 0) $wheres[] = " `type` = " . intval($type);
			
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_topics`";
			if(!empty($wheres)){
				$sql .= " WHERE " . implode($wheres, " AND ");
			}
			
			$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			if( $item_count_sql ) $items_count = $this->wpforo->db->get_var($item_count_sql);
			
			$sql .= " ORDER BY " . str_replace(',', ' ' . esc_sql($order) . ',', esc_sql($orderby)) . " " . esc_sql($order);
			
			if(!is_null($row_count)){
				if(!is_null($offset)){
					$sql .= esc_sql(" LIMIT $offset,$row_count");
				}else{
					$sql .= esc_sql(" LIMIT $row_count");
				}
			}
			
			return $this->wpforo->db->get_results($sql, ARRAY_A);
			
		}
	}
	/**
	 * 
	 * Search in your chosen column and return array with needles
	 *
	 * @since   1.0.0
	 *
	 * @param	string	needle 
	 *
	 * @param	column name in db	( slug, title, body )
	 * 
	 * @param $additional if it's true' return multi-dimensional arrays, if false it return simple array
	 * 
	 * @return	array	with  matches
	 */
	 
	function search( $needle = '', $fields = array( 'title', 'body' )){
		if($needle != ''){
			
			$needle = stripslashes($needle);
			
			if(!is_array($fields)){
				$fields = array($fields);  // if is it string it will be convert to array
			}
			
			$topicids = array();
			foreach($fields as $field){
				if($field == 'body'){
					$matches = $this->wpforo->db->get_col( "SELECT `topicid` FROM ".$this->wpforo->db->prefix."wpforo_posts WHERE `".esc_sql($field)."` LIKE '%". esc_sql(sanitize_text_field($needle)) ."%'" );	
				}else{
					$matches = $this->wpforo->db->get_col( "SELECT `topicid` FROM ".$this->wpforo->db->prefix."wpforo_topics WHERE `".esc_sql($field)."`LIKE '%". esc_sql(sanitize_text_field($needle)) ."%'" );		
				}
				$topicids = array_merge( $topicids, $matches );
			}
			return array_unique($topicids);
		}
		else{
			return $matches = array();
		}
	}
	
	function get_sum_answer($forumids){
		$sum = $this->wpforo->db->get_var("SELECT SUM(`answers`) FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `forumid` IN(". implode(', ', array_map('intval', $forumids)) .")");
		if($sum) return $sum;
		return 0;
	}
	
	function get_forumslug($forumid){
		$slug = $this->wpforo->db->get_var("SELECT `slug` FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `forumid` = " . intval($forumid));
		if($slug) return $slug;
		return 0;
	}
	
	function get_forumslug_byid($topicid){
		$slug = $this->wpforo->db->get_var("SELECT `slug` FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `forumid` =(SELECT forumid FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `topicid` =".intval($topicid).")");
		if($slug) return $slug;
		return 0;
	}
	
	function is_sticky( $topicid ){
		$type = $this->wpforo->db->get_var( "SELECT `type` FROM " . $this->wpforo->db->prefix."wpforo_topics WHERE `topicid` = " . intval($topicid) );
		if( $type == 1 ) return TRUE;
		return FALSE;
	}
	
	function is_closed( $topicid ){
		$type = $this->wpforo->db->get_var( "SELECT `closed` FROM " . $this->wpforo->db->prefix."wpforo_topics WHERE `topicid` = " . intval($topicid) );
		if( $type == 1 ) return TRUE;
		return FALSE;
	}
	
	function is_solved( $topicid ){
		$post = $this->wpforo->db->get_var( "SELECT `postid` FROM " . $this->wpforo->db->prefix."wpforo_posts WHERE `is_answer` = 1 AND `topicid` = " . intval($topicid) . " LIMIT 1" );
		if( $post ) return TRUE;
		return FALSE;
	}
	
	/**
	 * move topic to another forum
	 *
	 * @since 1.0.0
	 *
	 * @param	topicid and new forumid
	 *
	 * @return false and true 
	 */
	function move($topicid, $forumid){
		$topic = $this->get_topic( $topicid );
		if( $this->wpforo->db->query( "UPDATE `".$this->wpforo->db->prefix."wpforo_topics` SET `forumid` = ". intval($forumid) ." WHERE `topicid` = ". intval($topicid) ) ){
			$this->wpforo->db->query( "UPDATE `".$this->wpforo->db->prefix."wpforo_posts` SET `forumid` = ". intval($forumid) ." WHERE `topicid` = ". intval($topicid) );
			$post = $this->wpforo->post->get_post($topic['last_post']);
			
			$this->wpforo->db->query( "UPDATE `".$this->wpforo->db->prefix."wpforo_forums` SET `topics` = `topics` - 1, `posts` = `posts` - ".intval($topic['posts'])." WHERE `forumid` = ".intval($topic['forumid']) );
			$this->wpforo->db->query( "UPDATE `".$this->wpforo->db->prefix."wpforo_forums` SET `topics` = `topics` + 1, `posts` = `posts` + ".intval($topic['posts']).", `last_topicid` = ".intval($topicid).", `last_postid` = ".intval($topic['last_post']).", `last_userid` = ".intval($post['userid']).", `last_post_date` = '". esc_sql($post['created']) ."' WHERE `forumid` = ". intval($forumid) );
			
			$this->wpforo->forum->rebuild_last_infos($topic['forumid']);
			
			$this->wpforo->notice->add('Topic successfully moved', 'success');
			return $topicid;
		}
		
		$this->wpforo->notice->add('Topic Move Error', 'error');
		return FALSE;
	}
	
	function get_posts_count($topicid){
		if($topicid){
			return $this->wpforo->db->get_var("SELECT `posts` FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `topicid` = " . intval($topicid));
		}
		else{
			return $this->wpforo->db->get_var("SELECT `posts` FROM `".$this->wpforo->db->prefix."wpforo_topics`");
		}
	}
	
	function get_topic_url($topic, $forum = array()){
		
		if( !is_array($topic) ) $topic = $this->get_topic( $topic ); 
		
		if( is_array($topic) && !empty($topic) ){
			
			if( is_array($forum) && !empty($forum)){
				$forum_slug = $forum['slug'];
			}
			else{
				
				if( isset($topic['forumid']) && !$topic['forumid'] ){
					if( isset(self::$cache['forum_slug'][$topic['forumid']]) ){
						$forum_slug = self::$cache['forum_slug'][$topic['forumid']];
					}
					else{
						$forum_slug = $this->get_forumslug($topic['forumid']);
					}
					self::$cache['forum_slug'][$topic['forumid']] = $forum_slug;
				}
				else{
					$forum_slug = $this->get_forumslug_byid($topic['topicid']);
				}
				
			}
			
			return WPFORO_BASE_URL . $forum_slug . '/' . $topic['slug'];
			
		}else{
			return WPFORO_BASE_URL;
		}
	}
	
	
	function get_count(){
		return $this->wpforo->db->get_var( "SELECT COUNT(`topicid`) FROM `".$this->wpforo->db->prefix."wpforo_topics`" );
	}
	
	
}
?>