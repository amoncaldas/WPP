<?php
/**
 * Plugin Name: FAM oauth
 * Description: Add api endpoint to process oauth *
 * Version:     0.0.1
 *
 * Author:      Amon Caldas
 * Author URI:  https://github.com/amoncaldas
 *
 * Text Domain: fam-oauth
 *
 * @package FAM_OAUTH
 */

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// WP API v1.
include_once 'includes/ors-oauth-wp-api.php';

if ( ! function_exists ( 'ors_auth_init' ) ) :

	function ors_auth_init() {
        if ( ! defined( 'JSON_API_VERSION' ) && ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {
			$class = new OrsOauthWPApi();
			 add_filter( 'rest_api_init', array( $class, 'register_routes' ) );
		} else {
			$class = new OrsOauthWPApi();
			add_filter( 'json_endpoints', array( $class, 'register_routes' ) );
		}
    }

	add_action( 'init', 'ors_auth_init' );

endif;
