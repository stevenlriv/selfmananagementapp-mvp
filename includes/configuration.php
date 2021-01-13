<?php
	/**
	 * Pepper
	 *
	 * Is required for cryptographic salt purposes
	 *
	 * Needs to be changed every new project
	 *
	 * @location class.CryptoLib.php
	 */
	define ( 'PEPPER', '' );

	/**
	 * Cookies Key
	 *
	 * Is required for cryptographic encryption/decryption purposes
	 *
	 * Needs to be changed every new project
	 *
	 * @location class.cookies.php
	 */
	define ( 'COOKIES_KEY', '' );

	/**
	 * MYSQL Host
	 *
	 * Is required for database access
	 *
	 * Needs to be changed every new project
	 */
	define ( 'MYSQL_HOST', '' );

	/**
	 * MYSQL USER
	 *
	 * Is required for database access
	 *
	 * Needs to be changed every new project
	 */
	define ( 'MYSQL_USER', '' );

	/**
	 * MYSQL PASSWORD
	 *
	 * Is required for database access
	 *
	 * Needs to be changed every new project
	 */
	define ( 'MYSQL_PASSWORD', '' );

	/**
	 * MYSQL DATABASE
	 *
	 * Is required for database access
	 *
	 * Needs to be changed every new project
	 */
	define ( 'MYSQL_DATABASE', '' );

	/**
	 *
	 * DO NOT EDIT BELOW THIS LINE
	 *
	 * ///////////////////////////////////////////////////////////////////////////////////////////////////////////
	 *
	 * Note on globals vars
	 *
	 * GENERAL: $db
	 */
	define ( 'UPLOAD_LOCATION', dirname(__DIR__).'/assets/uploads/' );

	$db = new mysqli ( MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE );
	$db->set_charset ( 'utf8' );

	if ( $db->connect_errno ) die ('Error while connecting to database');

	if ( !isset($_SESSION) ) { session_start(); }
?>
