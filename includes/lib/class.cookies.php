<?php
	/**
	 * This file compiles all cookies actions
	 *
	 * @category   Cookies
	 * @author     Steven Rivera <stevenlrr@gmail.com>
	 * @copyright  2018 VOS Group
	 * @license    http://creativecommons.org/licenses/by-nc-nd/4.0/
	 * @version    1.0.0
	 * @since      File available since 1.0.0
	 */

	function get_cookie($name) {
		//$crypt = new CryptoLib();

		if ( !empty($_COOKIE[$name]) ) {
			//return $crypt->decryptData ($_COOKIE[$name], COOKIES_KEY);
            return $_COOKIE[$name];
		}
		return false;
	}

	function delete_cookie($name) {
		if ( get_cookie($name) ) {
			if( new_cookie($name, 'delete') ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Create a Cookie
	 *
	 * Notes:
	 *   $expire - Expiration date (24 hours from now by default; time()+60*60*24*7 for 7 days)
	 *   $path - Path to be used on ('/' is global)
	 *   $domain - Domain of the cookie to be used on (blank for actual)
	 *   $secure - If true only create cookies on HTTPS request
	 *   $http - If true disable cookie use via JavaScript
	 *
	 * @return: true or false
	 */
	function new_cookie($name, $value, $expire = '') {
		//$crypt = new CryptoLib();

		if ( empty($expire) ) {
			$expire = time()+60*60*24;
		}

		$path = '/';
		$domain = 'selfmanagementapp.com';
		$secure = true; //SSL Required for true
		$http = true;

        
		//$cookie = $crypt->encryptData ( $value, COOKIES_KEY );
        $cookie = $value;
        
		if ( setcookie ($name, $cookie, $expire, $path, $domain, $secure, $http) ) {
			return true;
		}
        
		return false;
	}
?>
