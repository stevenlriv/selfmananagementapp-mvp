<?php
	/**
	 * This file compiles all assets actions
	 *
	 * @category   Personal Management
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function get_as ($var, $specific = '') {
		global $db;

		if ( $var == 'all' or $var == 'count' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_as $specific" );
		}
		elseif ( $var == 'sum' ) {
			$q = $db->prepare ( "SELECT SUM(as_body_1) FROM vos_gen_as $specific" );
		}
		else {
			$q = $db->prepare ( "SELECT * FROM vos_gen_as WHERE id_as = ?" );
			$q->bind_param ( 'i', $var );
		}

		$q->execute();
		$result = $q->get_result();
		$array = array();

		if ( $var == 'count' ) {
			return $result->num_rows;
		}

		while ( $o = $result->fetch_array(MYSQLI_ASSOC) ) {
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

	function delete_as ( $i ) {
		global $db;

		if ( !get_as ( $i ) ) {
			return false;
		}

		//Verify that the as belongs to the logged in user
		$user = is_login_user();

		if ( get_as($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		$q = $db->prepare ( "DELETE FROM vos_gen_as WHERE id_as = ?" );
		$q->bind_param ( 'i', $i );

		if ( $q->execute() ) {
			return true;
		}

		return false;
	}

	function update_as ($i, $table, $value) {
		global $db;

		if ( !get_as ($i) ) {
			return false;
		}

		//Verify that the as belongs to the logged in user
		$user = is_login_user();

		if ( get_as($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		if ( $table != 'as_type' && $table != 'as_position' && $table != 'as_title' && $table != 'as_body_1' && $table != 'as_body_2' && $table != 'as_body_3'
			&& $table != 'as_body_4' && $table != 'as_body_5' && $table != 'as_body_6' && $table != 'as_query_date' && $table != 'as_status' ) {
			return false;
		}

		$q = $db->prepare ( "UPDATE vos_gen_as SET $table = ? WHERE id_as = ?" );
		$q->bind_param ( 'si', $value, $i );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}

	function new_as ( $as_type, $as_title = '', $as_query_date = '', $as_body_1 = '', $as_body_2 = '', $as_body_3 = '', $as_body_4 = '', $as_body_5 = '', $as_body_6 = '' ) {
		global $db;

		$id_user = is_login_user()['id_user'];
		$as_position = time();

		$user = is_login_user();
		$actual_date = new DateTime($user['user_time_zone']);

		if ( empty($as_type) ) {
			return false;
		}

		if ( empty($as_query_date) ) {
			$as_query_date = $actual_date->format('d').' '.$actual_date->format('M').' '.$actual_date->format('Y');
		}

		$q = $db->prepare ( "INSERT INTO vos_gen_as (id_user, as_type, as_position, as_title, as_body_1, as_body_2, as_body_3, as_body_4, as_body_5, as_body_6, as_query_date)
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		$q->bind_param ( 'sssssssssss', $id_user, $as_type, $as_position, $as_title, $as_body_1, $as_body_2, $as_body_3, $as_body_4, $as_body_5, $as_body_6, $as_query_date );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}
?>
