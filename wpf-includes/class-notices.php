<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

class wpForoNotices{
	
	function __construct(){
		$this->init();
	}
 	
 	/**
	 * @return void
	 */
 	private function init(){
		if(!$this->is_session_started()) session_start();
	}
 	
	/**
	* @return bool
	*/
	private function is_session_started(){
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
	            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
	        } else {
	            return session_id() === '' ? FALSE : TRUE;
	        }
	    }
	    return FALSE;
	}
 	
 	/**
	 * 
	 * @param mixed $args
	 * @param string $type (e.g. success|error)
	 * 
	 * @return bool
	 */
	public function add( $args, $type = '' ){
		if(!$args) return FALSE;
		if(!is_array($args)) $args = array($args);
		
		if( $this->is_session_started() ){
			$_SESSION['wpforo_notice_type'] = $type;
			if( !empty($_SESSION['wpforo_notices']) ){
				foreach($args as $arg) $_SESSION['wpforo_notices'][] = $arg;
			}else{
				$_SESSION['wpforo_notices'] = $args;
			}
			$_SESSION['wpforo_notices'] = array_unique($_SESSION['wpforo_notices']);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	* 
	* @return bool
	* 
	*/
	public function clear(){
		if( $this->is_session_started() ){
			unset($_SESSION['wpforo_notice_type'], $_SESSION['wpforo_notices']);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	* get notices $notices['type'], $notices['text']
	* 
	* @return array
	*/
	public function get_notices(){
		if(!isset($_SESSION['wpforo_notices'])) return array( 'type' => '', 'text' => '' );
		if($_SESSION['wpforo_notice_type'] == 'success'){
			$class = 'success';
		}elseif($_SESSION['wpforo_notice_type'] == 'error'){
			$class = 'error';
		}else{
			$class = '';
		}
		
		$notices = array();
		$notices['type'] = $class;
		$notices['text'] = '';
		foreach($_SESSION['wpforo_notices'] as $notice) if( !is_array($notice) ) $notices['text'] .= wpforo_phrase($notice, FALSE) . '<br/>';
		
		$this->clear();
		return $notices;
	}
	
	/**
	* 
	* @param bool $frontend (default TRUE)
	* 
	* @return void
	*/
	public function show( $frontend = TRUE ){
		if(!isset($_SESSION['wpforo_notices'])) return;
		if($_SESSION['wpforo_notice_type'] == 'success'){
			$class = 'success';
		}elseif($_SESSION['wpforo_notice_type'] == 'error'){
			$class = 'error';
		}else{
			$class = '';
		}
		$inner = '';
		foreach($_SESSION['wpforo_notices'] as $notice) if( !is_array($notice) ) $inner .= wpforo_phrase($notice, FALSE) . '<br/>';
		?>
		<?php if($frontend) : ?>
			<script type="text/javascript">
	    		jQuery(document).ready(function($){
	    			<?php if($class) : ?>
	    				$("#wpf-msg-box p.wpf-msg-box-triangle-right").removeClass("success").removeClass("error").addClass("<?php echo  sanitize_html_class($class) ?>");
					<?php endif ?>
					$("#wpf-msg-box p.wpf-msg-box-triangle-right").html("<span><?php echo addslashes(wpforo_kses($inner)) ?></span>");
					$("#wpf-msg-box").show(150).delay(1000);
					<?php if($class) : ?>
						setTimeout(function(){ $("#wpf-msg-box").hide(); }, <?php echo ($class == 'error' ? 6000 : 3000 ) ?>);
					<?php endif ?>
				});
			</script>
		<?php else : ?>
			<div class="notice is-dismissible<?php if($class) echo ' notice-' . sanitize_html_class($class) ?>">
				<p><?php echo wpforo_kses($inner) ?></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'wpforo') ?></span></button>
			</div>
		<?php endif ?>
		<?php
		$this->clear();
	}
	
}

?>