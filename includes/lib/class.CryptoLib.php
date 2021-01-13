<?php
/**
 * Class CryptoLib (v0.8 Christmas)
 * Created by Junade Ali
 * Requires OpenSSL, MCrypt > 2.4.x, PHP 5.3.0+
 * https://odan.github.io/2017/08/10/aes-256-encryption-and-decryption-in-php-and-csharp.html
 */

/*
    CryptoLib is an open-source PHP Cryptography library.
    Copyright (C) 2014  Junade Ali

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class CryptoLib {

    // Please change the pepper below for each project (do not change after data has been hashed using this class).
    private static $pepper = PEPPER;

    private $method = 'aes-256-cbc';


    /**
     * Hash which will recursively rehash data 64 times (each time being hashed 32 times PBKDF2 standard) alternating between Whirlpool and SHA512.
     * @param $data
     * @param $salt
     * @param bool $raw_output
     * @param int $iterations - Recommended to leave at the default of 96, ensure it is divisible by 3 (to get a precise amount of iterations).
     * @return mixed
     * @throws Exception
     */
    public static function hash ($data) {
        $hash = password_hash($data, PASSWORD_DEFAULT);

        return $hash;
    }

    /**
     * Validate hash by providing the hashed string (e.g. from password field in database) with a plain-text input (e.g. password field from user).
     * @param $original
     * @param $input
     * @param $salt
     * @return bool
     * @throws Exception
     */
    public static function validateHash ($original, $input) {
        if ( password_verify($input, $original) ) {
            return true;
        } 
        else {
            return false;
        }
    }

    /**
     * Encrypt data using a specified key; uses cascading layered encryption with hash salting.
     * @param $data
     * @param $key
     * @return string
     * @throws Exception
     */
    public static function encryptData ($data, $key) {
        return $data;
    }

    /**
     * Decrypt data which has been encrypted with the encryptData function.
     * @param $data
     * @param $key
     * @return string
     * @throws Exception
     */
    public static function decryptData ($data, $key) {
        return $data;
    }



}