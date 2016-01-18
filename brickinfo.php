<?php 
/**
* Plugin Name: BrickInfo
* Plugin URI: https://github.com/jodawill/brickinfo
* Description: Adds BrickLink feedback and BL forum-style item links to your BBPress forum.
* Version: 0.01
* Author: Josh Williams
* Author http://jodawill.com
* License: GPL2
*/
defined ('ABSPATH') or die('');

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

include_once('get_info.php');
include_once('bi-options.php');

function bi_activate() {
 bl_api_create_cache_table();
} register_activation_hook(__FILE__, 'bi_activate');

function bi_add_css() {
 wp_register_style('brickinfo', plugin_dir_url( __FILE__ ) . 'brickinfo.css');
 wp_enqueue_style('brickinfo');
} add_action('wp_enqueue_scripts', 'bi_add_css');

function bi_get_set_array($strs) {
 return bi_get_set($strs[1]);
}

function bi_get_part_array($strs) {
 return bi_get_part($strs[1]);
}

function bi_get_gear_array($strs) {
 return bi_get_gear($strs[1]);
}

function bi_get_minifig_array($strs) {
 return bi_get_minifig($strs[1]);
}

function bi_convert_links($message) {
 $message = preg_replace_callback('/\[s[^=]*=([0-9a-z-][0-9a-z-]*)\]/i', 'bi_get_set_array', $message);
 $message = preg_replace_callback('/\[p[^=]*=([0-9a-z-][0-9a-z-]*)\]/i', 'bi_get_part_array', $message);
 $message = preg_replace_callback('/\[g[^=]*=([0-9a-z-][0-9a-z-]*)\]/i', 'bi_get_gear_array', $message);
 $message = preg_replace_callback('/\[m[^=]*=([0-9a-z-][0-9a-z-]*)\]/i', 'bi_get_minifig_array', $message);
 return $message;
} add_filter('bbp_get_reply_content', 'bi_convert_links');

function bi_add_bl_contact($user_contact) {
 $user_contact['bricklink'] = __('BrickLink Username');
 return $user_contact;
} add_filter('user_contactmethods', 'bi_add_bl_contact');

function bi_add_profile_details() {
 $user_id = bbp_get_reply_author_id(bbp_get_reply_id());
 $username = get_user_meta($user_id, 'bricklink', true);
 echo bi_get_feedback($username);
} add_filter('bbp_theme_after_reply_author_details', 'bi_add_profile_details');
?>
