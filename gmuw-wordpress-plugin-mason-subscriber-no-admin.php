<?php

/**
 * Main plugin file for the Mason WordPress plugin: Subscriber No Admin
 */

/**
 * Plugin Name:       Mason WordPress: Subscriber No Admin
 * Author:            Mason Web Administration
 * Plugin URI:        https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-subscriber-no-admin
 * Description:       Mason WordPress Plugin to prevent subscribers from accessing the admin area, and otherwise customize the admin interface for subscribers
 * Version:           1.0
 */


// exit if this file is not called directly.
if (!defined('WPINC')) {
	die;
}


// Set up auto-updates
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
'https://github.com/mason-webmaster/gmuw-wordpress-plugin-mason-subscriber-no-admin/',
__FILE__,
'gmuw-wordpress-plugin-mason-subscriber-no-admin'
);


//function to determine if the user is logged in as a subscriber
function gmuw_sna_is_subscriber() {

	//get user current user info
	$current_user = wp_get_current_user();

	//if the user is logged in as a subscriber...
	if (count($current_user->roles) == 1 && $current_user->roles[0] == 'subscriber') {

		//user is logged-in as a subscriber
		return true;

	}

	//user is not logged-in as a subscriber
	return false;

}

// for subscribers, customize the wordpress admin bar
add_action('admin_bar_menu', 'gmuw_sna_customize_admin_bar', 999);
function gmuw_sna_customize_admin_bar($wp_admin_bar) {

	//if the user is logged in as a subscriber...
	if (gmuw_sna_is_subscriber()) {


		//turn off the admin bar completely (disabled)
		//show_admin_bar(false);

		// move logout link to be a parent link in the admin bar, for subscribers
		$wp_admin_bar->add_node(array(
			'id'     => 'logout',
			'parent' => false,
		));

		// remove unwanted links from admin bar
		$wp_admin_bar->remove_node( 'wp-logo' );
		$wp_admin_bar->remove_node( 'site-name' );
		$wp_admin_bar->remove_node( 'new-content' );
		$wp_admin_bar->remove_node( 'top-secondary' );

		// enqueue the custom subscriber stylesheet
		wp_enqueue_style(
			'gmuw_sna_subscriber_css',
			plugin_dir_url( __FILE__ ).'css/gmuw-sna-subscriber.css',
		);

	}

}

// redirect subscriber accounts from admin area to homepage
add_action('admin_init', 'gmuw_sna_redirect_subscriber_to_frontend');
function gmuw_sna_redirect_subscriber_to_frontend() {

	//if the user is logged in as a subscriber...
	if (gmuw_sna_is_subscriber()) {

		//redirect to home
		wp_redirect(site_url('/'));

		//exit
		exit;

	}

}


// redirect to home page ater logging-out
add_action('wp_logout','gmuw_sna_redirect_after_logout');
function gmuw_sna_redirect_after_logout(){

	//redirect to home page
	wp_safe_redirect(home_url());
	exit;

}
