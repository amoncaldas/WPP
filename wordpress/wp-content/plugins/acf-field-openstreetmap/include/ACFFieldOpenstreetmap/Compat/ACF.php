<?php

namespace ACFFieldOpenstreetmap\Compat;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


use ACFFieldOpenstreetmap\Core;
use ACFFieldOpenstreetmap\Field;


class ACF extends Core\PluginComponent {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		// include field
		if ( 'acf/include_field_types' === current_action() ) {
			$this->include_field_types();
		} else {
			add_action('acf/include_field_types', 	array( $this, 'include_field_types')); // v5
		}
		add_action( 'acf/render_field/type=leaflet_map', array( $this, 'render_map_input' ) );

		// Compat with https://github.com/mcguffin/polylang-sync
		add_filter('polylang_acf_sync_supported_fields', array( $this, 'add_pll_sync_field_type') );

	}

	/**
	 *	@filter polylang_acf_sync_supported_fields
	 */
	public function add_pll_sync_field_type($fields) {
		$fields[] = 'open_street_map';
		return $fields;
	}

	/**
	 *	@action acf/render_field/type=leaflet_map
	 */
	public function render_map_input( $field ) {

		$inp_field = array(
			'return_format'	=> 'leaflet',
			'height'		=> 400,
		);

		if ( isset( $field['attr'] ) ) {
			$inp_field['attr'] = $field['attr'];
		}

		$map_field = Field\OpenStreetMap::get_instance();

		echo $map_field->format_value( $field['value'], null, $inp_field );
	}

	/**
	 *  include_field_types
	 *
	 *  This function will include the field type class
	 *
	 *  @type	function
	 *  @date	17/02/2016
	 *  @since	1.0.0
	 *
	 *  @param	$version (int) major ACF version. Defaults to false
	 *  @return	n/a
	 */
	function include_field_types( $version = false ) {

		// array(
		// 	'version'	=> '0.0.1',
		// 	'url'		=> plugin_dir_url( __FILE__ ),
		// 	'path'		=> plugin_dir_path( __FILE__ )
		// );

		Field\OpenStreetMap::get_instance();
	}

	/**
	 *	@inheritdoc
	 */
	public function activate(){

	}

	/**
	 *	@inheritdoc
	 */
	public function deactivate(){

	}

	/**
	 *	@inheritdoc
	 */
	public static function uninstall() {
	 // remove content and settings
	}

	/**
 	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {
	}

}
