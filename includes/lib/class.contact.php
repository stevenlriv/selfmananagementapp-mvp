<?php
	/**
	 * This file compiles all contact actions
	 *
	 * @category   Contact
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */	
	
	function get_contact($var, $specific = '') {
		global $db;

		if ( $var == 'all' or $var == 'count' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_contact $specific" );
		}		
		elseif ( is_email($var) ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_contact WHERE email = ? ORDER BY date DESC" );
			$q->bind_param ( 's', $var );
		}
		elseif( is_numeric($var) ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_contact WHERE id_contact = ?" );
			$q->bind_param ( 'i', $var );
		}
		
		$q->execute();
		$result = $q->get_result();
		$array = array();

		if ( $var == 'count' ) {
			return $result->num_rows;
		}
			
		while ( $o = $result->fetch_array ( MYSQLI_ASSOC ) ) {			
			if ( $var == 'all' ) {
				array_push($array, $o);
			}
			else {
				return $o;
			}
		}
		$q->close();	

		if ( $var == 'all' ) {
			return $array;
		}	
		
		return false;
	}

	function delete_contact($i) {
		global $db;
	
		if ( !get_contact ($i) ) {
			return false;
		}
	
		$q = $db->prepare ( "DELETE FROM vos_gen_contact WHERE id_contact = ?" );
		$q->bind_param ( 'i', $i );
	
		if ( $q->execute() ) {
			return true;
		}

		return false;
	}

	function update_contact ($i, $table, $value) {
		global $db;
	
		if ( !get_contact ($i) ) {
			return false;
		}

		if ( $table != 'name' && $table != 'phone' && $table != 'email' && $table != 'comments' && $table != 'status' && $table != 'status_user' && $table != '	date_responded' ) {
			return false;
		}
		
		$q = $db->prepare ( "UPDATE vos_gen_contact SET $table = ? WHERE id_contact = ?" );
		$q->bind_param ( 'si', $value, $i );
	
		if ( $q->execute() ) {
			return true;
		}
		$q->close();
	
		return false;
	}
	
	function new_contact($name, $phone, $email, $comments) {
		global $db;

		if ( empty($name) or empty($phone) or !is_email($email) or empty($comments) ) {
			return false;
		}
	
		$vs = new visitorTracking();
		$v  = $vs->getThisVisit();
		$status = 0;
		$status_user = 0;
		$date = time();
		$date_responded = 0;

		
		$q = $db->prepare ( "INSERT INTO vos_gen_contact (name, phone, email, comments, status, status_user, date, date_responded, visitor_ip, visitor_city, visitor_state, visitor_country, visitor_browser, visitor_OS, visitor_referer, visitor_page) 
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		$q->bind_param ( 'ssssssssssssssss', $name, $phone, $email, $comments, $status, $status_user, $date, $date_responded, $v['visitor_ip'], $v['visitor_city'], $v['visitor_state'], $v['visitor_country'], $v['visitor_browser'], $v['visitor_OS'], $v['visitor_referer'], $v['visitor_page'] );
	
		if ( $q->execute() ) {	
			return true;
		}
		$q->close();
	
		return false;
	}
?>