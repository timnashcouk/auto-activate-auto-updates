<?php
/*
Plugin Name: Auto Activate Auto Updates
Description: Switch to opt-out rather then opt-in for updates
Version: 1.1.0
Author: Tim Nash
Author URI: https://timnash.co.uk
*/

/**
 *  Triggers on any activation of plugin
 *
 * @since 1.0.0
 * @return bool
 */
add_action( 'activated_plugin', function($plugin, $network){
	$auto_updates   = (array) get_site_option( 'auto_update_plugins', array() );
	$auto_updates[] = $plugin;
	$auto_updates   = array_unique( $auto_updates );

	$all_items      = apply_filters( 'all_plugins', get_plugins() );
	$auto_updates   = array_intersect( $auto_updates, array_keys( $all_items ) );

	// Update or set the auto update option
	update_site_option( 'auto_update_plugins', $auto_updates );
 }, 10,2);

/**
 * Triggers Activation Hook when plugin is first activated
 * @since 1.1.0
 * @return bool
 */
register_activation_hook( __FILE__, function(){
	$auto_updates   = (array) get_site_option( 'auto_update_plugins', array() );
	if( !empty( $auto_updates ) ){
		$all_items    = apply_filters( 'all_plugins', get_plugins() );
		return update_site_option( 'auto_update_plugins', array_keys( $all_items ) );
	}
	return true;
});
