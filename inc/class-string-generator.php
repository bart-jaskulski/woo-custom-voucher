<?php

class String_Generator {
	/**
	 * Create a random string for voucher.
	 *
	 * @return string
	 */
	public function generate() : string {
		return substr( md5( time() ), 0, 12 );
	}
}
