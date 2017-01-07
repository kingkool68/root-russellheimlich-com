<?php
// Default mu-plugins directory if you haven't set it
defined( 'WPMU_PLUGIN_DIR' ) or define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );

require WPMU_PLUGIN_DIR . '/rh-multisite-functions/rh-multisite-functions.php';
require WPMU_PLUGIN_DIR . '/mercator/mercator.php';
