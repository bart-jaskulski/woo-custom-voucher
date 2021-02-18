<?php
/**
 * Dentonet\WP\Component_Interface interface
 *
 * @package denotnet
 */

namespace Dentonet\WP;

defined( 'ABSPATH' ) || exit;

/**
 * Implement this interface if any WordPress hooks are in use.
 */
interface Component_Interface {

	/**
	 * Add all hooks and filters to WordPress.
	 */
	public function initialize();
}
