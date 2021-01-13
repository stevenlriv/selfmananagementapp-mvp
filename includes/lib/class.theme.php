<?php
	/**
	 * This file compiles all themes actions
	 *
	 * The $array variable is an array composed of five parts: 
	 * 		title, extra (for a second part of the title), metaDesc, socialTitle, socialImg
	 *
	 * @category   Themes
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function get_canonical() {
		global $translate;		
		
		echo "\n".'		<link rel="canonical" href="'.get_actual_url( 'NO_LANG' ).'" />	'."\n";
		
		foreach ( $translate->get_langs() as $name => $value ) {		
			echo '		<link rel="alternate" hreflang="'.$value['code'].'" href="'.get_actual_url( 'NO_LANG' ).'?lang='.$value['code'].'" />'."\n";
		}
	}
	
	function get_seo($array) {
		global $translate;
		
		echo "\n".'		<meta name="og:site_name" content="'.get_setting(1).'">'."\n";
		echo '		<meta name="og:type" content="website">'."\n";
		echo '		<meta property="og:image:width" content="1200">'."\n";
		echo '		<meta property="og:image:height" content="628">'."\n";
		echo '		<meta name="twitter:card" content="summary">'."\n";

		if ( !empty(get_setting(52)) ) { 
			echo '		<meta name="twitter:site" content="'.get_setting(52).'">'."\n";
			echo '		<meta name="twitter:creator" content="'.get_setting(52).'">'."\n";
		}
			
		if ( !empty($array['socialTitle']) ) {
			echo '		<meta property="og:title" content="'.$translate->__($array['socialTitle']).'">'."\n";
			echo '		<meta name="twitter:title" content="'.$translate->__($array['socialTitle']).'">'."\n";
		}
		else {
			echo '		<meta property="og:title" content="'.$translate->__(get_setting(51)).'">'."\n";
			echo '		<meta name="twitter:title" content="'.$translate->__(get_setting(51)).'">'."\n";
		}		
			
		if ( !empty($array['metaDesc']) ) {
			echo '		<meta property="og:description" content="'.$translate->__($array['metaDesc']).'">'."\n";
			echo '		<meta name="twitter:description" content="'.$translate->__($array['metaDesc']).'">'."\n";
		}
		else {
			echo '		<meta property="og:description" content="'.$translate->__(get_setting(28)).'">'."\n";
			echo '		<meta name="twitter:description" content="'.$translate->__(get_setting(28)).'">'."\n";
		}		

		if ( !empty($array['socialImg']) ) {
			echo '		<meta property="og:image" content="'.$array['socialImg'].'">'."\n";
			echo '		<meta name="twitter:image:src" content="'.$array['socialImg'].'">'."\n";
		}
		else {
			echo '		<meta property="og:image" content="'.get_domain().'/assets/theme/media/components/general.jpg">'."\n";
			echo '		<meta name="twitter:image:src" content="'.get_domain().'/assets/theme/media/components/general.jpg">'."\n";
		}	
		
		echo '		<meta property="og:url" content="'.get_actual_url().'">'."\n\n";
		
		if( !empty($array['metaDesc']) ) {
			echo '		<meta name="description" content="'.$translate->__($array['metaDesc']).'">'."\n";
		}
	}

	function get_404() {			
		echo "\n".'		<meta name="robots" content="noindex">'."\n";			
		echo '		<meta name="googlebot" content="noindex">'."\n";
	}
	
	function get_title($array) {
		
		if ( !empty($array['extra']) ) { 
			echo $array['extra'].' | '; 
		}	 
		if ( !empty($array['title']) ) { 
			echo $array['title']; 
		} 
		if ( $array == '404' ) {
			echo 'Oops! Page not found'; 
		}
		echo ' | ';
		get_setting_print(1);
	}
	
	function get_li_services($class = '') {
		global $db, $translate;
						
		$q = $db->prepare ( "SELECT * FROM vos_gen_services" );
		$q->execute();
		$result = $q->get_result();

		while ( $o = $result->fetch_array(MYSQLI_ASSOC) ) {			
			echo '		<li class="'.$class.'"><a href="/services/'.$o['local_uri'].'">'.$translate->__($o['name']).'</a></li>'."\n";
		}
		$q->close();
	}
	
	function get_li_langs($class = '') {
		global $translate;
		
		foreach ( $translate->get_langs() as $name => $value ) {
			if ( $value['code'] != $translate->lng ) {
				echo '		<li class="'.$class.'"><a href="?lang='.$value['code'].'">'.$translate->__($value['name']).'</a></li>'."\n";
			}
		}
	}
?>