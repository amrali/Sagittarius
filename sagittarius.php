<?php

/*  Copyright (c) 2012 Amr Ali <amr.ali.cc@gmail.com>
 *  All rights reserved.
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions
 *  are met:
 *  1. Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *  2. Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *  3. Neither the name of Amr Ali nor the names of his contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 *  THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 *  IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 *  OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 *  IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 *  INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 *  NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 *  DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 *  THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 *  THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 * Sagittarius
 *  A PHP Code Obfuscater
 *
 * Author
 *  Amr Ali
 *
 * Happy New Year to...
 *  All folks at - in no peculiar order -...
 *  STS, US, HaKT, CyberArmy, Cryogenix, DarkScience, GNY, CyberShade, XOR.CX,
 *  Rohitab, SABS
 */

/*
 * Rabbit Stream Cipher (RFC-4503) PHP Implementation.
 */
$rabbit_content = <<<'END_CONTENT'
class Rabbit {

	public $key;
	private $x;
	private $c;
	private $carry;
	private $ready;

	public function __construct($key) {
		$this->x = array();
		$this->c = array();
		$this->reset_flag = false;
		$this->key = $key;
	}

	public function encrypt(&$data) {
		$cipher_text = "";

		if (!$this->ready)
			$this->reset();

		$this->cipher($data, $cipher_text);
		return $cipher_text;
	}

	public function decrypt(&$data) {
		$plain_text = "";

		if (!$this->ready)
			$this->reset();

		$this->cipher($data, $plain_text);
		return rtrim($plain_text, "\0");
	}

	public function reset() {
		$this->key_setup($this->key);
		$this->ready = !$this->ready;
	}

	private function rotl($x, $rot) {
		return ($x << $rot) | ($x >> (32 - $rot));
	}

	private function toasc(&$str, $start, $range = 1) {
		$asc = 0;

		for ($i = 0; $i < $range; ++$i)
			$asc = ($asc << 8) | ord(substr($str, $start + $i, 1));

		return $asc;
	}

	private function tobytes($val, $range = 1) {
		$bytes = "";

		for ($i = 0; $i < $range; ++$i) {
			$bytes = $bytes . chr($val & 0xff);
			$val = $val >> 8;
		}

		return strrev($bytes);
	}

	private function g_func($x) {
		// Construct high and low argument for squaring
		$a = $x & 0xFFFF;
		$b = $x >> 16;

		// Calculate high and low result of squaring
		$h = (((($a * $a) >> 17) + ($a * $b)) >> 15) + ($b * $b);
		$l = $x * $x;

		// Return high XOR low
		return ($h ^ $l);
	}

	private function next_state() {
		$g = array();
		$c_old = array();

		// Save old counter values
		for ($i = 0; $i < 8; ++$i)
			$c_old[$i] = $this->c[$i];

		// Calculate new counter values
		$this->c[0] += 0x4d34d34d + $this->carry;
		$this->c[1] += 0xd34d34d3 + ($this->c[0] < $c_old[0]);
		$this->c[2] += 0x34d34d34 + ($this->c[1] < $c_old[1]);
		$this->c[3] += 0x4d34d34d + ($this->c[2] < $c_old[2]);
		$this->c[4] += 0xd34d34d3 + ($this->c[3] < $c_old[3]);
		$this->c[5] += 0x34d34d34 + ($this->c[4] < $c_old[4]);
		$this->c[6] += 0x4d34d34d + ($this->c[5] < $c_old[5]);
		$this->c[7] += 0xd34d34d3 + ($this->c[6] < $c_old[6]);
		$this->carry = ($this->c[7] < $c_old[7]);

		// Calculate the g-functions
		for ($i = 0; $i < 8; ++$i)
			$g[$i] = $this->g_func($this->x[$i] + $this->c[$i]);

		// Calculate new state values
		$this->x[0] = $g[0] + $this->rotl($g[7], 16) + $this->rotl($g[6], 16);
		$this->x[1] = $g[1] + $this->rotl($g[0], 8) + $g[7];
		$this->x[2] = $g[2] + $this->rotl($g[1], 16) + $this->rotl($g[0], 16);
		$this->x[3] = $g[3] + $this->rotl($g[2], 8) + $g[1];
		$this->x[4] = $g[4] + $this->rotl($g[3], 16) + $this->rotl($g[2], 16);
		$this->x[5] = $g[5] + $this->rotl($g[4], 8) + $g[3];
		$this->x[6] = $g[6] + $this->rotl($g[5], 16) + $this->rotl($g[4], 16);
		$this->x[7] = $g[7] + $this->rotl($g[6], 8) + $g[5];
	}

	private function key_setup(&$p_key) {
		// Generate four subkeys
		$k0 = $this->toasc($p_key, 0, 4);
		$k1 = $this->toasc($p_key, 4, 4);
		$k2 = $this->toasc($p_key, 8, 4);
		$k3 = $this->toasc($p_key, 12, 4);

		// Generate initial state variables
		$this->x[0] = $k0;
		$this->x[2] = $k1;
		$this->x[4] = $k2;
		$this->x[6] = $k3;
		$this->x[1] = ($k3 << 16) | ($k2 >> 16);
		$this->x[3] = ($k0 << 16) | ($k3 >> 16);
		$this->x[5] = ($k1 << 16) | ($k0 >> 16);
		$this->x[7] = ($k2 << 16) | ($k1 >> 16);

		// Generate initial counter values
		$this->c[0] = $this->rotl($k2, 16);
		$this->c[2] = $this->rotl($k3, 16);
		$this->c[4] = $this->rotl($k0, 16);
		$this->c[6] = $this->rotl($k1, 16);
		$this->c[1] = ($k0 & 0xFFFF0000) | ($k1 & 0xFFFF);
		$this->c[3] = ($k1 & 0xFFFF0000) | ($k2 & 0xFFFF);
		$this->c[5] = ($k2 & 0xFFFF0000) | ($k3 & 0xFFFF);
		$this->c[7] = ($k3 & 0xFFFF0000) | ($k0 & 0xFFFF);

		// Reset carry flag
		$this->carry = 0;

		// Iterate the system four times
		for ($i = 0; $i < 4; ++$i)
			$this->next_state();

		// Modify the counters
		for ($i = 0; $i < 8; ++$i)
			$this->c[($i + 4) & 0x7] ^= $this->x[$i];
	}

	private function cipher(&$p_src, &$p_dest) {
		$data_size = strlen($p_src);

		for ($i = 0; $i < $data_size; $i += 16) {
			// Iterate the system
			$this->next_state();

			// Encrypt 16 bytes of data
			$p_dest = $p_dest . $this->tobytes(
					$this->toasc($p_src, $i, 4) ^
					($this->x[0]) ^ ($this->x[5] >> 16) ^
					($this->x[3] << 16), 4);

			$p_dest = $p_dest . $this->tobytes(
					$this->toasc($p_src, $i + 4, 4) ^
					($this->x[2]) ^ ($this->x[7] >> 16) ^
					($this->x[5] << 16), 4);

			$p_dest = $p_dest . $this->tobytes(
					$this->toasc($p_src, $i + 8, 4) ^
					($this->x[4]) ^ ($this->x[1] >> 16) ^
					($this->x[7] << 16), 4);

			$p_dest = $p_dest . $this->tobytes(
					$this->toasc($p_src, $i + 12, 4) ^
					($this->x[6]) ^ ($this->x[3] >> 16) ^
					($this->x[1] << 16), 4);
		}
	}
}
END_CONTENT;

// Interpret class Rabbit
eval($rabbit_content);

// Code obfuscater
class Obfuscate {

	private $level;

	public function __construct($level = 5) {
		$this->level = $level;
	}

	public function encode(&$input, &$rabbit_instance = NULL) {
		$buffer = gzdeflate($input, $this->level);

		if ($rabbit_instance)
			$buffer = $rabbit_instance->encrypt($buffer);

		return base64_encode($buffer);
	}

	public function decode(&$input, &$rabbit_instance = NULL) {
		$buffer = base64_decode($input);

		if ($rabbit_instance)
			$buffer = $rabbit_instance->decrypt($buffer);

		return gzinflate($buffer);
	}

	public function encode_contained(&$input, &$rabbit_instance = NULL) {
		$content = $this->encode($input, $rabbit_instance);
		$content = '$_0xdeadbeef = ' . "'$content';\n";

		if ($rabbit_instance) {
			global $rabbit_content;
			$payload = $this->encode($rabbit_content);
			$content =
				"\$_0x31337 = '128-bit KEY';\n"
				. "\$_0xdeadc0de = '$payload';\n$content\n"
				. "eval(gzinflate(base64_decode(\$_0xdeadc0de)));\n"
				. "\$_0xfee1dead = new Rabbit(\$_0x31337);\n"
				. "\$_0xdefec8ed = base64_decode(\$_0xdeadbeef);\n"
				. "eval(gzinflate(\$_0xfee1dead->decrypt(\$_0xdefec8ed)));\n";
		} else
			$content .= "eval(gzinflate(base64_decode(\$_0xdeadbeef)));\n";

		return $content;
	}
}

?>
