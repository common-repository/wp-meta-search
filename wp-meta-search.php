<?php
/*
Plugin Name: WP Meta Search
Description: Flexible custom search form including custom-fields(postmeta), post-types, categories and tag queries.
Version: 1.1b
Plugin URI: 
Author: Y.Kohno
Author URI: 
License: GPLv2
Text Domain: yks-search
Domain Path: /languages
*/

/*
Copyright (C) 2017 Y.Kohno

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('YKS_WPMS_PLUGIN_URL', plugins_url('',__FILE__));
define('YKS_WPMS_PLUGIN_GRADE', 2);
define('YKS_WPMS_TD', 'yks-search');
define('YKS_WPMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ));

require_once('inc/vars.php');
require_once('inc/tools.php');
require_once('inc/search.php');
require_once('inc/admin.php');
require_once('inc/front.php');

require_once('add/taxonomy.php');
//require_once('add/range.php');
//require_once('add/order.php');



$yks_form_settings = get_option('yks-form-settings');

load_plugin_textdomain(YKS_WPMS_TD, false, basename( dirname( __FILE__ ) ).'/languages' );

/*
 add scripts and style
*/
add_action( 'wp_enqueue_scripts', 'yks_wpms_enqueue' );

function yks_wpms_enqueue() {
		wp_enqueue_script('yks-js-front', YKS_WPMS_PLUGIN_URL.'/js/yks-front.js' , array('jquery'));
		wp_enqueue_style('yks-widget-css', YKS_WPMS_PLUGIN_URL.'/css/yks-widget.css');
}

/**
 * Instantiate plugin main class
 */
function yks_wpms_search() {

	$yk_search = new YKS_Search;

	$yk_search->alter_query();

	if(YKS_Search::DEBUG_MODE){
		add_action( 'wp_footer', 'yks_wpms_debug', 100);
	}

}
add_action( 'plugins_loaded', 'yks_wpms_search' );

/*
echo SQL for debug
*/
function yks_wpms_debug(){
		global $wp_query;
		echo "###### output SQL for debug  #######<br />\n";
		echo $wp_query->request;
		echo "<br />\n###### output SQL for debug  #######";
	}

