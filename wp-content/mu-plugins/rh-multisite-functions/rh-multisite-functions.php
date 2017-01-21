<?php
/*
Plugin Name: RH Multisite Functions
Description: Special functions available to all of the sites hosted on root.russellheimlich.com
Author: Russell Heimlich
Version: 0.1
*/

// Disable Mercators SSO functionality which slows down page loads via an AJAX request
add_filter( 'mercator.sso.enabled', '__return_false' );
add_filter( 'mercator.sso.multinetwork.enabled', '__return_false' );

/**
 * Helper conditionals to run code in certain environments.
 */

if ( ! defined( 'RH_ENV' ) ) {
	$hostname = $_SERVER['HTTP_HOST'];
	if ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
	    $hostname = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	if ( strstr( $hostname, '.dev' ) ) {
		define( 'RH_ENV', 'dev' );
	} else {
		define( 'RH_ENV', 'production' );
	}
}

/**
 * Is the enviornment production?
 * @return boolean
 */
function rh_is_production() {
	if ( 'production' === RH_ENV ) {
		return true;
	}
	return false;
}

/**
 * Alias of rh_is_production().
 * @return boolean
 */
function rh_is_prod() {
	return rh_is_production();
}

/**
 * Is the environment staging?
 * @return boolean
 */
function rh_is_staging() {
	if ( 'staging' === RH_ENV ) {
		return true;
	}
	return false;
}

/**
 * Alias of rh_is_staging().
 * @return boolean
 */
function rh_is_stage() {
	return rh_is_staging();
}

/**
 * Is the environment dev?
 * @return boolean
 */
function rh_is_dev() {
	if ( 'dev' === RH_ENV ) {
		return true;
	}
	return false;
}

/**
 * Custom From address for wp_mail()
 * @return string
 */
function rh_wp_mail_from() {
	return 'wordpress@russellheimlich.com';
}
add_filter( 'wp_mail_from', 'rh_wp_mail_from' );

/**
 * Custom From name for wp_mail()
 * @return string
 */
function rh_wp_mail_from_name() {
	$parts = parse_url( get_site_url() );
	return 'WordPress (' . $parts['host'] . ')';
}
add_filter( 'wp_mail_from_name', 'rh_wp_mail_from_name' );

function rh_replace_with_cdn_url( $url = '' ) {
	if ( ! defined( 'RH_CDN_URL' ) || empty( RH_CDN_URL ) ) {
		return $url;
	}
	$cdn_url = untrailingslashit( RH_CDN_URL );
	$site_url = get_site_url();
	$url = str_ireplace( $site_url, $cdn_url, $url );
	return $url;
}

$filters = array(
	'stylesheet_uri',
	'stylesheet_directory_uri',
	'template_directory_uri',
	'script_loader_src',
	'style_loader_src',
	'includes_url',
	'plugins_url',
	'theme_root_uri',
	'wp_get_attachment_url',
	'cache_busting_path_base_url',
);
foreach ( $filters as $filter ) {
	add_filter( $filter, 'rh_replace_with_cdn_url', 10, 1 );
}

function rh_filter_upload_dir( $data = array() ) {
	$data['baseurl'] = rh_replace_with_cdn_url($data['baseurl']);
	return $data;
}
add_filter( 'upload_dir', 'rh_filter_upload_dir' );

/**
 * Correct the cookie domain when a custom domain is mapped to the site.
 *
 * @see https://blog.handbuilt.co/2016/07/07/fixing-cookie_domain-for-mapped-domains-on-multisite/
 */
add_action( 'muplugins_loaded', function() {
    global $current_blog, $current_site;
    if ( false === stripos( $current_blog->domain, $current_site->cookie_domain ) ) {
        $current_site->cookie_domain = $current_blog->domain;
    }
} );
