<?php
	/**
	 * This file compiles all audits actions
	 *
	 * @category   Personal Management
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function get_audit ($var, $specific = '') {
		global $db;

		if ( $var == 'all' or $var == 'count' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_audit $specific" );
		}
		elseif ( $var == 'sum' ) {
			$q = $db->prepare ( "SELECT SUM(audit_body_1) FROM vos_gen_audit $specific" );
		}
		else {
			$q = $db->prepare ( "SELECT * FROM vos_gen_audit WHERE id_audit = ?" );
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

	function delete_audit ( $i ) {
		global $db;

		if ( !get_audit ( $i ) ) {
			return false;
		}

		//Verify that the audit belongs to the logged in user
		$user = is_login_user();

		if ( get_audit($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		$q = $db->prepare ( "DELETE FROM vos_gen_audit WHERE id_audit = ?" );
		$q->bind_param ( 'i', $i );

		if ( $q->execute() ) {
			return true;
		}

		return false;
	}

	function update_audit ($i, $table, $value) {
		global $db;

		if ( !get_audit ($i) ) {
			return false;
		}

		//Verify that the audit belongs to the logged in user
		$user = is_login_user();

		if ( get_audit($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		if ( $table != 'audit_type' && $table != 'audit_position' && $table != 'audit_title' && $table != 'audit_body_1' && $table != 'audit_body_2' && $table != 'audit_body_3'
			&& $table != 'audit_body_4' && $table != 'audit_body_5' && $table != 'audit_body_6' && $table != 'audit_query_date' && $table != 'audit_status' ) {
			return false;
		}

		$q = $db->prepare ( "UPDATE vos_gen_audit SET $table = ? WHERE id_audit = ?" );
		$q->bind_param ( 'si', $value, $i );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}

	function new_audit ( $audit_type, $audit_title = '', $audit_query_date = '', $audit_body_1 = '', $audit_body_2 = '', $audit_body_3 = '', $audit_body_4 = '', $audit_body_5 = '', $audit_body_6 = '' ) {
		global $db;

		$id_user = is_login_user()['id_user'];
		$audit_position = time();

		$user = is_login_user();
		$actual_date = new DateTime($user['user_time_zone']);

		if ( empty($audit_type) ) {
			return false;
		}

		if ( empty($audit_query_date) ) {
			$audit_query_date = $actual_date->format('d').' '.$actual_date->format('M').' '.$actual_date->format('Y');
		}

		$q = $db->prepare ( "INSERT INTO vos_gen_audit (id_user, audit_type, audit_position, audit_title, audit_body_1, audit_body_2, audit_body_3, audit_body_4, audit_body_5, audit_body_6, audit_query_date)
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
		$q->bind_param ( 'sssssssssss', $id_user, $audit_type, $audit_position, $audit_title, $audit_body_1, $audit_body_2, $audit_body_3, $audit_body_4, $audit_body_5, $audit_body_6, $audit_query_date );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}
?>
