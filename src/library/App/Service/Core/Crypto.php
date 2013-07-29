<?php
/**
 * Thesis Planet - Digital Education Platform
 *
 * LICENSE
 *
 * This source file is subject to the licensing terms found at http://www.thesisplanet.com/platform/tos
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to sales@thesisplanet.com so we can send you a copy immediately.
 *
 * @category  ThesisPlanet
 * @copyright  Copyright (c) 2009-2012 Thesis Planet, LLC. All Rights Reserved. (http://www.thesisplanet.com)
 * @license   http://www.thesisplanet.com/platform/tos   ** DUAL LICENSED **  #1 - Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. #2 - Thesis Planet Commercial Use EULA.
 */
namespace App\Service\Core;
class Crypto {
	private $key;

	/** Encryption Procedure
	 *
	 *	@param   mixed    msg      message/data
	 *	@param   string   k        encryption key
	 *	@param   boolean  base64   base64 encode result
	 *
	 *	@return  string   iv+ciphertext+mac or
	 *           boolean  false on error
	 */
	private function getKey() {
		$file = \Zend_registry::getInstance()->get('encryptionKey');
		$fh = fopen($file, 'r');
		$key = fread($fh, filesize($file));
		fclose($fh);
		$this->key = base64_decode($key);
		if ($this->key <> NULL) {
			return TRUE;
		}else{
			return FALSE;
		}
	}
	public function encrypt($msg) {
		if ($this->key <> NULL) {
			$k = $this->key;
		}else{
			$this->getKey();
			if ($this->key == NULL) {
				return FALSE;
			}
			$k = $this->key;
		}
		# open cipher module (do not change cipher/mode)
		if ( ! $td = mcrypt_module_open('rijndael-256', '', 'ctr', '') )
		return false;

		$msg = serialize($msg);							# serialize
		$iv  = mcrypt_create_iv(32, MCRYPT_RAND);		# create iv

		if ( mcrypt_generic_init($td, $k, $iv) !== 0 )	# initialize buffers
		return false;

		$msg  = mcrypt_generic($td, $msg);				# encrypt
		$msg  = $iv . $msg;								# prepend iv
		$mac  = $this->pbkdf2($msg, $k, 1000, 32);		# create mac
		$msg .= $mac;									# append mac

		mcrypt_generic_deinit($td);						# clear buffers
		mcrypt_module_close($td);						# close cipher module



		return base64_encode($msg);									# return iv+ciphertext+mac
	}

	/** Decryption Procedure
	 *
	 *	@param   string   msg      output from encrypt()
	 *	@param   string   k        encryption key
	 *	@param   boolean  base64   base64 decode msg
	 *
	 *	@return  string   original message/data or
	 *           boolean  false on error
	 */
	public function decrypt($msg) {
	if ($this->key <> NULL) {
			$k = $this->key;
		}else{
			$this->getKey();
			if ($this->key == NULL) {
				return FALSE;
			}
			$k = $this->key;
		}

		$msg = base64_decode($msg);
		# open cipher module (do not change cipher/mode)
		if ( ! $td = mcrypt_module_open('rijndael-256', '', 'ctr', '') )
		return false;

		$iv  = substr($msg, 0, 32);							# extract iv
		$mo  = strlen($msg) - 32;							# mac offset
		$em  = substr($msg, $mo);							# extract mac
		$msg = substr($msg, 32, strlen($msg)-64);			# extract ciphertext
		$mac = $this->pbkdf2($iv . $msg, $k, 1000, 32);		# create mac

		if ( $em !== $mac )									# authenticate mac
		return false;

		if ( mcrypt_generic_init($td, $k, $iv) !== 0 )		# initialize buffers
		return false;

		$msg = mdecrypt_generic($td, $msg);					# decrypt
		$msg = unserialize($msg);							# unserialize

		mcrypt_generic_deinit($td);							# clear buffers
		mcrypt_module_close($td);							# close cipher module

		return $msg;										# return original msg
	}

	/** PBKDF2 Implementation (SEE RFC 2898);
	 *
	 *	@param   string  p   password
	 *	@param   string  s   salt
	 *	@param   int     c   iteration count (use 1000 or higher)
	 *	@param   int     kl  derived key length
	 *	@param   string  a   hash algorithm
	 *
	 *	@return  string  derived key
	 */
	public function pbkdf2( $p, $s, $c, $kl, $a = 'sha256' ) {

		$hl = strlen(hash($a, null, true));	# Hash length
		$kb = ceil($kl / $hl);				# Key blocks to compute
		$dk = '';							# Derived key

		# Create key
		for ( $block = 1; $block <= $kb; $block ++ ) {

			# Initial hash for this block
			$ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

			# Perform block iterations
			for ( $i = 1; $i < $c; $i ++ )

			# XOR each iterate
			$ib ^= ($b = hash_hmac($a, $b, $p, true));

			$dk .= $ib; # Append iterated block
		}

		# Return derived key of correct length
		return substr($dk, 0, $kl);
	}
}
?>