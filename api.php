<?php
define('URL_API', 'https://api.bricklink.com/api/store/v1');
define("CONSUMER_SECRET", get_option('bi_consumer_secret'));
define("CONSUMER_KEY", get_option('bi_consumer_key'));
define("TOKEN", get_option('bi_token'));
define("TOKEN_SECRET", get_option('bi_token_secret'));

function bi_get_request($request) {
 if (!bi_are_options_set()) {
  return 'BAD_CONFIG';
 }

 # Check to see if request exists in cache.
 $result = bi_find_cache($request);

 # If not, store it in the cache.
 if (strlen($result) <= 0) $result = bi_store_cache($request);

 return $result;
}

# Attempt to retrieve request response from cache
function bi_find_cache($request) {
 global $wpdb;
 $table_name = bi_table_name();
 $sql = "SELECT id, response, date FROM $table_name
         WHERE request = '$request'";
 $result = $wpdb -> get_results($sql);
 $ret = '';
 foreach ($result as $row) {
  $ret = $row -> response;
  $date = $row -> date;

  # Delete item from database if cache has expired
  if (intval(strtotime($date)) < intval(current_time('U')) - 3600*intval(get_option('bi_cache_time'))) {
   $wpdb -> delete($table_name, array('ID' => ($row -> id)));
   $ret = '';
  }
 }
 return unserialize($ret);
}

function bi_store_cache($request) {
 global $wpdb;
 $table_name = bi_table_name();
 $response = bi_api_call($request);

 # If the timestamp is reused, don't store bad data in cache
 if (strpos($response, "TIMESTAMP_REUSED") !== false) return 'TIMESTAMP_REUSED';

 $ret = $response;
 $response = serialize($response);
 $data = array(
               'response' => $response,
               'request' => $request,
               'date' => current_time('mysql')
              );
 $wpdb -> insert($table_name, $data);
 return $ret;
}

function bl_api_create_cache_table() {
 require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 global $wpdb;
 $table_name = bi_table_name();
 $charset_collate = $wpdb -> get_charset_collate();
 $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  request longtext,
  response longtext,
  date datetime,
  UNIQUE KEY id (id)
 ) $charset_collate;";
 dbDelta($sql);
}

function bi_table_name() {
 global $wpdb;
 return $wpdb -> prefix . "bi_api_cache";
}

function bi_are_options_set() {
 return strlen(TOKEN) > 0
     && strlen(TOKEN_SECRET) > 0
     && strlen(CONSUMER_SECRET) > 0
     && strlen(CONSUMER_KEY) > 0;
}

function bi_api_call($request) {
 $request = URL_API.$request;
 $auth = bi_auth_create($request);
 $curl_url = "$request?Authorization=$auth";
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $curl_url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLINFO_HEADER_OUT, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
 $result = curl_exec($ch);
 if (!$result) {
  return $curl_error($ch);
 } else {
  return $result;
 }
}

function bi_auth_create($request) {
 $time = time();
 $randstr = bi_generate_random_string(10);
 $ordered_str = 'oauth_consumer_key='.CONSUMER_KEY."&oauth_nonce=$randstr&oauth_signature_method=HMAC-SHA1&oauth_timestamp=$time&oauth_token=".TOKEN.'&oauth_version=1.0';
 $signature_base_string = "GET&" . rawurlencode($request) . '&' . rawurlencode($ordered_str);
 $signature = bi_create_signature($signature_base_string);
 return $auth = rawurlencode('{"oauth_consumer_key":"'.CONSUMER_KEY.'","oauth_nonce":"'.$randstr.'","oauth_signature_method":"HMAC-SHA1","oauth_signature":"'.$signature.'","oauth_timestamp":"'.$time.'","oauth_token":"'.TOKEN.'","oauth_version":"1.0"}');
}

function bi_create_signature($base_string) {
 $secret_string = CONSUMER_SECRET . '&' . TOKEN_SECRET;
 return base64_encode(hash_hmac('sha1', $base_string, $secret_string, true));
}

function bi_generate_random_string($length = 10) {
 $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
 $str = '';
 $chars_length = strlen($chars);
 for ($i = 0; $i < $length; $i++) {
  $str = $chars[rand(0, $chars_length - 1)];
 }
 return $str;
}
