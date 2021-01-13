<?php	
	/**
	 * This file compiles all images actions
	 *
	 * @category   Images
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */
	
	function delete_image($name) {
		if ( is_file(UPLOAD_LOCATION.$name) && unlink(UPLOAD_LOCATION.$name) ) {
			return true;
		}
		
		return false;
	}

	function new_image($file, $random) {	
		$handle = new upload($file);
		$handle->allowed = array('image/*');
		$handle->file_max_size = '9000000'; // 9 MB
		
		$handle->file_name_body_pre = $random.'_';
		
				
		if ($handle->uploaded) {
	
			$handle->process(UPLOAD_LOCATION);
						
			if ($handle->processed) {
				$handle->clean();
				return true;
			} 
		}
		return false;
	}
?>