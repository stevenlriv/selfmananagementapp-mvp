<?php
	/**
	 * This file compiles all settings actions
	 *
	 * @category   Settings
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */
	 
	function clean_setting($str) {
		$text = $str;

		$text = trim($text);
		$text = substr ($text, 2); // Eliminate "#_"		
		$text = str_replace('_', ' ', $text);
		$text = str_replace('INPUT-BOX', '', $text);
		$text = str_replace('INPUT-IMAGE', '', $text);
		$text = str_replace('INPUT-TEXT', '', $text);
		$text = str_replace('INPUT-HTML', '', $text);
		$text = str_replace('TRANSLATE', '', $text);
		$text = str_replace('SEO', '', $text);
		
		// Eliminate the group name
		if ( substr_count($text, "NAME:") > 0 ) {
			$text = explode('NAME:', trim($text));
			$text = $text[0];
		}
	
		$text = trim($text);
		
		// Eliminate the group
		if ( substr_count($text, "GROUP:") > 0 ) {
			$text = str_replace('GROUP:', '', $text);
			$text = substr ($text, 1);
		}
	
		return trim($text);
	}	

	function exists_group_setting() {
		if ( empty($_GET['p']) or !is_numeric ($_GET['p']) ) {
			return false;
		}
		
		foreach ( get_group_setting() as $id => $name ) {
			if ( $_GET['p'] == $id ) {
				return $name;
			}
		}
		
		return false;
	}
	
	function get_group_setting() {
		global $db;
		
		$q = $db->prepare ( "SELECT cat, group_name FROM vos_gen_settings WHERE cat != 0 ORDER BY cat ASC" );
		$q->execute();
		$result = $q->get_result();

		$array['categories'] = array();
		$array['group'] = array();
		while ( $o = $result->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ($array['categories'], $o['cat']); 
			array_push ($array['group'], $o['group_name']);
		}
		$q->close();
		
		$array['categories'] = array_unique($array['categories']);
		$array['group'] = array_unique($array['group']);
		
		return array_combine($array['categories'], $array['group']);
	}
	
	function get_setting_print($i) {
		echo get_setting($i);
	}
	
	function get_setting($var, $specific = '') {
		global $db;

		if ( $var == 'all' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_settings $specific" );
			if ( !empty($_GET['p']) ) {
				$q->bind_param ( 's', $_GET['p'] );	
			}
		}
		elseif ( $specific == true ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_settings WHERE id_setting = ?" );
			$q->bind_param ( 'i', $var );
		}
		else {
			$q = $db->prepare ( "SELECT value FROM vos_gen_settings WHERE id_setting = ?" );
			$q->bind_param ( 'i', $var );
		}

		$q->execute();
		$result = $q->get_result();
		$array = array();
		
		while ( $o = $result->fetch_array ( MYSQLI_ASSOC ) ) {
			if ( $var == 'all' ) {
				array_push($array, $o);
			}
			elseif ( $specific == true ) {
				return $o;	
			}
			else {
				return $o['value'];	
			}
		}
		$q->close();

		if ( $var == 'all' ) {
			return $array;
		}	
		
		return false;
	}

	function update_setting($i, $v) {
		global $db;
		
		$q = $db->prepare ( "UPDATE vos_gen_settings SET value = ? WHERE id_setting = ?" );
		$q->bind_param ( 'ss', $v, $i );		
	
		if ( $q->execute() ) {
			return true;
		}
		$q->close();
	
		return false;
	}
?>