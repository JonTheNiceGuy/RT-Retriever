<?php

set_time_limit(0);
date_default_timezone_set("Europe/London");

$url='http://xmltv.radiotimes.com/xmltv/';

require_once("db.php");

d_mysql_query("TRUNCATE channels;");
d_mysql_query("TRUNCATE listings;");
d_mysql_query("TRUNCATE people;");

$channel_map=getQueryAsArray("SELECT channel_id FROM channel_map WHERE have_access='1'", "channel_id");

$stream=getStream($url . "channels.dat");
if($stream!=FALSE) {
  $channel_lines=preg_split('/[\r\n]/', trim($stream));
  foreach($channel_lines as $channel_line) {
    list($channel_no, $channel_name)=explode("|", $channel_line);
    if($channel_no!='' and $channel_name!='') {
      $channel[$channel_no]=$channel_name;
      $sql="INSERT INTO channels VALUES ('$channel_no', '" . mysql_real_escape_string($channel_name) . "')";
      d_mysql_query($sql);
    }
  }

  foreach($channel as $channel_no=>$channel_name) {
    echo "Processing $channel_name [Stream No: $channel_no]\r\n";
    if(!isset($channel_map[$channel_no]) and $get_all_channels!=TRUE) {echo "No access.\r\n";} else {
    $stream=getStream($url . $channel_no . ".dat");
    if($stream!=FALSE) {
      $listing_lines=preg_split('/[\r\n]/', trim($stream));
      $listing_count=count($listing_lines);
      for($line_no=1; $line_no<=$listing_count; $line_no++) {
        $line=$listing_lines[$line_no];
        $columns=explode("~", $line);
        while(count($columns)<23 AND $line_no<=$listing_count) {
          $line_no++;
          $line.="\r\n" . $listing_lines[$line_no];
          $columns=explode("~", $line);
        }
        $start_date=substr($columns[19], 6, 4) . "-" . substr($columns[19], 3, 2) . "-" . substr($columns[19], 0, 2);
        $start_time=strtotime($start_date . " " . $columns[20]);
        $end_time=strtotime($start_date . " " . $columns[21]);
        if($columns[21]<$columns[20]) {
          $end_time=$end_time+86400;
        }
        $start=date('Y-m-d H:i:s', $start_time);
        $end=date('Y-m-d H:i:s', $end_time);
        $sql="INSERT INTO listings VALUES (".
                                   "'', ".                                                     // listing_id
                                   "'$channel_no', ".                                          // channel_id
                                   "'" . mysql_real_escape_string(trim($columns[0])) . "', ".  // show_name
                                   "'" . mysql_real_escape_string(trim($columns[1])) . "', ".  // location
                                   "'" . mysql_real_escape_string(trim($columns[2])) . "', ".  // episode_name
                                   "'" . mysql_real_escape_string(trim($columns[3])) . "', ".  // release_year
                                   "'" . mysql_real_escape_string(trim($columns[4])) . "', ".  // director
                                   "'" . mysql_real_escape_string(trim($columns[5])) . "', ".  // cast
                                   "'" . mysql_real_escape_string(trim($columns[6])) . "', ".  // is_premier
                                   "'" . mysql_real_escape_string(trim($columns[7])) . "', ".  // is_film
                                   "'" . mysql_real_escape_string(trim($columns[8])) . "', ".  // is_repeat
                                   "'" . mysql_real_escape_string(trim($columns[9])) . "', ".  // is_subtitled
                                   "'" . mysql_real_escape_string(trim($columns[10])) . "', ". // is_in_widescreen
                                   "'" . mysql_real_escape_string(trim($columns[11])) . "', ". // is_new_series
                                   "'" . mysql_real_escape_string(trim($columns[12])) . "', ". // is_BSL_signed
                                   "'" . mysql_real_escape_string(trim($columns[13])) . "', ". // is_black_and_white
                                   "'" . mysql_real_escape_string(trim($columns[14])) . "', ". // rating_stars
                                   "'" . mysql_real_escape_string(trim($columns[15])) . "', ". // rating_age
                                   "'" . mysql_real_escape_string(trim($columns[16])) . "', ". // genre
                                   "'" . mysql_real_escape_string(trim($columns[17])) . "', ". // summary
                                   "'" . mysql_real_escape_string(trim($columns[18])) . "', ". // is_radio_times_choice
                                   "'$start', '$end', ".                                       // time_start, time_end
                                   "'" . mysql_real_escape_string(trim($columns[22])) . "');"; // duration
        if($start!='1970-01-01 01:00:00') {
          d_mysql_query($sql);
          $show_id=mysql_insert_id();
          if(strstr($columns[5], ",")) {
            foreach(explode(",", $columns[5]) as $person) {
              $e_person=mysql_real_escape_string($person);
              $sql="INSERT INTO people VALUES ('', '$show_id', '$e_person', '')";
              d_mysql_query($sql);
            }
          } elseif(strstr($columns[5], "|")) {
            foreach(explode("|", $columns[5]) as $person) {
              list($role,$actor)=explode("*", $person);
              $e_actor=mysql_real_escape_string($actor);
              $e_role=mysql_real_escape_string($role);
              $sql="INSERT INTO people VALUES ('', '$show_id', '$e_actor', '$role')";
              d_mysql_query($sql);
            }
          }
        }
      }
    } else {
      echo "This channel isn't providing a feed right now\r\n";
    }
  }}
}

function getStream($url) {
  echo "Opening stream $url\r\n";
  $handle=fopen($url,'r');
  $stream_counter=0;
  while($get=fread($handle, 128)) {
    $stream_counter++;
    $stream.=$get;
  }
  fclose($handle);
  if(strlen($stream_counter<2)) {$stream=FALSE;}
  return($stream);
}

?>
