<?php
include_once('api.php');

function bi_bad_config() {
 return '<div class="brickinfo"><strong>BrickInfo Error:</strong> Not configured</div>';
}

function bi_timestamp_reused() {
 return '<div class="brickinfo"><strong>BrickInfo Error:</strong> Timestamp reused. Reload page to try again.';
}

function bi_dne() {
 return '<div><strong>BrickInfo Error:</strong> Item does not exist</div>';
}

function bi_get_set($set_number) {
 # Get JSON data from BrickLink API
 $request='/items/set/'.$set_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 if (!strpos($result, 'name') !== false) {
  return bi_dne();
 }

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;

 $name = $data -> name;
 $thumb_url = $data -> thumbnail_url;
 $set_info = $name;
 $year_released = $data -> year_released;

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img title="'.$set_info.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?S='.$set_number.'">'.$set_number.'</a> (<a style="font-weight:bold" href="http://www.bricklink.com/catalogItemInv.asp?S='.$set_number.'">Inv</a>) '.$set_info.'</strong><br>Released in '.$year_released.'
         <br/><hr/></div>';
 return $ret;
}

function bi_get_part($part_number) {
 # Get JSON data from BrickLink API
 $request='/items/part/'.$part_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 if (!strpos($result, 'name') !== false) {
  return bi_dne();
 }

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;
 $part_name = $data -> name;
 $thumb_url = $data -> thumbnail_url;
 $year_released = $data -> year_released;

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img title="'.$part_name.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?S='.$part_number.'">'.$part_number.'</a> '.$part_name.'</strong></div><hr/>';
 return $ret;
}

function bi_get_gear($gear_number) {
 # Get JSON data from BrickLink API
 $request='/items/gear/'.$gear_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 if (!strpos($result, 'name') !== false) {
  return bi_dne();
 }

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;
 $gear_name = $data -> name;
 $thumb_url = $data -> image_url; # API is broken; thumbnail URL for gear is 404
 $year_released = $data -> year_released;

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img width="80" height="60" title="'.$gear_name.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?S='.$gear_number.'">'.$gear_number.'</a> '.$gear_name.'</strong></div><hr/>';
 return $ret;
}

function bi_get_minifig($minifig_number) {
 # Get JSON data from BrickLink API
 $request='/items/minifig/'.$minifig_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 if (!strpos($result, 'name') !== false) {
  return bi_dne();
 }

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;

 $name = $data -> name;
 $thumb_url = $data -> thumbnail_url;
 $set_info = $name;
 $year_released = $data -> year_released;

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img title="'.$set_info.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?S='.$minifig_number.'">'.$minifig_number.'</a> (<a style="font-weight:bold" href="http://www.bricklink.com/catalogItemInv.asp?S='.$minifig_number.'">Inv</a>) '.$set_info.'</strong><br>Released in '.$year_released.'
         <br/><hr/></div>';
 return $ret;
}

function bi_get_feedback($username) {
 if ($username == '') return '';
 $data = bi_get_request("/members/$username/ratings");
 if (strpos($data, 'RESOURCE_NOT_FOUND') !== false) {
  return '<strong>Bad BL username</strong>';
 }
 $data = json_decode($data);
 $data = $data -> data;
 $data = $data -> rating;
 $good = $data -> PRAISE;
 $neutral = $data -> NEUTRAL;
 $bad = $data -> COMPLAINT;
 if (get_option('bi_show_fb_details') == "on") {
  $good = "<font style='color:#009900'>$good</font>";
  $neutral = "<font style='color:#999999'>$neutral</font>";
  $bad = "<font style='color:#990000'>$bad</font>";
  $good = "<a href='http://www.bricklink.com/feedback.asp?fdbType=0&u=$username'>$good</a>";
  $neutral = "<a href='http://www.bricklink.com/feedback.asp?fdbType=1&u=$username'>$neutral</a>";
  $bad = "<a href='http://www.bricklink.com/feedback.asp?fdbType=2&u=$username'>$bad</a>";
  $username = "<a href='http://www.bricklink.com/feedback.asp?u=$username'>$username</a>";
  $html = "$username<br/>($good|$neutral|$bad)";
 } else {
  $feedback = strval(intval($good) - intval($bad));
  $feedback = "<a href='http://www.bricklink.com/feedback.asp?u=$username'>$feedback</a>";
  $username = "<font style='color:#000099'>$username</font>";
  $html = "$username ($feedback)";
 }
 return $html;
}
?>