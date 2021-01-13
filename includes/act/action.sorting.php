<?php
	/**
	 * This file execute the actions for sorting some forms on the site
	 *
	 * @category   Actions of Sorting
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */
	if ( !$_POST ) die();

	include ( dirname(__DIR__).'/configuration.php' );
	include ( dirname(__DIR__).'/lib.php' );

	// Only Access To logged in users
	if ( !is_login_user() ) {
		header ( 'Location: login.php' );
	}

	$user = is_login_user();

	// We proccess the forms values
	if (empty($_POST['moved_item_id']) or empty($_POST['prev_item_id']) or empty($_POST['moved_item_timestamp']) or empty($_POST['prev_item_timestamp'])) {
		#DEBUG
			#echo 'ERROR - DID NOT RECEIVED ALL POST REQUEST';
		die();
	}
	else {
		#DEBUG
			#echo 'RECEIVED ALL POST REQUEST';

		// We put the previous item timestamp, reduce the time by 5 minutes, and then we added to the new one
		// That way we moved it upper level of that one
		$_POST['prev_item_timestamp'] = strtotime("-05 minutes", $_POST['prev_item_timestamp']);

		if ( !empty($_POST['database']) && $_POST['database'] == 'lb' ) {
			update_lb ( $_POST['moved_item_id'], 'lb_position', trim($_POST['prev_item_timestamp']) );
		}
		elseif ( !empty($_POST['database']) && $_POST['database'] == 'as' ) {
			update_as ( $_POST['moved_item_id'], 'as_position', trim($_POST['prev_item_timestamp']) );
		}
		elseif ( !empty($_POST['database']) && $_POST['database'] == 'au' ) {
			update_audit ( $_POST['moved_item_id'], 'audit_position', trim($_POST['prev_item_timestamp']) );
		}
		else {
			update_pm ( $_POST['moved_item_id'], 'pm_position', trim($_POST['prev_item_timestamp']) );
		}
	}

?>
