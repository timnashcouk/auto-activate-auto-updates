<?php
/*
Plugin Name: Auto Activate Auto Updates
Description: Switch to opt-out rather then opt-in for updates
Version: 1.2.0
Author: Tim Nash
Author URI: https://timnash.co.uk
*/

/**
 *  Triggers on any activation of a plugin
 *
 * @since 1.0.0
 * @return bool
 */
add_action( 'activated_plugin', function($plugin, $network){
	// Check our existing deactivation list of plugins to check if we shouldn't be activating the plugin
	$deactivated_auto_updates   = (array) get_site_option( 'auto_update_deactivated_plugins', array() );
	if( in_array( $plugin, $deactivated_auto_updates ) ){
		//remove it from the list as its now an active plugin
		unset( $deactivated_auto_updates[ $plugin ] );
		return update_site_option( 'auto_update_deactivated_plugins', $deactivated_auto_updates );
	}
	// If plugin hasn't been previously deactivatate carry on
	$auto_updates   = (array) get_site_option( 'auto_update_plugins', array() );
	$auto_updates[] = $plugin;
	$auto_updates   = array_unique( $auto_updates );

	$all_items      = apply_filters( 'all_plugins', get_plugins() );
	$auto_updates   = array_intersect( $auto_updates, array_keys( $all_items ) );

	// Update or set the auto update option
	update_site_option( 'auto_update_plugins', $auto_updates );
 }, 10,2);

 /**
  *  Store plugins that should be auto updated on reactivation
  *  @since 1.2.0
  *  @return bool
  */
 add_action( 'deactivate_plugin', function( $plugin, $network ){
	$no_plugin_update	= true;
	$auto_updates   = (array) get_site_option( 'auto_update_plugins', array() );
	if( in_array( $plugin, $auto_updates ) ) $no_plugin_update = false;
	if( isset($no_plugin_update) && true === $no_plugin_update ){
		$deactivated_auto_updates   = (array) get_site_option( 'auto_update_deactivated_plugins', array() );
		$deactivated_auto_updates[]	= $plugin;
		update_site_option( 'auto_update_deactivated_plugins', $deactivated_auto_updates );
	}
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

/**
 * Triggers Uninstall Hook when the plugin is removed
 * @since 1.2.0
 * @return bool
 */
register_uninstall_hook( __FILE__, function(){
	// Delete our option 
	delete_option( 'auto_update_deactivated_plugins' );
});