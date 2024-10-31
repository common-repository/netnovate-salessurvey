<?php

/*
Plugin Name: salessurvey.de Auctions Plugin for eBay
Plugin URI: https://www.salessurvey.de/
Description: This plugin gives you the ability to show your on eBay listed items on your WordPress in realtime using various display templates like listings, item-sliders or as an item-gallery. You are also able to embed feedback-badges and widgets on your sidebar using <a href="widgets.php">Widgets</a>.
Author: Tommy Haas
Text Domain: netnovate-salessurvey
Domain Path: /languages/
Version: 1.31
Author URI: https://www.salessurvey.de
License: GPL2
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

$ss_plugin_config = array();

require_once('inc/backend.php'); 
require_once('inc/frontend.php'); 
require_once('inc/widgets.php'); 

// load languages
function salessurvey_load_plugin_textdomain() {
    load_plugin_textdomain('netnovate-salessurvey', false, basename(dirname(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'salessurvey_load_plugin_textdomain');
