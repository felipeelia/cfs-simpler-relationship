<?php
/**
 * Plugin Name: CFS - Simpler Relationship Field
 * Description: Generate a simpler field for relations between posts
 * Version: 1.0
 * Author: Felipe Elia
 * Author URI: https://felipeelia.com.br/
 * Text Domain: cfs-simpler-relationship
 * Domain Path: /languages
 * License: GPLv2 or later
 *
 * @package CFS_Simpler_Relationship
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the field to CFS.
 *
 * @param array $types Types already registered.
 * @return array       Types with ours included
 */
function cfs_simpler_relationship_add_field_type( $types ) {
	$types['simpler_relationship'] = __DIR__ . '/class-cfs-simpler-relationship.php';
	return $types;
}
add_filter( 'cfs_field_types', 'cfs_simpler_relationship_add_field_type' );

/**
 * Load plugin text domain.
 */
function cfs_simpler_relationship_load_textdomain() {
	load_plugin_textdomain( 'cfs-simpler-relationship', false, __DIR__ . '/languages/' );
}
add_action( 'init', 'cfs_simpler_relationship_load_textdomain' );
