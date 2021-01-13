<?php
	/**
	 * This file execute the actions for hidding the Small Descriptions
	 *
	 * @category   Actions of Small Descriptions
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

	// We proccess the verify the values obtained
	if ( !empty($_POST['small_desc']) ) {
		//We update the cookie for 1 year for that notification
		if (update_user($user['id_user'], 'cookies_track', $user['cookies_track'].$_POST['small_desc'].";") ) {
		//if (new_cookie($_POST['small_desc'], 'DISABLED', time()+60*60*24*365)) {
			#DEBUG
			#echo 'Cookie Created';
		}
		else {
			#DEBUG
			#echo 'Error Creating the Cookie';
		}
	}
	else {
		#DEBUG
		#echo 'Cookie Exists';
	}

?>
