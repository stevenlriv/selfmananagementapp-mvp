<?php
	/**
	 * This file compiles all Liabilities actions
	 *
	 * @category   Personal Management
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function get_lb ($var, $specific = '', $new = '') {
		global $db;

		if ( $var == 'all' or $var == 'count' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_lb $specific" );
		}
		elseif ( $var == 'sum' ) {
			$q = $db->prepare ( "SELECT SUM(lb_body_1) FROM vos_gen_lb $specific" );
		}
		elseif ( $var == 'sum-new' ) {
			$q = $db->prepare ( "SELECT SUM($new) FROM vos_gen_lb $specific" );
		}
		else {
			$q = $db->prepare ( "SELECT * FROM vos_gen_lb WHERE id_lb = ?" );
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

	function delete_lb ( $i ) {
		global $db;

		if ( !get_lb ( $i ) ) {
			return false;
		}

		//Verify that the lb belongs to the logged in user
		$user = is_login_user();

		if ( get_lb($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		$q = $db->prepare ( "DELETE FROM vos_gen_lb WHERE id_lb = ?" );
		$q->bind_param ( 'i', $i );

		if ( $q->execute() ) {
			return true;
		}

		return false;
	}

	function update_lb ($i, $table, $value) {
		global $db;

		if ( !get_lb ($i) ) {
			return false;
		}

		//Verify that the lb belongs to the logged in user
		$user = is_login_user();

		if ( get_lb($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		if ( $table != 'lb_type' && $table != 'lb_position' && $table != 'lb_title' && $table != 'lb_body_1' && $table != 'lb_body_2' && $table != 'lb_body_3'
			&& $table != 'lb_body_4' && $table != 'lb_body_5' && $table != 'lb_body_6' && $table != 'lb_query_date' && $table != 'lb_status' ) {
			return false;
		}

		$q = $db->prepare ( "UPDATE vos_gen_lb SET $table = ? WHERE id_lb = ?" );
		$q->bind_param ( 'si', $value, $i );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}

	function new_lb ( $lb_type, $lb_title = '', $lb_query_date = '', $lb_body_1 = '', $lb_body_2 = '', $lb_body_3 = '', $lb_body_4 = '', $lb_body_5 = '', $lb_body_6 = '' ) {
		global $db;

		$id_user = is_login_user()['id_user'];
		$lb_position = time();

		$user = is_login_user();
		$actual_date = new DateTime($user['user_time_zone']);

		if ( empty($lb_type) ) {
			return false;
		}

		if ( empty($lb_query_date) ) {
			$lb_query_date = $actual_date->format('d').' '.$actual_date->format('M').' '.$actual_date->format('Y');
		}

		$q = $db->prepare ( "INSERT INTO vos_gen_lb (id_user, lb_type, lb_position, lb_title, lb_body_1, lb_body_2, lb_body_3, lb_body_4, lb_body_5, lb_body_6, lb_query_date)
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		$q->bind_param ( 'sssssssssss', $id_user, $lb_type, $lb_position, $lb_title, $lb_body_1, $lb_body_2, $lb_body_3, $lb_body_4, $lb_body_5, $lb_body_6, $lb_query_date );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}
?>
