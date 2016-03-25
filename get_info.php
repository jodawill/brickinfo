<?php
include_once('api.php');

function bi_bad_config() {
 return '<div class="brickinfo"><strong>BrickInfo Error:</strong> Not configured</div>';
}

function bi_timestamp_reused() {
 return '<div class="brickinfo"><strong>BrickInfo Error:</strong> Timestamp reused. Reload page to try again.';
}

function bi_get_set($set_number) {
 # Get JSON data from BrickLink API
 $request='/items/set/'.$set_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;

 if (isset($data -> thumbnail_url)) {
  $thumb_url = $data -> thumbnail_url;
 } else {
  $thumb_url = 'http://static.bricklink.com/clone/img/no_image.png';
 }

 if (isset($data -> name)) {
  $name = $data -> name;
 } else {
  $name = 'Unknown Set';
 }

 if (isset($data -> year_released)) {
  $year_released = $data -> year_released;
 } else {
  $year_released = '????';
 }

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img title="'.$name.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?S='.$set_number.'">'.$set_number.'</a> (<a style="font-weight:bold" href="http://www.bricklink.com/catalogItemInv.asp?S='.$set_number.'">Inv</a>) '.$name.'</strong><br>Released in '.$year_released.'
         <br/><hr/></div>';
 return $ret;
}

function bi_get_part($part_number) {
 # Get JSON data from BrickLink API
 $request='/items/part/'.$part_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 $result = json_decode($result);
 $data = $result -> data;
 # Convert JSON into meaningful data
 if (isset($data -> thumbnail_url)) {
  $thumb_url = $data -> thumbnail_url;
 } else {
  $thumb_url = 'http://static.bricklink.com/clone/img/no_image.png';
 }
 if (isset($data -> name)) {
  $part_name = $data -> name;
 } else {
  $part_name = 'Unknown Part';
 }
 if (isset($data -> year_released)) {
  $year_released = $data -> year_released;
 } else {
  $year_released = '????';
 }

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img title="'.$part_name.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?P='.$part_number.'">'.$part_number.'</a> '.$part_name."</strong><br/>Released in $year_released</div><hr/>";
 return $ret;
}

function bi_get_gear($gear_number) {
 # Get JSON data from BrickLink API
 $request='/items/gear/'.$gear_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;

 if (isset($data -> name)) {
  $gear_name = $data -> name;
 } else {
  $gear_name = 'Unknown gear';
 }

 if (isset($data -> image_url)) {
  $thumb_url = $data -> image_url; # API is broken; thumbnail URL for gear is 404
 } else {
  $thumb_url = 'http://static.bricklink.com/clone/img/no_image.png';
 }

 if (isset($data -> year_released)) {
  $year_released = $data -> year_released;
 } else {
  $year_released = '????';
 }

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img width="80" height="60" title="'.$gear_name.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?G='.$gear_number.'">'.$gear_number.'</a>'.$gear_name."</strong><br/>Released in $year_released</div><hr/>";
 return $ret;
}

function bi_get_minifig($minifig_number) {
 # Get JSON data from BrickLink API
 $request='/items/minifig/'.$minifig_number;
 $result=bi_get_request($request);

 if ($result == "BAD_CONFIG") return bi_bad_config();
 if ($result == "TIMESTAMP_REUSED") return bi_timestamp_reused();

 # Convert JSON into meaningful data
 $result = json_decode($result);
 $data = $result -> data;

 if (isset($data -> name)) {
  $name = $data -> name;
 } else {
  $name = 'Unknown Minifigure';
 }
 if (isset($data -> thumbnail_url)) {
  $thumb_url = $data -> thumbnail_url;
 } else {
  $thumb_url = 'http://static.bricklink.com/clone/img/no_image.png';
 }
 if (isset($data -> year_released)) {
  $year_released = $data -> year_released;
 } else {
  $year_released = '????';
 }

 # Create pretty display of set information
 $ret = '<div class="brickinfo">
          <img title="'.$name.'" src="'.$thumb_url.'"><strong><a style="font-weight:bold" href="http://alpha.bricklink.com/pages/clone/catalogitem.page?M='.$minifig_number.'">'.$minifig_number.'</a> (<a style="font-weight:bold" href="http://www.bricklink.com/catalogItemInv.asp?S='.$minifig_number.'">Inv</a>) '.$name.'</strong><br>Released in '.$year_released.'
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
 if (get_option('bi_show_fb_details', 'on') == "on") {
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
