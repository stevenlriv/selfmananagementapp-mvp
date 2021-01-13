<?php
	/**
	 * This file compiles all task actions
	 *
	 * @category   Task
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */		

	function get_task ($var, $specific = '') {
		global $db;

		if ( $var == 'all' or $var == 'count' ) {
			$q = $db->prepare ( "SELECT * FROM vos_gen_task $specific" );
		}
		else {
			$q = $db->prepare ( "SELECT * FROM vos_gen_task WHERE id_task = ?" );
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

	function delete_task ( $i ) {
		global $db;

		if ( !get_task ( $i ) ) {
			return false;
		}

		//Verify that the task belongs to the logged in user
		$user = is_login_user();

		if ( get_task($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		$q = $db->prepare ( "DELETE FROM vos_gen_task WHERE id_task = ?" );
		$q->bind_param ( 'i', $i );

		if ( $q->execute() ) {
			return true;
		}

		return false;
	}

	function update_task ($i, $table, $value) {
		global $db;

		if ( !get_task ($i) ) {
			return false;
		}

		//Verify that the task belongs to the logged in user
		$user = is_login_user();

		if ( get_task($i)['id_user'] != $user['id_user'] ) {
			return false;
		}

		if ( $table != 'task_push' && $table != 'status' && $table != 'task_title' && $table != 'task' && $table != 'task_desc' && $table != 'task_time' && $table != 'task_weekdays' ) {
			return false;
		}

		$q = $db->prepare ( "UPDATE vos_gen_task SET $table = ? WHERE id_task = ?" );
		$q->bind_param ( 'si', $value, $i );

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}

	function new_task ( $task_title, $task, $task_desc, $task_time, $task_weekdays, $task_push = 'true' ) {
		global $db;

		$id_user = is_login_user()['id_user'];

		if ( empty($task_title) or empty($task) or empty($task_weekdays) ) {
			return false;
		}

		if ( strlen($task_title)>120 or strlen($task)>120 ) {
			return false;
		}

		$q = $db->prepare ( "INSERT INTO vos_gen_task (id_user, task_push, task_title, task, task_desc, task_time, task_weekdays)
							VALUES (?, ?, ?, ?, ?, ?, ?)" );
		$q->bind_param ( 'sssssss', $id_user, $task_push, $task_title, $task, $task_desc, $task_time, $task_weekdays);

		if ( $q->execute() ) {
			return true;
		}
		$q->close();

		return false;
	}
?>
