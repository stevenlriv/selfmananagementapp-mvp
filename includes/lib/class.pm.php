<?php
	/**
	 * This file compiles all Personal Management actions
	 *
	 * @category   Personal Management
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function get_pm ($var, $specific = '') {
		global $db;

		if ( $var == 'all' or $var == 'count' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_pm $specific" );
		}
		else {
			$q = $db->prepare ( "SELECT * FROM vos_gen_pm WHERE id_pm = ?" );
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

	function delete_pm ( $i ) {
		global $db;

		if ( !get_pm ( $i ) ) {
			return false;
		}

		//Verify that the pm belongs to the logged in user
		$user = is_login_user();

		if ( get_pm($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		$q = $db->prepare ( "DELETE FROM vos_gen_pm WHERE id_pm = ?" );
		$q->bind_param ( 'i', $i );

		if ( $q->execute() ) {
			return true;
		}

		return false;
	}

	function update_pm ($i, $table, $value) {
		global $db;

		if ( !get_pm ($i) ) {
			return false;
		}

		//Verify that the pm belongs to the logged in user
		$user = is_login_user();

		if ( get_pm($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		if ( $table != 'pm_type' && $table != 'pm_position' && $table != 'pm_title' && $table != 'pm_body_1' && $table != 'pm_body_2' && $table != 'pm_body_3'
			&& $table != 'pm_body_4' && $table != 'pm_body_5' && $table != 'pm_body_6' && $table != 'pm_query_date' && $table != 'pm_status' ) {
			return false;
		}

		$q = $db->prepare ( "UPDATE vos_gen_pm SET $table = ? WHERE id_pm = ?" );
		$q->bind_param ( 'si', $value, $i );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}

	function new_pm ( $pm_type, $pm_title = '', $pm_query_date = '', $pm_body_1 = '', $pm_body_2 = '', $pm_body_3 = '', $pm_body_4 = '', $pm_body_5 = '', $pm_body_6 = '' ) {
		global $db;

		$id_user = is_login_user()['id_user'];
		$pm_position = time();

		$user = is_login_user();
		$actual_date = new DateTime($user['user_time_zone']);

		if ( empty($pm_type) ) {
			return false;
		}

		if ( empty($pm_query_date) ) {
			$pm_query_date = $actual_date->format('d').' '.$actual_date->format('M').' '.$actual_date->format('Y');
		}

		$q = $db->prepare ( "INSERT INTO vos_gen_pm (id_user, pm_type, pm_position, pm_title, pm_body_1, pm_body_2, pm_body_3, pm_body_4, pm_body_5, pm_body_6, pm_query_date)
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		$q->bind_param ( 'sssssssssss', $id_user, $pm_type, $pm_position, $pm_title, $pm_body_1, $pm_body_2, $pm_body_3, $pm_body_4, $pm_body_5, $pm_body_6, $pm_query_date );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}
?>
