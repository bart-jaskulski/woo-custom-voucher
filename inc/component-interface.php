<?php
defined( 'ABSPATH' ) || exit;

interface Component_Interface {

  /**
   * Add all hooks and filters to WordPress.
   */
  public function initialize();
}
