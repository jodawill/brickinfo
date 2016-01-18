<?php
add_action('admin_menu', 'bi_create_menu');

function bi_create_menu() {
 add_menu_page('brickinfo', 'BrickInfo', 'administrator', __FILE__, 'bi_settings_page');

 add_action('admin_init', 'bi_register_settings');
}

function bi_add_settings_link($links, $file) {
 if ( $file == plugin_basename(dirname(__FILE__) . '/brickinfo.php')) {
  $in = '<a href="admin.php?page=brickinfo/bi-options.php">Settings</a>';
  array_unshift($links, $in);
 }
 return $links;
}
add_filter( 'plugin_action_links', 'bi_add_settings_link', 10, 2 );

function bi_register_settings() {
 register_setting('brickinfo-settings-group', 'bi_token');
 register_setting('brickinfo-settings-group', 'bi_token_secret');
 register_setting('brickinfo-settings-group', 'bi_consumer_secret');
 register_setting('brickinfo-settings-group', 'bi_consumer_key');
 register_setting('brickinfo-settings-group', 'bi_cache_time');
 register_setting('brickinfo-settings-group', 'bi_show_fb_details');
}

function bi_settings_page() {
?>
<div class="wrap">
<h2>BrickInfo Settings</h2>

<form method="post" action="options.php">
 <?php settings_fields('brickinfo-settings-group'); ?>
 <?php do_settings_sections('brickinfo-settings-group'); ?>
 <table class="form-table">
  <tr valign="top">
   <th scope="row">Token</th>
   <td><input type="text" name="bi_token" value="<?php echo esc_attr(get_option('bi_token')); ?>" /></td>
  </tr>

  <tr valign="top">
   <th scope="row">Token Secret</th>
   <td><input type="text" name="bi_token_secret" value="<?php echo esc_attr(get_option('bi_token_secret')); ?>" /></td>
  </tr>

  <tr valign="top">
   <th scope="row">Consumer Key</th>
   <td><input type="text" name="bi_consumer_key" value="<?php echo esc_attr(get_option('bi_consumer_key')); ?>" /></td>
  </tr>

  <tr valign="top">
   <th scope="row">Consumer Secret</th>
   <td><input type="text" name="bi_consumer_secret" value="<?php echo esc_attr(get_option('bi_consumer_secret')); ?>" /></td>
  </tr>

  <tr valign="top">
   <th scope="row">Cache TTL (in hours)<br/><small>Recommended: 24</small></th>
   <td><input type="number" step="1" name="bi_cache_time" value="<?php echo esc_attr(get_option('bi_cache_time')); ?>" /></td>
  </tr>

  <tr valign="top">
   <th scope="row">Show Negative &amp; Neutral Feedback<br/></th>
   <td><input type="checkbox" name="bi_show_fb_details" <?php if (get_option('bi_show_fb_details') == "on") echo "checked"; else echo "unchecked"; ?>/></td>
  </tr>
 </table>
 <?php submit_button(); ?>
</form>
</div>
<?php } ?>
