<?php
class ArOn_Crud_Tools_Crypt {

	private $td;
	private $iv;
	private $ks;
	private $salt;
	private $encStr;
	private $decStr;

	private $encMessage;
	private $id = null;
	private $date = null;

	/**
	 *  The constructor initializes the cryptography library
	 * @param $salt string The encryption key
	 * @return void
	 */
	function __construct($salt) {
		$this->td = mcrypt_module_open ( 'rijndael-256', '', 'ofb', '' ); // algorithm
		$this->ks = mcrypt_enc_get_key_size ( $this->td ); // key size needed for the algorithm
		$this->salt = substr ( sha1 ( $salt ), 0, $this->ks );
	}

	/**
	 * Generates a hex string of $src
	 * @param $src string String to be encrypted
	 * @return void
	 */
	function encrypt($src) {
		srand ( ( double ) microtime () * 1000000 ); //for sake of MCRYPT_RAND
		$this->iv = mcrypt_create_iv ( $this->ks, MCRYPT_RAND );
		mcrypt_generic_init ( $this->td, $this->salt, $this->iv );
		$tmpStr = mcrypt_generic ( $this->td, $src );
		mcrypt_generic_deinit ( $this->td );
		mcrypt_module_close ( $this->td );

		//convert the encrypted binary string to hex
		//$this->iv is needed to decrypt the string later. It has a fixed length and can easily
		//be seperated out from the encrypted String


		$this->encStr = bin2hex ( $this->iv . $tmpStr );
		return $this;
	}

	function addSha($src) {
		$this->encMessage = $src . "_" . sha1 ( $src );
		return $this;
	}

	function getEncMessage() {
		return $this->encMessage;
	}

	function encUserId($id) {
		return $this->addSha ( $id . "|" . time () )->encrypt ( $this->encMessage )->getEncStr ();
	}
	/**
	 * Decrypts a hex string
	 * @param $src string String to be decrypted
	 * @return void
	 */
	function decrypt($src) {
		//convert the hex string to binary
		$corrected = preg_replace ( "/[^0-9a-fA-F]/i", "", $src );
		$binenc = pack ( "H" . strlen ( $corrected ), $corrected );

		//retrieve the iv from the encrypted string
		$this->iv = substr ( $binenc, 0, $this->ks );

		//retrieve the encrypted string alone(minus iv)
		$binstr = substr ( $binenc, $this->ks );

		/* Initialize encryption module for decryption */
		mcrypt_generic_init ( $this->td, $this->salt, $this->iv );
		/* Decrypt encrypted string */
		$decrypted = mdecrypt_generic ( $this->td, $binstr );

		/* Terminate decryption handle and close module */
		mcrypt_generic_deinit ( $this->td );
		mcrypt_module_close ( $this->td );
		$this->decStr = trim ( $decrypted );

	}

	function check($src) {
		$this->decrypt ( $src );
		$m_a = explode ( "_", $this->decStr );
		if (is_array ( $m_a ) && ! empty ( $m_a [0] ) && ! empty ( $m_a [1] ) && sha1 ( $m_a [0] ) == $m_a [1]) {
			$date = explode ( '|', $m_a [0] );
			$this->id = $date [0];
			$this->date = $date [1];
			return true;
		}
		return false;
	}

	function getEncStr() {
		return $this->encStr;
	}

	function getDecStr() {
		return $this->decStr;
	}

	function getUserId() {
		return ($this->id !== null) ? $this->id : false;
	}

	function getUserDate() {
		return ($this->date !== null) ? $this->date : false;
	}
	
	
	/* XXTEA encryption arithmetic library.
	*
	* This library is free.  You can redistribute it and/or modify it.
	*/
	
	static function long2str($v, $w) {
	    $len = count($v);
	    $n = ($len - 1) << 2;
	    if ($w) {
	        $m = $v[$len - 1];
	        if (($m < $n - 3) || ($m > $n)) return false;
	        $n = $m;
	    }
	    $s = array();
	    for ($i = 0; $i < $len; $i++) {
	        $s[$i] = pack("V", $v[$i]);
	    }
	    if ($w) {
	        return substr(join('', $s), 0, $n);
	    } else {
	        return join('', $s);
	    }
	}
	
	static function str2long($s, $w) {
	    $v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
	    $v = array_values($v);
	    if ($w) {
	        $v[count($v)] = strlen($s);
	    }
	    return $v;
	}

	static function int32($n) {
	    while ($n >= 2147483648) $n -= 4294967296;
	    while ($n <= -2147483649) $n += 4294967296;
	    return (int)$n;
	}
	
	static function xxtea_encrypt($str, $key) {
	    if ($str == "") {
	        return "";
	    }
	    $v = self::str2long($str, true);
	    $k = self::str2long($key, false);
	    if (count($k) < 4) {
	        for ($i = count($k); $i < 4; $i++) {
	            $k[$i] = 0;
	        }
	    }
	    $n = count($v) - 1;
	
	    $z = $v[$n];
	    $y = $v[0];
	    $delta = 0x9E3779B9;
	    $q = floor(6 + 52 / ($n + 1));
	    $sum = 0;
	    while (0 < $q--) {
	        $sum = self::int32($sum + $delta);
	        $e = $sum >> 2 & 3;
	        for ($p = 0; $p < $n; $p++) {
	            $y = $v[$p + 1];
	            $mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
	            $z = $v[$p] = int32($v[$p] + $mx);
	        }
	        $y = $v[0];
	        $mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
	        $z = $v[$n] = self::int32($v[$n] + $mx);
	    }
	    return self::long2str($v, false);
	}

	static function xxtea_decrypt($str, $key) {
	    if ($str == "") {
	        return "";
	    }
	    $v = self::str2long($str, false);
	    $k = self::str2long($key, false);
	    if (count($k) < 4) {
	        for ($i = count($k); $i < 4; $i++) {
	            $k[$i] = 0;
	        }
	    }
	    $n = count($v) - 1;
	
	    $z = $v[$n];
	    $y = $v[0];
	    $delta = 0x9E3779B9;
	    $q = floor(6 + 52 / ($n + 1));
	    $sum = self::int32($q * $delta);
	    while ($sum != 0) {
	        $e = $sum >> 2 & 3;
	        for ($p = $n; $p > 0; $p--) {
	            $z = $v[$p - 1];
	            $mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
	            $y = $v[$p] = self::int32($v[$p] - $mx);
	        }
	        $z = $v[$n];
	        $mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
	        $y = $v[0] = self::int32($v[0] - $mx);
	        $sum = self::int32($sum - $delta);
	    }
	    return self::long2str($v, true);
	}
	
}